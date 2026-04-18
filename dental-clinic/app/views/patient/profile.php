<?php $currentPage = 'patient_profile'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-title">My Profile</div>
    </div>
    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; showFlash(); ?>

      <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start;">

        <!-- Profile Card -->
        <div class="card" style="margin-bottom:0;">
          <div class="card-body" style="text-align:center;padding:2rem 1.5rem;">
            <?php $avatarFile = $_SESSION['avatar'] ?? ''; ?>
            <?php if (!empty($avatarFile)): ?>
              <img src="<?= APP_URL ?>/public/uploads/avatars/<?= htmlspecialchars($avatarFile) ?>"
                   style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid #E1F5EE;margin-bottom:16px;">
            <?php else: ?>
              <div style="width:90px;height:90px;border-radius:50%;background:#E1F5EE;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;color:#1D9E75;margin:0 auto 16px;">
                <?= strtoupper(substr($user['first_name']??'U',0,1)) ?>
              </div>
            <?php endif; ?>
            <div style="font-size:18px;font-weight:700;color:#1a2e3b;"><?= htmlspecialchars(($user['first_name']??'').' '.($user['last_name']??'')) ?></div>
            <div style="font-size:13px;color:#5a7080;margin-top:4px;"><?= htmlspecialchars($user['email']??'') ?></div>
            <div style="margin-top:12px;">
              <span style="background:#E1F5EE;color:#0F6E56;font-size:12px;font-weight:600;padding:4px 14px;border-radius:20px;">Patient</span>
            </div>

            <!-- Quick Stats -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:20px;">
              <div style="background:#f8f9fa;border-radius:10px;padding:12px;">
                <div style="font-size:20px;font-weight:700;color:#1D9E75;"><?= count($appointments) ?></div>
                <div style="font-size:11px;color:#5a7080;margin-top:2px;">Total Visits</div>
              </div>
              <div style="background:#f8f9fa;border-radius:10px;padding:12px;">
                <div style="font-size:16px;font-weight:700;color:#1D9E75;">₱<?= number_format($totalSpent, 0) ?></div>
                <div style="font-size:11px;color:#5a7080;margin-top:2px;">Total Spent</div>
              </div>
            </div>

            <a href="?page=settings" class="btn btn-primary btn-sm" style="margin-top:16px;width:100%;display:block;text-align:center;">Edit Profile</a>
          </div>
        </div>

        <!-- Info + History -->
        <div style="display:flex;flex-direction:column;gap:20px;">

          <!-- Personal Info -->
          <div class="card" style="margin-bottom:0;">
            <div class="card-header"><div class="card-title">Personal Information</div></div>
            <div class="card-body">
              <table style="width:100%;border-collapse:collapse;">
                <tr style="border-bottom:1px solid #f0f0f0;">
                  <td style="padding:12px 0;color:#5a7080;font-size:13px;width:35%;">Full Name</td>
                  <td style="padding:12px 0;font-weight:600;font-size:14px;color:#1a2e3b;"><?= htmlspecialchars(($user['first_name']??'').' '.($user['last_name']??'')) ?></td>
                </tr>
                <tr style="border-bottom:1px solid #f0f0f0;">
                  <td style="padding:12px 0;color:#5a7080;font-size:13px;">Email</td>
                  <td style="padding:12px 0;font-size:14px;color:#1a2e3b;"><?= htmlspecialchars($user['email']??'') ?></td>
                </tr>
                <tr style="border-bottom:1px solid #f0f0f0;">
                  <td style="padding:12px 0;color:#5a7080;font-size:13px;">Phone</td>
                  <td style="padding:12px 0;font-size:14px;color:#1a2e3b;"><?= htmlspecialchars($user['phone']??'—') ?></td>
                </tr>
                <tr>
                  <td style="padding:12px 0;color:#5a7080;font-size:13px;">Role</td>
                  <td style="padding:12px 0;font-size:14px;color:#1a2e3b;">Patient</td>
                </tr>
              </table>
            </div>
          </div>

          <!-- Recent Appointments -->
          <div class="card" style="margin-bottom:0;">
            <div class="card-header">
              <div class="card-title">Recent Appointments</div>
              <a href="?page=patient_appointments" class="btn btn-ghost btn-sm">View All</a>
            </div>
            <div class="card-body">
              <?php
                $recent = array_slice($appointments, 0, 5);
              ?>
              <?php if (empty($recent)): ?>
                <div style="text-align:center;padding:1.5rem;color:#5a7080;font-size:14px;">No appointments yet.</div>
              <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:8px;">
                  <?php foreach ($recent as $a):
                    $colors = ['pending'=>'#F59E0B','confirmed'=>'#3B82F6','completed'=>'#1D9E75','cancelled'=>'#FC8181'];
                    $c = $colors[$a['status']] ?? '#888';
                  ?>
                  <div style="display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:10px;background:#f8f9fa;border-left:3px solid <?= $c ?>;">
                    <div style="flex:1;">
                      <div style="font-size:13px;font-weight:600;color:#1a2e3b;"><?= htmlspecialchars($a['service_name']) ?></div>
                      <div style="font-size:11px;color:#5a7080;margin-top:2px;">Dr. <?= htmlspecialchars($a['dentist_name']) ?> &bull; <?= date('M j, Y', strtotime($a['appointment_date'])) ?></div>
                    </div>
                    <div style="text-align:right;">
                      <?= statusBadge($a['status']) ?>
                      <div style="font-size:12px;font-weight:600;color:#1a2e3b;margin-top:4px;">₱<?= number_format($a['price'], 2) ?></div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>