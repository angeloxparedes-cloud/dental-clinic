<?php require_once __DIR__ . '/../shared/helpers.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
<style>
  html, body {
    margin: 0 !important;
    padding: 0 !important;
    min-height: 100vh !important;
    background: none !important;
  }

  .page-shell {
    min-height: 100vh;
    display: flex;
    position: relative;
    overflow: hidden;
  }

  /* ── LEFT PANEL ── */
  .left-panel {
    flex: 1;
    background: linear-gradient(145deg, #063a2e 0%, #0a5240 40%, #0d6b52 70%, #0f7a5e 100%);
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 2.5rem;
    overflow: hidden;
  }

  .left-panel::before {
    content: '';
    position: absolute;
    width: 500px; height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(29,200,140,0.18) 0%, transparent 70%);
    top: -100px; left: -100px;
    animation: orbPulse 7s ease-in-out infinite;
  }
  .left-panel::after {
    content: '';
    position: absolute;
    width: 350px; height: 350px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(10,220,160,0.14) 0%, transparent 70%);
    bottom: -80px; right: -60px;
    animation: orbPulse 9s ease-in-out infinite reverse;
  }

  @keyframes orbPulse {
    0%, 100% { transform: scale(1) translate(0,0); opacity: 0.7; }
    50%       { transform: scale(1.15) translate(20px, -20px); opacity: 1; }
  }

  .dot-grid {
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,0.08) 1.5px, transparent 1.5px);
    background-size: 30px 30px;
    pointer-events: none;
    z-index: 0;
  }

  .ring {
    position: absolute;
    border-radius: 50%;
    border: 1.5px solid rgba(255,255,255,0.08);
    pointer-events: none;
    animation: ringFloat linear infinite;
  }
  .ring-1 { width:180px; height:180px; top:8%;  left:5%;  animation-duration:14s; }
  .ring-2 { width:100px; height:100px; top:55%; left:70%; animation-duration:10s; animation-delay:-3s; }
  .ring-3 { width:240px; height:240px; top:70%; left:2%;  animation-duration:18s; animation-delay:-6s; }
  .ring-4 { width: 60px; height: 60px; top:20%; left:75%; animation-duration:8s;  animation-delay:-2s; }

  @keyframes ringFloat {
    0%   { transform: translateY(0) rotate(0deg); }
    50%  { transform: translateY(-18px) rotate(180deg); }
    100% { transform: translateY(0) rotate(360deg); }
  }

  .float-icon {
    position: absolute;
    opacity: 0.13;
    animation: iconDrift ease-in-out infinite;
    color: #fff;
    pointer-events: none;
  }
  .fi-1 { top:12%; left:12%; animation-duration:6s; }
  .fi-2 { top:30%; left:78%; animation-duration:8s;  animation-delay:-2s; }
  .fi-3 { top:62%; left:18%; animation-duration:7s;  animation-delay:-4s; }
  .fi-4 { top:75%; left:68%; animation-duration:9s;  animation-delay:-1s; }
  .fi-5 { top:48%; left:48%; animation-duration:11s; animation-delay:-5s; }

  @keyframes iconDrift {
    0%, 100% { transform: translateY(0) scale(1); opacity: 0.13; }
    50%       { transform: translateY(-14px) scale(1.1); opacity: 0.22; }
  }

  .left-content {
    position: relative;
    z-index: 1;
    text-align: center;
    color: #fff;
  }

  .left-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 50px;
    padding: 6px 16px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.85);
    margin-bottom: 2rem;
    backdrop-filter: blur(8px);
  }

  .left-badge-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #4DF0B0;
    animation: dotBlink 2s ease-in-out infinite;
  }

  @keyframes dotBlink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.3; }
  }

  .left-headline {
    font-size: 2.4rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 1rem;
    letter-spacing: -0.02em;
  }

  .left-headline span { color: #4DF0B0; }

  .left-sub {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.65);
    line-height: 1.6;
    max-width: 280px;
    margin: 0 auto 2.5rem;
  }

  .left-stats {
    display: flex;
    gap: 2rem;
    justify-content: center;
  }

  .stat-item { text-align: center; }

  .stat-num {
    font-size: 1.6rem;
    font-weight: 800;
    color: #4DF0B0;
    line-height: 1;
  }

  .stat-label {
    font-size: 0.72rem;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-top: 4px;
  }

  .stat-divider {
    width: 1px;
    background: rgba(255,255,255,0.15);
    align-self: stretch;
  }

  .tooth-illustration {
    position: relative;
    z-index: 1;
    margin-bottom: 2.5rem;
  }

  .tooth-illustration svg {
    filter: drop-shadow(0 0 30px rgba(77,240,176,0.3));
    animation: toothFloat 4s ease-in-out infinite;
  }

  @keyframes toothFloat {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-10px); }
  }

  /* ── RIGHT PANEL ── */
  .right-panel {
    width: 480px;
    flex-shrink: 0;
    background: #f5f7f6;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
  }

  .right-panel .auth-card {
    width: 100%;
    max-width: 400px;
    box-shadow: 0 4px 32px rgba(0,0,0,0.08);
  }

  @media (max-width: 768px) {
    .left-panel { display: none; }
    .right-panel { width: 100%; background: linear-gradient(145deg, #063a2e, #0a5240); }
    .right-panel .auth-card { background: #fff; }
  }

  /* ── EYE TOGGLE ── */
  .eye-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    color: #8a9bb0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
  }
  .eye-toggle:hover { color: #1D9E75; }

  /* ── FORGOT PASSWORD ── */
  .forgot-link {
    font-size: 0.82rem;
    color: #1D9E75;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
  }
  .forgot-link:hover {
    color: #0a5240;
    text-decoration: underline;
  }

</style>
</head>
<body>

<div class="page-shell">

  <!-- LEFT PANEL -->
  <div class="left-panel">
    <div class="dot-grid"></div>
    <div class="ring ring-1"></div>
    <div class="ring ring-2"></div>
    <div class="ring ring-3"></div>
    <div class="ring ring-4"></div>

    <div class="float-icon fi-1">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2C8 2 5 5 5 9c0 2.5.8 4.5 2 6l1 5c.3 1 1 2 2 2h4c1 0 1.7-1 2-2l1-5c1.2-1.5 2-3.5 2-6 0-4-3-7-7-7z"/></svg>
    </div>
    <div class="float-icon fi-2">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="float-icon fi-3">
      <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
    </div>
    <div class="float-icon fi-4">
      <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2C8 2 5 5 5 9c0 2.5.8 4.5 2 6l1 5c.3 1 1 2 2 2h4c1 0 1.7-1 2-2l1-5c1.2-1.5 2-3.5 2-6 0-4-3-7-7-7z"/></svg>
    </div>
    <div class="float-icon fi-5">
      <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
    </div>

    <div class="left-content">
      <div class="tooth-illustration">
        <svg width="90" height="90" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="50" cy="50" r="46" fill="rgba(255,255,255,0.07)" stroke="rgba(255,255,255,0.15)" stroke-width="1.5"/>
          <path d="M50 18C38 18 28 28 28 40C28 48 31 54 36 59L38 76C38.8 79 40 82 43 82H57C60 82 61.2 79 62 76L64 59C69 54 72 48 72 40C72 28 62 18 50 18Z" fill="rgba(77,240,176,0.25)" stroke="#4DF0B0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M42 40C42 40 44 34 50 34C56 34 58 40 58 40" stroke="rgba(255,255,255,0.4)" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
      </div>

      <div class="left-badge">
        <div class="left-badge-dot"></div>
        Auza Dental Clinic
      </div>

      <div class="left-headline">
        Your smile<br>deserves the<br><span>best care</span>
      </div>

      <p class="left-sub">
        Modern dental care backed by experienced professionals who put your comfort first.
      </p>

      <div class="left-stats">
        <div class="stat-item">
          <div class="stat-num">5k+</div>
          <div class="stat-label">Patients</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <div class="stat-num">12yr</div>
          <div class="stat-label">Experience</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <div class="stat-num">4.9★</div>
          <div class="stat-label">Rating</div>
        </div>
      </div>
    </div>

    <!-- ✅ Contact info pinned to bottom of left panel — horizontal row -->
    <div style="position:absolute;bottom:1.5rem;left:0;right:0;z-index:1;display:flex;align-items:center;justify-content:center;gap:20px;flex-wrap:nowrap;padding:0 1.5rem;">
      <div style="display:flex;align-items:center;gap:5px;color:rgba(255,255,255,0.5);font-size:0.75rem;white-space:nowrap;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="rgba(255,255,255,0.5)"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
        facebook.com/marieedsyl
      </div>
      <div style="width:1px;height:14px;background:rgba(255,255,255,0.2);"></div>
      <div style="display:flex;align-items:center;gap:5px;color:rgba(255,255,255,0.5);font-size:0.75rem;white-space:nowrap;">
        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.5)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        0917 123 4567
      </div>
      <div style="width:1px;height:14px;background:rgba(255,255,255,0.2);"></div>
      <div style="display:flex;align-items:center;gap:5px;color:rgba(255,255,255,0.5);font-size:0.75rem;white-space:nowrap;">
        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.5)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Poblacion, Talibon, Bohol
      </div>
    </div>

  </div>

  <!-- RIGHT PANEL -->
  <div class="right-panel">
    <div class="auth-card" style="position: relative;">

      <a href="<?= APP_URL ?>/" style="
        position: absolute; top: 1.25rem; left: 1.25rem;
        width: 32px; height: 32px; border-radius: 50%;
        border: 1.5px solid #d0dde0;
        display: flex; align-items: center; justify-content: center;
        color: #5a7080; text-decoration: none; transition: all 0.2s;
      " onmouseover="this.style.borderColor='#1D9E75';this.style.color='#1D9E75'"
         onmouseout="this.style.borderColor='#d0dde0';this.style.color='#5a7080'"
         title="Back to home">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
      </a>

      <div class="auth-logo">
        <div class="logo-wrap">
          <?php renderLogo(52); ?>
          <div class="logo-text">
            <span class="brand">Auza Dental</span>
            <span class="tagline">Clinic</span>
          </div>
        </div>
      </div>

      <h1 class="auth-title">Welcome Back</h1>
      <p class="auth-sub">Sign in to your account to continue</p>

      <?php if (!empty($error)): ?>
        <div class="alert alert-error">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      <?php showFlash(); ?>

      <form method="POST" action="?page=login" autocomplete="on">
        <div class="form-group">
          <label>Email Address</label>
          <div class="input-wrap">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <input type="email" name="email" placeholder="Enter your email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
          </div>
        </div>

        <!-- PASSWORD WITH EYE TOGGLE -->
        <div class="form-group">
          <label>Password</label>
          <div class="input-wrap" style="position:relative;">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <input type="password" id="login_password" name="password" placeholder="Enter your password" required style="padding-right:40px;">
            <button type="button" class="eye-toggle" onclick="togglePassword('login_password', this)" tabindex="-1">
              <svg id="login_password_eye_open" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              <svg id="login_password_eye_closed" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </button>
          </div>
          <!-- ✅ Forgot password moved here, below the input -->
          <div style="text-align:right; margin-top:6px;">
            <a href="?page=forgot_password" class="forgot-link">Forgot password?</a>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
          Sign In
        </button>
      </form>

      <hr class="divider">
      <p class="text-center text-sm text-gray">
        Don't have an account? <a href="?page=register"><strong>Create one</strong></a>
      </p>

    </div>
  </div>

</div>

<script>
function togglePassword(inputId, btn) {
  const input     = document.getElementById(inputId);
  const eyeOpen   = document.getElementById(inputId + '_eye_open');
  const eyeClosed = document.getElementById(inputId + '_eye_closed');
  if (input.type === 'password') {
    input.type = 'text';
    eyeOpen.style.display   = 'none';
    eyeClosed.style.display = 'block';
  } else {
    input.type = 'password';
    eyeOpen.style.display   = 'block';
    eyeClosed.style.display = 'none';
  }
}
</script>

</body>
</html>