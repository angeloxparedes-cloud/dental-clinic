<?php $currentPage = 'admin_calendar'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Calendar – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-title">Appointment Calendar</div>
    </div>
    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; ?>
      <?php
        $db = getDB();
        $res = $db->query("
          SELECT a.appointment_date, a.appointment_time, a.status,
                 CONCAT(u.first_name,' ',u.last_name) AS patient_name,
                 CONCAT(d.first_name,' ',d.last_name) AS dentist_name,
                 s.name AS service_name
          FROM appointments a
          JOIN users u ON a.patient_id = u.id
          JOIN dentists d ON a.dentist_id = d.id
          JOIN services s ON a.service_id = s.id
          ORDER BY a.appointment_date, a.appointment_time
        ");
        $allAppts = $res->fetch_all(MYSQLI_ASSOC);

        // Group by date
        $byDate = [];
        foreach ($allAppts as $a) {
          $byDate[$a['appointment_date']][] = $a;
        }

        $month = isset($_GET['m']) ? (int)$_GET['m'] : (int)date('m');
        $year  = isset($_GET['y']) ? (int)$_GET['y'] : (int)date('Y');
        if ($month < 1) { $month = 12; $year--; }
        if ($month > 12) { $month = 1; $year++; }
        $prevM = $month - 1; $prevY = $year;
        $nextM = $month + 1; $nextY = $year;
        if ($prevM < 1)  { $prevM = 12; $prevY--; }
        if ($nextM > 12) { $nextM = 1;  $nextY++; }
        $firstDay   = (int)date('N', mktime(0,0,0,$month,1,$year));
        $daysInMonth = (int)date('t', mktime(0,0,0,$month,1,$year));
        $today = date('Y-m-d');

        $statusColors = [
          'pending'   => '#F59E0B',
          'confirmed' => '#3B82F6',
          'completed' => '#1D9E75',
          'cancelled' => '#FC8181',
        ];
      ?>

      <div class="card">
        <!-- Calendar Header -->
        <div class="card-header" style="display:flex; align-items:center; justify-content:space-between;">
          <a href="?page=admin_calendar&m=<?= $prevM ?>&y=<?= $prevY ?>" class="btn btn-ghost btn-sm">&#8592; Prev</a>
          <div class="card-title" style="margin:0;"><?= date('F Y', mktime(0,0,0,$month,1,$year)) ?></div>
          <a href="?page=admin_calendar&m=<?= $nextM ?>&y=<?= $nextY ?>" class="btn btn-ghost btn-sm">Next &#8594;</a>
        </div>

        <!-- Legend -->
        <div style="display:flex; gap:16px; padding:0 20px 16px; flex-wrap:wrap;">
          <?php foreach ($statusColors as $s => $c): ?>
            <div style="display:flex; align-items:center; gap:5px; font-size:12px; color:#5a7080;">
              <span style="width:10px; height:10px; border-radius:50%; background:<?= $c ?>; display:inline-block;"></span>
              <?= ucfirst($s) ?>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Calendar Grid -->
        <div style="padding:0 16px 20px;">
          <!-- Day headers -->
          <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-bottom:4px;">
            <?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d): ?>
              <div style="text-align:center; font-size:12px; font-weight:600; color:#5a7080; padding:8px 0;"><?= $d ?></div>
            <?php endforeach; ?>
          </div>

          <!-- Day cells -->
          <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:4px;">
            <?php
              // Empty cells before first day
              for ($i = 1; $i < $firstDay; $i++):
            ?>
              <div style="min-height:90px;"></div>
            <?php endfor; ?>

            <?php for ($day = 1; $day <= $daysInMonth; $day++):
              $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
              $isToday = $dateStr === $today;
              $dayAppts = $byDate[$dateStr] ?? [];
            ?>
              <div style="min-height:90px; border-radius:8px; border:1px solid <?= $isToday ? '#1D9E75' : '#e8edf0' ?>; background:<?= $isToday ? '#f0fff8' : '#fff' ?>; padding:6px; position:relative;">
                <div style="font-size:12px; font-weight:<?= $isToday ? '700' : '500' ?>; color:<?= $isToday ? '#1D9E75' : '#1a2e3b' ?>; margin-bottom:4px;"><?= $day ?></div>
                <?php foreach (array_slice($dayAppts, 0, 3) as $appt): ?>
                  <div onclick="showApptDetail('<?= htmlspecialchars(json_encode($appt), ENT_QUOTES) ?>')"
                       style="font-size:10px; background:<?= $statusColors[$appt['status']] ?>22; border-left:2px solid <?= $statusColors[$appt['status']] ?>; color:#1a2e3b; padding:2px 5px; border-radius:3px; margin-bottom:2px; cursor:pointer; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                       title="<?= htmlspecialchars($appt['patient_name']) ?>">
                    <?= date('g:i A', strtotime($appt['appointment_time'])) ?> <?= htmlspecialchars($appt['patient_name']) ?>
                  </div>
                <?php endforeach; ?>
                <?php if (count($dayAppts) > 3): ?>
                  <div style="font-size:10px; color:#5a7080; margin-top:2px;">+<?= count($dayAppts) - 3 ?> more</div>
                <?php endif; ?>
              </div>
            <?php endfor; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Appointment Detail Modal -->
<div class="modal-overlay" id="apptDetailModal">
  <div class="modal" style="max-width:360px;">
    <div class="modal-header">
      <div class="modal-title">Appointment Details</div>
      <button class="modal-close" onclick="closeModal('apptDetailModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <table style="width:100%; font-size:14px; border-collapse:collapse;">
        <tr><td style="padding:8px 0; color:#5a7080; width:40%;">Patient</td><td style="padding:8px 0; font-weight:600;" id="d-patient"></td></tr>
        <tr><td style="padding:8px 0; color:#5a7080;">Dentist</td><td style="padding:8px 0; font-weight:600;" id="d-dentist"></td></tr>
        <tr><td style="padding:8px 0; color:#5a7080;">Service</td><td style="padding:8px 0;" id="d-service"></td></tr>
        <tr><td style="padding:8px 0; color:#5a7080;">Date</td><td style="padding:8px 0;" id="d-date"></td></tr>
        <tr><td style="padding:8px 0; color:#5a7080;">Time</td><td style="padding:8px 0;" id="d-time"></td></tr>
        <tr><td style="padding:8px 0; color:#5a7080;">Status</td><td style="padding:8px 0;" id="d-status"></td></tr>
      </table>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('apptDetailModal')">Close</button>
      <a href="?page=admin_appointments" class="btn btn-primary">View All</a>
    </div>
  </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function showApptDetail(json) {
  const a = JSON.parse(json);
  document.getElementById('d-patient').textContent  = a.patient_name;
  document.getElementById('d-dentist').textContent  = 'Dr. ' + a.dentist_name;
  document.getElementById('d-service').textContent  = a.service_name;
  document.getElementById('d-date').textContent     = new Date(a.appointment_date).toLocaleDateString('en-US',{year:'numeric',month:'long',day:'numeric'});
  document.getElementById('d-time').textContent     = new Date('1970-01-01T'+a.appointment_time).toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'});
  document.getElementById('d-status').innerHTML     = `<span style="text-transform:capitalize;font-weight:600;">${a.status}</span>`;
  openModal('apptDetailModal');
}
document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', function(e) { if(e.target===this) this.classList.remove('open'); });
});
</script>
</body>
</html>