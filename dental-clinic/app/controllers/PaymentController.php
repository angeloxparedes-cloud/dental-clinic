<?php
require_once __DIR__ . '/../config.php';

class PaymentController {

    public function patientPayments() {
        requirePatient();
        $patient_id = $_SESSION['user_id'];
        $payments   = callProcedure('sp_get_patient_payments', [$patient_id]);
        $appointments = callProcedure('sp_get_patient_appointments', [$patient_id]);
        // Only confirmed/completed appointments without existing payment
        $payable = [];
        foreach ($appointments as $a) {
            if (in_array($a['status'], ['confirmed', 'completed'])) {
                $existing = callProcedure('sp_get_payment_by_appointment', [(int)$a['id']]);
                if (empty($existing)) $payable[] = $a;
            }
        }
        require __DIR__ . '/../views/patient/payments.php';
    }

    public function submitPayment() {
        requirePatient();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('patient_payments');

        $patient_id     = $_SESSION['user_id'];
        $appointment_id = (int)($_POST['appointment_id'] ?? 0);
        $amount         = (float)($_POST['amount'] ?? 0);
        $method         = sanitize($_POST['method'] ?? 'cash');
        $reference_no   = sanitize($_POST['reference_no'] ?? '');
        $notes          = sanitize($_POST['notes'] ?? '');

        if (!$appointment_id || $amount <= 0) {
            redirect('patient_payments', 'Invalid payment details.', 'error');
        }

        $valid_methods = ['cash','gcash','maya','card'];
        if (!in_array($method, $valid_methods)) $method = 'cash';

        $result = callProcedure('sp_create_payment', [$appointment_id, $patient_id, $amount, $method, $reference_no, $notes]);
        if (isset($result['error'])) {
            redirect('patient_payments', 'Payment submission failed. It may already exist.', 'error');
        } else {
            redirect('patient_payments', 'Payment submitted! Awaiting admin confirmation.', 'success');
        }
    }
}
