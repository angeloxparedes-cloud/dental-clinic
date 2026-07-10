<?php $currentPage = 'patient_appointments'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Appointments – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-title">My Appointments</div>
      <div class="topbar-actions">
        <button class="btn btn-primary btn-sm" onclick="openModal('bookModal')">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Book Appointment
        </button>
      </div>
    </div>
    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; showFlash(); ?>

      <!-- Filter -->
      <div class="filter-bar">
        <?php
          $filters = ['' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
          $current = $_GET['status'] ?? '';
          foreach ($filters as $val => $label):
        ?>
          <a href="?page=patient_appointments<?= $val ? '&status='.$val : '' ?>" class="filter-btn <?= $current === $val ? 'active' : '' ?>"><?= $label ?></a>
        <?php endforeach; ?>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <?= $filters[$current] ?? 'All' ?> Appointments
            <span style="font-size:0.85rem;font-weight:400;color:var(--gray);margin-left:8px;">(<?= count($appointments) ?>)</span>
          </div>
        </div>
        <div class="table-wrap">
          <?php if (empty($appointments)): ?>
            <div class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <h3>No Appointments Found</h3>
              <p>No appointments match the selected filter</p>
              <button class="btn btn-primary btn-sm mt-1" onclick="openModal('bookModal')">Book Now</button>
            </div>
          <?php else: ?>
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Service</th>
                  <th>Dentist</th>
                  <th>Date & Time</th>
                  <th>Price</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($appointments as $appt):
                  $isToday = $appt['appointment_date'] === date('Y-m-d');
                ?>
                <tr>
                  <td class="text-gray text-sm">#<?= $appt['id'] ?></td>
                  <td>
                    <div class="font-bold"><?= htmlspecialchars($appt['service_name']) ?></div>
                    <?php if ($isToday): ?>
                      <div style="font-size:11px; color:#1D9E75; font-weight:600;">Today!</div>
                    <?php endif; ?>
                  </td>
                  <td>Dr. <?= htmlspecialchars($appt['dentist_name']) ?></td>
                  <td>
                    <div><?= formatDate($appt['appointment_date']) ?></div>
                    <div class="text-sm text-gray"><?= formatTime($appt['appointment_time']) ?></div>
                  </td>
                  <td>₱<?= number_format($appt['price'], 2) ?></td>
                  <td><?= statusBadge($appt['status']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
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
              <option value="">-- Choose a dentist --</option>
              <?php foreach ($dentists as $d): ?>
                <option value="<?= $d['id'] ?>">Dr. <?= htmlspecialchars($d['first_name'].' '.$d['last_name']) ?> – <?= htmlspecialchars($d['specialization']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Select Service <span style="color:red">*</span></label>
          <div class="input-wrap no-icon">
            <select name="service_id" required id="serviceSelect" onchange="updatePrice(this)">
              <option value="">-- Choose a service --</option>
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
                <option value="">-- Time --</option>
                <?php
                  $times = ['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30'];
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

<script>
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', function(e) { if(e.target===this) this.classList.remove('open'); });
});
function updatePrice(sel) {
  const opt = sel.options[sel.selectedIndex];
  const info = document.getElementById('serviceInfo');
  if (opt.value) {
    document.getElementById('servicePrice').textContent = '₱' + parseFloat(opt.dataset.price).toLocaleString('en',{minimumFractionDigits:2});
    document.getElementById('serviceDur').textContent = opt.dataset.dur + ' minutes';
    info.style.display = 'block';
  } else { info.style.display = 'none'; }
}
</script>
</body>
</html>