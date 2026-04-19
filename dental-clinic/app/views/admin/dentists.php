<?php $currentPage = 'admin_dentists'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dentists – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-title">Manage Dentists</div>
      <button class="btn btn-primary btn-sm" onclick="openModal('addModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Dentist
      </button>
    </div>
    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; showFlash(); ?>
      <div class="card">
        <div class="card-header">
          <div class="card-title">All Dentists <span style="font-size:.85rem;font-weight:400;color:var(--gray);margin-left:8px;">(<?= count($dentists) ?>)</span></div>
        </div>
        <div class="table-wrap">
          <?php if (empty($dentists)): ?>
            <div class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <h3>No Dentists Found</h3>
              <p>Add your first dentist to get started</p>
            </div>
          <?php else: ?>
          <table>
            <thead>
              <tr><th>#</th><th>Name</th><th>Specialization</th><th>Email</th><th>Phone</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php foreach ($dentists as $i => $d): ?>
              <tr>
                <td class="text-gray text-sm"><?= $i+1 ?></td>
                <td>
                  <div class="flex items-center gap-2">
                    <div class="user-avatar" style="width:36px;height:36px;font-size:.8rem;background:var(--accent);">
                      <?= strtoupper(substr($d['first_name'],0,1).substr($d['last_name'],0,1)) ?>
                    </div>
                    <div class="font-bold">Dr. <?= htmlspecialchars($d['first_name'].' '.$d['last_name']) ?></div>
                  </div>
                </td>
                <td><?= htmlspecialchars($d['specialization'] ?? '—') ?></td>
                <td><?= htmlspecialchars($d['email'] ?? '—') ?></td>
                <td><?= htmlspecialchars($d['phone'] ?? '—') ?></td>
                <td>
                  <span class="badge <?= $d['is_active'] ? 'badge-confirmed' : 'badge-cancelled' ?>">
                    <?= $d['is_active'] ? 'Active' : 'Inactive' ?>
                  </span>
                </td>
                <td>
                  <div class="flex gap-2">
                    <button class="btn btn-ghost btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($d)) ?>)">Edit</button>
                    <?php if ($d['is_active']): ?>
                      <a href="?page=admin_delete_dentist&id=<?= $d['id'] ?>" class="btn btn-danger btn-sm"
                         onclick="return confirm('Deactivate this dentist?')">Deactivate</a>
                    <?php endif; ?>
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

<!-- Add Dentist Modal -->
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Add New Dentist</div>
      <button class="modal-close" onclick="closeModal('addModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="?page=admin_add_dentist">
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group">
            <label>First Name *</label>
            <div class="input-wrap no-icon"><input type="text" name="first_name" required placeholder="Maria"></div>
          </div>
          <div class="form-group">
            <label>Last Name *</label>
            <div class="input-wrap no-icon"><input type="text" name="last_name" required placeholder="Santos"></div>
          </div>
        </div>
        <div class="form-group">
          <label>Specialization</label>
          <div class="input-wrap no-icon"><input type="text" name="specialization" placeholder="e.g. General Dentistry, Orthodontics"></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Email</label>
            <div class="input-wrap no-icon"><input type="email" name="email" placeholder="doctor@clinic.com"></div>
          </div>
          <div class="form-group">
            <label>Phone</label>
            <div class="input-wrap no-icon"><input type="text" name="phone" placeholder="09XXXXXXXXX"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Add Dentist</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Dentist Modal -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit Dentist</div>
      <button class="modal-close" onclick="closeModal('editModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="?page=admin_edit_dentist">
      <div class="modal-body">
        <input type="hidden" name="id" id="editId">
        <div class="form-row">
          <div class="form-group">
            <label>First Name *</label>
            <div class="input-wrap no-icon"><input type="text" name="first_name" id="editFirst" required></div>
          </div>
          <div class="form-group">
            <label>Last Name *</label>
            <div class="input-wrap no-icon"><input type="text" name="last_name" id="editLast" required></div>
          </div>
        </div>
        <div class="form-group">
          <label>Specialization</label>
          <div class="input-wrap no-icon"><input type="text" name="specialization" id="editSpec"></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Email</label>
            <div class="input-wrap no-icon"><input type="email" name="email" id="editEmail"></div>
          </div>
          <div class="form-group">
            <label>Phone</label>
            <div class="input-wrap no-icon"><input type="text" name="phone" id="editPhone"></div>
          </div>
        </div>
        <div class="form-group">
          <label>Status</label>
          <div class="input-wrap no-icon">
            <select name="is_active" id="editActive">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function openEditModal(d) {
  document.getElementById('editId').value    = d.id;
  document.getElementById('editFirst').value = d.first_name;
  document.getElementById('editLast').value  = d.last_name;
  document.getElementById('editSpec').value  = d.specialization || '';
  document.getElementById('editEmail').value = d.email || '';
  document.getElementById('editPhone').value = d.phone || '';
  document.getElementById('editActive').value = d.is_active;
  openModal('editModal');
}
document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', function(e) { if(e.target===this) this.classList.remove('open'); });
});
</script>
</body>
</html>
