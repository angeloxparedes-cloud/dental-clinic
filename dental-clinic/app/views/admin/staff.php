<?php $currentPage = 'admin_staff'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Staff – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-title">Manage Staff</div>
      <button class="btn btn-primary btn-sm" onclick="openModal('addStaffModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Staff
      </button>
    </div>
    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; showFlash(); ?>
      <div class="card">
        <div class="card-header">
          <div class="card-title">All Staff <span style="font-size:.85rem;font-weight:400;color:var(--gray);margin-left:8px;">(<?= count($staff) ?>)</span></div>
        </div>
        <div class="table-wrap">
          <?php if (empty($staff)): ?>
            <div class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              <h3>No Staff Accounts Yet</h3>
              <p>Add your first staff member to get started</p>
            </div>
          <?php else: ?>
          <table>
            <thead>
              <tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php foreach ($staff as $i => $s): ?>
              <tr>
                <td class="text-gray text-sm"><?= $i+1 ?></td>
                <td>
                  <div class="flex items-center gap-2">
                    <div class="user-avatar" style="width:36px;height:36px;font-size:.8rem;background:var(--accent);">
                      <?= strtoupper(substr($s['first_name'],0,1).substr($s['last_name'],0,1)) ?>
                    </div>
                    <div class="font-bold"><?= htmlspecialchars($s['first_name'].' '.$s['last_name']) ?></div>
                  </div>
                </td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars($s['phone'] ?: '—') ?></td>
                <td>
                  <span class="badge <?= $s['status'] === 'approved' ? 'badge-confirmed' : 'badge-cancelled' ?>">
                    <?= $s['status'] === 'approved' ? 'Active' : 'Deactivated' ?>
                  </span>
                </td>
                <td class="text-sm text-gray"><?= formatDate($s['created_at']) ?></td>
                <td>
                  <div class="flex gap-2">
                    <?php if ($s['status'] === 'approved'): ?>
                      <a href="?page=admin_staff_status&id=<?= $s['id'] ?>&action=rejected" class="btn btn-danger btn-sm"
                         onclick="return confirm('Deactivate this staff account? They will no longer be able to log in.')">Deactivate</a>
                    <?php else: ?>
                      <a href="?page=admin_staff_status&id=<?= $s['id'] ?>&action=approved" class="btn btn-primary btn-sm">Reactivate</a>
                    <?php endif; ?>
                    <a href="?page=admin_delete_staff&id=<?= $s['id'] ?>" class="btn btn-outline-danger btn-sm"
                       onclick="return confirm('Permanently delete this staff account? This cannot be undone.')">Delete</a>
                  </div>
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

<!-- Add Staff Modal -->
<div class="modal-overlay" id="addStaffModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Add New Staff</div>
      <button class="modal-close" onclick="closeModal('addStaffModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="?page=admin_add_staff">
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group">
            <label>First Name *</label>
            <div class="input-wrap no-icon"><input type="text" name="first_name" required placeholder="Juan"></div>
          </div>
          <div class="form-group">
            <label>Last Name *</label>
            <div class="input-wrap no-icon"><input type="text" name="last_name" required placeholder="Dela Cruz"></div>
          </div>
        </div>
        <div class="form-group">
          <label>Email *</label>
          <div class="input-wrap no-icon"><input type="email" name="email" required placeholder="staff@clinic.com"></div>
        </div>
        <div class="form-group">
          <label>Phone</label>
          <div class="input-wrap no-icon"><input type="text" name="phone" placeholder="09XXXXXXXXX"></div>
        </div>
        <p style="font-size:0.82rem;color:var(--gray);margin-top:4px;">
          A temporary password will be generated automatically. It will be shown once after
          the account is created — you'll need to share it with the staff member yourself.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('addStaffModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Account</button>
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
</script>
</body>
</html>