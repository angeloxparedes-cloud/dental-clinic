<?php $currentPage = 'admin_patients'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patients – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require_once __DIR__ . '/../shared/sidebar.php'; ?>

  <div class="main-content">
    <div class="topbar">
      <div class="topbar-title">Patients</div>
      <div class="topbar-actions">
        <input type="text" id="searchInput" placeholder="Search patients..." onkeyup="searchTable()"
          style="padding:8px 14px;border:1.5px solid var(--border);border-radius:8px;font-family:var(--font-body);font-size:0.88rem;outline:none;width:220px;">
      </div>
    </div>

    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; showFlash(); ?>

      <div class="card">
        <div class="card-header">
          <div class="card-title">
            All Patients
            <span style="font-size:0.85rem;font-weight:400;color:var(--gray);margin-left:8px;">(<?= count($patients) ?>)</span>
          </div>
        </div>
        <div class="table-wrap">
          <?php if (empty($patients)): ?>
            <div class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <h3>No Patients Yet</h3>
              <p>Patients will appear here once they register</p>
            </div>
          <?php else: ?>
            <table id="patientsTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Registered</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($patients as $i => $p): ?>
                <tr class="patient-row" onclick="openPatientModal(<?= $p['id'] ?>)" style="cursor:pointer;">
                  <td class="text-gray text-sm"><?= $i + 1 ?></td>
                  <td>
                    <div class="flex items-center gap-2">
                      <div class="user-avatar" style="width:32px;height:32px;font-size:0.75rem;background:var(--primary);">
                        <?= strtoupper(substr($p['first_name'], 0, 1) . substr($p['last_name'], 0, 1)) ?>
                      </div>
                      <div>
                        <div class="font-bold"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($p['email']) ?></td>
                  <td><?= htmlspecialchars($p['phone'] ?? '—') ?></td>
                  <td class="text-sm text-gray"><?= formatDate($p['created_at']) ?></td>
                  <td onclick="event.stopPropagation();">
                    <button
                      onclick="confirmDelete(<?= $p['id'] ?>, '<?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?>')"
                      style="background:var(--danger,#e53e3e);color:#fff;border:none;padding:6px 14px;border-radius:6px;cursor:pointer;font-size:0.82rem;font-family:var(--font-body);">
                      Delete
                    </button>
                  </td>
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:12px;padding:32px;max-width:400px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
    <h3 style="margin:0 0 10px;font-size:1.1rem;">Delete Patient</h3>
    <p style="color:var(--gray);margin:0 0 24px;font-size:0.93rem;">
      Are you sure you want to permanently delete <strong id="patientName"></strong>?
      This will also remove all their appointments and payments. This cannot be undone.
    </p>
    <div style="display:flex;gap:10px;justify-content:flex-end;">
      <button onclick="closeModal()"
        style="padding:8px 20px;border-radius:7px;border:1.5px solid var(--border);background:#fff;cursor:pointer;font-family:var(--font-body);">
        Cancel
      </button>
      <a id="confirmDeleteBtn" href="#"
        style="padding:8px 20px;border-radius:7px;background:var(--danger,#e53e3e);color:#fff;text-decoration:none;font-family:var(--font-body);font-size:0.93rem;">
        Yes, Delete
      </a>
    </div>
  </div>
</div>

<!-- Patient Details Modal -->
<div id="patientDetailsModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:12px;padding:32px;max-width:560px;width:92%;max-height:85vh;overflow-y:auto;box-shadow:0 8px 32px rgba(0,0,0,0.18);position:relative;">

    <button onclick="closePatientModal()" aria-label="Close"
      style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:22px;line-height:1;cursor:pointer;color:var(--gray);">
      &times;
    </button>

    <div id="patientDetailsLoading" style="text-align:center;padding:40px 0;color:var(--gray);">
      Loading patient details...
    </div>

    <div id="patientDetailsContent" style="display:none;">
      <!-- Header -->
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
        <div class="user-avatar" id="pdAvatar" style="width:52px;height:52px;font-size:1.1rem;background:var(--primary);">--</div>
        <div>
          <div id="pdName" style="font-size:1.15rem;font-weight:700;"></div>
          <div id="pdSince" style="font-size:0.82rem;color:var(--gray);"></div>
        </div>
      </div>

      <!-- Contact info -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;padding:14px;background:#f8f9fb;border-radius:10px;">
        <div>
          <div style="font-size:0.75rem;color:var(--gray);text-transform:uppercase;letter-spacing:0.03em;">Email</div>
          <div id="pdEmail" style="font-size:0.9rem;font-weight:600;word-break:break-all;"></div>
        </div>
        <div>
          <div style="font-size:0.75rem;color:var(--gray);text-transform:uppercase;letter-spacing:0.03em;">Phone</div>
          <div id="pdPhone" style="font-size:0.9rem;font-weight:600;"></div>
        </div>
      </div>

      <!-- Stats -->
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:24px;">
        <div style="text-align:center;padding:12px 4px;border-radius:10px;background:#eef4ff;">
          <div id="pdTotal" style="font-size:1.3rem;font-weight:700;color:#3B82F6;"></div>
          <div style="font-size:0.72rem;color:var(--gray);">Total</div>
        </div>
        <div style="text-align:center;padding:12px 4px;border-radius:10px;background:#eafbf3;">
          <div id="pdCompleted" style="font-size:1.3rem;font-weight:700;color:#1D9E75;"></div>
          <div style="font-size:0.72rem;color:var(--gray);">Completed</div>
        </div>
        <div style="text-align:center;padding:12px 4px;border-radius:10px;background:#fff8ea;">
          <div id="pdUpcoming" style="font-size:1.3rem;font-weight:700;color:#F59E0B;"></div>
          <div style="font-size:0.72rem;color:var(--gray);">Upcoming</div>
        </div>
        <div style="text-align:center;padding:12px 4px;border-radius:10px;background:#fdecec;">
          <div id="pdCancelled" style="font-size:1.3rem;font-weight:700;color:#FC8181;"></div>
          <div style="font-size:0.72rem;color:var(--gray);">Cancelled</div>
        </div>
      </div>

      <div style="margin-bottom:20px;font-size:0.9rem;">
        <span style="color:var(--gray);">Total spent:</span>
        <strong id="pdSpent" style="margin-left:6px;"></strong>
      </div>

      <!-- Appointment history -->
      <div style="font-weight:700;font-size:0.95rem;margin-bottom:10px;">Recent Appointments</div>
      <div id="pdHistoryWrap">
        <table style="width:100%;font-size:0.85rem;border-collapse:collapse;">
          <thead>
            <tr style="text-align:left;color:var(--gray);border-bottom:1px solid var(--border);">
              <th style="padding:6px 4px;">Date</th>
              <th style="padding:6px 4px;">Dentist</th>
              <th style="padding:6px 4px;">Service</th>
              <th style="padding:6px 4px;">Status</th>
            </tr>
          </thead>
          <tbody id="pdHistoryBody"></tbody>
        </table>
        <div id="pdNoHistory" style="display:none;color:var(--gray);font-size:0.85rem;padding:12px 0;text-align:center;">
          No appointments yet.
        </div>
      </div>
    </div>

    <div id="patientDetailsError" style="display:none;color:var(--danger,#e53e3e);text-align:center;padding:40px 0;"></div>
  </div>
</div>

<script>
function confirmDelete(id, name) {
  document.getElementById('patientName').textContent = name;
  document.getElementById('confirmDeleteBtn').href = '?page=admin_delete_patient&id=' + id;
  const modal = document.getElementById('deleteModal');
  modal.style.display = 'flex';
}

function closeModal() {
  document.getElementById('deleteModal').style.display = 'none';
}

function searchTable() {
  const input = document.getElementById('searchInput').value.toLowerCase();
  const rows  = document.querySelectorAll('#patientsTable tbody tr');
  rows.forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(input) ? '' : 'none';
  });
}

const statusColors = {
  pending:   '#F59E0B',
  confirmed: '#3B82F6',
  completed: '#1D9E75',
  cancelled: '#FC8181'
};

function openPatientModal(id) {
  const modal   = document.getElementById('patientDetailsModal');
  const loading = document.getElementById('patientDetailsLoading');
  const content = document.getElementById('patientDetailsContent');
  const errorEl = document.getElementById('patientDetailsError');

  modal.style.display = 'flex';
  loading.style.display = 'block';
  content.style.display = 'none';
  errorEl.style.display = 'none';

  fetch('?page=admin_patient_details&id=' + id)
    .then(res => res.json())
    .then(data => {
      if (data.error) throw new Error(data.error);

      loading.style.display = 'none';
      content.style.display = 'block';

      document.getElementById('pdAvatar').textContent = data.patient.initials;
      document.getElementById('pdName').textContent = data.patient.name;
      document.getElementById('pdSince').textContent = 'Patient since ' + data.patient.created_at;
      document.getElementById('pdEmail').textContent = data.patient.email;
      document.getElementById('pdPhone').textContent = data.patient.phone;

      document.getElementById('pdTotal').textContent = data.stats.total;
      document.getElementById('pdCompleted').textContent = data.stats.completed;
      document.getElementById('pdUpcoming').textContent = data.stats.upcoming;
      document.getElementById('pdCancelled').textContent = data.stats.cancelled;
      document.getElementById('pdSpent').textContent = '₱' + Number(data.stats.total_spent).toLocaleString();

      const body = document.getElementById('pdHistoryBody');
      const noHistory = document.getElementById('pdNoHistory');
      body.innerHTML = '';

      if (data.history.length === 0) {
        noHistory.style.display = 'block';
      } else {
        noHistory.style.display = 'none';
        data.history.forEach(h => {
          const tr = document.createElement('tr');
          tr.style.borderBottom = '1px solid #f1f1f1';
          const color = statusColors[h.status] || '#999';
          tr.innerHTML = `
            <td style="padding:8px 4px;">${h.date}<br><span style="color:var(--gray);font-size:0.78rem;">${h.time}</span></td>
            <td style="padding:8px 4px;">${h.dentist}</td>
            <td style="padding:8px 4px;">${h.service}</td>
            <td style="padding:8px 4px;"><span style="color:${color};font-weight:600;text-transform:capitalize;">${h.status}</span></td>
          `;
          body.appendChild(tr);
        });
      }
    })
    .catch(err => {
      loading.style.display = 'none';
      errorEl.style.display = 'block';
      errorEl.textContent = 'Could not load patient details. ' + err.message;
    });
}

function closePatientModal() {
  document.getElementById('patientDetailsModal').style.display = 'none';
}
</script>
</body>
</html>