<?php
require 'config.php';
$id = intval($_GET['id'] ?? 0);
$charter = $pdo->prepare("SELECT * FROM project_charter WHERE id = ?");
$charter->execute([$id]);
$c = $charter->fetch(PDO::FETCH_ASSOC);
if (!$c) { header("Location: index.php"); exit; }
$new = isset($_GET['new']);
$tasks = $pdo->prepare("SELECT * FROM timeline_tasks WHERE charter_id = ? ORDER BY start_date ASC");
$tasks->execute([$id]);
$taskList = $tasks->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($c['event_name']) ?> – EMS</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #f0f7f0; color: #1a1a1a; }
    .sidebar {
      width: 240px; background: #1a6b3a; position: fixed;
      top: 0; left: 0; height: 100vh; display: flex; flex-direction: column;
    }
    .sidebar-logo { background: #145a30; padding: 24px 20px; display: flex; align-items: center; gap: 10px; }
    .sidebar-logo .icon { font-size: 28px; }
    .sidebar-logo h2 { color: #fff; font-size: 14px; line-height: 1.3; }
    .sidebar-logo span { color: #a8d5b5; font-size: 11px; display: block; }
    .sidebar nav { padding: 16px 0; flex: 1; }
    .sidebar nav a {
      display: flex; align-items: center; gap: 12px;
      padding: 12px 20px; color: #c8e6d0; text-decoration: none;
      font-size: 14px; transition: background 0.2s;
    }
    .sidebar nav a:hover { background: #145a30; color: #fff; border-left: 3px solid #4caf50; }
    .sidebar nav a .nav-icon { font-size: 18px; width: 20px; text-align: center; }
    .sidebar-footer { padding: 16px 20px; font-size: 11px; color: #7ab98a; border-top: 1px solid #145a30; }
    .main { margin-left: 240px; padding: 32px; min-height: 100vh; }
    .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
    .page-header h1 { font-size: 22px; color: #1a6b3a; font-weight: 700; }
    .page-header p { font-size: 13px; color: #666; margin-top: 2px; }
    .btn {
      padding: 10px 18px; border-radius: 6px; border: none; cursor: pointer;
      font-size: 13px; font-weight: 600; text-decoration: none;
      display: inline-flex; align-items: center; gap: 6px; transition: opacity 0.2s;
    }
    .btn:hover { opacity: 0.85; }
    .btn-green { background: #2e7d32; color: #fff; }
    .btn-blue { background: #1565c0; color: #fff; }
    .btn-gray { background: #eceff1; color: #546e7a; }
    .btn-group { display: flex; gap: 10px; flex-wrap: wrap; }
    .card { background: #fff; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); margin-bottom: 20px; overflow: hidden; }
    .card-header { padding: 16px 24px; background: #e8f5e9; border-bottom: 1px solid #c8e6c9; display: flex; justify-content: space-between; align-items: center; }
    .card-header h3 { font-size: 15px; color: #2e7d32; font-weight: 700; }
    .card-body { padding: 24px; }
    .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .info-item label { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px; }
    .info-item p { font-size: 14px; color: #1a1a1a; font-weight: 500; }
    .info-full { grid-column: 1 / -1; }
    .info-item.two { grid-column: span 2; }
    .badge {
      padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block;
    }
    .badge-planning { background: #fff3e0; color: #e65100; }
    .badge-ongoing { background: #e3f2fd; color: #1565c0; }
    .badge-completed { background: #e8f5e9; color: #2e7d32; }
    .badge-cancelled { background: #fce4ec; color: #c62828; }
    .divider { height: 1px; background: #e8f5e9; margin: 16px 0; }
    .alert-success {
      background: #e8f5e9; color: #2e7d32; border-left: 4px solid #2e7d32;
      padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;
    }
    .task-count { font-size: 12px; color: #888; font-weight: 400; }
  </style>
</head>
<body>
<div class="sidebar">
  <div class="sidebar-logo">
    <div class="icon">🎓</div>
    <h2>EMS<span>Academic Event Manager</span></h2>
  </div>
  <nav>
    <a href="index.php"><span class="nav-icon">🏠</span> Dashboard</a>
    <a href="charter_create.php"><span class="nav-icon">📋</span> New Project Charter</a>
    <a href="index.php"><span class="nav-icon">📁</span> All Projects</a>
  </nav>
  <div class="sidebar-footer">School/Academic Events v1.0</div>
</div>

<div class="main">
  <?php if ($new): ?>
    <div class="alert-success">✅ Project charter created successfully! You can now add timeline tasks below.</div>
  <?php endif; ?>

  <div class="page-header">
    <div>
      <h1>📋 <?= htmlspecialchars($c['event_name']) ?></h1>
      <p><?= htmlspecialchars($c['project_title']) ?></p>
    </div>
    <div class="btn-group">
      <a href="timeline.php?id=<?= $c['id'] ?>" class="btn btn-blue">📅 View Timeline</a>
      <a href="index.php" class="btn btn-gray">← Back</a>
    </div>
  </div>

  <!-- Charter Details -->
  <div class="card">
    <div class="card-header">
      <h3>📄 Project Charter</h3>
      <span class="badge badge-<?= strtolower($c['status']) ?>"><?= $c['status'] ?></span>
    </div>
    <div class="card-body">
      <div class="info-grid">
        <div class="info-item">
          <label>Project Title</label>
          <p><?= htmlspecialchars($c['project_title']) ?></p>
        </div>
        <div class="info-item">
          <label>Event Name</label>
          <p><?= htmlspecialchars($c['event_name']) ?></p>
        </div>
        <div class="info-item">
          <label>Organization</label>
          <p><?= htmlspecialchars($c['organization']) ?></p>
        </div>
        <div class="info-item">
          <label>Project Manager</label>
          <p><?= htmlspecialchars($c['project_manager']) ?></p>
        </div>
        <div class="info-item two">
          <label>Team Members</label>
          <p><?= htmlspecialchars($c['team_members'] ?: '—') ?></p>
        </div>

        <div class="divider info-full"></div>

        <div class="info-item full info-full">
          <label>Objectives</label>
          <p style="white-space: pre-wrap; line-height:1.6"><?= htmlspecialchars($c['objectives']) ?></p>
        </div>
        <div class="info-item full info-full">
          <label>Scope</label>
          <p style="white-space: pre-wrap; line-height:1.6"><?= htmlspecialchars($c['scope'] ?: '—') ?></p>
        </div>

        <div class="divider info-full"></div>

        <div class="info-item">
          <label>Venue</label>
          <p><?= htmlspecialchars($c['venue'] ?: '—') ?></p>
        </div>
        <div class="info-item">
          <label>Budget</label>
          <p>₱<?= number_format($c['budget'], 2) ?></p>
        </div>
        <div class="info-item">
          <label>Status</label>
          <p><span class="badge badge-<?= strtolower($c['status']) ?>"><?= $c['status'] ?></span></p>
        </div>
        <div class="info-item">
          <label>Start Date</label>
          <p><?= date('F d, Y', strtotime($c['start_date'])) ?></p>
        </div>
        <div class="info-item">
          <label>End Date</label>
          <p><?= date('F d, Y', strtotime($c['end_date'])) ?></p>
        </div>
        <div class="info-item">
          <label>Duration</label>
          <?php
            $d1 = new DateTime($c['start_date']);
            $d2 = new DateTime($c['end_date']);
            $diff = $d1->diff($d2)->days;
          ?>
          <p><?= $diff ?> day<?= $diff != 1 ? 's' : '' ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tasks Summary -->
  <div class="card">
    <div class="card-header">
      <h3>📅 Timeline Tasks <span class="task-count">(<?= count($taskList) ?> tasks)</span></h3>
      <a href="timeline.php?id=<?= $c['id'] ?>" class="btn btn-green">➕ Manage Timeline</a>
    </div>
    <div class="card-body">
      <?php if (empty($taskList)): ?>
        <p style="color:#aaa; font-size:14px; text-align:center; padding:20px 0">
          No timeline tasks yet. <a href="timeline.php?id=<?= $c['id'] ?>" style="color:#2e7d32">Add tasks →</a>
        </p>
      <?php else: ?>
        <?php foreach ($taskList as $t): ?>
          <div style="display:flex; justify-content:space-between; align-items:center; padding: 10px 0; border-bottom: 1px solid #f1f8f1;">
            <div>
              <strong style="font-size:14px"><?= htmlspecialchars($t['task_name']) ?></strong>
              <?php if ($t['assigned_to']): ?>
                <span style="font-size:12px; color:#888; margin-left:8px">👤 <?= htmlspecialchars($t['assigned_to']) ?></span>
              <?php endif; ?>
            </div>
            <div style="font-size:12px; color:#888">
              <?= date('M d', strtotime($t['start_date'])) ?> – <?= date('M d, Y', strtotime($t['end_date'])) ?>
              &nbsp;
              <span class="badge badge-<?= strtolower(str_replace(' ', '', $t['status'])) ?>" style="font-size:11px">
                <?= $t['status'] ?>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
