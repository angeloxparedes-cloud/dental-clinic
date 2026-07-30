<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers/mailer.php';

class AdminController {

    public function dashboard() {
        requireStaffOrAdmin();
        $stats        = callProcedure('sp_get_dashboard_stats');
        $stats        = $stats[0] ?? [];
        $appointments = callProcedure('sp_get_all_appointments');
        $today        = date('Y-m-d');
        $today_appointments = array_filter($appointments, fn($a) => $a['appointment_date'] === $today);
        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function appointments() {
        requireStaffOrAdmin();
        $filter       = sanitize($_GET['status'] ?? '');
        $appointments = callProcedure('sp_get_all_appointments');
        if ($filter) {
            $appointments = array_filter($appointments, fn($a) => $a['status'] === $filter);
        }
        require __DIR__ . '/../views/admin/appointments.php';
    }

    public function patients() {
        requireStaffOrAdmin();
        $patients = callProcedure('sp_get_all_patients');
        require __DIR__ . '/../views/admin/patients.php';
    }

    public function patientDetails() {
        requireStaffOrAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid patient id.']);
            exit;
        }

        $db = getDB();

        $stmt = $db->prepare("
            SELECT id, first_name, last_name, email, phone, created_at
            FROM users
            WHERE id = ? AND role = 'patient'
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $patient = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$patient) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Patient not found.']);
            exit;
        }

        $stmt = $db->prepare("
            SELECT
                COUNT(*) AS total,
                SUM(status = 'completed') AS completed,
                SUM(status IN ('pending','confirmed')) AS upcoming,
                SUM(status = 'cancelled') AS cancelled,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN s.price ELSE 0 END), 0) AS total_spent
            FROM appointments a
            LEFT JOIN services s ON a.service_id = s.id
            WHERE a.patient_id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $stmt = $db->prepare("
            SELECT a.appointment_date, a.appointment_time, a.status,
                   CONCAT(d.first_name, ' ', d.last_name) AS dentist_name,
                   s.name AS service_name
            FROM appointments a
            JOIN dentists d ON a.dentist_id = d.id
            JOIN services s ON a.service_id = s.id
            WHERE a.patient_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
            LIMIT 10
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode([
            'patient' => [
                'id'         => (int)$patient['id'],
                'name'       => $patient['first_name'] . ' ' . $patient['last_name'],
                'initials'   => strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)),
                'email'      => $patient['email'],
                'phone'      => $patient['phone'] ?: '—',
                'created_at' => formatDate($patient['created_at']),
            ],
            'stats' => [
                'total'       => (int)($stats['total'] ?? 0),
                'completed'   => (int)($stats['completed'] ?? 0),
                'upcoming'    => (int)($stats['upcoming'] ?? 0),
                'cancelled'   => (int)($stats['cancelled'] ?? 0),
                'total_spent' => (float)($stats['total_spent'] ?? 0),
            ],
            'history' => array_map(fn($h) => [
                'date'    => formatDate($h['appointment_date']),
                'time'    => $h['appointment_time'],
                'dentist' => $h['dentist_name'],
                'service' => $h['service_name'],
                'status'  => $h['status'],
            ], $history),
        ]);
        exit;
    }

    public function updateStatus() {
        requireStaffOrAdmin();
        $id     = (int)($_POST['id'] ?? 0);
        $status = sanitize($_POST['status'] ?? '');
        $valid  = ['pending','confirmed','completed','cancelled'];

        if (!$id || !in_array($status, $valid)) {
            redirect('admin_appointments', 'Invalid request.', 'error');
        }

        callProcedure('sp_update_appointment_status', [$id, $status]);

        if ($status === 'confirmed') {
            $db = getDB();
            $stmt = $db->prepare("
                SELECT u.email, CONCAT(u.first_name,' ',u.last_name) AS patient_name,
                       a.appointment_date, a.appointment_time,
                       CONCAT(d.first_name,' ',d.last_name) AS dentist_name,
                       s.name AS service_name
                FROM appointments a
                JOIN users u    ON a.patient_id = u.id
                JOIN dentists d ON a.dentist_id = d.id
                JOIN services s ON a.service_id = s.id
                WHERE a.id = ?
            ");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $appt = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($appt) {
                sendAppointmentEmail(
                    $appt['email'],
                    $appt['patient_name'],
                    $appt['appointment_date'],
                    $appt['appointment_time'],
                    $appt['dentist_name'],
                    $appt['service_name']
                );
            }
        }

        redirect('admin_appointments', 'Status updated successfully.', 'success');
    }

    public function dentists() {
        requireAdmin();
        $dentists = callProcedure('sp_get_all_dentists');
        require __DIR__ . '/../views/admin/dentists.php';
    }

    public function dentistDetails() {
        requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid dentist id.']);
            exit;
        }

        $db = getDB();

        $stmt = $db->prepare("
            SELECT id, first_name, last_name, specialization, email, phone, is_active
            FROM dentists
            WHERE id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $dentist = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$dentist) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Dentist not found.']);
            exit;
        }

        $stmt = $db->prepare("
            SELECT
                COUNT(*) AS total,
                COUNT(DISTINCT patient_id) AS total_patients,
                SUM(status = 'completed') AS completed,
                SUM(status IN ('pending','confirmed')) AS upcoming,
                SUM(status = 'cancelled') AS cancelled
            FROM appointments
            WHERE dentist_id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $stmt = $db->prepare("
            SELECT a.appointment_date, a.appointment_time, a.status,
                   CONCAT(u.first_name, ' ', u.last_name) AS patient_name,
                   s.name AS service_name
            FROM appointments a
            JOIN users u    ON a.patient_id = u.id
            JOIN services s ON a.service_id = s.id
            WHERE a.dentist_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
            LIMIT 10
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode([
            'dentist' => [
                'id'             => (int)$dentist['id'],
                'name'           => 'Dr. ' . $dentist['first_name'] . ' ' . $dentist['last_name'],
                'initials'       => strtoupper(substr($dentist['first_name'], 0, 1) . substr($dentist['last_name'], 0, 1)),
                'specialization' => $dentist['specialization'] ?: '—',
                'email'          => $dentist['email'] ?: '—',
                'phone'          => $dentist['phone'] ?: '—',
                'is_active'      => (bool)$dentist['is_active'],
            ],
            'stats' => [
                'total_patients' => (int)($stats['total_patients'] ?? 0),
                'total'          => (int)($stats['total'] ?? 0),
                'completed'      => (int)($stats['completed'] ?? 0),
                'upcoming'       => (int)($stats['upcoming'] ?? 0),
                'cancelled'      => (int)($stats['cancelled'] ?? 0),
            ],
            'history' => array_map(fn($h) => [
                'date'    => formatDate($h['appointment_date']),
                'time'    => $h['appointment_time'],
                'patient' => $h['patient_name'],
                'service' => $h['service_name'],
                'status'  => $h['status'],
            ], $history),
        ]);
        exit;
    }

    public function addDentist() {
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin_dentists');
        $first_name     = sanitize($_POST['first_name'] ?? '');
        $last_name      = sanitize($_POST['last_name'] ?? '');
        $specialization = sanitize($_POST['specialization'] ?? '');
        $email          = sanitize($_POST['email'] ?? '');
        $phone          = sanitize($_POST['phone'] ?? '');
        if (empty($first_name) || empty($last_name)) redirect('admin_dentists','Name is required.','error');
        callProcedure('sp_add_dentist', [$first_name, $last_name, $specialization, $email, $phone]);
        redirect('admin_dentists', 'Dentist added successfully!', 'success');
    }

    public function editDentist() {
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin_dentists');
        $id             = (int)($_POST['id'] ?? 0);
        $first_name     = sanitize($_POST['first_name'] ?? '');
        $last_name      = sanitize($_POST['last_name'] ?? '');
        $specialization = sanitize($_POST['specialization'] ?? '');
        $email          = sanitize($_POST['email'] ?? '');
        $phone          = sanitize($_POST['phone'] ?? '');
        $is_active      = (int)($_POST['is_active'] ?? 1);
        if (!$id || empty($first_name) || empty($last_name)) redirect('admin_dentists','Invalid data.','error');
        callProcedure('sp_update_dentist', [$id, $first_name, $last_name, $specialization, $email, $phone, $is_active]);
        redirect('admin_dentists', 'Dentist updated successfully!', 'success');
    }

    public function deleteDentist() {
        requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect('admin_dentists','Invalid dentist.','error');
        callProcedure('sp_delete_dentist', [$id]);
        redirect('admin_dentists', 'Dentist deactivated.', 'info');
    }

    public function payments() {
        requireStaffOrAdmin();
        $filter   = sanitize($_GET['status'] ?? '');
        $payments = callProcedure('sp_get_all_payments');
        if ($filter) {
            $payments = array_filter($payments, fn($p) => $p['status'] === $filter);
        }
        require __DIR__ . '/../views/admin/payments.php';
    }

    public function updatePayment() {
        requireStaffOrAdmin();
        $id     = (int)($_POST['id'] ?? 0);
        $status = sanitize($_POST['status'] ?? '');
        $notes  = sanitize($_POST['notes'] ?? '');
        $valid  = ['pending','paid','refunded'];
        if (!$id || !in_array($status, $valid)) redirect('admin_payments','Invalid request.','error');
        callProcedure('sp_update_payment_status', [$id, $status, $notes]);
        redirect('admin_payments', 'Payment status updated.', 'success');
    }

    public function deletePatient() {
        requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect('admin_patients', 'Invalid patient.', 'error');
        callProcedure('sp_delete_patient', [$id]);
        redirect('admin_patients', 'Patient deleted successfully.', 'success');
    }

    public function calendar() {
        requireStaffOrAdmin();
        $db = getDB();
        $res = $db->query("
            SELECT a.appointment_date, a.appointment_time, a.status,
                   CONCAT(u.first_name,' ',u.last_name) AS patient_name,
                   CONCAT(d.first_name,' ',d.last_name) AS dentist_name,
                   s.name AS service_name
            FROM appointments a
            JOIN users u ON a.patient_id = u.id
            JOIN dentists d ON a.dentist_id = d.id
            JOIN services s ON a.service_id = s.id
            ORDER BY a.appointment_date, a.appointment_time
        ");
        $allAppts = $res->fetch_all(MYSQLI_ASSOC);
        require __DIR__ . '/../views/admin/calendar.php';
    }

    public function reports() {
        requireAdmin();
        $db = getDB();

        $r1 = $db->query("SELECT status, COUNT(*) as cnt FROM appointments GROUP BY status");
        $byStatus = $r1->fetch_all(MYSQLI_ASSOC);

        $r2 = $db->query("
            SELECT DATE_FORMAT(appointment_date,'%b %Y') as month,
                   DATE_FORMAT(appointment_date,'%Y-%m') as sort_key,
                   COUNT(*) as cnt
            FROM appointments
            WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY sort_key, month ORDER BY sort_key ASC
        ");
        $byMonth = $r2->fetch_all(MYSQLI_ASSOC);

        $r3 = $db->query("
            SELECT s.name, COUNT(*) as cnt
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            GROUP BY s.name ORDER BY cnt DESC LIMIT 5
        ");
        $topServices = $r3->fetch_all(MYSQLI_ASSOC);

        $r4 = $db->query("
            SELECT DATE_FORMAT(a.appointment_date,'%b %Y') as month,
                   DATE_FORMAT(a.appointment_date,'%Y-%m') as sort_key,
                   SUM(s.price) as revenue
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            WHERE a.status = 'completed'
              AND a.appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY sort_key, month ORDER BY sort_key ASC
        ");
        $revenueByMonth = $r4->fetch_all(MYSQLI_ASSOC);

        $r5 = $db->query("SELECT COUNT(*) as total FROM appointments");
        $totalAppts = $r5->fetch_assoc()['total'];

        $r6 = $db->query("SELECT COALESCE(SUM(s.price),0) as rev FROM appointments a JOIN services s ON a.service_id=s.id WHERE a.status='completed'");
        $totalRevenue = $r6->fetch_assoc()['rev'];

        $r7 = $db->query("SELECT COUNT(*) as total FROM users WHERE role='patient'");
        $totalPatients = $r7->fetch_assoc()['total'];

        $r8 = $db->query("SELECT COUNT(*) as total FROM appointments WHERE status='completed'");
        $totalCompleted = $r8->fetch_assoc()['total'];

        require __DIR__ . '/../views/admin/reports.php';
    }

    public function notifications() {
        requireStaffOrAdmin();
        $db = getDB();

        $r1 = $db->query("
            SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.created_at,
                   CONCAT(u.first_name,' ',u.last_name) AS patient_name,
                   CONCAT(d.first_name,' ',d.last_name) AS dentist_name,
                   s.name AS service_name
            FROM appointments a
            JOIN users u ON a.patient_id = u.id
            JOIN dentists d ON a.dentist_id = d.id
            JOIN services s ON a.service_id = s.id
            WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY a.created_at DESC
        ");
        $newAppts = $r1->fetch_all(MYSQLI_ASSOC);

        $r2 = $db->query("
            SELECT a.id, a.appointment_date, a.appointment_time, a.status,
                   CONCAT(u.first_name,' ',u.last_name) AS patient_name,
                   CONCAT(d.first_name,' ',d.last_name) AS dentist_name,
                   s.name AS service_name
            FROM appointments a
            JOIN users u ON a.patient_id = u.id
            JOIN dentists d ON a.dentist_id = d.id
            JOIN services s ON a.service_id = s.id
            WHERE a.appointment_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
              AND a.status IN ('pending','confirmed')
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ");
        $upcomingAppts = $r2->fetch_all(MYSQLI_ASSOC);

        $r3 = $db->query("
            SELECT a.id, a.appointment_date, a.appointment_time,
                   CONCAT(u.first_name,' ',u.last_name) AS patient_name,
                   s.name AS service_name
            FROM appointments a
            JOIN users u ON a.patient_id = u.id
            JOIN services s ON a.service_id = s.id
            WHERE a.status = 'cancelled'
              AND a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY a.appointment_date DESC
        ");
        $cancelledAppts = $r3->fetch_all(MYSQLI_ASSOC);

        require __DIR__ . '/../views/admin/notifications.php';
    }

    public function updatePatientStatus() {
        if (!isAdmin() && !isStaff()) redirect('login');

        $id     = (int)($_GET['id'] ?? 0);
        $action = $_GET['action'] ?? '';

        if (!in_array($action, ['approved', 'rejected']) || $id <= 0) {
            redirect('pending_patients', 'Invalid action.', 'error');
        }

        callProcedure('sp_update_patient_status', [$id, $action]);
        $msg = $action === 'approved' ? 'Patient approved successfully.' : 'Patient rejected.';
        redirect('pending_patients', $msg, 'success');
    }

    public function pendingPatients() {
        if (!isAdmin() && !isStaff()) redirect('login');
        $patients = callProcedure('sp_get_pending_patients', []);
        require __DIR__ . '/../views/admin/pending_patients.php';
    }

    public function resetRequests() {
        requireStaffOrAdmin();
        $requests = callProcedure('sp_get_password_reset_requests', []);
        require __DIR__ . '/../views/admin/reset_requests.php';
    }

    public function approveReset() {
        requireStaffOrAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect('admin_reset_requests', 'Invalid request.', 'error');

        $temp   = 'Temp@' . rand(1000, 9999);
        $hashed = password_hash($temp, PASSWORD_BCRYPT);

        callProcedure('sp_approve_password_reset', [$id, $hashed, $temp]);

        redirect('admin_reset_requests', 'Approved! Temporary password generated: ' . $temp, 'success');
    }

    // ─── Staff Management ───────────────────────────────────────────

    public function staff() {
        requireAdmin();
        $staff = callProcedure('sp_get_all_staff', []);
        require __DIR__ . '/../views/admin/staff.php';
    }

    public function addStaff() {
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin_staff');

        $first_name = sanitize($_POST['first_name'] ?? '');
        $last_name  = sanitize($_POST['last_name'] ?? '');
        $email      = sanitize($_POST['email'] ?? '');
        $phone      = sanitize($_POST['phone'] ?? '');

        if (empty($first_name) || empty($last_name) || empty($email)) {
            redirect('admin_staff', 'Name and email are required.', 'error');
        }

        $temp   = 'Temp@' . rand(1000, 9999);
        $hashed = password_hash($temp, PASSWORD_BCRYPT);

        callProcedure('sp_add_staff', [$first_name, $last_name, $email, $phone, $hashed]);

        redirect('admin_staff', 'Staff account created! Temporary password: ' . $temp, 'success');
    }

    public function updateStaffStatus() {
        requireAdmin();
        $id     = (int)($_GET['id'] ?? 0);
        $action = $_GET['action'] ?? '';

        if (!in_array($action, ['approved', 'rejected']) || $id <= 0) {
            redirect('admin_staff', 'Invalid action.', 'error');
        }

        callProcedure('sp_update_staff_status', [$id, $action]);
        $msg = $action === 'approved' ? 'Staff account reactivated.' : 'Staff account deactivated.';
        redirect('admin_staff', $msg, 'success');
    }
    public function deleteStaff() {
    requireAdmin();
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) redirect('admin_staff', 'Invalid staff account.', 'error');
    callProcedure('sp_delete_staff', [$id]);
    redirect('admin_staff', 'Staff account deleted.', 'success');
}
}