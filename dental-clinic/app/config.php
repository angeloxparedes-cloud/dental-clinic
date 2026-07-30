<?php
// ============================================
// DATABASE CONFIGURATION
// ============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dental_clinic');

define('APP_NAME', 'Auza Dental Clinic');
define('APP_URL', 'http://localhost/dental-clinic');

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// DATABASE CONNECTION
// ============================================
function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

// ============================================
// HELPER FUNCTIONS
// ============================================
function callProcedure($proc_name, $params = []) {
    $db = getDB();
    if (empty($params)) {
        $sql = "CALL {$proc_name}()";
        $result = $db->query($sql);
    } else {
        $placeholders = implode(',', array_fill(0, count($params), '?'));
        $sql = "CALL {$proc_name}({$placeholders})";
        $stmt = $db->prepare($sql);
        if (!$stmt) return ['error' => $db->error];
        $types = '';
        foreach ($params as $p) {
            if (is_int($p)) $types .= 'i';
            elseif (is_float($p)) $types .= 'd';
            else $types .= 's';
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }
    if ($result === false) return ['error' => $db->error];
    if ($result === true) return ['success' => true, 'affected' => $db->affected_rows];
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    // Free all result sets
    while ($db->more_results()) { $db->next_result(); }
    return $rows;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isPatient() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'patient';
}

function isStaff() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'staff';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/index.php?page=login');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . APP_URL . '/index.php?page=patient_dashboard');
        exit;
    }
}

// Allows BOTH staff and admin through. Use this on pages staff should
// be able to see (Appointments, Calendar, Patients, Pending Approvals,
// Reset Requests, Payments). Pages that stay admin-only (Dentists,
// Reports, Staff management) should keep using requireAdmin() instead.
function requireStaffOrAdmin() {
    requireLogin();
    if (!isAdmin() && !isStaff()) {
        header('Location: ' . APP_URL . '/index.php?page=patient_dashboard');
        exit;
    }
}

function requirePatient() {
    requireLogin();
    if (!isPatient()) {
        header('Location: ' . APP_URL . '/index.php?page=admin_dashboard');
        exit;
    }
}

function redirect($page, $msg = '', $type = 'success') {
    $url = APP_URL . '/index.php?page=' . $page;
    if ($msg) $url .= '&msg=' . urlencode($msg) . '&msg_type=' . $type;
    header('Location: ' . $url);
    exit;
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

function formatTime($time) {
    return date('g:i A', strtotime($time));
}

function statusBadge($status) {
    $classes = [
        'pending'   => 'badge-pending',
        'confirmed' => 'badge-confirmed',
        'completed' => 'badge-completed',
        'cancelled' => 'badge-cancelled',
    ];
    $class = $classes[$status] ?? 'badge-pending';
    return "<span class='badge {$class}'>" . ucfirst($status) . "</span>";
}