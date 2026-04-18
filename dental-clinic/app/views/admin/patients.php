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
                <tr>
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
                  <td>
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
</script>
</body>
</html>