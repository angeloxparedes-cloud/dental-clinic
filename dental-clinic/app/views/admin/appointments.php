<?php $currentPage = 'admin_appointments'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Appointments – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>

  <div class="main-content">
    <div class="topbar">
      <div class="topbar-title">All Appointments</div>
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
          <a href="?page=admin_appointments<?= $val ? '&status='.$val : '' ?>" class="filter-btn <?= $current === $val ? 'active' : '' ?>"><?= $label ?></a>
        <?php endforeach; ?>
      </div>

      <div class="card">
        <div class="card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
          <div class="card-title">
            <?= isset($filters[$current]) ? $filters[$current] : 'All' ?> Appointments
            <span style="font-size:0.85rem;font-weight:400;color:var(--gray);margin-left:8px;">(<span id="appt-count"><?= count($appointments) ?></span>)</span>
          </div>

          <!-- Search Bar -->
          <div style="display:flex; align-items:center; gap:8px; background:#f4f6f8; border:1px solid #e0e6ea; border-radius:8px; padding:8px 14px; min-width:240px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#5a7080" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input
              type="text"
              id="searchInput"
              placeholder="Search patient name..."
              oninput="filterTable()"
              style="border:none; background:transparent; outline:none; font-size:13px; color:#1a2e3b; width:100%; font-family:inherit;"
            >
          </div>
        </div>

        <div class="table-wrap">
          <?php if (empty($appointments)): ?>
            <div class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              <h3>No Appointments Found</h3>
              <p>No appointments match the selected filter</p>
            </div>
          <?php else: ?>
            <table id="apptTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Patient</th>
                  <th>Dentist</th>
                  <th>Service</th>
                  <th>Date & Time</th>
                  <th>Price</th>
                  <th>Notes</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="apptBody">
                <?php foreach ($appointments as $i => $appt): ?>
                <tr>
                  <td class="text-gray text-sm"><?= $i + 1 ?></td>
                  <td>
                    <div class="font-bold"><?= htmlspecialchars($appt['patient_name']) ?></div>
                    <div class="text-sm text-gray"><?= htmlspecialchars($appt['patient_email']) ?></div>
                  </td>
                  <td>Dr. <?= htmlspecialchars($appt['dentist_name']) ?></td>
                  <td><?= htmlspecialchars($appt['service_name']) ?></td>
                  <td>
                    <div><?= formatDate($appt['appointment_date']) ?></div>
                    <div class="text-sm text-gray"><?= formatTime($appt['appointment_time']) ?></div>
                  </td>
                  <td>₱<?= number_format($appt['price'], 2) ?></td>
                  <td>
                    <?php if (!empty($appt['notes'])): ?>
                      <span title="<?= htmlspecialchars($appt['notes']) ?>"
                        style="display:inline-block;max-width:150px;white-space:nowrap;overflow:hidden;
                               text-overflow:ellipsis;font-size:12px;color:#5a7080;cursor:help;">
                        <?= htmlspecialchars($appt['notes']) ?>
                      </span>
                    <?php else: ?>
                      <span style="color:#ccc;font-size:12px;">—</span>
                    <?php endif; ?>
                  </td>
                  <td><?= statusBadge($appt['status']) ?></td>
                  <td>
                    <button class="btn btn-ghost btn-sm" onclick="openStatusModal(<?= $appt['id'] ?>, '<?= $appt['status'] ?>')">Update</button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

            <!-- No results message -->
            <div id="noResults" style="display:none; text-align:center; padding:2.5rem 1rem; color:#5a7080;">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="margin:0 auto 12px; display:block; opacity:0.4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
              <p style="font-size:14px;">No patients found matching your search.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Update Status Modal -->
<div class="modal-overlay" id="statusModal">
  <div class="modal" style="max-width:380px;">
    <div class="modal-header">
      <div class="modal-title">Update Status</div>
      <button class="modal-close" onclick="closeModal('statusModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="?page=admin_update_status">
      <div class="modal-body">
        <input type="hidden" name="id" id="statusApptId">
        <div class="form-group">
          <label>New Status</label>
          <div class="input-wrap no-icon">
            <select name="status" id="statusSelect">
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('statusModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script>
function filterTable() {
  const query = document.getElementById('searchInput').value.toLowerCase().trim();
  const rows = document.querySelectorAll('#apptBody tr');
  let visibleCount = 0;

  rows.forEach(row => {
    const patientName = row.querySelector('td:nth-child(2)')?.innerText.toLowerCase() || '';
    const match = patientName.includes(query);
    row.style.display = match ? '' : 'none';
    if (match) visibleCount++;
  });

  document.getElementById('appt-count').textContent = visibleCount;
  document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
}

function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function openStatusModal(id, status) {
  document.getElementById('statusApptId').value = id;
  document.getElementById('statusSelect').value = status;
  openModal('statusModal');
}
document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('open'); });
});
</script>
</body>
</html>