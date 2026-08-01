<?php

require_once 'app/config.php';

requireLogin();

$payment_id = intval($_GET['id'] ?? 0);
$user_id    = $_SESSION['user_id'];

if (!$payment_id) {
    die('Invalid payment ID.');
}

$db = getDB();

if (isAdmin()) {
    $stmt = $db->prepare("
        SELECT
            p.id,
            p.amount,
            p.method,
            p.reference_no,
            p.notes,
            p.status,
            p.paid_at,
            p.created_at,
            CONCAT(u.first_name, ' ', u.last_name) AS patient_name,
            u.email                                 AS patient_email,
            s.name                                  AS service_name,
            CONCAT(d.first_name, ' ', d.last_name)  AS dentist_name,
            a.appointment_date,
            a.appointment_time
        FROM payments p
        JOIN users        u ON u.id = p.patient_id
        JOIN appointments a ON a.id = p.appointment_id
        JOIN services     s ON s.id = a.service_id
        JOIN dentists     d ON d.id = a.dentist_id
        WHERE p.id = ?
    ");
    $stmt->bind_param('i', $payment_id);
} else {
    $stmt = $db->prepare("
        SELECT
            p.id,
            p.amount,
            p.method,
            p.reference_no,
            p.notes,
            p.status,
            p.paid_at,
            p.created_at,
            CONCAT(u.first_name, ' ', u.last_name) AS patient_name,
            u.email                                 AS patient_email,
            s.name                                  AS service_name,
            CONCAT(d.first_name, ' ', d.last_name)  AS dentist_name,
            a.appointment_date,
            a.appointment_time
        FROM payments p
        JOIN users        u ON u.id = p.patient_id
        JOIN appointments a ON a.id = p.appointment_id
        JOIN services     s ON s.id = a.service_id
        JOIN dentists     d ON d.id = a.dentist_id
        WHERE p.id = ? AND p.patient_id = ?
    ");
    $stmt->bind_param('ii', $payment_id, $user_id);
}

$stmt->execute();
$result  = $stmt->get_result();
$payment = $result->fetch_assoc();
$stmt->close();

if (!$payment) {
    die('Receipt not found or access denied.');
}

if (strtolower($payment['status']) !== 'paid') {
    die('Receipt is only available for confirmed (Paid) payments.');
}

$receipt_number = 'RCP-' . str_pad($payment['id'], 6, '0', STR_PAD_LEFT);
$paid_date      = formatDate($payment['paid_at'] ?? $payment['created_at']);
$appt_date      = formatDate($payment['appointment_date']);
$appt_time      = formatTime($payment['appointment_time']);
$amount         = number_format($payment['amount'], 2);

$methodIcons = ['cash' => '💵', 'gcash' => '📱', 'maya' => '💳', 'card' => '💳'];
$methodIcon  = $methodIcons[$payment['method']] ?? '💵';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Receipt <?= htmlspecialchars($receipt_number) ?> – Auza Dental Clinic</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; color: #333; }

  .receipt-wrapper {
    max-width: 680px;
    margin: 40px auto;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.10);
  }

  .receipt-header {
    background: linear-gradient(135deg, #0d9488, #0f766e);
    color: #fff;
    padding: 32px 40px 24px;
    display: flex;
    align-items: center;
    gap: 20px;
  }
  .clinic-logo {
    width: 60px; height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px;
  }
  .clinic-info h1 { font-size: 22px; font-weight: 700; }
  .clinic-info p  { font-size: 13px; opacity: 0.85; margin-top: 2px; }

  .paid-banner {
    background: #ecfdf5;
    border-top: 3px solid #10b981;
    padding: 14px 40px;
    display: flex; align-items: center; gap: 10px;
    color: #065f46; font-weight: 600; font-size: 15px;
  }
  .paid-banner .check {
    width: 26px; height: 26px; background: #10b981; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 14px;
  }

  .receipt-body { padding: 32px 40px; }

  .receipt-meta {
    display: flex; justify-content: space-between;
    margin-bottom: 28px;
  }
  .receipt-meta label { font-size: 12px; color: #9ca3af; display: block; margin-bottom: 2px; }
  .receipt-meta strong { color: #111; font-size: 14px; display: block; }

  .section-title {
    font-size: 11px; text-transform: uppercase; letter-spacing: 1px;
    color: #9ca3af; margin-bottom: 10px; margin-top: 22px;
  }

  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .info-item label { font-size: 12px; color: #9ca3af; display: block; margin-bottom: 2px; }
  .info-item span  { font-size: 14px; color: #111; font-weight: 500; }

  .amount-table { width: 100%; border-collapse: collapse; margin-top: 12px; }
  .amount-table thead th {
    background: #f9fafb; padding: 10px 14px;
    text-align: left; font-size: 12px; color: #6b7280;
    text-transform: uppercase; letter-spacing: 0.5px;
  }
  .amount-table tbody td { padding: 12px 14px; font-size: 14px; border-top: 1px solid #f0f0f0; }
  .amount-table tfoot td { padding: 14px; font-weight: 700; border-top: 2px solid #e5e7eb; }
  .amount-table thead th:last-child,
  .amount-table tbody td:last-child,
  .amount-table tfoot td:last-child { text-align: right; }
  .amount-table tfoot td:last-child { color: #0d9488; font-size: 18px; }

  .receipt-footer {
    background: #f9fafb; border-top: 1px solid #e5e7eb;
    padding: 20px 40px; font-size: 12px; color: #9ca3af; text-align: center;
  }

  .action-bar { text-align: center; padding: 20px 40px 32px; }
  .btn-print, .btn-close {
    display: inline-block; padding: 11px 28px; border-radius: 8px;
    font-size: 14px; font-weight: 600; cursor: pointer; border: none;
    text-decoration: none; margin: 0 6px;
  }
  .btn-print { background: #0d9488; color: #fff; }
  .btn-print:hover { background: #0f766e; }
  .btn-close { background: #f3f4f6; color: #374151; }
  .btn-close:hover { background: #e5e7eb; }

  @media print {
    body { background: #fff; }
    .receipt-wrapper { box-shadow: none; margin: 0; border-radius: 0; }
    .action-bar { display: none; }
  }
</style>
</head>
<body>

<div class="receipt-wrapper">

  <div class="receipt-header">
    <div class="clinic-logo">🦷</div>
    <div class="clinic-info">
      <h1>Auza Dental Clinic</h1>
      <p>Official Payment Receipt</p>
    </div>
  </div>

  <div class="paid-banner">
    <span class="check">✓</span>
    Payment Confirmed &amp; Verified
  </div>

  <div class="receipt-body">

    <div class="receipt-meta">
      <div>
        <label>Receipt No.</label>
        <strong><?= htmlspecialchars($receipt_number) ?></strong>
      </div>
      <div style="text-align:right;">
        <label>Date Issued</label>
        <strong><?= $paid_date ?></strong>
      </div>
    </div>

    <div class="section-title">Patient Information</div>
    <div class="info-grid">
      <div class="info-item">
        <label>Full Name</label>
        <span><?= htmlspecialchars($payment['patient_name']) ?></span>
      </div>
      <div class="info-item">
        <label>Email</label>
        <span><?= htmlspecialchars($payment['patient_email']) ?></span>
      </div>
    </div>

    <div class="section-title">Appointment Details</div>
    <div class="info-grid">
      <div class="info-item">
        <label>Service</label>
        <span><?= htmlspecialchars($payment['service_name']) ?></span>
      </div>
      <div class="info-item">
        <label>Dentist</label>
        <span>Dr. <?= htmlspecialchars($payment['dentist_name']) ?></span>
      </div>
      <div class="info-item">
        <label>Date</label>
        <span><?= $appt_date ?></span>
      </div>
      <div class="info-item">
        <label>Time</label>
        <span><?= $appt_time ?></span>
      </div>
    </div>

    <div class="section-title">Payment Summary</div>
    <table class="amount-table">
      <thead>
        <tr>
          <th>Description</th>
          <th>Method</th>
          <th>Reference #</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?= htmlspecialchars($payment['service_name']) ?></td>
          <td><?= $methodIcon . ' ' . strtoupper($payment['method']) ?></td>
          <td><?= htmlspecialchars($payment['reference_no'] ?: '—') ?></td>
          <td>₱<?= $amount ?></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" style="text-align:right; color:#6b7280; font-size:14px;">Total Paid</td>
          <td>₱<?= $amount ?></td>
        </tr>
      </tfoot>
    </table>

    <?php if (!empty($payment['notes'])): ?>
    <div class="section-title" style="margin-top:20px;">Notes</div>
    <p style="font-size:14px;color:#374151;"><?= htmlspecialchars($payment['notes']) ?></p>
    <?php endif; ?>

  </div>

  <div class="action-bar">
    <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
  </div>

  <div class="receipt-footer">
    This is an official receipt from Auza Dental Clinic. Thank you for your payment!<br>
    For inquiries, please contact your clinic administrator.
  </div>

</div>

</body>
</html>