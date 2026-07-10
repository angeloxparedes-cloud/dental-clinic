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
                 s.name AS service_name,
                 u.avatar
          FROM appointments a
          JOIN users u ON a.patient_id = u.id
          JOIN dentists d ON a.dentist_id = d.id
          JOIN services s ON a.service_id = s.id
          WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            AND (a.reschedule_reason IS NULL OR a.reschedule_reason = '')
          ORDER BY a.created_at DESC
        ");
        $newAppts = $r1->fetch_all(MYSQLI_ASSOC);

        // Rescheduled appointments (last 7 days)
        $r_res = $db->query("
          SELECT a.id, a.appointment_date, a.appointment_time,
                 a.original_date, a.original_time,
                 a.reschedule_reason, a.rescheduled_at,
                 CONCAT(u.first_name,' ',u.last_name) AS patient_name,
                 CONCAT(d.first_name,' ',d.last_name) AS dentist_name,
                 s.name AS service_name,
                 u.avatar
          FROM appointments a
          JOIN users u ON a.patient_id = u.id
          JOIN dentists d ON a.dentist_id = d.id
          JOIN services s ON a.service_id = s.id
          WHERE a.reschedule_reason IS NOT NULL
            AND a.reschedule_reason != ''
            AND a.rescheduled_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
          ORDER BY a.rescheduled_at DESC
        ");
        $rescheduledAppts = $r_res->fetch_all(MYSQLI_ASSOC);

        // Upcoming appointments (next 3 days)
        $r2 = $db->query("
          SELECT a.id, a.appointment_date, a.appointment_time, a.status,
                 CONCAT(u.first_name,' ',u.last_name) AS patient_name,
                 CONCAT(d.first_name,' ',d.last_name) AS dentist_name,
                 s.name AS service_name,
                 u.avatar
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
                 s.name AS service_name,
                 u.avatar
          FROM appointments a
          JOIN users u ON a.patient_id = u.id
          JOIN services s ON a.service_id = s.id
          WHERE a.status = 'cancelled'
            AND a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
          ORDER BY a.appointment_date DESC
        ");
        $cancelledAppts = $r3->fetch_all(MYSQLI_ASSOC);

        // Helper: render avatar circle
        function renderAvatar($a, $bgColor, $textColor, $fallbackInitial) {
          $avatarUrl = !empty($a['avatar'])
            ? APP_URL . '/public/uploads/avatars/' . htmlspecialchars($a['avatar'])
            : null;
          if ($avatarUrl): ?>
            <div style="width:40px;height:40px;border-radius:50%;overflow:hidden;flex-shrink:0;">
              <img src="<?= $avatarUrl ?>" alt="<?= htmlspecialchars($a['patient_name']) ?>"
                   style="width:100%;height:100%;object-fit:cover;"
                   onerror="this.parentElement.innerHTML='<span style=\'display:flex;align-items:center;justify-content:center;width:100%;height:100%;font-size:13px;font-weight:700;color:<?= $textColor ?>;background:<?= $bgColor ?>\'><?= strtoupper(substr($fallbackInitial,0,1)) ?></span>'">
            </div>
          <?php else: ?>
            <div style="width:40px;height:40px;border-radius:50%;background:<?= $bgColor ?>;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:<?= $textColor ?>;flex-shrink:0;">
              <?= strtoupper(substr($fallbackInitial,0,1)) ?>
            </div>
          <?php endif;
        }
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
            <div style="text-align:center;padding:2rem;color:#5a7080;font-size:14px;">No new bookings in the last 7 days.</div>
          <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:10px;">
              <?php foreach ($newAppts as $a): ?>
              <div style="display:flex;align-items:center;gap:14px;padding:12px 16px;border-radius:10px;background:#EFF6FF;border-left:4px solid #3B82F6;">
                <?php renderAvatar($a, '#DBEAFE', '#1E40AF', $a['patient_name']); ?>
                <div style="flex:1;">
                  <div style="font-size:13px;font-weight:600;color:#1a2e3b;">
                    <?= htmlspecialchars($a['patient_name']) ?> booked
                    <span style="color:#3B82F6;"><?= htmlspecialchars($a['service_name']) ?></span>
                  </div>
                  <div style="font-size:12px;color:#5a7080;margin-top:2px;">
                    Dr. <?= htmlspecialchars($a['dentist_name']) ?> &bull;
                    <?= date('M j, Y', strtotime($a['appointment_date'])) ?> at <?= date('g:i A', strtotime($a['appointment_time'])) ?>
                  </div>
                </div>
                <div><?= statusBadge($a['status']) ?></div>
                <div style="font-size:11px;color:#5a7080;white-space:nowrap;"><?= date('M j, g:i A', strtotime($a['created_at'])) ?></div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Rescheduled Appointments -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <span style="display:inline-flex;align-items:center;gap:8px;">
              <span style="width:10px;height:10px;border-radius:50%;background:#F59E0B;display:inline-block;"></span>
              Rescheduled Appointments
              <span style="font-size:0.85rem;font-weight:400;color:var(--gray);">(last 7 days — <?= count($rescheduledAppts) ?>)</span>
            </span>
          </div>
        </div>
        <div class="card-body">
          <?php if (empty($rescheduledAppts)): ?>
            <div style="text-align:center;padding:2rem;color:#5a7080;font-size:14px;">No rescheduled appointments this week.</div>
          <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:10px;">
              <?php foreach ($rescheduledAppts as $a): ?>
              <div style="display:flex;align-items:flex-start;gap:14px;padding:14px 16px;border-radius:10px;background:#FFFBEB;border-left:4px solid #F59E0B;">
                <div style="margin-top:2px;"><?php renderAvatar($a, '#FEF3C7', '#92400E', $a['patient_name']); ?></div>
                <div style="flex:1;">
                  <div style="font-size:13px;font-weight:600;color:#1a2e3b;">
                    <?= htmlspecialchars($a['patient_name']) ?> rescheduled
                    <span style="color:#D97706;"><?= htmlspecialchars($a['service_name']) ?></span>
                  </div>
                  <div style="font-size:12px;color:#5a7080;margin-top:3px;">
                    Dr. <?= htmlspecialchars($a['dentist_name']) ?>
                  </div>
                  <?php if (!empty($a['original_date'])): ?>
                  <div style="display:flex;align-items:center;gap:6px;margin-top:6px;font-size:12px;color:#5a7080;">
                    <span style="background:#FEE2E2;color:#9B2C2C;padding:2px 8px;border-radius:20px;white-space:nowrap;">
                      <?= date('M j, Y', strtotime($a['original_date'])) ?> at <?= date('g:i A', strtotime($a['original_time'])) ?>
                    </span>
                    <span style="color:#9ca3af;">&rarr;</span>
                    <span style="background:#D1FAE5;color:#065F46;padding:2px 8px;border-radius:20px;white-space:nowrap;">
                      <?= date('M j, Y', strtotime($a['appointment_date'])) ?> at <?= date('g:i A', strtotime($a['appointment_time'])) ?>
                    </span>
                  </div>
                  <?php endif; ?>
                  <div style="margin-top:8px;display:inline-flex;align-items:flex-start;gap:6px;background:#FEF3C7;border-radius:6px;padding:7px 10px;font-size:12px;color:#92400E;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><strong>Reason:</strong> <?= htmlspecialchars($a['reschedule_reason']) ?></span>
                  </div>
                </div>
                <div style="font-size:11px;color:#5a7080;white-space:nowrap;flex-shrink:0;">
                  <?= date('M j, g:i A', strtotime($a['rescheduled_at'])) ?>
                </div>
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
            <div style="text-align:center;padding:2rem;color:#5a7080;font-size:14px;">No upcoming appointments in the next 3 days.</div>
          <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:10px;">
              <?php foreach ($upcomingAppts as $a):
                $daysLeft = (int)(new DateTime($a['appointment_date']))->diff(new DateTime(date('Y-m-d')))->days;
                $dayLabel = $daysLeft === 0 ? 'Today' : ($daysLeft === 1 ? 'Tomorrow' : "In {$daysLeft} days");
              ?>
              <div style="display:flex;align-items:center;gap:14px;padding:12px 16px;border-radius:10px;background:#F0FFF4;border-left:4px solid #1D9E75;">
                <?php renderAvatar($a, '#D1FAE5', '#065F46', $a['patient_name']); ?>
                <div style="flex:1;">
                  <div style="font-size:13px;font-weight:600;color:#1a2e3b;">
                    <?= htmlspecialchars($a['patient_name']) ?> —
                    <span style="color:#0F6E56;"><?= htmlspecialchars($a['service_name']) ?></span>
                  </div>
                  <div style="font-size:12px;color:#5a7080;margin-top:2px;">
                    Dr. <?= htmlspecialchars($a['dentist_name']) ?> &bull;
                    <?= date('M j, Y', strtotime($a['appointment_date'])) ?> at <?= date('g:i A', strtotime($a['appointment_time'])) ?>
                  </div>
                </div>
                <span style="background:#1D9E75;color:#fff;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;white-space:nowrap;"><?= $dayLabel ?></span>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Cancellations -->
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
            <div style="text-align:center;padding:2rem;color:#5a7080;font-size:14px;">No cancellations this week.</div>
          <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:10px;">
              <?php foreach ($cancelledAppts as $a): ?>
              <div style="display:flex;align-items:center;gap:14px;padding:12px 16px;border-radius:10px;background:#FFF5F5;border-left:4px solid #FC8181;">
                <?php renderAvatar($a, '#FEE2E2', '#9B2C2C', $a['patient_name']); ?>
                <div style="flex:1;">
                  <div style="font-size:13px;font-weight:600;color:#1a2e3b;">
                    <?= htmlspecialchars($a['patient_name']) ?> cancelled
                    <span style="color:#FC8181;"><?= htmlspecialchars($a['service_name']) ?></span>
                  </div>
                  <div style="font-size:12px;color:#5a7080;margin-top:2px;">
                    <?= date('M j, Y', strtotime($a['appointment_date'])) ?> at <?= date('g:i A', strtotime($a['appointment_time'])) ?>
                  </div>
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