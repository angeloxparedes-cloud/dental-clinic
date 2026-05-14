<?php
require 'config.php';
$id = intval($_GET['id'] ?? 0);
$charter = $pdo->prepare("SELECT * FROM project_charter WHERE id = ?");
$charter->execute([$id]);
$c = $charter->fetch(PDO::FETCH_ASSOC);
if (!$c) { header("Location: index.php"); exit; }

// Add task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $stmt = $pdo->prepare("INSERT INTO timeline_tasks (charter_id, task_name, assigned_to, start_date, end_date, status) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$id, $_POST['task_name'], $_POST['assigned_to'], $_POST['task_start'], $_POST['task_end'], $_POST['task_status']]);
    header("Location: timeline.php?id=$id");
    exit;
}
// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE timeline_tasks SET status=? WHERE id=? AND charter_id=?");
    $stmt->execute([$_POST['new_status'], $_POST['task_id'], $id]);
    header("Location: timeline.php?id=$id");
    exit;
}
// Delete task
if (isset($_GET['delete_task'])) {
    $pdo->prepare("DELETE FROM timeline_tasks WHERE id=? AND charter_id=?")->execute([$_GET['delete_task'], $id]);
    header("Location: timeline.php?id=$id");
    exit;
}

$tasks = $pdo->prepare("SELECT * FROM timeline_tasks WHERE charter_id = ? ORDER BY start_date ASC");
$tasks->execute([$id]);
$taskList = $tasks->fetchAll(PDO::FETCH_ASSOC);

// Gantt setup
$projStart = new DateTime($c['start_date']);
$projEnd   = new DateTime($c['end_date']);
$totalDays = max(1, $projStart->diff($projEnd)->days);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Timeline – <?= htmlspecialchars($c['event_name']) ?></title>
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
    .btn-gray { background: #eceff1; color: #546e7a; }
    .btn-red { background: #c62828; color: #fff; }
    .btn-sm { padding: 6px 12px; font-size: 12px; }
    .card { background: #fff; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); margin-bottom: 24px; overflow: hidden; }
    .card-header { padding: 16px 24px; background: #e8f5e9; border-bottom: 1px solid #c8e6c9; }
    .card-header h3 { font-size: 15px; color: #2e7d32; font-weight: 700; }
    .card-body { padding: 24px; }

    /* Add Task Form */
    .task-form { display: grid; grid-template-columns: 2fr 1.5fr 1fr 1fr 1fr auto; gap: 10px; align-items: end; }
    .form-group label { font-size: 12px; font-weight: 600; color: #2e7d32; display: block; margin-bottom: 4px; }
    input, select {
      padding: 9px 12px; border: 1.5px solid #c8e6c9; border-radius: 6px;
      font-size: 13px; font-family: Arial; width: 100%;
      background: #f9fff9; outline: none; transition: border 0.2s;
    }
    input:focus, select:focus { border-color: #2e7d32; background: #fff; }

    /* Gantt Chart */
    .gantt-container { overflow-x: auto; }
    .gantt-table { width: 100%; border-collapse: collapse; min-width: 700px; }
    .gantt-table th {
      background: #e8f5e9; color: #2e7d32; font-size: 11px;
      text-transform: uppercase; padding: 10px 12px; border: 1px solid #c8e6c9;
    }
    .gantt-table td { padding: 10px 12px; border: 1px solid #f1f8f1; font-size: 13px; vertical-align: middle; }
    .gantt-bar-cell { padding: 8px 12px; }
    .gantt-bar-bg { background: #f1f8f1; border-radius: 4px; height: 28px; position: relative; }
    .gantt-bar {
      position: absolute; height: 100%; border-radius: 4px;
      display: flex; align-items: center; padding: 0 8px;
      font-size: 11px; font-weight: 600; color: #fff;
      overflow: hidden; white-space: nowrap;
      min-width: 8px;
    }
    .bar-not-started { background: #78909c; }
    .bar-in-progress { background: #1565c0; }
    .bar-completed { background: #2e7d32; }
    .badge {
      padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
    }
    .badge-notstarted { background: #eceff1; color: #546e7a; }
    .badge-inprogress { background: #e3f2fd; color: #1565c0; }
    .badge-completed { background: #e8f5e9; color: #2e7d32; }
    .action-btns { display: flex; gap: 6px; }
    .empty-state { text-align: center; padding: 40px; color: #aaa; font-size: 14px; }

    /* Progress Summary */
    .progress-bar-wrap { background: #e8f5e9; border-radius: 20px; height: 12px; overflow: hidden; margin-top: 6px; }
    .progress-bar-fill { height: 100%; background: #2e7d32; border-radius: 20px; transition: width 0.4s; }
    .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
    .summary-card { background: #f9fff9; border: 1px solid #e8f5e9; border-radius: 8px; padding: 16px; text-align: center; }
    .summary-card .num { font-size: 24px; font-weight: 700; color: #1a6b3a; }
    .summary-card .lbl { font-size: 12px; color: #888; margin-top: 2px; }
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
  <div class="page-header">
    <div>
      <h1>📅 Project Timeline</h1>
      <p><?= htmlspecialchars($c['event_name']) ?> &nbsp;·&nbsp;
         <?= date('M d', strtotime($c['start_date'])) ?> – <?= date('M d, Y', strtotime($c['end_date'])) ?>
      </p>
    </div>
    <div style="display:flex; gap:10px">
      <a href="charter_view.php?id=<?= $id ?>" class="btn btn-gray">📋 View Charter</a>
      <a href="index.php" class="btn btn-gray">← Dashboard</a>
    </div>
  </div>

  <!-- Progress Summary -->
  <?php
    $total    = count($taskList);
    $done     = count(array_filter($taskList, fn($t) => $t['status'] === 'Completed'));
    $progress = $total > 0 ? round(($done / $total) * 100) : 0;
    $inprog   = count(array_filter($taskList, fn($t) => $t['status'] === 'In Progress'));
  ?>
  <div class="card">
    <div class="card-header"><h3>📊 Progress Overview</h3></div>
    <div class="card-body">
      <div class="summary-grid">
        <div class="summary-card">
          <div class="num"><?= $total ?></div>
          <div class="lbl">Total Tasks</div>
        </div>
        <div class="summary-card">
          <div class="num" style="color:#1565c0"><?= $inprog ?></div>
          <div class="lbl">In Progress</div>
        </div>
        <div class="summary-card">
          <div class="num"><?= $done ?></div>
          <div class="lbl">Completed</div>
        </div>
      </div>
      <div style="display:flex; justify-content:space-between; margin-bottom:6px">
        <span style="font-size:13px; font-weight:600; color:#2e7d32">Overall Progress</span>
        <span style="font-size:13px; font-weight:700; color:#2e7d32"><?= $progress ?>%</span>
      </div>
      <div class="progress-bar-wrap">
        <div class="progress-bar-fill" style="width:<?= $progress ?>%"></div>
      </div>
    </div>
  </div>

  <!-- Add Task -->
  <div class="card">
    <div class="card-header"><h3>➕ Add Timeline Task</h3></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="add_task" value="1">
        <div class="task-form">
          <div class="form-group">
            <label>Task Name *</label>
            <input type="text" name="task_name" placeholder="e.g. Venue Reservation" required>
          </div>
          <div class="form-group">
            <label>Assigned To</label>
            <input type="text" name="assigned_to" placeholder="e.g. Juan dela Cruz">
          </div>
          <div class="form-group">
            <label>Start Date *</label>
            <input type="date" name="task_start" value="<?= $c['start_date'] ?>" required>
          </div>
          <div class="form-group">
            <label>End Date *</label>
            <input type="date" name="task_end" value="<?= $c['end_date'] ?>" required>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="task_status">
              <option value="Not Started">Not Started</option>
              <option value="In Progress">In Progress</option>
              <option value="Completed">Completed</option>
            </select>
          </div>
          <div class="form-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-green">Add</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Gantt Chart -->
  <div class="card">
    <div class="card-header"><h3>📊 Gantt Chart</h3></div>
    <div class="card-body">
      <?php if (empty($taskList)): ?>
        <div class="empty-state">No tasks yet. Add tasks above to see the Gantt chart!</div>
      <?php else: ?>
      <div class="gantt-container">
        <table class="gantt-table">
          <thead>
            <tr>
              <th style="width:180px">Task</th>
              <th style="width:130px">Assigned To</th>
              <th style="width:90px">Start</th>
              <th style="width:90px">End</th>
              <th>Timeline (<?= date('M d', strtotime($c['start_date'])) ?> – <?= date('M d, Y', strtotime($c['end_date'])) ?>)</th>
              <th style="width:110px">Status</th>
              <th style="width:120px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($taskList as $t):
              $tStart  = new DateTime($t['start_date']);
              $tEnd    = new DateTime($t['end_date']);
              $offset  = max(0, $projStart->diff($tStart)->days);
              $dur     = max(1, $tStart->diff($tEnd)->days);
              $left    = round(($offset / $totalDays) * 100, 1);
              $width   = min(100 - $left, round(($dur / $totalDays) * 100, 1));
              $barClass = match($t['status']) {
                'Completed'   => 'bar-completed',
                'In Progress' => 'bar-in-progress',
                default       => 'bar-not-started'
              };
              $badgeClass = match($t['status']) {
                'Completed'   => 'badge-completed',
                'In Progress' => 'badge-inprogress',
                default       => 'badge-notstarted'
              };
            ?>
            <tr>
              <td><strong><?= htmlspecialchars($t['task_name']) ?></strong></td>
              <td style="color:#555"><?= htmlspecialchars($t['assigned_to'] ?: '—') ?></td>
              <td style="font-size:12px"><?= date('M d', strtotime($t['start_date'])) ?></td>
              <td style="font-size:12px"><?= date('M d', strtotime($t['end_date'])) ?></td>
              <td class="gantt-bar-cell">
                <div class="gantt-bar-bg">
                  <div class="gantt-bar <?= $barClass ?>"
                       style="left:<?= $left ?>%; width:<?= max(2, $width) ?>%">
                    <?= $width > 10 ? htmlspecialchars($t['task_name']) : '' ?>
                  </div>
                </div>
              </td>
              <td>
                <form method="POST" style="display:inline">
                  <input type="hidden" name="update_status" value="1">
                  <input type="hidden" name="task_id" value="<?= $t['id'] ?>">
                  <select name="new_status" onchange="this.form.submit()" style="font-size:12px; padding:5px 8px;">
                    <option <?= $t['status']==='Not Started' ? 'selected':'' ?>>Not Started</option>
                    <option <?= $t['status']==='In Progress' ? 'selected':'' ?>>In Progress</option>
                    <option <?= $t['status']==='Completed'   ? 'selected':'' ?>>Completed</option>
                  </select>
                </form>
              </td>
              <td>
                <a href="timeline.php?id=<?= $id ?>&delete_task=<?= $t['id'] ?>"
                   class="btn btn-red btn-sm"
                   onclick="return confirm('Delete this task?')">🗑 Delete</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
