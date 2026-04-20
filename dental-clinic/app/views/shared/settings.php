<?php $currentPage = 'settings'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Settings – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require_once __DIR__ . '/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-title">Account Settings</div>
    </div>
    <div class="page-content">
      <?php require_once __DIR__ . '/helpers.php'; showFlash(); ?>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;max-width:900px;">

        <!-- Edit Profile -->
        <div class="card">
          <div class="card-header">
            <div class="card-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:inline;vertical-align:middle;margin-right:8px;color:var(--primary)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              Edit Profile
            </div>
          </div>
          <div class="card-body">

            <!-- Avatar -->
            <div style="text-align:center;margin-bottom:24px;">
              <div style="position:relative;display:inline-block;">
                <?php if (!empty($user['avatar'])): ?>
                  <img src="<?= APP_URL ?>/public/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>"
                    id="avatarPreview"
                    style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--primary);">
                <?php else: ?>
                  <div class="user-avatar" id="avatarInitials"
                    style="width:72px;height:72px;font-size:1.6rem;margin:0 auto;background:var(--primary);display:flex;align-items:center;justify-content:center;">
                    <?= strtoupper(substr($user['first_name']??'U',0,1)) ?>
                  </div>
                  <img id="avatarPreview" style="display:none;width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--primary);">
                <?php endif; ?>

                <!-- Camera button overlay -->
                <label for="avatarInput" style="position:absolute;bottom:0;right:0;background:var(--primary);color:#fff;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,0.2);" title="Change photo">
                  <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </label>
              </div>

              <div style="font-weight:700;font-size:1rem;margin-top:10px;"><?= htmlspecialchars(($user['first_name']??'').' '.($user['last_name']??'')) ?></div>
              <div class="text-sm text-gray"><?= htmlspecialchars($user['email']??'') ?></div>
              <span class="badge badge-confirmed" style="margin-top:6px;"><?= ucfirst($user['role']??'') ?></span>
              <div id="avatarFileName" class="text-sm text-gray" style="margin-top:6px;display:none;"></div>
            </div>

            <form method="POST" action="?page=update_profile" enctype="multipart/form-data">
              <!-- Hidden file input -->
              <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;">

              <div class="form-row">
                <div class="form-group">
                  <label>First Name *</label>
                  <div class="input-wrap no-icon">
                    <input type="text" name="first_name" required value="<?= htmlspecialchars($user['first_name']??'') ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label>Last Name *</label>
                  <div class="input-wrap no-icon">
                    <input type="text" name="last_name" required value="<?= htmlspecialchars($user['last_name']??'') ?>">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrap no-icon">
                  <input type="email" value="<?= htmlspecialchars($user['email']??'') ?>" disabled style="background:#f8fafc;color:var(--gray);cursor:not-allowed;">
                </div>
                <div class="text-sm text-gray mt-1">Email cannot be changed.</div>
              </div>
              <div class="form-group">
                <label>Phone Number</label>
                <div class="input-wrap no-icon">
                  <input type="text" name="phone" placeholder="09XXXXXXXXX" value="<?= htmlspecialchars($user['phone']??'') ?>">
                </div>
              </div>
              <button type="submit" class="btn btn-primary btn-block">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Profile
              </button>
            </form>
          </div>
        </div>

        <!-- Change Password -->
        <div class="card">
          <div class="card-header">
            <div class="card-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:inline;vertical-align:middle;margin-right:8px;color:var(--primary)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              Change Password
            </div>
          </div>
          <div class="card-body">
            <div style="background:var(--primary-light);border-radius:8px;padding:14px 16px;margin-bottom:20px;font-size:.85rem;color:var(--primary);">
              <strong>Password requirements:</strong><br>
              • At least 8 characters long<br>
              • Include a mix of letters and numbers for security
            </div>
            <form method="POST" action="?page=change_password">

              <!-- Current Password -->
              <div class="form-group">
                <label>Current Password *</label>
                <div class="input-wrap" style="position:relative;">
                  <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                  <input type="password" name="current_password" id="current_password" required placeholder="Enter current password" style="padding-right:40px;">
                  <span onclick="togglePassword('current_password')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:#9ca3af;display:flex;align-items:center;">
                    <svg id="current_password_eye_open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:block;">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg id="current_password_eye_closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                      <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                      <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                      <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                  </span>
                </div>
              </div>

              <!-- New Password -->
              <div class="form-group">
                <label>New Password *</label>
                <div class="input-wrap" style="position:relative;">
                  <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                  <input type="password" name="new_password" id="newPass" required placeholder="Min. 8 characters" style="padding-right:40px;">
                  <span onclick="togglePassword('newPass')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:#9ca3af;display:flex;align-items:center;">
                    <svg id="newPass_eye_open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:block;">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg id="newPass_eye_closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                      <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                      <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                      <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                  </span>
                </div>
              </div>

              <!-- Confirm New Password -->
              <div class="form-group">
                <label>Confirm New Password *</label>
                <div class="input-wrap" style="position:relative;">
                  <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                  <input type="password" name="confirm_password" id="confPass" required placeholder="Repeat new password" style="padding-right:40px;">
                  <span onclick="togglePassword('confPass')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:#9ca3af;display:flex;align-items:center;">
                    <svg id="confPass_eye_open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:block;">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg id="confPass_eye_closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                      <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                      <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                      <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                  </span>
                </div>
              </div>

              <div id="matchMsg" style="font-size:.82rem;margin-bottom:12px;display:none;"></div>
              <button type="submit" class="btn btn-primary btn-block">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Change Password
              </button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
// Avatar preview
document.getElementById('avatarInput').addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = function(e) {
    const preview  = document.getElementById('avatarPreview');
    const initials = document.getElementById('avatarInitials');
    preview.src = e.target.result;
    preview.style.display = 'block';
    if (initials) initials.style.display = 'none';
    const nameEl = document.getElementById('avatarFileName');
    nameEl.textContent = '📎 ' + file.name;
    nameEl.style.display = 'block';
  };
  reader.readAsDataURL(file);
});

// Password match checker
const newPass  = document.getElementById('newPass');
const confPass = document.getElementById('confPass');
const matchMsg = document.getElementById('matchMsg');
function checkMatch() {
  if (!confPass.value) { matchMsg.style.display = 'none'; return; }
  matchMsg.style.display = 'block';
  if (newPass.value === confPass.value) {
    matchMsg.style.color   = 'var(--success)';
    matchMsg.textContent   = '✓ Passwords match';
  } else {
    matchMsg.style.color   = 'var(--danger,#e53e3e)';
    matchMsg.textContent   = '✗ Passwords do not match';
  }
}
newPass.addEventListener('input', checkMatch);
confPass.addEventListener('input', checkMatch);

// Eye toggle
function togglePassword(inputId) {
  const input     = document.getElementById(inputId);
  const eyeOpen   = document.getElementById(inputId + '_eye_open');
  const eyeClosed = document.getElementById(inputId + '_eye_closed');
  if (input.type === 'password') {
    input.type              = 'text';
    eyeOpen.style.display   = 'none';
    eyeClosed.style.display = 'block';
  } else {
    input.type              = 'password';
    eyeOpen.style.display   = 'block';
    eyeClosed.style.display = 'none';
  }
}
</script>
</body>
</html>