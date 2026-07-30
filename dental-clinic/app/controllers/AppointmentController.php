<?php
require_once __DIR__ . '/../config.php';

class AppointmentController {

    // Helper to deduplicate arrays by 'id' key
    private function deduplicateById(array $items): array {
        $seen = [];
        foreach ($items as $item) {
            $seen[$item['id']] = $item;
        }
        return array_values($seen);
    }

    public function dashboard() {
        requirePatient();
        $patient_id   = $_SESSION['user_id'];
        $appointments = callProcedure('sp_get_patient_appointments', [$patient_id]);
        $dentists     = $this->deduplicateById(callProcedure('sp_get_dentists'));
        $services     = $this->deduplicateById(callProcedure('sp_get_services'));
        require __DIR__ . '/../views/patient/dashboard.php';
    }

    public function book() {
        requirePatient();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('patient_dashboard');
        }

        $patient_id  = $_SESSION['user_id'];
        $dentist_id  = (int)($_POST['dentist_id'] ?? 0);
        $service_id  = (int)($_POST['service_id'] ?? 0);
        $date        = sanitize($_POST['appointment_date'] ?? '');
        $time        = sanitize($_POST['appointment_time'] ?? '');
        $notes       = sanitize($_POST['notes'] ?? '');

        if (!$dentist_id || !$service_id || empty($date) || empty($time)) {
            redirect('patient_dashboard', 'Please fill in all required fields.', 'error');
        }

        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            redirect('patient_dashboard', 'Please select a future date.', 'error');
        }

        $result = callProcedure('sp_book_appointment', [$patient_id, $dentist_id, $service_id, $date, $time, $notes]);

        if (isset($result['error'])) {
            $msg = strpos($result['error'], 'already booked') !== false
                ? 'That time slot is already taken. Please choose another.'
                : 'Booking failed. Please try again.';
            redirect('patient_dashboard', $msg, 'error');
        } else {
            redirect('patient_dashboard', 'Appointment booked successfully!', 'success');
        }
    }

    public function cancel() {
        requirePatient();
        $id         = (int)($_GET['id'] ?? 0);
        $patient_id = $_SESSION['user_id'];
        if (!$id) redirect('patient_dashboard', 'Invalid appointment.', 'error');
        $result = callProcedure('sp_cancel_appointment', [$id, $patient_id]);
        redirect('patient_dashboard', 'Appointment cancelled.', 'info');
    }

    public function myAppointments() {
        requirePatient();
        $patient_id   = $_SESSION['user_id'];
        $filter       = sanitize($_GET['status'] ?? '');
        $appointments = callProcedure('sp_get_patient_appointments', [$patient_id]);
        if ($filter) {
            $appointments = array_filter($appointments, fn($a) => $a['status'] === $filter);
        }
        $dentists = $this->deduplicateById(callProcedure('sp_get_dentists'));
        $services = $this->deduplicateById(callProcedure('sp_get_services'));
        require __DIR__ . '/../views/patient/appointments.php';
    }

    public function notifications() {
        requirePatient();
        $patient_id   = $_SESSION['user_id'];
        $db           = getDB();

        // Upcoming appointments (next 7 days)
        $stmt = $db->prepare("
            SELECT a.id, a.appointment_date, a.appointment_time, a.status,
                   s.name AS service_name, s.price,
                   CONCAT(d.first_name,' ',d.last_name) AS dentist_name
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            JOIN dentists d ON a.dentist_id = d.id
            WHERE a.patient_id = ?
              AND a.appointment_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
              AND a.status IN ('pending','confirmed')
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ");
        $stmt->bind_param('i', $patient_id);
        $stmt->execute();
        $upcomingAppts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Recently confirmed
        $stmt2 = $db->prepare("
            SELECT a.id, a.appointment_date, a.appointment_time, a.status,
                   s.name AS service_name,
                   CONCAT(d.first_name,' ',d.last_name) AS dentist_name
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            JOIN dentists d ON a.dentist_id = d.id
            WHERE a.patient_id = ?
              AND a.status = 'confirmed'
            ORDER BY a.appointment_date ASC
        ");
        $stmt2->bind_param('i', $patient_id);
        $stmt2->execute();
        $confirmedAppts = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt2->close();

        // Recently cancelled
        $stmt3 = $db->prepare("
            SELECT a.id, a.appointment_date, a.appointment_time,
                   s.name AS service_name,
                   CONCAT(d.first_name,' ',d.last_name) AS dentist_name
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            JOIN dentists d ON a.dentist_id = d.id
            WHERE a.patient_id = ?
              AND a.status = 'cancelled'
            ORDER BY a.appointment_date DESC
            LIMIT 5
        ");
        $stmt3->bind_param('i', $patient_id);
        $stmt3->execute();
        $cancelledAppts = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt3->close();

        // Pending payments
        $stmt4 = $db->prepare("
            SELECT p.id, p.amount, p.status AS pay_status,
                   s.name AS service_name, a.appointment_date
            FROM payments p
            JOIN appointments a ON p.appointment_id = a.id
            JOIN services s ON a.service_id = s.id
            WHERE p.patient_id = ? AND p.status = 'pending'
            ORDER BY p.created_at DESC
        ");
        $stmt4->bind_param('i', $patient_id);
        $stmt4->execute();
        $pendingPayments = $stmt4->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt4->close();

        require __DIR__ . '/../views/patient/notifications.php';
    }

    public function profile() {
        requirePatient();
        $patient_id = $_SESSION['user_id'];
        $user       = callProcedure('sp_get_user_id', [$patient_id]);
        $user       = $user[0] ?? [];
        $appointments = callProcedure('sp_get_patient_appointments', [$patient_id]);
        $totalSpent = array_sum(array_map(
            fn($a) => $a['status'] === 'completed' ? (float)$a['price'] : 0,
            $appointments
        ));
        require __DIR__ . '/../views/patient/profile.php';
    }
    public function reschedule() {
    requirePatient();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('patient_dashboard');
    }

    $patient_id     = $_SESSION['user_id'];
    $appointment_id = (int)($_POST['appointment_id'] ?? 0);
    $new_date       = sanitize($_POST['new_date'] ?? '');
    $new_time       = sanitize($_POST['new_time'] ?? '');
    $reason         = sanitize($_POST['reschedule_reason'] ?? '');

    if (!$appointment_id || empty($new_date) || empty($new_time) || empty($reason)) {
        redirect('patient_dashboard', 'Please fill in all fields including the reason.', 'error');
    }

    if (strtotime($new_date) < strtotime(date('Y-m-d'))) {
        redirect('patient_dashboard', 'Please select a future date.', 'error');
    }

    callProcedure('sp_reschedule_appointment', [
        $appointment_id, $patient_id, $new_date, $new_time, $reason
    ]);

    redirect('patient_dashboard', 'Appointment rescheduled successfully!', 'success');
}
}