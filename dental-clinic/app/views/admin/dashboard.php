<?php $currentPage = 'admin_dashboard'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>

  <div class="main-content">
    <div class="topbar">
      <div>
        <div class="topbar-title"><?= isAdmin() ? 'Admin Dashboard' : 'Dashboard' ?></div>
        <div class="text-sm text-gray"><?= date('l, F j, Y') ?></div>
      </div>
    </div>

    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; showFlash(); ?>

      <!-- Stats -->
      <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
        <div class="stat-card">
          <div class="stat-icon yellow">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div><div class="stat-value"><?= $stats['pending_count'] ?? 0 ?></div><div class="stat-label">Pending</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div><div class="stat-value"><?= $stats['confirmed_count'] ?? 0 ?></div><div class="stat-label">Confirmed</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          </div>
          <div><div class="stat-value"><?= $stats['completed_count'] ?? 0 ?></div><div class="stat-label">Completed</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon teal">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          <div><div class="stat-value"><?= $stats['today_count'] ?? 0 ?></div><div class="stat-label">Today</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          </div>
          <div><div class="stat-value"><?= $stats['total_patients'] ?? 0 ?></div><div class="stat-label">Total Patients</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon teal">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          </div>
          <div><div class="stat-value"><?= $stats['total_appointments'] ?? 0 ?></div><div class="stat-label">All Appointments</div></div>
        </div>
      </div>

      <!-- Today's Appointments -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">Today's Appointments</div>
          <a href="?page=admin_appointments" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <div class="table-wrap">
          <?php if (empty($today_appointments)): ?>
            <div class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <h3>No appointments today</h3>
              <p>Nothing scheduled for today</p>
            </div>
          <?php else: ?>
            <table>
              <thead>
                <tr>
                  <th>Patient</th>
                  <th>Time</th>
                  <th>Service</th>
                  <th>Dentist</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($today_appointments as $appt): ?>
                <tr>
                  <td>
                    <div class="font-bold"><?= htmlspecialchars($appt['patient_name']) ?></div>
                    <div class="text-sm text-gray"><?= htmlspecialchars($appt['patient_phone'] ?? '') ?></div>
                  </td>
                  <td><?= formatTime($appt['appointment_time']) ?></td>
                  <td><?= htmlspecialchars($appt['service_name']) ?></td>
                  <td>Dr. <?= htmlspecialchars($appt['dentist_name']) ?></td>
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
</body>
</html>