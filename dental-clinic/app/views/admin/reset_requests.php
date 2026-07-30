<?php $currentPage = 'admin_reset_requests'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Requests – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>

  <div class="main-content">
    <div class="topbar">
      <div>
        <div class="topbar-title">Password Reset Requests</div>
        <div class="text-sm text-gray"><?= date('l, F j, Y') ?></div>
      </div>
    </div>

    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; showFlash(); ?>

      <div class="card">
        <div class="card-header">
          <div class="card-title">Pending Reset Requests</div>
        </div>
        <div class="table-wrap">
          <?php if (empty($requests)): ?>
            <div class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
              <h3>No pending reset requests</h3>
              <p>All password reset requests have been handled</p>
            </div>
          <?php else: ?>
            <table>
              <thead>
                <tr>
                  <th>Patient Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Requested At</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($requests as $r): ?>
                <tr>
                  <td>
                    <div class="font-bold"><?= htmlspecialchars(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?></div>
                  </td>
                  <td><?= htmlspecialchars($r['email']) ?></td>
                  <td><?= htmlspecialchars($r['phone'] ?? '-') ?></td>
                  <td class="text-sm text-gray"><?= formatDate($r['created_at']) ?></td>
                  <td>
                    <a href="?page=admin_approve_reset&id=<?= $r['id'] ?>"
                       onclick="return confirm('Approve password reset for <?= htmlspecialchars($r['first_name']) ?>?\n\nA temporary password will be generated and shown to you once.')"
                       class="btn btn-primary btn-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                      Approve &amp; Generate Password
                    </a>
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