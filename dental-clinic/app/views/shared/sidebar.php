<?php require_once __DIR__ . '/helpers.php'; ?>
<aside class="sidebar">
  <div class="sidebar-header">
    <div class="logo-wrap">
      <?php renderLogo(40); ?>
      <div class="logo-text">
        <span class="brand">Auza Dental</span>
        <span class="tagline">Clinic</span>
      </div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <?php if (isAdmin()): ?>
      <div class="nav-label">Main Menu</div>
      <a href="?page=admin_dashboard" class="nav-item <?= ($currentPage??'') === 'admin_dashboard' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
      </a>
      <a href="?page=admin_appointments" class="nav-item <?= ($currentPage??'') === 'admin_appointments' ? 'active' : '' ?>">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
  Appointments
  <?php
    $db = getDB();
    $apptRes   = $db->query("SELECT COUNT(*) as cnt FROM appointments WHERE status='pending'");
    $apptCount = $apptRes->fetch_assoc()['cnt'] ?? 0;
    if ($apptCount > 0): ?>
    <span style="margin-left:auto;background:#e74c3c;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;"><?= $apptCount ?></span>
  <?php endif; ?>
</a>
      <a href="?page=admin_calendar" class="nav-item <?= ($currentPage??'') === 'admin_calendar' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Calendar
      </a>
      <a href="?page=admin_patients" class="nav-item <?= ($currentPage??'') === 'admin_patients' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Patients
      </a>

      <!-- ── PENDING APPROVALS ── -->
      <a href="?page=pending_patients" class="nav-item <?= ($currentPage??'') === 'pending_patients' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        Pending Approvals
        <?php
          $db = getDB();
          $pendingRes   = $db->query("SELECT COUNT(*) as cnt FROM users WHERE role='patient' AND status='pending'");
          $pendingCount = $pendingRes->fetch_assoc()['cnt'] ?? 0;
          if ($pendingCount > 0): ?>
          <span style="margin-left:auto;background:#e74c3c;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;"><?= $pendingCount ?></span>
        <?php endif; ?>
      </a>

      <!-- ── RESET REQUESTS ── -->
      <a href="?page=admin_reset_requests" class="nav-item <?= ($currentPage??'') === 'admin_reset_requests' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        Reset Requests
        <?php
          try {
            $db = getDB();
            $resetRes   = $db->query("SELECT COUNT(*) as cnt FROM password_resets WHERE status='pending'");
            $resetCount = $resetRes ? ($resetRes->fetch_assoc()['cnt'] ?? 0) : 0;
            if ($resetCount > 0): ?>
            <span style="margin-left:auto;background:#e74c3c;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;"><?= $resetCount ?></span>
          <?php endif;
          } catch (Exception $e) { /* table may not exist yet */ } ?>
      </a>

      <a href="?page=admin_dentists" class="nav-item <?= ($currentPage??'') === 'admin_dentists' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        Dentists
      </a>
      <a href="?page=admin_staff" class="nav-item <?= ($currentPage??'') === 'admin_staff' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Staff
      </a>
      <a href="?page=admin_payments" class="nav-item <?= ($currentPage??'') === 'admin_payments' ? 'active' : '' ?>">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
  Payments
  <?php
    $db = getDB();
    $payRes   = $db->query("SELECT COUNT(*) as cnt FROM payments WHERE status='pending'");
    $payCount = $payRes->fetch_assoc()['cnt'] ?? 0;
    if ($payCount > 0): ?>
    <span style="margin-left:auto;background:#e74c3c;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;"><?= $payCount ?></span>
  <?php endif; ?>
</a>
      <a href="?page=admin_reports" class="nav-item <?= ($currentPage??'') === 'admin_reports' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Reports
      </a>

      <div class="nav-label" style="margin-top:12px;">Account</div>
      <a href="?page=admin_notifications" class="nav-item <?= ($currentPage??'') === 'admin_notifications' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        Notifications
        <?php
          $db = getDB();
          $notifRes = $db->query("SELECT COUNT(*) as cnt FROM appointments WHERE status='pending' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
          $notifCount = $notifRes->fetch_assoc()['cnt'] ?? 0;
          if ($notifCount > 0): ?>
          <span style="margin-left:auto;background:#e74c3c;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;"><?= $notifCount ?></span>
        <?php endif; ?>
      </a>
      <a href="?page=settings" class="nav-item <?= ($currentPage??'') === 'settings' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Settings
      </a>

    <?php elseif (isStaff()): ?>
      <!--
        STAFF MENU — mirrors the admin menu above, but leaves out
        "Dentists", "Staff", "Reports", and "Reset Requests" since
        those stay owner-only. All links point at the exact same
        pages as admin (the controller methods behind them already
        accept staff via requireStaffOrAdmin()).
      -->
      <div class="nav-label">Main Menu</div>
      <a href="?page=admin_dashboard" class="nav-item <?= ($currentPage??'') === 'admin_dashboard' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
      </a>
      <a href="?page=admin_appointments" class="nav-item <?= ($currentPage??'') === 'admin_appointments' ? 'active' : '' ?>">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
  Appointments
  <?php
    $db = getDB();
    $apptRes   = $db->query("SELECT COUNT(*) as cnt FROM appointments WHERE status='pending'");
    $apptCount = $apptRes->fetch_assoc()['cnt'] ?? 0;
    if ($apptCount > 0): ?>
    <span style="margin-left:auto;background:#e74c3c;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;"><?= $apptCount ?></span>
  <?php endif; ?>
</a>
      <a href="?page=admin_calendar" class="nav-item <?= ($currentPage??'') === 'admin_calendar' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Calendar
      </a>

      <!-- ── PENDING APPROVALS ── -->
      <a href="?page=pending_patients" class="nav-item <?= ($currentPage??'') === 'pending_patients' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        Pending Approvals
        <?php
          $db = getDB();
          $pendingRes   = $db->query("SELECT COUNT(*) as cnt FROM users WHERE role='patient' AND status='pending'");
          $pendingCount = $pendingRes->fetch_assoc()['cnt'] ?? 0;
          if ($pendingCount > 0): ?>
          <span style="margin-left:auto;background:#e74c3c;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;"><?= $pendingCount ?></span>
        <?php endif; ?>
      </a>

      <a href="?page=admin_payments" class="nav-item <?= ($currentPage??'') === 'admin_payments' ? 'active' : '' ?>">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
  Payments
  <?php
    $db = getDB();
    $payRes   = $db->query("SELECT COUNT(*) as cnt FROM payments WHERE status='pending'");
    $payCount = $payRes->fetch_assoc()['cnt'] ?? 0;
    if ($payCount > 0): ?>
    <span style="margin-left:auto;background:#e74c3c;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;"><?= $payCount ?></span>
  <?php endif; ?>
</a>

      <div class="nav-label" style="margin-top:12px;">Account</div>
      <a href="?page=admin_notifications" class="nav-item <?= ($currentPage??'') === 'admin_notifications' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        Notifications
        <?php
          $db = getDB();
          $notifRes = $db->query("SELECT COUNT(*) as cnt FROM appointments WHERE status='pending' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
          $notifCount = $notifRes->fetch_assoc()['cnt'] ?? 0;
          if ($notifCount > 0): ?>
          <span style="margin-left:auto;background:#e74c3c;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;"><?= $notifCount ?></span>
        <?php endif; ?>
      </a>
      <a href="?page=settings" class="nav-item <?= ($currentPage??'') === 'settings' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Settings
      </a>

    <?php else: ?>
      <?php
        $sid = $_SESSION['user_id'] ?? 0;
        $scheduleAppts   = [];
        $sidebarDentists = [];
        if ($sid) {
          $db = getDB();
          $stmt = $db->prepare("
            SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.notes,
                   s.name as service_name, s.price,
                   CONCAT(d.first_name,' ',d.last_name) as dentist_name
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            JOIN dentists d ON a.dentist_id = d.id
            WHERE a.patient_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
          ");
          $stmt->bind_param('i', $sid);
          $stmt->execute();
          $scheduleAppts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
          $stmt->close();
          $res = $db->query("SELECT first_name, last_name, specialization, is_active FROM dentists ORDER BY is_active DESC, first_name ASC");
          $sidebarDentists = $res->fetch_all(MYSQLI_ASSOC);
        }
      ?>
      <div class="nav-label">Main Menu</div>
      <a href="?page=patient_dashboard" class="nav-item <?= ($currentPage??'') === 'patient_dashboard' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        My Dashboard
      </a>
      <a href="?page=patient_payments" class="nav-item <?= ($currentPage??'') === 'patient_payments' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        My Payments
      </a>
      <a href="?page=patient_appointments" class="nav-item <?= ($currentPage??'') === 'patient_appointments' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        My Appointments
      </a>
      <a href="?page=patient_notifications" class="nav-item <?= ($currentPage??'') === 'patient_notifications' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        Notifications
        <?php
          $db = getDB();
          $sid = $_SESSION['user_id'] ?? 0;
          $nStmt = $db->prepare("SELECT COUNT(*) as cnt FROM appointments WHERE patient_id=? AND appointment_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status IN ('pending','confirmed')");
          $nStmt->bind_param('i', $sid);
          $nStmt->execute();
          $nCount = $nStmt->get_result()->fetch_assoc()['cnt'] ?? 0;
          $nStmt->close();
          if ($nCount > 0): ?>
          <span style="margin-left:auto;background:#e74c3c;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;"><?= $nCount ?></span>
        <?php endif; ?>
      </a>
      <a href="?page=patient_profile" class="nav-item <?= ($currentPage??'') === 'patient_profile' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        My Profile
      </a>
      <a href="#" class="nav-item" onclick="openSchedulePanel(); return false;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        My Schedule
      </a>
      <div class="nav-label" style="margin-top:12px;">Account</div>
      <a href="?page=settings" class="nav-item <?= ($currentPage??'') === 'settings' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Settings
      </a>
    <?php endif; ?>
  </nav>

  <div class="sidebar-footer">
    <div class="user-info">
      <?php $avatarFile = $_SESSION['avatar'] ?? ''; ?>
      <?php if (!empty($avatarFile)): ?>
        <img src="<?= APP_URL ?>/public/uploads/avatars/<?= htmlspecialchars($avatarFile) ?>"
             style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.3);">
      <?php else: ?>
        <div class="user-avatar"><?= strtoupper(substr($_SESSION['first_name']??'U',0,1)) ?></div>
      <?php endif; ?>
      <div class="user-details">
        <div class="name"><?= htmlspecialchars(($_SESSION['first_name']??'').' '.($_SESSION['last_name']??'')) ?></div>
        <div class="role"><?= htmlspecialchars($_SESSION['role']??'') ?></div>
      </div>
    </div>
    <a href="?page=logout" class="nav-item" style="color:rgba(255,100,100,0.8)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      Logout
    </a>
  </div>
</aside>

<?php if (!isAdmin() && !isStaff()): ?>
<div id="scheduleOverlay" onclick="closeSchedulePanel()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:999;"></div>
<div id="schedulePanel" style="position:fixed;top:0;left:-420px;width:400px;height:100vh;background:#fff;z-index:1000;box-shadow:4px 0 24px rgba(0,0,0,0.15);display:flex;flex-direction:column;transition:left 0.3s ease;overflow:hidden;">
  <div style="background:#0d2b3e;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
    <div>
      <div style="font-size:16px;font-weight:600;color:#fff;">My Schedule</div>
      <div style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:2px;"><?= date('l, F j, Y') ?></div>
    </div>
    <button onclick="closeSchedulePanel()" style="background:rgba(255,255,255,0.1);border:none;border-radius:8px;width:32px;height:32px;cursor:pointer;color:#fff;font-size:20px;display:flex;align-items:center;justify-content:center;line-height:1;">&times;</button>
  </div>
  <div style="display:flex;border-bottom:1px solid #e8edf0;flex-shrink:0;">
    <button onclick="switchTab('appointments')" id="tab-appointments" style="flex:1;padding:12px;border:none;background:#fff;font-size:13px;font-weight:600;color:#1D9E75;border-bottom:2px solid #1D9E75;cursor:pointer;font-family:inherit;">My Appointments</button>
    <button onclick="switchTab('dentists')" id="tab-dentists" style="flex:1;padding:12px;border:none;background:#fff;font-size:13px;font-weight:500;color:#5a7080;border-bottom:2px solid transparent;cursor:pointer;font-family:inherit;">Dentist Availability</button>
  </div>
  <div style="flex:1;overflow-y:auto;padding:16px;">
    <div id="pane-appointments">
      <?php if (empty($scheduleAppts)): ?>
        <div style="text-align:center;padding:3rem 1rem;color:#5a7080;">
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="margin:0 auto 10px;display:block;opacity:0.3;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <p style="font-size:13px;">No appointments yet.</p>
        </div>
      <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:10px;">
          <?php
            $statusColors = ['pending'=>['bg'=>'#FFF8E7','border'=>'#F59E0B','text'=>'#92610A'],'confirmed'=>['bg'=>'#EFF6FF','border'=>'#3B82F6','text'=>'#1E40AF'],'completed'=>['bg'=>'#F0FFF4','border'=>'#1D9E75','text'=>'#0F6E56'],'cancelled'=>['bg'=>'#FFF5F5','border'=>'#FC8181','text'=>'#9B2C2C']];
            foreach ($scheduleAppts as $appt):
              $isToday = $appt['appointment_date'] === date('Y-m-d');
              $sc = $statusColors[$appt['status']] ?? $statusColors['pending'];
          ?>
          <div style="border:1px solid <?= $sc['border'] ?>;background:<?= $sc['bg'] ?>;border-radius:10px;padding:12px 14px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
              <div style="font-size:13px;font-weight:600;color:#1a2e3b;"><?= htmlspecialchars($appt['service_name']) ?></div>
              <span style="font-size:10px;font-weight:600;color:<?= $sc['text'] ?>;background:rgba(255,255,255,0.7);padding:2px 8px;border-radius:20px;text-transform:capitalize;white-space:nowrap;margin-left:8px;"><?= $appt['status'] ?></span>
            </div>
            <div style="font-size:12px;color:#5a7080;display:flex;flex-direction:column;gap:5px;">
              <div style="display:flex;align-items:center;gap:6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span style="color:#1a2e3b;font-weight:500;"><?= date('M j, Y', strtotime($appt['appointment_date'])) ?><?php if ($isToday): ?> <span style="color:#1D9E75;font-weight:700;">— Today!</span><?php endif; ?></span>
              </div>
              <div style="display:flex;align-items:center;gap:6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <?= date('g:i A', strtotime($appt['appointment_time'])) ?>
              </div>
              <div style="display:flex;align-items:center;gap:6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Dr. <?= htmlspecialchars($appt['dentist_name']) ?>
              </div>
              <div style="margin-top:2px;font-weight:600;color:#1a2e3b;">₱<?= number_format($appt['price'], 2) ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <div id="pane-dentists" style="display:none;">
      <?php if (empty($sidebarDentists)): ?>
        <div style="text-align:center;padding:3rem 1rem;color:#5a7080;"><p style="font-size:13px;">No dentists found.</p></div>
      <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:8px;">
          <?php foreach ($sidebarDentists as $d): ?>
          <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:10px;background:#f8f9fa;border:1px solid #e8edf0;">
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:38px;height:38px;border-radius:50%;background:#E1F5EE;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#1D9E75;flex-shrink:0;"><?= strtoupper(substr($d['first_name'],0,1).substr($d['last_name'],0,1)) ?></div>
              <div>
                <div style="font-size:13px;font-weight:600;color:#1a2e3b;">Dr. <?= htmlspecialchars($d['first_name'].' '.$d['last_name']) ?></div>
                <div style="font-size:11px;color:#5a7080;"><?= htmlspecialchars($d['specialization'] ?? 'General Dentistry') ?></div>
              </div>
            </div>
            <?php if ($d['is_active']): ?>
              <span style="display:inline-flex;align-items:center;gap:5px;background:#E1F5EE;color:#0F6E56;font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;white-space:nowrap;flex-shrink:0;"><span style="width:6px;height:6px;border-radius:50%;background:#1D9E75;display:inline-block;"></span>Available</span>
            <?php else: ?>
              <span style="display:inline-flex;align-items:center;gap:5px;background:#FFF5F5;color:#9B2C2C;font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;white-space:nowrap;flex-shrink:0;"><span style="width:6px;height:6px;border-radius:50%;background:#FC8181;display:inline-block;"></span>Unavailable</span>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script>
function openSchedulePanel() { document.getElementById('schedulePanel').style.left='0'; document.getElementById('scheduleOverlay').style.display='block'; }
function closeSchedulePanel() { document.getElementById('schedulePanel').style.left='-420px'; document.getElementById('scheduleOverlay').style.display='none'; }
function switchTab(tab) {
  document.getElementById('pane-appointments').style.display = tab==='appointments'?'block':'none';
  document.getElementById('pane-dentists').style.display = tab==='dentists'?'block':'none';
  document.getElementById('tab-appointments').style.color = tab==='appointments'?'#1D9E75':'#5a7080';
  document.getElementById('tab-appointments').style.borderBottom = tab==='appointments'?'2px solid #1D9E75':'2px solid transparent';
  document.getElementById('tab-dentists').style.color = tab==='dentists'?'#1D9E75':'#5a7080';
  document.getElementById('tab-dentists').style.borderBottom = tab==='dentists'?'2px solid #1D9E75':'2px solid transparent';
}
</script>
<?php endif; ?>