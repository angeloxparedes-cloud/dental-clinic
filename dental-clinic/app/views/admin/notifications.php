<?php $currentPage = 'admin_notifications'; ?>
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
      <?php
        $db = getDB();

        // New appointments (last 7 days)
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

        // Upcoming appointments (next 3 days)
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

        // Cancelled this week
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
      ?>

      <!-- New Bookings -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <span style="display:inline-flex;align-items:center;gap:8px;">
              <span style="width:10px;height:10px;border-radius:50%;background:#3B82F6;display:inline-block;"></span>
              New Bookings
              <span style="font-size:0.85rem;font-weight:400;color:var(--gray);">(last 7 days — <?= count($newAppts) ?>)</span>
            </span>
          </div>
        </div>
        <div class="card-body">
          <?php if (empty($newAppts)): ?>
            <div style="text-align:center; padding:2rem; color:#5a7080; font-size:14px;">No new bookings in the last 7 days.</div>
          <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:10px;">
              <?php foreach ($newAppts as $a): ?>
              <div style="display:flex; align-items:center; gap:14px; padding:12px 16px; border-radius:10px; background:#EFF6FF; border-left:4px solid #3B82F6;">
                <div style="width:40px; height:40px; border-radius:50%; background:#DBEAFE; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#1E40AF; flex-shrink:0;">
                  <?= strtoupper(substr($a['patient_name'],0,1)) ?>
                </div>
                <div style="flex:1;">
                  <div style="font-size:13px; font-weight:600; color:#1a2e3b;"><?= htmlspecialchars($a['patient_name']) ?> booked <span style="color:#3B82F6;"><?= htmlspecialchars($a['service_name']) ?></span></div>
                  <div style="font-size:12px; color:#5a7080; margin-top:2px;">
                    Dr. <?= htmlspecialchars($a['dentist_name']) ?> &bull;
                    <?= date('M j, Y', strtotime($a['appointment_date'])) ?> at <?= date('g:i A', strtotime($a['appointment_time'])) ?>
                  </div>
                </div>
                <div><?= statusBadge($a['status']) ?></div>
                <div style="font-size:11px; color:#5a7080; white-space:nowrap;"><?= date('M j, g:i A', strtotime($a['created_at'])) ?></div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Upcoming (next 3 days) -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <span style="display:inline-flex;align-items:center;gap:8px;">
              <span style="width:10px;height:10px;border-radius:50%;background:#1D9E75;display:inline-block;"></span>
              Upcoming Appointments
              <span style="font-size:0.85rem;font-weight:400;color:var(--gray);">(next 3 days — <?= count($upcomingAppts) ?>)</span>
            </span>
          </div>
        </div>
        <div class="card-body">
          <?php if (empty($upcomingAppts)): ?>
            <div style="text-align:center; padding:2rem; color:#5a7080; font-size:14px;">No upcoming appointments in the next 3 days.</div>
          <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:10px;">
              <?php foreach ($upcomingAppts as $a):
                $daysLeft = (int)(new DateTime($a['appointment_date']))->diff(new DateTime(date('Y-m-d')))->days;
                $dayLabel = $daysLeft === 0 ? 'Today' : ($daysLeft === 1 ? 'Tomorrow' : "In {$daysLeft} days");
              ?>
              <div style="display:flex; align-items:center; gap:14px; padding:12px 16px; border-radius:10px; background:#F0FFF4; border-left:4px solid #1D9E75;">
                <div style="width:40px; height:40px; border-radius:50%; background:#D1FAE5; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#065F46; flex-shrink:0;">
                  <?= strtoupper(substr($a['patient_name'],0,1)) ?>
                </div>
                <div style="flex:1;">
                  <div style="font-size:13px; font-weight:600; color:#1a2e3b;"><?= htmlspecialchars($a['patient_name']) ?> — <span style="color:#0F6E56;"><?= htmlspecialchars($a['service_name']) ?></span></div>
                  <div style="font-size:12px; color:#5a7080; margin-top:2px;">
                    Dr. <?= htmlspecialchars($a['dentist_name']) ?> &bull;
                    <?= date('M j, Y', strtotime($a['appointment_date'])) ?> at <?= date('g:i A', strtotime($a['appointment_time'])) ?>
                  </div>
                </div>
                <span style="background:#1D9E75; color:#fff; font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; white-space:nowrap;"><?= $dayLabel ?></span>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Cancelled This Week -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <span style="display:inline-flex;align-items:center;gap:8px;">
              <span style="width:10px;height:10px;border-radius:50%;background:#FC8181;display:inline-block;"></span>
              Cancellations
              <span style="font-size:0.85rem;font-weight:400;color:var(--gray);">(last 7 days — <?= count($cancelledAppts) ?>)</span>
            </span>
          </div>
        </div>
        <div class="card-body">
          <?php if (empty($cancelledAppts)): ?>
            <div style="text-align:center; padding:2rem; color:#5a7080; font-size:14px;">No cancellations this week.</div>
          <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:10px;">
              <?php foreach ($cancelledAppts as $a): ?>
              <div style="display:flex; align-items:center; gap:14px; padding:12px 16px; border-radius:10px; background:#FFF5F5; border-left:4px solid #FC8181;">
                <div style="width:40px; height:40px; border-radius:50%; background:#FEE2E2; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#9B2C2C; flex-shrink:0;">
                  <?= strtoupper(substr($a['patient_name'],0,1)) ?>
                </div>
                <div style="flex:1;">
                  <div style="font-size:13px; font-weight:600; color:#1a2e3b;"><?= htmlspecialchars($a['patient_name']) ?> cancelled <span style="color:#FC8181;"><?= htmlspecialchars($a['service_name']) ?></span></div>
                  <div style="font-size:12px; color:#5a7080; margin-top:2px;"><?= date('M j, Y', strtotime($a['appointment_date'])) ?> at <?= date('g:i A', strtotime($a['appointment_time'])) ?></div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</div>
</body>
</html>