<?php
if (defined('HELPERS_LOADED')) return;
define('HELPERS_LOADED', true);

// Shared SVG Logo
function renderLogo($size = 44) {
    echo <<<SVG
<svg width="{$size}" height="{$size}" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" class="logo-icon">
  <defs>
    <linearGradient id="lg1" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#1a6fa8"/>
      <stop offset="100%" style="stop-color:#28b4a0"/>
    </linearGradient>
  </defs>
  <!-- Tooth shape -->
  <ellipse cx="50" cy="35" rx="28" ry="22" fill="url(#lg1)"/>
  <ellipse cx="50" cy="35" rx="22" ry="17" fill="white" opacity="0.15"/>
  <!-- Tooth roots -->
  <path d="M36 52 Q33 72 30 80 Q28 86 32 86 Q36 86 38 78 Q40 72 42 65" fill="url(#lg1)"/>
  <path d="M50 55 Q50 72 50 82 Q50 88 54 88 Q58 88 58 82 Q58 72 58 55" fill="url(#lg1)"/>
  <path d="M64 52 Q67 72 70 80 Q72 86 68 86 Q64 86 62 78 Q60 72 58 65" fill="url(#lg1)"/>
  <!-- Cross/plus highlight -->
  <rect x="47" y="22" width="6" height="20" rx="3" fill="white" opacity="0.6"/>
  <rect x="38" y="31" width="24" height="6" rx="3" fill="white" opacity="0.6"/>
</svg>
SVG;
}

// Flash message helper
function showFlash() {
    if (!empty($_GET['msg'])) {
        $msg  = htmlspecialchars($_GET['msg']);
        $type = in_array($_GET['msg_type'] ?? '', ['success','error','info']) ? $_GET['msg_type'] : 'info';
        $icons = [
            'success' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'error'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'info'    => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        ];
        echo "<div class='alert alert-{$type}'>{$icons[$type]} {$msg}</div>";
    }
}
?>
