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
              <tr class="dentist-row" onclick="openDentistModal(<?= $d['id'] ?>)" style="cursor:pointer;">
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
                <td onclick="event.stopPropagation();">
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

<!-- Dentist Details Modal -->
<div class="modal-overlay" id="dentistDetailsModal">
  <div class="modal" style="max-width:560px;">
    <div class="modal-header">
      <div class="modal-title" id="ddName">Dentist Details</div>
      <button class="modal-close" onclick="closeModal('dentistDetailsModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <div class="modal-body">
      <div id="ddLoading" style="text-align:center;padding:40px 0;color:var(--gray);">
        Loading dentist details...
      </div>

      <div id="ddError" style="display:none;color:var(--danger,#e53e3e);text-align:center;padding:40px 0;"></div>

      <div id="ddContent" style="display:none;">
        <!-- Header -->
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
          <div class="user-avatar" id="ddAvatar" style="width:52px;height:52px;font-size:1.1rem;background:var(--accent);">--</div>
          <div>
            <div id="ddNameDisplay" style="font-size:1.15rem;font-weight:700;"></div>
            <div id="ddSpec" style="font-size:0.85rem;color:var(--gray);"></div>
          </div>
          <span id="ddStatusBadge" class="badge" style="margin-left:auto;"></span>
        </div>

        <!-- Contact info -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;padding:14px;background:#f8f9fb;border-radius:10px;">
          <div>
            <div style="font-size:0.75rem;color:var(--gray);text-transform:uppercase;letter-spacing:0.03em;">Email</div>
            <div id="ddEmail" style="font-size:0.9rem;font-weight:600;word-break:break-all;"></div>
          </div>
          <div>
            <div style="font-size:0.75rem;color:var(--gray);text-transform:uppercase;letter-spacing:0.03em;">Phone</div>
            <div id="ddPhone" style="font-size:0.9rem;font-weight:600;"></div>
          </div>
        </div>

        <!-- Stats -->
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:24px;">
          <div style="text-align:center;padding:10px 2px;border-radius:10px;background:#f2effc;">
            <div id="ddPatients" style="font-size:1.2rem;font-weight:700;color:#8B5CF6;"></div>
            <div style="font-size:0.68rem;color:var(--gray);">Patients</div>
          </div>
          <div style="text-align:center;padding:10px 2px;border-radius:10px;background:#eef4ff;">
            <div id="ddTotal" style="font-size:1.2rem;font-weight:700;color:#3B82F6;"></div>
            <div style="font-size:0.68rem;color:var(--gray);">Total</div>
          </div>
          <div style="text-align:center;padding:10px 2px;border-radius:10px;background:#eafbf3;">
            <div id="ddCompleted" style="font-size:1.2rem;font-weight:700;color:#1D9E75;"></div>
            <div style="font-size:0.68rem;color:var(--gray);">Completed</div>
          </div>
          <div style="text-align:center;padding:10px 2px;border-radius:10px;background:#fff8ea;">
            <div id="ddUpcoming" style="font-size:1.2rem;font-weight:700;color:#F59E0B;"></div>
            <div style="font-size:0.68rem;color:var(--gray);">Upcoming</div>
          </div>
          <div style="text-align:center;padding:10px 2px;border-radius:10px;background:#fdecec;">
            <div id="ddCancelled" style="font-size:1.2rem;font-weight:700;color:#FC8181;"></div>
            <div style="font-size:0.68rem;color:var(--gray);">Cancelled</div>
          </div>
        </div>

        <!-- Appointment history -->
        <div style="font-weight:700;font-size:0.95rem;margin-bottom:10px;">Recent Appointments</div>
        <div id="ddHistoryWrap">
          <table style="width:100%;font-size:0.85rem;border-collapse:collapse;">
            <thead>
              <tr style="text-align:left;color:var(--gray);border-bottom:1px solid var(--border);">
                <th style="padding:6px 4px;">Date</th>
                <th style="padding:6px 4px;">Patient</th>
                <th style="padding:6px 4px;">Service</th>
                <th style="padding:6px 4px;">Status</th>
              </tr>
            </thead>
            <tbody id="ddHistoryBody"></tbody>
          </table>
          <div id="ddNoHistory" style="display:none;color:var(--gray);font-size:0.85rem;padding:12px 0;text-align:center;">
            No appointments yet.
          </div>
        </div>
      </div>
    </div>
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

const dentistStatusColors = {
  pending:   '#F59E0B',
  confirmed: '#3B82F6',
  completed: '#1D9E75',
  cancelled: '#FC8181'
};

function openDentistModal(id) {
  const loading = document.getElementById('ddLoading');
  const content = document.getElementById('ddContent');
  const errorEl = document.getElementById('ddError');

  document.getElementById('ddName').textContent = 'Dentist Details';
  loading.style.display = 'block';
  content.style.display = 'none';
  errorEl.style.display = 'none';
  openModal('dentistDetailsModal');

  fetch('?page=admin_dentist_details&id=' + id)
    .then(res => res.json())
    .then(data => {
      if (data.error) throw new Error(data.error);

      loading.style.display = 'none';
      content.style.display = 'block';

      document.getElementById('ddName').textContent = data.dentist.name;
      document.getElementById('ddAvatar').textContent = data.dentist.initials;
      document.getElementById('ddNameDisplay').textContent = data.dentist.name;
      document.getElementById('ddSpec').textContent = data.dentist.specialization;
      document.getElementById('ddEmail').textContent = data.dentist.email;
      document.getElementById('ddPhone').textContent = data.dentist.phone;

      const badge = document.getElementById('ddStatusBadge');
      badge.textContent = data.dentist.is_active ? 'Active' : 'Inactive';
      badge.className = 'badge ' + (data.dentist.is_active ? 'badge-confirmed' : 'badge-cancelled');

      document.getElementById('ddPatients').textContent = data.stats.total_patients;
      document.getElementById('ddTotal').textContent = data.stats.total;
      document.getElementById('ddCompleted').textContent = data.stats.completed;
      document.getElementById('ddUpcoming').textContent = data.stats.upcoming;
      document.getElementById('ddCancelled').textContent = data.stats.cancelled;

      const body = document.getElementById('ddHistoryBody');
      const noHistory = document.getElementById('ddNoHistory');
      body.innerHTML = '';

      if (data.history.length === 0) {
        noHistory.style.display = 'block';
      } else {
        noHistory.style.display = 'none';
        data.history.forEach(h => {
          const tr = document.createElement('tr');
          tr.style.borderBottom = '1px solid #f1f1f1';
          const color = dentistStatusColors[h.status] || '#999';
          tr.innerHTML = `
            <td style="padding:8px 4px;">${h.date}<br><span style="color:var(--gray);font-size:0.78rem;">${h.time}</span></td>
            <td style="padding:8px 4px;">${h.patient}</td>
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
      errorEl.textContent = 'Could not load dentist details. ' + err.message;
    });
}
</script>
</body>
</html>