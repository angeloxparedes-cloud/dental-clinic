<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Auza Dental Clinic — Welcome</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --teal:    #1D9E75;
    --teal-dk: #0F6E56;
    --teal-lt: #E1F5EE;
    --navy:    #0d2b3e;
    --navy2:   #163a50;
    --cream:   #f8f5f0;
    --white:   #ffffff;
    --text:    #1a2e3b;
    --muted:   #5a7080;
    --accent:  #c8f0df;
  }

  html, body { height: 100%; }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--cream);
    color: var(--text);
    overflow-x: hidden;
  }

  .page {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 100vh;
  }

  .left {
    background: var(--navy);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2.5rem 3rem;
    position: relative;
    overflow: hidden;
  }

  .left::before {
    content: '';
    position: absolute;
    top: -80px; left: -80px;
    width: 320px; height: 320px;
    border-radius: 50%;
    background: rgba(29,158,117,0.12);
  }

  .left::after {
    content: '';
    position: absolute;
    bottom: -100px; right: -60px;
    width: 280px; height: 280px;
    border-radius: 50%;
    background: rgba(29,158,117,0.08);
  }

  .logo {
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    z-index: 1;
  }

  .logo-icon {
    width: 36px; height: 36px;
    background: var(--teal);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
  }

  .logo-icon svg { width: 20px; height: 20px; }

  .logo-text {
    font-family: 'Playfair Display', serif;
    font-size: 18px;
    color: var(--white);
    font-weight: 600;
    letter-spacing: 0.02em;
  }

  .illustration-wrap {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 1;
    padding: 1.5rem 0;
  }

  .illustration-wrap svg {
    width: 100%;
    max-width: 380px;
    height: auto;
    filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
  }

  .stats {
    display: flex;
    gap: 2rem;
    position: relative;
    z-index: 1;
  }

  .stat-num {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    color: var(--white);
    font-weight: 600;
    line-height: 1;
  }
  .stat-label {
    font-size: 12px;
    color: rgba(255,255,255,0.5);
    margin-top: 4px;
    letter-spacing: 0.04em;
  }
  .stat-divider {
    width: 1px;
    background: rgba(255,255,255,0.15);
  }

  .right {
    display: flex;
    flex-direction: column;
    padding: 2.5rem 3rem;
    background: var(--cream);
    overflow: hidden;
  }

  .topbar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .btn-login {
    padding: 9px 22px;
    border-radius: 8px;
    border: 1.5px solid var(--teal);
    background: transparent;
    color: var(--teal);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
  }

  .btn-login:hover {
    background: var(--teal);
    color: var(--white);
  }

  .carousel {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .slides-wrap {
    position: relative;
    min-height: 400px;
  }

  .slide {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.5s ease, transform 0.5s ease;
    pointer-events: none;
  }

  .slide.active {
    opacity: 1;
    transform: translateY(0);
    pointer-events: all;
  }

  .slide-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 1.25rem;
    width: fit-content;
  }

  .slide-badge .dot {
    width: 6px; height: 6px;
    border-radius: 50%;
  }

  .slide h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(28px, 3vw, 42px);
    font-weight: 600;
    color: var(--text);
    line-height: 1.2;
    margin-bottom: 1rem;
    max-width: 440px;
  }

  .slide p {
    font-size: 15px;
    color: var(--muted);
    line-height: 1.75;
    max-width: 400px;
    margin-bottom: 2rem;
    font-weight: 300;
  }

  .slide-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 2rem;
  }

  .chip {
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 12px;
    border: 1px solid #d8e8e2;
    color: var(--muted);
    background: var(--white);
  }

  .slide-cta {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
  }

  .btn-book {
    padding: 12px 28px;
    border-radius: 10px;
    border: none;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    transition: all 0.2s;
    text-decoration: none;
  }

  .btn-outline {
    padding: 12px 24px;
    border-radius: 10px;
    border: 1px solid #d0dde0;
    background: transparent;
    font-size: 14px;
    color: var(--muted);
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
  }

  .btn-outline:hover { border-color: var(--teal); color: var(--teal); }

  .carousel-nav {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-top: 2.5rem;
  }

  .dots {
    display: flex;
    gap: 7px;
    align-items: center;
  }

  .dot-btn {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #ccd8db;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
    padding: 0;
  }

  .dot-btn.on {
    width: 22px;
    border-radius: 4px;
    background: var(--teal);
  }

  .arr-btn {
    width: 38px; height: 38px;
    border-radius: 50%;
    border: 1px solid #d0dde0;
    background: var(--white);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
    color: var(--muted);
    font-size: 16px;
  }

  .arr-btn:hover { border-color: var(--teal); color: var(--teal); }

  .slide-counter {
    font-size: 12px;
    color: var(--muted);
    margin-left: auto;
  }

  .progress-bar {
    height: 2px;
    background: #e2eaed;
    border-radius: 2px;
    margin-top: 1.5rem;
    overflow: hidden;
  }

  .progress-fill {
    height: 100%;
    background: var(--teal);
    border-radius: 2px;
    transition: width 0.1s linear;
  }


  @media (max-width: 800px) {
    .page { grid-template-columns: 1fr; }
    .left { min-height: 260px; padding: 2rem; }
    .illustration-wrap { padding: 1rem 0; }
    .illustration-wrap svg { max-width: 220px; }
    .stats { gap: 1.5rem; }
    .right { padding: 2rem 1.5rem; }
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .logo      { animation: fadeUp 0.6s ease both; animation-delay: 0.1s; }
  .illustration-wrap { animation: fadeUp 0.7s ease both; animation-delay: 0.2s; }
  .stats     { animation: fadeUp 0.6s ease both; animation-delay: 0.35s; }
  .topbar    { animation: fadeUp 0.6s ease both; animation-delay: 0.15s; }
  .carousel  { animation: fadeUp 0.7s ease both; animation-delay: 0.3s; }
</style>
</head>
<body>

<div class="page">

  <!-- LEFT PANEL -->
  <div class="left">

    <div class="logo">
      <div class="logo-icon">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2C9.5 2 7 4 7 7c0 1.5.5 3 1 4.5C8.5 13 9 15 9 17c0 2 1 4 3 4s3-2 3-4c0-2 .5-4 1-5.5C16.5 10 17 8.5 17 7c0-3-2.5-5-5-5z" fill="white" opacity="0.9"/>
          <circle cx="9.5" cy="7" r="1" fill="rgba(255,255,255,0.4)"/>
        </svg>
      </div>
      <span class="logo-text">Auza Dental Clinic</span>
    </div>

    <div class="illustration-wrap">
      <svg viewBox="0 0 340 400" xmlns="http://www.w3.org/2000/svg">
        <circle cx="170" cy="200" r="160" fill="rgba(29,158,117,0.1)"/>
        <circle cx="170" cy="200" r="130" fill="rgba(29,158,117,0.08)"/>
        <rect x="80" y="300" width="180" height="18" rx="9" fill="#0F6E56" opacity="0.6"/>
        <rect x="100" y="310" width="140" height="60" rx="8" fill="#085041" opacity="0.5"/>
        <rect x="190" y="190" width="80" height="130" rx="12" fill="#0F6E56" opacity="0.7"/>
        <rect x="90" y="280" width="160" height="35" rx="10" fill="#0F6E56" opacity="0.7"/>
        <ellipse cx="155" cy="268" rx="62" ry="18" fill="#163a50" opacity="0.9"/>
        <rect x="96" y="260" width="120" height="20" rx="10" fill="#1a4a60" opacity="0.8"/>
        <ellipse cx="155" cy="240" rx="38" ry="28" fill="#e8f4f0"/>
        <path d="M135 222 L155 245 L175 222" fill="none" stroke="#1D9E75" stroke-width="2" opacity="0.6"/>
        <ellipse cx="110" cy="230" rx="28" ry="24" fill="#f5d5b0"/>
        <path d="M84 218 Q95 200 110 205 Q125 200 136 218" fill="#3d2b1f"/>
        <path d="M100 226 Q104 228 108 226" stroke="#8B6347" stroke-width="1.5" fill="none" stroke-linecap="round"/>
        <path d="M113 226 Q117 228 121 226" stroke="#8B6347" stroke-width="1.5" fill="none" stroke-linecap="round"/>
        <path d="M104 236 Q110 240 116 236" stroke="#c0896b" stroke-width="1.5" fill="none" stroke-linecap="round"/>
        <ellipse cx="230" cy="240" rx="30" ry="45" fill="#f0f8f5"/>
        <rect x="202" y="215" width="56" height="70" rx="6" fill="#f0f8f5"/>
        <line x1="230" y1="215" x2="230" y2="280" stroke="#d0e8e0" stroke-width="1.5"/>
        <rect x="210" y="215" width="40" height="65" rx="4" fill="#1D9E75" opacity="0.15"/>
        <path d="M218 225 Q210 235 212 245 Q214 252 220 252" stroke="#0F6E56" stroke-width="2" fill="none" opacity="0.7"/>
        <circle cx="220" cy="254" r="4" fill="#0F6E56" opacity="0.7"/>
        <ellipse cx="230" cy="175" rx="26" ry="28" fill="#f0c8a0"/>
        <rect x="207" y="182" width="46" height="24" rx="8" fill="#9FE1CB" opacity="0.85"/>
        <line x1="207" y1="188" x2="253" y2="188" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>
        <ellipse cx="221" cy="174" rx="4" ry="4.5" fill="white"/>
        <ellipse cx="239" cy="174" rx="4" ry="4.5" fill="white"/>
        <ellipse cx="222" cy="175" rx="2.5" ry="3" fill="#3d2b1f"/>
        <ellipse cx="240" cy="175" rx="2.5" ry="3" fill="#3d2b1f"/>
        <path d="M217 169 Q221 167 225 169" stroke="#6b4e3d" stroke-width="1.5" fill="none" stroke-linecap="round"/>
        <path d="M235 169 Q239 167 243 169" stroke="#6b4e3d" stroke-width="1.5" fill="none" stroke-linecap="round"/>
        <path d="M206 170 Q210 148 230 147 Q250 148 254 170" fill="#4a3020"/>
        <ellipse cx="230" cy="156" rx="26" ry="12" fill="#5DCAA5" opacity="0.7"/>
        <path d="M208 235 Q185 235 168 228" stroke="#f0c8a0" stroke-width="14" fill="none" stroke-linecap="round"/>
        <line x1="168" y1="228" x2="148" y2="235" stroke="#ccc" stroke-width="3"/>
        <circle cx="145" cy="236" r="6" fill="none" stroke="#aaa" stroke-width="2"/>
        <ellipse cx="230" cy="115" rx="30" ry="10" fill="#163a50"/>
        <rect x="225" y="90" width="10" height="28" rx="5" fill="#0F6E56" opacity="0.6"/>
        <ellipse cx="230" cy="125" rx="22" ry="7" fill="#FAC775" opacity="0.35"/>
        <path d="M210 125 L195 220 L165 220 L180 125 Z" fill="rgba(250,199,117,0.06)"/>
        <g opacity="0.7">
          <text x="50" y="170" font-size="18" fill="#1D9E75">✦</text>
          <text x="290" y="140" font-size="12" fill="#5DCAA5">✦</text>
          <text x="60" y="310" font-size="10" fill="#9FE1CB">✦</text>
          <text x="300" y="290" font-size="14" fill="#1D9E75">✦</text>
        </g>
        <circle cx="68" cy="130" r="28" fill="rgba(29,158,117,0.15)"/>
        <path d="M68 115 C63 115 58 119 58 124 C58 127 59 130 60 133 C61.5 137 62 140 62 143 C62 146 63 149 68 149 C73 149 74 146 74 143 C74 140 74.5 137 76 133 C77 130 78 127 78 124 C78 119 73 115 68 115 Z" fill="#1D9E75" opacity="0.8"/>
        <circle cx="290" cy="320" r="22" fill="rgba(29,158,117,0.15)"/>
        <path d="M280 320 L287 327 L300 313" stroke="#1D9E75" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <div class="stats">
      <div class="stat-item">
        <div class="stat-num">5,000+</div>
        <div class="stat-label">Happy patients</div>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <div class="stat-num">12 yrs</div>
        <div class="stat-label">Experience</div>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <div class="stat-num">4.9★</div>
        <div class="stat-label">Patient rating</div>
      </div>
    </div>

  </div>

  <!-- RIGHT PANEL -->
  <div class="right">

    <div class="topbar">
      <span style="font-size:13px; color:var(--muted);">Already have an account?</span>
      <a href="<?= APP_URL ?>/?page=login" class="btn-login">Log in</a>
    </div>

    <div class="carousel">
      <div class="slides-wrap">

        <!-- Slide 1: Welcome -->
        <div class="slide active" id="sl0">
          <div class="slide-badge" style="background:#E1F5EE; color:#0F6E56;">
            <span class="dot" style="background:#1D9E75;"></span>
            Welcome
          </div>
          <h1>Your smile deserves the best care</h1>
          <p>Compassionate, modern dental care for your whole family. Backed by a team of experienced professionals who put your comfort first.</p>
          <div class="slide-chips">
            <span class="chip">Family-friendly</span>
            <span class="chip">Modern equipment</span>
            <span class="chip">Gentle approach</span>
          </div>
          <div class="slide-cta">
            <a href="<?= APP_URL ?>/?page=register" class="btn-book" style="background:var(--teal);color:white;">Book an appointment</a>
            <a href="<?= APP_URL ?>/?page=login" class="btn-outline">Patient login</a>
          </div>
        </div>

        <!-- Slide 2: Cosmetic -->
        <div class="slide" id="sl1">
          <div class="slide-badge" style="background:#EEEDFE; color:#534AB7;">
            <span class="dot" style="background:#7F77DD;"></span>
            Cosmetic dentistry
          </div>
          <h1>Achieve the smile you've always wanted</h1>
          <p>From professional whitening to custom porcelain veneers — our cosmetic treatments are designed to boost your confidence naturally.</p>
          <div class="slide-chips">
            <span class="chip">Laser whitening</span>
            <span class="chip">Porcelain veneers</span>
            <span class="chip">Smile makeovers</span>
          </div>
          <div class="slide-cta">
            <a href="<?= APP_URL ?>/?page=register" class="btn-book" style="background:#534AB7;color:white;">Book a consultation</a>
            <a href="<?= APP_URL ?>/?page=login" class="btn-outline">Patient login</a>
          </div>
        </div>

        <!-- Slide 3: Restorative -->
        <div class="slide" id="sl2">
          <div class="slide-badge" style="background:#E6F1FB; color:#185FA5;">
            <span class="dot" style="background:#378ADD;"></span>
            Restorative care
          </div>
          <h1>Restore function, reclaim your confidence</h1>
          <p>Dental implants, crowns, bridges, and dentures — durable solutions built to last, designed to look and feel completely natural.</p>
          <div class="slide-chips">
            <span class="chip">Dental implants</span>
            <span class="chip">Crowns & bridges</span>
            <span class="chip">Dentures</span>
          </div>
          <div class="slide-cta">
            <a href="<?= APP_URL ?>/?page=register" class="btn-book" style="background:#185FA5;color:white;">Learn more</a>
            <a href="<?= APP_URL ?>/?page=login" class="btn-outline">Patient login</a>
          </div>
        </div>

        <!-- Slide 4: Pediatric -->
        <div class="slide" id="sl3">
          <div class="slide-badge" style="background:#FAECE7; color:#993C1D;">
            <span class="dot" style="background:#D85A30;"></span>
            Pediatric dentistry
          </div>
          <h1>Gentle dental care for your little ones</h1>
          <p>Our child-friendly clinic makes every visit fun and stress-free. Early dental care builds healthy habits that last a lifetime.</p>
          <div class="slide-chips">
            <span class="chip">Kid-friendly space</span>
            <span class="chip">Preventive care</span>
            <span class="chip">Pit & fissure sealants</span>
          </div>
          <div class="slide-cta">
            <a href="<?= APP_URL ?>/?page=register" class="btn-book" style="background:#D85A30;color:white;">Book for your child</a>
            <a href="<?= APP_URL ?>/?page=login" class="btn-outline">Patient login</a>
          </div>
        </div>

        <!-- Slide 5: Booking -->
        <div class="slide" id="sl4">
          <div class="slide-badge" style="background:#EAF3DE; color:#3B6D11;">
            <span class="dot" style="background:#639922;"></span>
            Easy online booking
          </div>
          <h1>Schedule your visit in under a minute</h1>
          <p>No waiting on hold. Pick your preferred dentist, choose a time slot, and receive instant confirmation — all from your phone or laptop.</p>
          <div class="slide-chips">
            <span class="chip">Online scheduling</span>
            <span class="chip">SMS reminders</span>
            <span class="chip">PhilHealth accepted</span>
          </div>
          <div class="slide-cta">
            <a href="<?= APP_URL ?>/?page=register" class="btn-book" style="background:#3B6D11;color:white;">Get started now</a>
            <a href="<?= APP_URL ?>/?page=login" class="btn-outline">Patient login</a>
          </div>
        </div>

      </div>

      <!-- Progress bar -->
      <div class="progress-bar"><div class="progress-fill" id="prog" style="width:0%"></div></div>

      <!-- Navigation -->
      <div class="carousel-nav">
        <button class="arr-btn" onclick="move(-1)">&#8592;</button>
        <div class="dots" id="dots"></div>
        <button class="arr-btn" onclick="move(1)">&#8594;</button>
        <span class="slide-counter" id="counter">1 / 5</span>
      </div>

    </div>

  </div>

</div>

<script>
  const total = 5;
  const duration = 5000;
  let cur = 0, timer, progTimer, progStart;

  const dotsEl = document.getElementById('dots');
  for (let i = 0; i < total; i++) {
    const d = document.createElement('button');
    d.className = 'dot-btn' + (i === 0 ? ' on' : '');
    d.onclick = () => go(i);
    dotsEl.appendChild(d);
  }

  const colors = ['#1D9E75','#534AB7','#185FA5','#D85A30','#3B6D11'];

  function go(n) {
    document.querySelectorAll('.slide')[cur].classList.remove('active');
    cur = (n + total) % total;
    document.querySelectorAll('.slide')[cur].classList.add('active');
    document.querySelectorAll('.dot-btn').forEach((d, i) => {
      d.classList.toggle('on', i === cur);
      d.style.background = i === cur ? colors[cur] : '';
    });
    document.getElementById('counter').textContent = (cur + 1) + ' / ' + total;
    resetProgress();
    clearInterval(timer);
    timer = setInterval(() => move(1), duration);
  }

  function move(d) { go(cur + d); }

  function resetProgress() {
    const fill = document.getElementById('prog');
    fill.style.transition = 'none';
    fill.style.width = '0%';
    clearInterval(progTimer);
    const step = 50;
    let elapsed = 0;
    setTimeout(() => {
      fill.style.transition = '';
      progTimer = setInterval(() => {
        elapsed += step;
        fill.style.width = Math.min(100, (elapsed / duration) * 100) + '%';
        if (elapsed >= duration) clearInterval(progTimer);
      }, step);
    }, 30);
  }

  timer = setInterval(() => move(1), duration);
  resetProgress();
</script>

</body>
</html>