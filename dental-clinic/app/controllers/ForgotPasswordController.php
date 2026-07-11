<?php
require_once __DIR__ . '/../config.php';

class ForgotPasswordController {

    public function index() {
        if (isLoggedIn()) redirect(isAdmin() ? 'admin_dashboard' : 'patient_dashboard');
        require __DIR__ . '/../views/auth/forgot_password.php';
    }

    public function requestReset() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('forgot_password');

        $email = sanitize($_POST['email'] ?? '');
        if (empty($email)) {
            redirect('forgot_password', 'Please enter your email.', 'error');
        }

        $db = getDB();

        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND role = 'patient' LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user) {
            redirect('forgot_password', 'No account found with that email address.', 'error');
        }

        // Check existing reset request
        $stmt2 = $db->prepare("
            SELECT pr.id, pr.status, pr.temp_password_plain
            FROM password_resets pr
            JOIN users u ON pr.user_id = u.id
            WHERE u.email = ? AND u.role = 'patient'
            ORDER BY pr.created_at DESC LIMIT 1
        ");
        $stmt2->bind_param('s', $email);
        $stmt2->execute();
        $reset = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();

        if ($reset && $reset['status'] === 'approved' && !empty($reset['temp_password_plain'])) {
            // Admin approved — show temp password on page
            $_SESSION['show_temp_pass']       = $reset['temp_password_plain'];
            $_SESSION['show_temp_pass_email'] = $email;
            header('Location: ?page=forgot_password&email=' . urlencode($email));
            exit;
        }

        if ($reset && $reset['status'] === 'pending') {
            // Still waiting for admin
            header('Location: ?page=forgot_password&email=' . urlencode($email));
            exit;
        }

        // Submit new request
        callProcedure('sp_request_password_reset', [$email]);
        redirect('forgot_password', 'Request submitted! Check back here once the admin approves it.', 'success');
    }

    public function resetPassword() {
        if (!isLoggedIn() || !isPatient()) redirect('login');
        require __DIR__ . '/../views/auth/reset_password.php';
    }

    public function saveNewPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('reset_password');
        if (!isLoggedIn() || !isPatient()) redirect('login');

        $user_id  = $_SESSION['user_id'];
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (empty($password) || strlen($password) < 8) {
            redirect('reset_password', 'Password must be at least 8 characters.', 'error');
        }
        if ($password !== $confirm) {
            redirect('reset_password', 'Passwords do not match.', 'error');
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        callProcedure('sp_change_password', [$user_id, $hashed]);
        callProcedure('sp_use_temp_password', [$user_id]);

        unset($_SESSION['force_reset'], $_SESSION['show_temp_pass'], $_SESSION['show_temp_pass_email']);

        redirect('patient_dashboard', 'Password changed successfully! Welcome back.', 'success');
    }
    public function checkResetStatus() {
    header('Content-Type: application/json');
 
    $email = sanitize($_GET['email'] ?? '');
    if (empty($email)) {
        echo json_encode(['status' => 'unknown']);
        exit;
    }
 
    $db = getDB();
    $stmt = $db->prepare("
        SELECT pr.status
        FROM password_resets pr
        JOIN users u ON pr.user_id = u.id
        WHERE u.email = ? AND u.role = 'patient'
        ORDER BY pr.created_at DESC LIMIT 1
    ");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
 
    echo json_encode(['status' => $row['status'] ?? 'unknown']);
    exit;
}
}