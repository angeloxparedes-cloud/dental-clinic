<?php
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/AppointmentController.php';
require_once __DIR__ . '/app/controllers/AdminController.php';
require_once __DIR__ . '/app/controllers/PaymentController.php';
require_once __DIR__ . '/app/controllers/SettingsController.php';
require_once __DIR__ . '/app/controllers/ForgotPasswordController.php';

$page = $_GET['page'] ?? 'intro';

if ($page === 'intro') {
    require __DIR__ . '/app/views/intro.php';
    exit;
}

$routes = [
    'login'                => ['AuthController',      'login'],
    'register'             => ['AuthController',      'register'],
    'logout'               => ['AuthController',      'logout'],
    'patient_dashboard'    => ['AppointmentController','dashboard'],
    'book_appointment'     => ['AppointmentController','book'],
    'cancel_appointment'   => ['AppointmentController','cancel'],
    'patient_payments'     => ['PaymentController',   'patientPayments'],
    'submit_payment'       => ['PaymentController',   'submitPayment'],
    'admin_dashboard'      => ['AdminController',     'dashboard'],
    'admin_appointments'   => ['AdminController',     'appointments'],
    'admin_patients'       => ['AdminController',     'patients'],
    'admin_update_status'  => ['AdminController',     'updateStatus'],
    'admin_dentists'       => ['AdminController',     'dentists'],
    'admin_add_dentist'    => ['AdminController',     'addDentist'],
    'admin_edit_dentist'   => ['AdminController',     'editDentist'],
    'admin_delete_dentist' => ['AdminController',     'deleteDentist'],
    'admin_payments'       => ['AdminController',     'payments'],
    'admin_update_payment' => ['AdminController',     'updatePayment'],
    'settings'             => ['SettingsController',  'index'],
    'update_profile'       => ['SettingsController',  'updateProfile'],
    'change_password'      => ['SettingsController',  'changePassword'],
    'admin_delete_patient' => ['AdminController',     'deletePatient'],
    'admin_calendar'       => ['AdminController',     'calendar'],
    'admin_reports'        => ['AdminController',     'reports'],
    'admin_notifications'  => ['AdminController',     'notifications'],
    'patient_appointments'   => ['AppointmentController', 'myAppointments'],
    'patient_notifications'  => ['AppointmentController', 'notifications'],
    'patient_profile'        => ['AppointmentController', 'profile'],
    'pending_patients'       => ['AdminController',   'pendingPatients'],
    'update_patient_status'  => ['AdminController',   'updatePatientStatus'],
    'forgot_password'        => ['ForgotPasswordController', 'index'],
    'request_reset'          => ['ForgotPasswordController', 'requestReset'],
    'reset_password'         => ['ForgotPasswordController', 'resetPassword'],
    'save_new_password'      => ['ForgotPasswordController', 'saveNewPassword'],
    'admin_reset_requests'   => ['AdminController',          'resetRequests'],
    'admin_approve_reset'    => ['AdminController',          'approveReset'],
    'check_reset_status' => ['ForgotPasswordController', 'checkResetStatus'],
    'cancel_appointment'   => ['AppointmentController','cancel'],
    'reschedule_appointment' => ['AppointmentController','reschedule'],
];

if (isset($routes[$page])) {
    [$controllerClass, $method] = $routes[$page];
    $controller = new $controllerClass();
    $controller->$method();
} else {
    if (isLoggedIn()) {
        redirect(isAdmin() ? 'admin_dashboard' : 'patient_dashboard');
    } else {
        redirect('login');
    }
}