<?php
require 'config.php';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO project_charter
            (project_title, event_name, organization, project_manager, team_members,
             objectives, scope, budget, venue, start_date, end_date, status)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $_POST['project_title'], $_POST['event_name'], $_POST['organization'],
            $_POST['project_manager'], $_POST['team_members'], $_POST['objectives'],
            $_POST['scope'], $_POST['budget'], $_POST['venue'],
            $_POST['start_date'], $_POST['end_date'], $_POST['status']
        ]);
        header("Location: charter_view.php?id=" . $pdo->lastInsertId() . "&new=1");
        exit;
    } catch (Exception $e) {
        $error = "Error saving: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Project Charter – EMS</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #f0f7f0; color: #1a1a1a; }
    .sidebar {
      width: 240px; background: #1a6b3a; position: fixed;
      top: 0; left: 0; height: 100vh; padding: 0;
      display: flex; flex-direction: column;
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
    .sidebar nav a:hover, .sidebar nav a.active {
      background: #145a30; color: #fff; border-left: 3px solid #4caf50;
    }
    .sidebar nav a .nav-icon { font-size: 18px; width: 20px; text-align: center; }
    .sidebar-footer { padding: 16px 20px; font-size: 11px; color: #7ab98a; border-top: 1px solid #145a30; }
    .main { margin-left: 240px; padding: 32px; min-height: 100vh; }
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 22px; color: #1a6b3a; font-weight: 700; }
    .page-header p { font-size: 13px; color: #666; margin-top: 2px; }
    .card { background: #fff; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); padding: 28px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: 1 / -1; }
    label { font-size: 13px; font-weight: 600; color: #2e7d32; }
    label span { color: #c62828; }
    input, select, textarea {
      padding: 10px 14px; border: 1.5px solid #c8e6c9; border-radius: 6px;
      font-size: 14px; font-family: Arial, sans-serif;
      transition: border 0.2s; outline: none; background: #f9fff9;
    }
    input:focus, select:focus, textarea:focus { border-color: #2e7d32; background: #fff; }
    textarea { resize: vertical; min-height: 90px; }
    .section-title {
      font-size: 13px; font-weight: 700; color: #1a6b3a;
      text-transform: uppercase; letter-spacing: 0.5px;
      padding-bottom: 8px; border-bottom: 2px solid #e8f5e9;
      margin: 24px 0 16px; grid-column: 1 / -1;
    }
    .form-actions { display: flex; gap: 12px; margin-top: 28px; }
    .btn {
      padding: 11px 24px; border-radius: 6px; border: none; cursor: pointer;
      font-size: 14px; font-weight: 600; text-decoration: none;
      display: inline-flex; align-items: center; gap: 8px; transition: opacity 0.2s;
    }
    .btn:hover { opacity: 0.85; }
    .btn-green { background: #2e7d32; color: #fff; }
    .btn-gray { background: #eceff1; color: #546e7a; }
    .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
    .alert-error { background: #fce4ec; color: #c62828; border-left: 4px solid #c62828; }
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
    <a href="charter_create.php" class="active"><span class="nav-icon">📋</span> New Project Charter</a>
    <a href="index.php"><span class="nav-icon">📁</span> All Projects</a>
  </nav>
  <div class="sidebar-footer">School/Academic Events v1.0</div>
</div>

<div class="main">
  <div class="page-header">
    <h1>📋 New Project Charter</h1>
    <p>Fill in the details below to create a new event project charter</p>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
  <?php endif; ?>

  <div class="card">
    <form method="POST">
      <div class="form-grid">

        <div class="section-title">📌 Basic Information</div>

        <div class="form-group">
          <label>Project Title <span>*</span></label>
          <input type="text" name="project_title" placeholder="e.g. Foundation Day 2026 Project" required>
        </div>
        <div class="form-group">
          <label>Event Name <span>*</span></label>
          <input type="text" name="event_name" placeholder="e.g. TPC Foundation Day 2026" required>
        </div>
        <div class="form-group">
          <label>Organization / Department <span>*</span></label>
          <input type="text" name="organization" placeholder="e.g. Supreme Student Council – TPC" required>
        </div>
        <div class="form-group">
          <label>Project Manager <span>*</span></label>
          <input type="text" name="project_manager" placeholder="e.g. Juan dela Cruz" required>
        </div>
        <div class="form-group full">
          <label>Team Members</label>
          <input type="text" name="team_members" placeholder="e.g. Maria Santos, Pedro Reyes, Ana Garcia">
        </div>

        <div class="section-title">🎯 Project Details</div>

        <div class="form-group full">
          <label>Objectives <span>*</span></label>
          <textarea name="objectives" placeholder="What are the goals of this event?" required></textarea>
        </div>
        <div class="form-group full">
          <label>Scope</label>
          <textarea name="scope" placeholder="What is included and excluded in this project?"></textarea>
        </div>

        <div class="section-title">📅 Schedule & Budget</div>

        <div class="form-group">
          <label>Venue</label>
          <input type="text" name="venue" placeholder="e.g. TPC Gymnasium">
        </div>
        <div class="form-group">
          <label>Budget (₱)</label>
          <input type="number" name="budget" placeholder="e.g. 15000" step="0.01" min="0">
        </div>
        <div class="form-group">
          <label>Start Date <span>*</span></label>
          <input type="date" name="start_date" required>
        </div>
        <div class="form-group">
          <label>End Date <span>*</span></label>
          <input type="date" name="end_date" required>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <option value="Planning">Planning</option>
            <option value="Ongoing">Ongoing</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>

      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-green">✅ Save Project Charter</button>
        <a href="index.php" class="btn btn-gray">✖ Cancel</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
