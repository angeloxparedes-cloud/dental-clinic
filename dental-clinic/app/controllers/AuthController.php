<?php
require_once __DIR__ . '/../config.php';

class AuthController {

    public function login() {
        if (isLoggedIn()) {
            redirect(isAdmin() ? 'admin_dashboard' : 'patient_dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = 'Please fill in all fields.';
            } else {
                $rows = callProcedure('sp_find_user_by_email', [$email]);
                if (isset($rows['error']) || empty($rows)) {
                    $error = 'Invalid email or password.';
                } else {
                    $user = $rows[0];

                    // ── CHECK TEMP PASSWORD FIRST ────────────────────────────
                    // If admin set a temp password, check if patient is using it
                    $tempPass = $user['temp_password'] ?? null;
                    $usingTempPassword = false;

                    if ($tempPass && $tempPass !== 'pending') {
                        if (password_verify($password, $tempPass)) {
                            $usingTempPassword = true;
                        }
                    }
                    // ─────────────────────────────────────────────────────────

                    if ($usingTempPassword || password_verify($password, $user['password'])) {

                        // ── ACCOUNT VERIFICATION CHECK ───────────────────────
                        if ($user['role'] === 'patient') {
                            if ($user['status'] === 'pending') {
                                $error = 'Your account is still pending admin approval. Please wait.';
                            } elseif ($user['status'] === 'rejected') {
                                $error = 'Your account has been rejected. Please contact the clinic.';
                            } else {
                                // approved — allow login
                                $this->createSession($user);

                                // ── FORCE PASSWORD RESET IF USING TEMP ──────
                                if ($usingTempPassword) {
                                    $_SESSION['force_reset'] = true;
                                    redirect('reset_password');
                                }
                                // ────────────────────────────────────────────

                                redirect('patient_dashboard');
                            }
                        } elseif ($user['role'] === 'staff') {
                            if ($user['status'] !== 'approved') {
                                $error = 'Your account has been deactivated. Please contact the admin.';
                            } else {
                                $this->createSession($user);

                                // ── FORCE PASSWORD RESET IF USING TEMP ──────
                                if ($usingTempPassword) {
                                    $_SESSION['force_reset'] = true;
                                    redirect('reset_password');
                                }
                                // ────────────────────────────────────────────

                                redirect('admin_dashboard');
                            }
                        } else {
                            // admin — always allow login
                            $this->createSession($user);
                            redirect('admin_dashboard');
                        }
                        // ─────────────────────────────────────────────────────

                    } else {
                        $error = 'Invalid email or password.';
                    }
                }
            }
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function register() {
        if (isLoggedIn()) {
            redirect(isAdmin() ? 'admin_dashboard' : 'patient_dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first_name = sanitize($_POST['first_name'] ?? '');
            $last_name  = sanitize($_POST['last_name'] ?? '');
            $email      = sanitize($_POST['email'] ?? '');
            $phone      = sanitize($_POST['phone'] ?? '');
            $password   = $_POST['password'] ?? '';
            $confirm    = $_POST['confirm_password'] ?? '';

            if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
                $error = 'Please fill in all required fields.';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match.';
            } elseif (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters.';
            } else {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $result = callProcedure('sp_register_patient', [$first_name, $last_name, $email, $hashed, $phone]);
                if (isset($result['error'])) {
                    $error = strpos($result['error'], 'Email already exists') !== false
                        ? 'Email already registered. Please login.'
                        : 'Registration failed. Please try again.';
                } else {
                    redirect('login', 'Registration submitted! Please wait for admin approval before logging in.', 'success');
                }
            }
        }

        require __DIR__ . '/../views/auth/register.php';
    }

    public function logout() {
        session_destroy();
        redirect('login', 'You have been logged out.', 'info');
    }

    // ── PRIVATE HELPER: sets session variables ───────────────────────────────
    private function createSession($user) {
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name']  = $user['last_name'];
        $_SESSION['email']      = $user['email'];
        $_SESSION['role']       = $user['role'];

        // Fetch fresh data from DB to get avatar and temp_password
        $fresh = callProcedure('sp_get_user_by_id', [$user['id']]);
        $_SESSION['avatar'] = $fresh[0]['avatar'] ?? '';
    }
    // ─────────────────────────────────────────────────────────────────────────
}