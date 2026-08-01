<?php $currentPage = 'patient_notifications'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notifications – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-title">Notifications</div>
    </div>
    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; ?>

      <!-- Upcoming -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <span style="display:inline-flex;align-items:center;gap:8px;">
              <span style="width:10px;height:10px;border-radius:50%;background:#1D9E75;display:inline-block;"></span>
              Upcoming Appointments
              <span style="font-size:0.85rem;font-weight:400;color:var(--gray);">(next 7 days — <?= count($upcomingAppts) ?>)</span>
            </span>
          </div>
        </div>
        <div class="card-body">
          <?php if (empty($upcomingAppts)): ?>
            <div style="text-align:center;padding:2rem;color:#5a7080;font-size:14px;">No upcoming appointments in the next 7 days.</div>
          <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:10px;">
              <?php foreach ($upcomingAppts as $a):
                $apptDate = new DateTime($a['appointment_date']);
                $today    = new DateTime(date('Y-m-d'));
                $daysLeft = (int)$today->diff($apptDate)->days;
                $dayLabel = $daysLeft === 0 ? 'Today!' : ($daysLeft === 1 ? 'Tomorrow' : "In {$daysLeft} days");
              ?>
              <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:10px;background:#F0FFF4;border-left:4px solid #1D9E75;">
                <div style="width:44px;height:44px;border-radius:50%;background:#D1FAE5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#065F46"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div style="flex:1;">
                  <div style="font-size:14px;font-weight:600;color:#1a2e3b;"><?= htmlspecialchars($a['service_name']) ?></div>
                  <div style="font-size:12px;color:#5a7080;margin-top:3px;">
                    Dr. <?= htmlspecialchars($a['dentist_name']) ?> &bull;
                    <?= date('M j, Y', strtotime($a['appointment_date'])) ?> at <?= date('g:i A', strtotime($a['appointment_time'])) ?>
                  </div>
                  <div style="font-size:12px;color:#1a2e3b;margin-top:3px;">₱<?= number_format($a['price'], 2) ?></div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
                  <span style="background:#1D9E75;color:#fff;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;white-space:nowrap;"><?= $dayLabel ?></span>
                  <?= statusBadge($a['status']) ?>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Confirmed -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <span style="display:inline-flex;align-items:center;gap:8px;">
              <span style="width:10px;height:10px;border-radius:50%;background:#3B82F6;display:inline-block;"></span>
              Confirmed Appointments
              <span style="font-size:0.85rem;font-weight:400;color:var(--gray);">(<?= count($confirmedAppts) ?>)</span>
            </span>
          </div>
        </div>
        <div class="card-body">
          <?php if (empty($confirmedAppts)): ?>
            <div style="text-align:center;padding:2rem;color:#5a7080;font-size:14px;">No confirmed appointments yet.</div>
          <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:10px;">
              <?php foreach ($confirmedAppts as $a): ?>
              <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:10px;background:#EFF6FF;border-left:4px solid #3B82F6;">
                <div style="width:44px;height:44px;border-radius:50%;background:#DBEAFE;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#1E40AF"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div style="flex:1;">
                  <div style="font-size:14px;font-weight:600;color:#1a2e3b;"><?= htmlspecialchars($a['service_name']) ?></div>
                  <div style="font-size:12px;color:#5a7080;margin-top:3px;">
                    Dr. <?= htmlspecialchars($a['dentist_name']) ?> &bull;
                    <?= date('M j, Y', strtotime($a['appointment_date'])) ?> at <?= date('g:i A', strtotime($a['appointment_time'])) ?>
                  </div>
                </div>
                <span style="background:#3B82F6;color:#fff;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">Confirmed</span>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Pending Payments -->
      <?php if (!empty($pendingPayments)): ?>
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <span style="display:inline-flex;align-items:center;gap:8px;">
              <span style="width:10px;height:10px;border-radius:50%;background:#F59E0B;display:inline-block;"></span>
              Pending Payments
              <span style="font-size:0.85rem;font-weight:400;color:var(--gray);">(<?= count($pendingPayments) ?>)</span>
            </span>
          </div>
        </div>
        <div class="card-body">
          <div style="display:flex;flex-direction:column;gap:10px;">
            <?php foreach ($pendingPayments as $p): ?>
            <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:10px;background:#FFFBEB;border-left:4px solid #F59E0B;">
              <div style="width:44px;height:44px;border-radius:50%;background:#FEF3C7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#92400E"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              </div>
              <div style="flex:1;">
                <div style="font-size:14px;font-weight:600;color:#1a2e3b;">Payment due for <?= htmlspecialchars($p['service_name']) ?></div>
                <div style="font-size:12px;color:#5a7080;margin-top:3px;"><?= date('M j, Y', strtotime($p['appointment_date'])) ?></div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                <div style="font-size:14px;font-weight:700;color:#1a2e3b;">₱<?= number_format($p['amount'], 2) ?></div>
                <a href="?page=patient_payments" style="font-size:11px;color:#F59E0B;font-weight:600;text-decoration:none;">View Payments →</a>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Cancelled -->
      <?php if (!empty($cancelledAppts)): ?>
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <span style="display:inline-flex;align-items:center;gap:8px;">
              <span style="width:10px;height:10px;border-radius:50%;background:#FC8181;display:inline-block;"></span>
              Recent Cancellations
              <span style="font-size:0.85rem;font-weight:400;color:var(--gray);">(<?= count($cancelledAppts) ?>)</span>
            </span>
          </div>
        </div>
        <div class="card-body">
          <div style="display:flex;flex-direction:column;gap:10px;">
            <?php foreach ($cancelledAppts as $a): ?>
            <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:10px;background:#FFF5F5;border-left:4px solid #FC8181;">
              <div style="width:44px;height:44px;border-radius:50%;background:#FEE2E2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#9B2C2C"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </div>
              <div style="flex:1;">
                <div style="font-size:14px;font-weight:600;color:#1a2e3b;"><?= htmlspecialchars($a['service_name']) ?> — Cancelled</div>
                <div style="font-size:12px;color:#5a7080;margin-top:3px;">
                  Dr. <?= htmlspecialchars($a['dentist_name']) ?> &bull;
                  <?= date('M j, Y', strtotime($a['appointment_date'])) ?> at <?= date('g:i A', strtotime($a['appointment_time'])) ?>
                </div>
              </div>
              <a href="?page=patient_appointments" style="font-size:12px;color:#3B82F6;font-weight:600;text-decoration:none;white-space:nowrap;">Rebook →</a>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>
</body>
</html>