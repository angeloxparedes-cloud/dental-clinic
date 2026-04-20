<?php require_once __DIR__ . '/../shared/helpers.php'; ?>
<?php
// Check if this email has an approved reset with a temp password waiting
$tempPassword  = null;
$requestStatus = null;

if (!empty($_GET['email'])) {
    $checkEmail = sanitize($_GET['email']);
    $db = getDB();
    $stmt = $db->prepare("
        SELECT pr.status, pr.temp_password_plain, u.password, pr.id
        FROM password_resets pr
        JOIN users u ON pr.user_id = u.id
        WHERE u.email = ? AND u.role = 'patient'
        ORDER BY pr.created_at DESC LIMIT 1
    ");
    $stmt->bind_param('s', $checkEmail);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $requestStatus = $row['status'];

        // ✅ FIX: Read temp password directly from DB if approved
        if ($row['status'] === 'approved' && !empty($row['temp_password_plain'])) {
            $tempPassword = $row['temp_password_plain'];
        }
    }
}

// Fallback: check session (set by requestReset() controller after form POST)
if (empty($tempPassword) && !empty($_SESSION['show_temp_pass_email'])) {
    $tempPassword = $_SESSION['show_temp_pass'] ?? null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
<style>
  html, body { margin:0!important;padding:0!important;min-height:100vh!important;background:none!important; }
  .page-shell { min-height:100vh;display:flex;position:relative;overflow:hidden; }
  .left-panel {
    flex:1;background:linear-gradient(145deg,#063a2e 0%,#0a5240 40%,#0d6b52 70%,#0f7a5e 100%);
    position:relative;display:flex;flex-direction:column;align-items:center;justify-content:center;
    padding:3rem 2.5rem;overflow:hidden;
  }
  .left-panel::before { content:'';position:absolute;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(29,200,140,0.18) 0%,transparent 70%);top:-100px;left:-100px;animation:orbPulse 7s ease-in-out infinite; }
  .left-panel::after  { content:'';position:absolute;width:350px;height:350px;border-radius:50%;background:radial-gradient(circle,rgba(10,220,160,0.14) 0%,transparent 70%);bottom:-80px;right:-60px;animation:orbPulse 9s ease-in-out infinite reverse; }
  @keyframes orbPulse { 0%,100%{transform:scale(1) translate(0,0);opacity:.7} 50%{transform:scale(1.15) translate(20px,-20px);opacity:1} }
  .dot-grid { position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,0.08) 1.5px,transparent 1.5px);background-size:30px 30px;pointer-events:none;z-index:0; }
  .left-content { position:relative;z-index:1;text-align:center;color:#fff; }
  .left-headline { font-size:2.2rem;font-weight:800;line-height:1.2;margin-bottom:1rem; }
  .left-headline span { color:#4DF0B0; }
  .left-sub { font-size:0.95rem;color:rgba(255,255,255,0.65);line-height:1.6;max-width:280px;margin:0 auto 2rem; }
  .right-panel { width:480px;flex-shrink:0;background:#f5f7f6;display:flex;align-items:center;justify-content:center;padding:2rem;position:relative; }
  .right-panel .auth-card { width:100%;max-width:400px;box-shadow:0 4px 32px rgba(0,0,0,0.08); }
  @media(max-width:768px){.left-panel{display:none}.right-panel{width:100%;background:linear-gradient(145deg,#063a2e,#0a5240)}.right-panel .auth-card{background:#fff}}
  .steps { display:flex;flex-direction:column;gap:14px;text-align:left;max-width:280px;margin:0 auto 2rem; }
  .step-item { display:flex;align-items:flex-start;gap:12px;color:rgba(255,255,255,0.8);font-size:0.88rem; }
  .step-num { width:24px;height:24px;border-radius:50%;background:rgba(77,240,176,0.2);border:1px solid rgba(77,240,176,0.4);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#4DF0B0;font-weight:700;font-size:0.8rem; }
  .step-item.done { color:rgba(255,255,255,0.4); text-decoration:line-through; }
  .step-item.done .step-num { background:rgba(77,240,176,0.5); }

  /* Temp password reveal box */
  .temp-pass-box {
    background: linear-gradient(135deg, #e8faf3, #d0f5e6);
    border: 2px solid #1D9E75;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.25rem;
    text-align: center;
  }
  .temp-pass-box .tp-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #0a5240;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 8px;
  }
  .temp-pass-box .tp-value {
    font-size: 1.4rem;
    font-weight: 800;
    color: #063a2e;
    letter-spacing: 0.1em;
    background: rgba(255,255,255,0.7);
    border: 1px dashed #1D9E75;
    border-radius: 8px;
    padding: 8px 14px;
    display: inline-block;
    margin-bottom: 10px;
    cursor: pointer;
    position: relative;
  }
  .temp-pass-box .tp-note {
    font-size: 0.78rem;
    color: #0a5240;
    line-height: 1.5;
  }
  .pending-box {
    background: #fffbeb;
    border: 1.5px solid #f59e0b;
    border-radius: 10px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: 10px;
  }
  .pending-box svg { flex-shrink: 0; color: #d97706; margin-top: 1px; }
  .pending-box p { font-size: 0.85rem; color: #92400e; margin: 0; line-height: 1.5; }
</style>
</head>
<body>
<div class="page-shell">
  <div class="left-panel">
    <div class="dot-grid"></div>
    <div class="left-content">
      <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom:1.5rem;filter:drop-shadow(0 0 20px rgba(77,240,176,0.3))">
        <circle cx="50" cy="50" r="46" fill="rgba(255,255,255,0.07)" stroke="rgba(255,255,255,0.15)" stroke-width="1.5"/>
        <path d="M50 18C38 18 28 28 28 40C28 48 31 54 36 59L38 76C38.8 79 40 82 43 82H57C60 82 61.2 79 62 76L64 59C69 54 72 48 72 40C72 28 62 18 50 18Z" fill="rgba(77,240,176,0.25)" stroke="#4DF0B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <div class="left-headline">Forgot your<br><span>password?</span></div>
      <p class="left-sub">No worries! Submit a request and our admin will approve a temporary password for you.</p>
      <div class="steps">
        <div class="step-item <?= ($requestStatus === 'pending' || $requestStatus === 'approved') ? 'done' : '' ?>">
          <div class="step-num">1</div>Enter your registered email below
        </div>
        <div class="step-item <?= $requestStatus === 'approved' ? 'done' : '' ?>">
          <div class="step-num">2</div>Admin reviews and approves your request
        </div>
        <div class="step-item <?= $requestStatus === 'approved' ? 'done' : '' ?>">
          <div class="step-num">3</div>Your temporary password appears on this page
        </div>
        <div class="step-item">
          <div class="step-num">4</div>Log in and set a new password
        </div>
      </div>
    </div>
  </div>

  <div class="right-panel">
    <div class="auth-card" style="position:relative;">
      <a href="?page=login" style="position:absolute;top:1.25rem;left:1.25rem;width:32px;height:32px;border-radius:50%;border:1.5px solid #d0dde0;display:flex;align-items:center;justify-content:center;color:#5a7080;text-decoration:none;transition:all 0.2s;" onmouseover="this.style.borderColor='#1D9E75';this.style.color='#1D9E75'" onmouseout="this.style.borderColor='#d0dde0';this.style.color='#5a7080'" title="Back to login">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
      </a>

      <div class="auth-logo">
        <div class="logo-wrap">
          <?php renderLogo(52); ?>
          <div class="logo-text"><span class="brand">Auza Dental</span><span class="tagline">Clinic</span></div>
        </div>
      </div>

      <h1 class="auth-title">Reset Password</h1>
      <p class="auth-sub">Enter your email and we'll notify the admin</p>

      <?php showFlash(); ?>

      <?php if (!empty($tempPassword)): ?>
        <!-- ── TEMP PASSWORD REVEALED ── -->
        <div class="temp-pass-box">
          <div class="tp-label">✅ Your Temporary Password</div>
          <div class="tp-value" onclick="copyTempPass(this)" title="Click to copy">
            <?= htmlspecialchars($tempPassword) ?>
          </div>
          <div class="tp-note">
            Click the password to copy it.<br>
            Use it to <a href="?page=login" style="color:#1D9E75;font-weight:600;">sign in</a>, then you'll be asked to set a new password.
          </div>
        </div>
      <?php elseif ($requestStatus === 'pending'): ?>
        <!-- ── WAITING FOR ADMIN ── -->
        <div class="pending-box">
          <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <p>Your request is <strong>pending admin approval</strong>. Please check back here soon — your temporary password will appear on this page once approved.</p>
        </div>
      <?php endif; ?>

      <form method="POST" action="?page=request_reset">
        <div class="form-group">
          <label>Email Address</label>
          <div class="input-wrap">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <input type="email" name="email" placeholder="Enter your registered email" required
                   value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block mt-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
          Check Status / Submit Request
        </button>
      </form>

      <hr class="divider">
      <p class="text-center text-sm text-gray">
        Remember your password? <a href="?page=login"><strong>Sign In</strong></a>
      </p>
    </div>
  </div>
</div>

<script>
function copyTempPass(el) {
  const text = el.innerText.trim();
  navigator.clipboard.writeText(text).then(() => {
    const orig = el.innerHTML;
    el.innerHTML = '✅ Copied!';
    setTimeout(() => el.innerHTML = orig, 1500);
  });
}

<?php if ($requestStatus === 'pending' && !empty($_GET['email'])): ?>
// Auto-poll every 5 seconds while request is pending
(function() {
  const email = <?= json_encode($_GET['email']) ?>;
  const pollInterval = 5000; // 5 seconds

  function checkStatus() {
    fetch('?page=check_reset_status&email=' + encodeURIComponent(email))
      .then(r => r.json())
      .then(data => {
        if (data.status === 'approved') {
          // Reload the page so the temp password shows up
          window.location.href = '?page=forgot_password&email=' + encodeURIComponent(email);
        }
      })
      .catch(() => {}); // Silently ignore network errors
  }

  const timer = setInterval(checkStatus, pollInterval);

  // Show a subtle "checking..." indicator
  const pendingBox = document.querySelector('.pending-box p');
  if (pendingBox) {
    let dots = 0;
    setInterval(() => {
      dots = (dots + 1) % 4;
      const indicator = pendingBox.querySelector('.checking');
      if (indicator) indicator.textContent = 'Checking' + '.'.repeat(dots);
    }, 600);

    pendingBox.insertAdjacentHTML('beforeend', ' <span class="checking" style="color:#d97706;font-size:0.8rem;">Checking...</span>');
  }
})();
<?php endif; ?>
</script>
</body>
</html>