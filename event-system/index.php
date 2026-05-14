<?php require 'config.php'; ?>
<?php
$charters = $pdo->query("SELECT * FROM project_charter ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Management System</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #f0f7f0; color: #1a1a1a; }

    /* Sidebar */
    .sidebar {
      width: 240px; background: #1a6b3a; position: fixed;
      top: 0; left: 0; height: 100vh; padding: 0;
      display: flex; flex-direction: column;
    }
    .sidebar-logo {
      background: #145a30; padding: 24px 20px;
      display: flex; align-items: center; gap: 10px;
    }
    .sidebar-logo .icon { font-size: 28px; }
    .sidebar-logo h2 { color: #fff; font-size: 14px; line-height: 1.3; }
    .sidebar-logo span { color: #a8d5b5; font-size: 11px; display: block; }
    .sidebar nav { padding: 16px 0; flex: 1; }
    .sidebar nav a {
      display: flex; align-items: center; gap: 12px;
      padding: 12px 20px; color: #c8e6d0; text-decoration: none;
      font-size: 14px; transition: background 0.2s;
    }
    .sidebar nav a:hover, .sidebar nav a.active {
      background: #145a30; color: #fff;
      border-left: 3px solid #4caf50;
    }
    .sidebar nav a .nav-icon { font-size: 18px; width: 20px; text-align: center; }
    .sidebar-footer {
      padding: 16px 20px; font-size: 11px;
      color: #7ab98a; border-top: 1px solid #145a30;
    }

    /* Main */
    .main { margin-left: 240px; padding: 32px; min-height: 100vh; }
    .page-header {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 28px;
    }
    .page-header h1 { font-size: 22px; color: #1a6b3a; font-weight: 700; }
    .page-header p { font-size: 13px; color: #666; margin-top: 2px; }
    .btn {
      padding: 10px 20px; border-radius: 6px; border: none;
      cursor: pointer; font-size: 14px; font-weight: 600;
      text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
      transition: opacity 0.2s;
    }
    .btn:hover { opacity: 0.85; }
    .btn-green { background: #2e7d32; color: #fff; }
    .btn-blue { background: #1565c0; color: #fff; }
    .btn-red { background: #c62828; color: #fff; }
    .btn-sm { padding: 6px 14px; font-size: 12px; }

    /* Stats */
    .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
    .stat-card {
      background: #fff; border-radius: 10px; padding: 20px;
      border-left: 4px solid #2e7d32;
      box-shadow: 0 1px 4px rgba(0,0,0,0.07);
    }
    .stat-card.orange { border-left-color: #e65100; }
    .stat-card.blue { border-left-color: #1565c0; }
    .stat-card.gray { border-left-color: #546e7a; }
    .stat-label { font-size: 12px; color: #888; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 28px; font-weight: 700; color: #1a1a1a; }

    /* Table */
    .card { background: #fff; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); overflow: hidden; }
    .card-header {
      padding: 18px 24px; border-bottom: 1px solid #e8f5e9;
      display: flex; justify-content: space-between; align-items: center;
    }
    .card-header h3 { font-size: 16px; color: #1a6b3a; }
    table { width: 100%; border-collapse: collapse; }
    th {
      background: #e8f5e9; color: #2e7d32; font-size: 12px;
      text-transform: uppercase; letter-spacing: 0.5px;
      padding: 12px 16px; text-align: left;
    }
    td { padding: 13px 16px; border-bottom: 1px solid #f1f8f1; font-size: 14px; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #f9fff9; }

    /* Badges */
    .badge {
      padding: 4px 10px; border-radius: 20px; font-size: 11px;
      font-weight: 600; display: inline-block;
    }
    .badge-planning { background: #fff3e0; color: #e65100; }
    .badge-ongoing { background: #e3f2fd; color: #1565c0; }
    .badge-completed { background: #e8f5e9; color: #2e7d32; }
    .badge-cancelled { background: #fce4ec; color: #c62828; }

    .empty-state {
      text-align: center; padding: 60px 20px; color: #aaa;
    }
    .empty-state .empty-icon { font-size: 48px; margin-bottom: 12px; }
    .empty-state p { font-size: 14px; }
    .action-btns { display: flex; gap: 6px; }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="sidebar-logo">
    <div class="icon">🎓</div>
    <h2>EMS<span>Academic Event Manager</span></h2>
  </div>
  <nav>
    <a href="index.php" class="active"><span class="nav-icon">🏠</span> Dashboard</a>
    <a href="charter_create.php"><span class="nav-icon">📋</span> New Project Charter</a>
    <a href="index.php"><span class="nav-icon">📁</span> All Projects</a>
  </nav>
  <div class="sidebar-footer">School/Academic Events v1.0</div>
</div>

<!-- Main -->
<div class="main">
  <div class="page-header">
    <div>
      <h1>📊 Dashboard</h1>
      <p>Manage your academic event projects</p>
    </div>
    <a href="charter_create.php" class="btn btn-green">＋ New Project</a>
  </div>

  <!-- Stats -->
  <?php
    $total    = count($charters);
    $planning = count(array_filter($charters, fn($c) => $c['status'] === 'Planning'));
    $ongoing  = count(array_filter($charters, fn($c) => $c['status'] === 'Ongoing'));
    $done     = count(array_filter($charters, fn($c) => $c['status'] === 'Completed'));
  ?>
  <div class="stats">
    <div class="stat-card">
      <div class="stat-label">Total Projects</div>
      <div class="stat-value"><?= $total ?></div>
    </div>
    <div class="stat-card orange">
      <div class="stat-label">Planning</div>
      <div class="stat-value"><?= $planning ?></div>
    </div>
    <div class="stat-card blue">
      <div class="stat-label">Ongoing</div>
      <div class="stat-value"><?= $ongoing ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Completed</div>
      <div class="stat-value"><?= $done ?></div>
    </div>
  </div>

  <!-- Table -->
  <div class="card">
    <div class="card-header">
      <h3>All Event Projects</h3>
    </div>
    <?php if (empty($charters)): ?>
      <div class="empty-state">
        <div class="empty-icon">📭</div>
        <p>No projects yet. Click <strong>New Project</strong> to get started!</p>
      </div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Event Name</th>
          <th>Project Manager</th>
          <th>Organization</th>
          <th>Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($charters as $i => $c): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><strong><?= htmlspecialchars($c['event_name']) ?></strong><br>
            <small style="color:#888"><?= htmlspecialchars($c['project_title']) ?></small></td>
          <td><?= htmlspecialchars($c['project_manager']) ?></td>
          <td><?= htmlspecialchars($c['organization']) ?></td>
          <td style="font-size:12px">
            <?= date('M d, Y', strtotime($c['start_date'])) ?><br>
            <span style="color:#888">to <?= date('M d, Y', strtotime($c['end_date'])) ?></span>
          </td>
          <td>
            <span class="badge badge-<?= strtolower($c['status']) ?>">
              <?= $c['status'] ?>
            </span>
          </td>
          <td>
            <div class="action-btns">
              <a href="charter_view.php?id=<?= $c['id'] ?>" class="btn btn-green btn-sm">👁 View</a>
              <a href="timeline.php?id=<?= $c['id'] ?>" class="btn btn-blue btn-sm">📅 Timeline</a>
              <a href="charter_delete.php?id=<?= $c['id'] ?>" class="btn btn-red btn-sm"
                 onclick="return confirm('Delete this project?')">🗑</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
