<?php $currentPage = 'patient_dashboard'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Dashboard – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>

  <div class="main-content">
    <div class="topbar">
      <div>
        <div class="topbar-title">My Dashboard</div>
        <div class="text-sm text-gray">Welcome back, <?= htmlspecialchars($_SESSION['first_name'] ?? '') ?>!</div>
      </div>
      <div class="topbar-actions">
        <button class="btn btn-primary btn-sm" onclick="openModal('bookModal')">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Book Appointment
        </button>
      </div>
    </div>

    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; showFlash(); ?>

      <!-- Stats -->
      <?php
        $total     = count($appointments ?: []);
        $pending   = count(array_filter($appointments ?: [], fn($a) => $a['status'] === 'pending'));
        $upcoming  = count(array_filter($appointments ?: [], fn($a) => in_array($a['status'], ['pending','confirmed']) && $a['appointment_date'] >= date('Y-m-d')));
        $completed = count(array_filter($appointments ?: [], fn($a) => $a['status'] === 'completed'));
      ?>
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          </div>
          <div><div class="stat-value"><?= $total ?></div><div class="stat-label">Total Appointments</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div><div class="stat-value"><?= $pending ?></div><div class="stat-label">Pending</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon teal">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          <div><div class="stat-value"><?= $upcoming ?></div><div class="stat-label">Upcoming</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div><div class="stat-value"><?= $completed ?></div><div class="stat-label">Completed</div></div>
        </div>
      </div>

      <!-- Appointments -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">My Appointments</div>
        </div>
        <div class="card-body">
          <?php if (empty($appointments)): ?>
            <div class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <h3>No Appointments Yet</h3>
              <p>Book your first appointment to get started</p>
              <button class="btn btn-primary btn-sm mt-1" onclick="openModal('bookModal')">Book Now</button>
            </div>
          <?php else: ?>
            <div class="appt-grid">
              <?php foreach ($appointments as $appt): ?>
              <div class="appt-card <?= $appt['status'] ?>">
                <div class="appt-card-header">
                  <div class="appt-service"><?= htmlspecialchars($appt['service_name']) ?></div>
                  <?= statusBadge($appt['status']) ?>
                </div>
                <div class="appt-meta">
                  <div class="appt-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <?= formatDate($appt['appointment_date']) ?>
                  </div>
                  <div class="appt-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?= formatTime($appt['appointment_time']) ?>
                  </div>
                  <div class="appt-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Dr. <?= htmlspecialchars($appt['dentist_name']) ?>
                  </div>
                  <?php if (!empty($appt['reschedule_reason'])): ?>
                  <div class="appt-meta-item" style="color:#F59E0B;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Rescheduled: <?= htmlspecialchars($appt['reschedule_reason']) ?>
                  </div>
                  <?php elseif (!empty($appt['notes'])): ?>
                  <div class="appt-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <?= htmlspecialchars($appt['notes']) ?>
                  </div>
                  <?php endif; ?>
                </div>
                <div class="flex items-center justify-between">
                  <div class="appt-price">₱<?= number_format($appt['price'], 2) ?></div>
                  <div style="display:flex;gap:6px;">
                    <?php if (in_array($appt['status'], ['pending', 'confirmed'])): ?>
                      <button class="btn btn-ghost btn-sm"
                        onclick="openReschedule(<?= $appt['id'] ?>, '<?= htmlspecialchars($appt['service_name'], ENT_QUOTES) ?>', '<?= $appt['appointment_date'] ?>', '<?= $appt['appointment_time'] ?>')">
                        Reschedule
                      </button>
                      <a href="?page=cancel_appointment&id=<?= $appt['id'] ?>" class="btn btn-danger btn-sm"
                         onclick="return confirm('Cancel this appointment?')">Cancel</a>
                    <?php endif; ?>
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

<!-- Book Appointment Modal -->
<div class="modal-overlay" id="bookModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Book Appointment</div>
      <button class="modal-close" onclick="closeModal('bookModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="?page=book_appointment">
      <div class="modal-body">
        <div class="form-group">
          <label>Select Dentist <span style="color:red">*</span></label>
          <div class="input-wrap no-icon">
            <select name="dentist_id" required>
              <option value="" disabled selected hidden>-- Choose a dentist --</option>
              <?php foreach ($dentists as $d): ?>
                <option value="<?= $d['id'] ?>">Dr. <?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?> – <?= htmlspecialchars($d['specialization']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Select Service <span style="color:red">*</span></label>
          <div class="input-wrap no-icon">
            <select name="service_id" required id="serviceSelect" onchange="updatePrice(this)">
              <option value="" disabled selected hidden>-- Choose a service --</option>
              <?php foreach ($services as $s): ?>
                <option value="<?= $s['id'] ?>" data-price="<?= $s['price'] ?>" data-dur="<?= $s['duration_minutes'] ?>">
                  <?= htmlspecialchars($s['name']) ?> – ₱<?= number_format($s['price'], 2) ?> (<?= $s['duration_minutes'] ?>min)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div id="serviceInfo" style="display:none;background:var(--primary-light);border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:0.85rem;color:var(--primary);">
          <strong>Price:</strong> <span id="servicePrice">-</span> &nbsp;|&nbsp; <strong>Duration:</strong> <span id="serviceDur">-</span>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Date <span style="color:red">*</span></label>
            <div class="input-wrap">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <input type="date" name="appointment_date" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
            </div>
          </div>
          <div class="form-group">
            <label>Time <span style="color:red">*</span></label>
            <div class="input-wrap">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <select name="appointment_time" required>
                <option value="" disabled selected hidden>-- Time --</option>
                <?php
                  $times = ['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30',
                            '13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30'];
                  foreach ($times as $t) echo "<option value='{$t}'>" . date('g:i A', strtotime($t)) . "</option>";
                ?>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Notes / Concerns</label>
          <div class="input-wrap">
            <svg class="icon" style="top:14px;transform:none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <textarea name="notes" placeholder="Describe your dental concern or symptoms..."></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('bookModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          Confirm Booking
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Reschedule Modal -->
<div class="modal-overlay" id="rescheduleModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Reschedule Appointment</div>
      <button class="modal-close" onclick="closeModal('rescheduleModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="?page=reschedule_appointment">
      <div class="modal-body">

        <input type="hidden" name="appointment_id" id="reschedule_appt_id">

        <!-- Current appointment info -->
        <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#92400E;">
          <strong>Rescheduling:</strong> <span id="reschedule_service_label"></span><br>
          <span style="color:#5a7080;">Original: <span id="reschedule_original_info"></span></span>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>New Date <span style="color:red">*</span></label>
            <div class="input-wrap">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <input type="date" name="new_date" id="reschedule_new_date" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
            </div>
          </div>
          <div class="form-group">
            <label>New Time <span style="color:red">*</span></label>
            <div class="input-wrap">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <select name="new_time" id="reschedule_new_time" required>
                <option value="" disabled selected hidden>-- Time --</option>
                <?php
                  foreach ($times as $t) echo "<option value='{$t}'>" . date('g:i A', strtotime($t)) . "</option>";
                ?>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Reason for Rescheduling <span style="color:red">*</span></label>
          <div class="input-wrap no-icon">
            <select name="reschedule_reason" id="reschedule_reason_select" required onchange="toggleOtherReason(this)">
              <option value="" disabled selected hidden>-- Select a reason --</option>
              <option value="Personal emergency">Personal emergency</option>
              <option value="Work conflict">Work conflict</option>
              <option value="Feeling unwell / sick">Feeling unwell / sick</option>
              <option value="Transportation issue">Transportation issue</option>
              <option value="Family matter">Family matter</option>
              <option value="Other">Other (please specify)</option>
            </select>
          </div>
        </div>

        <div class="form-group" id="other_reason_group" style="display:none;">
          <label>Please specify <span style="color:red">*</span></label>
          <div class="input-wrap">
            <svg class="icon" style="top:14px;transform:none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <textarea name="reschedule_reason_other" id="reschedule_reason_other" placeholder="Describe your reason..."></textarea>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('rescheduleModal')">Cancel</button>
        <button type="submit" class="btn btn-primary" onclick="return validateReschedule()">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          Confirm Reschedule
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('open'); });
});
function updatePrice(sel) {
  const opt  = sel.options[sel.selectedIndex];
  const info = document.getElementById('serviceInfo');
  if (opt.value) {
    document.getElementById('servicePrice').textContent = '₱' + parseFloat(opt.dataset.price).toLocaleString('en', {minimumFractionDigits:2});
    document.getElementById('serviceDur').textContent   = opt.dataset.dur + ' minutes';
    info.style.display = 'block';
  } else {
    info.style.display = 'none';
  }
}
function openReschedule(id, service, date, time) {
  document.getElementById('reschedule_appt_id').value = id;
  document.getElementById('reschedule_service_label').textContent = service;
  const d = new Date(date + 'T' + time);
  document.getElementById('reschedule_original_info').textContent =
    d.toLocaleDateString('en-US', {month:'long', day:'numeric', year:'numeric'}) +
    ' at ' + d.toLocaleTimeString('en-US', {hour:'numeric', minute:'2-digit'});
  document.getElementById('reschedule_new_date').value = '';
  document.getElementById('reschedule_new_time').value = '';
  document.getElementById('reschedule_reason_select').value = '';
  document.getElementById('other_reason_group').style.display = 'none';
  openModal('rescheduleModal');
}
function toggleOtherReason(sel) {
  const group = document.getElementById('other_reason_group');
  const textarea = document.getElementById('reschedule_reason_other');
  if (sel.value === 'Other') {
    group.style.display = 'block';
    textarea.required = true;
    sel.name = '_reschedule_reason_ignored';
    textarea.name = 'reschedule_reason';
  } else {
    group.style.display = 'none';
    textarea.required = false;
    sel.name = 'reschedule_reason';
    textarea.name = 'reschedule_reason_other';
  }
}
function validateReschedule() {
  const date   = document.getElementById('reschedule_new_date').value;
  const time   = document.getElementById('reschedule_new_time').value;
  const reason = document.querySelector('[name="reschedule_reason"]').value;
  if (!date || !time || !reason) {
    alert('Please fill in all required fields.');
    return false;
  }
  return true;
}
</script>
</body>
</html>