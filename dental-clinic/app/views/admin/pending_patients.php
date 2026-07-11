<?php $currentPage = 'pending_patients'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pending Approvals – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>

  <div class="main-content">
    <div class="topbar">
      <div>
        <div class="topbar-title">Pending Patient Approvals</div>
        <div class="text-sm text-gray"><?= date('l, F j, Y') ?></div>
      </div>
    </div>

    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; showFlash(); ?>

      <div class="card">
        <div class="card-header">
          <div class="card-title">Patients Awaiting Approval</div>
        </div>

        <div class="table-wrap">
          <?php if (empty($patients)): ?>
            <div class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <h3>No pending patients</h3>
              <p>All patient accounts have been reviewed.</p>
            </div>
          <?php else: ?>
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Registered</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($patients as $p): ?>
                <tr>
                  <td>
                    <div class="font-bold"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></div>
                  </td>
                  <td><?= htmlspecialchars($p['email']) ?></td>
                  <td><?= htmlspecialchars($p['phone'] ?? '—') ?></td>
                  <td><?= formatDate($p['created_at']) ?></td>
                  <td style="display:flex; gap:8px;">
                    <a href="index.php?page=update_patient_status&id=<?= $p['id'] ?>&action=approved"
                       class="btn btn-success btn-sm"
                       onclick="return confirm('Approve this patient?')">Approve</a>
                    <a href="index.php?page=update_patient_status&id=<?= $p['id'] ?>&action=rejected"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Reject this patient?')">Reject</a>
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
</body>
</html>