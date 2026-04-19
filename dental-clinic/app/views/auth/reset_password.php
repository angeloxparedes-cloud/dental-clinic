<?php
if (!isLoggedIn() || !isPatient()) { redirect('login'); }
$currentPage = 'reset_password';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Set New Password – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
<style>
  .center-wrap { min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f5f7f6;padding:2rem; }
  .eye-toggle { position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:0;color:#8a9bb0;display:flex;align-items:center;z-index:2; }
  .eye-toggle:hover { color:#1D9E75; }
</style>
</head>
<body>
<div class="center-wrap">
  <div class="auth-card" style="max-width:420px;width:100%;box-shadow:0 4px 32px rgba(0,0,0,0.1);">
    <div class="auth-logo">
      <div class="logo-wrap">
        <?php renderLogo(52); ?>
        <div class="logo-text"><span class="brand">Auza Dental</span><span class="tagline">Clinic</span></div>
      </div>
    </div>

    <h1 class="auth-title">Set New Password</h1>
    <p class="auth-sub">You logged in with a temporary password. Please set a new password to continue.</p>

    <?php require_once __DIR__ . '/helpers.php'; showFlash(); ?>

    <div style="background:#FFF8E7;border:1px solid #F59E0B;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#92610A;display:flex;gap:10px;align-items:flex-start;">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
      <span>You must set a new password before you can use the system.</span>
    </div>

    <form method="POST" action="?page=save_new_password">
      <div class="form-group">
        <label>New Password <span style="color:red">*</span></label>
        <div class="input-wrap" style="position:relative;">
          <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          <input type="password" id="new_password" name="password" placeholder="Min. 8 characters" required style="padding-right:40px;">
          <button type="button" class="eye-toggle" onclick="toggleEye('new_password')" tabindex="-1">
            <svg id="new_password_eye_open" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <svg id="new_password_eye_closed" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
          </button>
        </div>
      </div>
      <div class="form-group">
        <label>Confirm New Password <span style="color:red">*</span></label>
        <div class="input-wrap" style="position:relative;">
          <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat new password" required style="padding-right:40px;">
          <button type="button" class="eye-toggle" onclick="toggleEye('confirm_password')" tabindex="-1">
            <svg id="confirm_password_eye_open" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <svg id="confirm_password_eye_closed" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block mt-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Save New Password
      </button>
    </form>
  </div>
</div>
<script>
function toggleEye(id) {
  const input = document.getElementById(id);
  const open  = document.getElementById(id + '_eye_open');
  const closed = document.getElementById(id + '_eye_closed');
  if (input.type === 'password') { input.type = 'text'; open.style.display='none'; closed.style.display='block'; }
  else { input.type = 'password'; open.style.display='block'; closed.style.display='none'; }
}
</script>
</body>
</html>