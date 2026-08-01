<?php $currentPage = 'admin_reports'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reports – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
  /* Print-only header, hidden on screen */
  .print-header { display: none; }

  @media print {
    /* Hide everything that shouldn't be on paper */
    .sidebar, .topbar, .btn-print, .app-layout > .sidebar { display: none !important; }

    body { background: #fff !important; }
    .main-content { margin: 0 !important; width: 100% !important; }
    .page-content { padding: 0 !important; }

    .print-header {
      display: block;
      text-align: center;
      margin-bottom: 20px;
      border-bottom: 2px solid #1D9E75;
      padding-bottom: 12px;
    }
    .print-header h1 { margin: 0; font-size: 22px; color: #0F6E56; }
    .print-header p { margin: 4px 0 0; font-size: 12px; color: #555; }

    /* Avoid cutting a card in half across a page break */
    .stat-card, .card { break-inside: avoid; }

    /* Stack the two-column chart grids into one column for print */
    div[style*="grid-template-columns:1fr 1fr"] {
      grid-template-columns: 1fr !important;
    }

    canvas { max-width: 100% !important; }
  }
</style>
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-title">Reports & Analytics</div>
      <button type="button" class="btn-print" onclick="window.print()" style="display:flex;align-items:center;gap:8px;padding:8px 16px;background:#1D9E75;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6v-8z"/></svg>
        Print Report
      </button>
    </div>
    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; ?>
      <?php
        $db = getDB();

        // Appointments by status
        $r1 = $db->query("SELECT status, COUNT(*) as cnt FROM appointments GROUP BY status");
        $byStatus = $r1->fetch_all(MYSQLI_ASSOC);

        // Appointments per month (last 6 months)
        $r2 = $db->query("
          SELECT DATE_FORMAT(appointment_date,'%b %Y') as month,
                 DATE_FORMAT(appointment_date,'%Y-%m') as sort_key,
                 COUNT(*) as cnt
          FROM appointments
          WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
          GROUP BY sort_key, month
          ORDER BY sort_key ASC
        ");
        $byMonth = $r2->fetch_all(MYSQLI_ASSOC);

        // Top services
        $r3 = $db->query("
          SELECT s.name, COUNT(*) as cnt
          FROM appointments a
          JOIN services s ON a.service_id = s.id
          GROUP BY s.name ORDER BY cnt DESC LIMIT 5
        ");
        $topServices = $r3->fetch_all(MYSQLI_ASSOC);

        // Revenue per month
        $r4 = $db->query("
          SELECT DATE_FORMAT(a.appointment_date,'%b %Y') as month,
                 DATE_FORMAT(a.appointment_date,'%Y-%m') as sort_key,
                 SUM(s.price) as revenue
          FROM appointments a
          JOIN services s ON a.service_id = s.id
          WHERE a.status = 'completed'
            AND a.appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
          GROUP BY sort_key, month ORDER BY sort_key ASC
        ");
        $revenueByMonth = $r4->fetch_all(MYSQLI_ASSOC);

        // Summary stats
        $r5 = $db->query("SELECT COUNT(*) as total FROM appointments");
        $totalAppts = $r5->fetch_assoc()['total'];
        $r6 = $db->query("SELECT COALESCE(SUM(s.price),0) as rev FROM appointments a JOIN services s ON a.service_id=s.id WHERE a.status='completed'");
        $totalRevenue = $r6->fetch_assoc()['rev'];
        $r7 = $db->query("SELECT COUNT(*) as total FROM users WHERE role='patient'");
        $totalPatients = $r7->fetch_assoc()['total'];
        $r8 = $db->query("SELECT COUNT(*) as total FROM appointments WHERE status='completed'");
        $totalCompleted = $r8->fetch_assoc()['total'];
      ?>

      <!-- Shows only when printing -->
      <div class="print-header">
        <h1>Auza Dental Clinic — Reports & Analytics</h1>
        <p>Generated on <?= date('F j, Y g:i A') ?></p>
      </div>

      <!-- Summary Cards -->
      <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);">
        <div class="stat-card">
          <div class="stat-icon blue"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
          <div><div class="stat-value"><?= $totalAppts ?></div><div class="stat-label">Total Appointments</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
          <div><div class="stat-value"><?= $totalCompleted ?></div><div class="stat-label">Completed</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
          <div><div class="stat-value"><?= $totalPatients ?></div><div class="stat-label">Total Patients</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon teal"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
          <div><div class="stat-value">₱<?= number_format($totalRevenue, 0) ?></div><div class="stat-label">Total Revenue</div></div>
        </div>
      </div>

      <!-- Charts Row -->
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
        <!-- Appointments per month -->
        <div class="card" style="margin-bottom:0;">
          <div class="card-header"><div class="card-title">Appointments (Last 6 Months)</div></div>
          <div class="card-body"><canvas id="chartMonthly" height="200"></canvas></div>
        </div>
        <!-- Status breakdown -->
        <div class="card" style="margin-bottom:0;">
          <div class="card-header"><div class="card-title">Appointments by Status</div></div>
          <div class="card-body"><canvas id="chartStatus" height="200"></canvas></div>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
        <!-- Revenue per month -->
        <div class="card" style="margin-bottom:0;">
          <div class="card-header"><div class="card-title">Revenue (Last 6 Months)</div></div>
          <div class="card-body"><canvas id="chartRevenue" height="200"></canvas></div>
        </div>
        <!-- Top services -->
        <div class="card" style="margin-bottom:0;">
          <div class="card-header"><div class="card-title">Top Services</div></div>
          <div class="card-body"><canvas id="chartServices" height="200"></canvas></div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
const monthLabels   = <?= json_encode(array_column($byMonth, 'month')) ?>;
const monthData     = <?= json_encode(array_map('intval', array_column($byMonth, 'cnt'))) ?>;
const statusLabels  = <?= json_encode(array_column($byStatus, 'status')) ?>;
const statusData    = <?= json_encode(array_map('intval', array_column($byStatus, 'cnt'))) ?>;
const serviceLabels = <?= json_encode(array_column($topServices, 'name')) ?>;
const serviceData   = <?= json_encode(array_map('intval', array_column($topServices, 'cnt'))) ?>;
const revLabels     = <?= json_encode(array_column($revenueByMonth, 'month')) ?>;
const revData       = <?= json_encode(array_map('floatval', array_column($revenueByMonth, 'revenue'))) ?>;

new Chart(document.getElementById('chartMonthly'), {
  type: 'bar',
  data: { labels: monthLabels, datasets: [{ label: 'Appointments', data: monthData, backgroundColor: '#1D9E7588', borderColor: '#1D9E75', borderWidth: 1, borderRadius: 6 }] },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});

new Chart(document.getElementById('chartStatus'), {
  type: 'doughnut',
  data: { labels: statusLabels, datasets: [{ data: statusData, backgroundColor: ['#F59E0B','#3B82F6','#1D9E75','#FC8181'], borderWidth: 2 }] },
  options: { plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('chartRevenue'), {
  type: 'line',
  data: { labels: revLabels, datasets: [{ label: 'Revenue (₱)', data: revData, borderColor: '#0F6E56', backgroundColor: '#1D9E7522', fill: true, tension: 0.4, pointRadius: 4 }] },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('chartServices'), {
  type: 'bar',
  data: { labels: serviceLabels, datasets: [{ label: 'Bookings', data: serviceData, backgroundColor: ['#1D9E75','#3B82F6','#F59E0B','#FC8181','#8B5CF6'], borderRadius: 6 }] },
  options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
</body>
</html>