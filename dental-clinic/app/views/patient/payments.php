<?php $currentPage = 'patient_payments'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Payments – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div>
        <div class="topbar-title">My Payments</div>
        <div class="text-sm text-gray">Submit and track your payments</div>
      </div>
      <?php if (!empty($payable)): ?>
      <button class="btn btn-primary btn-sm" onclick="openModal('payModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Submit Payment
      </button>
      <?php endif; ?>
    </div>
    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; showFlash(); ?>

      <!-- Payment method info banner -->
      <div style="background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:var(--radius);padding:20px 24px;margin-bottom:24px;color:white;display:flex;align-items:center;gap:16px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="opacity:.8;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        <div>
          <div style="font-weight:700;font-size:1rem;margin-bottom:4px;">Accepted Payment Methods</div>
          <div style="opacity:.9;font-size:.88rem;">💵 Cash &nbsp;|&nbsp; 📱 GCash &nbsp;|&nbsp; 💳 Maya &nbsp;|&nbsp; 💳 Credit/Debit Card</div>
          <div style="opacity:.75;font-size:.8rem;margin-top:4px;">Payments are verified by admin. Status updates within 24 hours.</div>
        </div>
      </div>

      <!-- Payments history -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">Payment History</div>
        </div>
        <?php if (empty($payments)): ?>
          <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <h3>No Payments Yet</h3>
            <p>Submit a payment for your confirmed or completed appointments</p>
          </div>
        <?php else: ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Service</th>
                <th>Dentist</th>
                <th>Appointment</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Notes</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($payments as $p): ?>
              <tr>
                <td class="font-bold"><?= htmlspecialchars($p['service_name']) ?></td>
                <td>Dr. <?= htmlspecialchars($p['dentist_name']) ?></td>
                <td>
                  <div><?= formatDate($p['appointment_date']) ?></div>
                  <div class="text-sm text-gray"><?= formatTime($p['appointment_time']) ?></div>
                </td>
                <td class="font-bold text-primary">₱<?= number_format($p['amount'],2) ?></td>
                <td>
                  <?php
                    $icons = ['cash'=>'💵','gcash'=>'📱','maya'=>'💳','card'=>'💳'];
                    echo ($icons[$p['method']]??'💵').' '.strtoupper($p['method']);
                  ?>
                </td>
                <td class="text-sm"><?= htmlspecialchars($p['reference_no'] ?: '—') ?></td>
                <td class="text-sm">
                  <?php if (!empty($p['notes'])): ?>
                    <span title="<?= htmlspecialchars($p['notes']) ?>"
                      style="display:inline-block;max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#5a7080;cursor:help;">
                      <?= htmlspecialchars($p['notes']) ?>
                    </span>
                  <?php else: ?>
                    <span style="color:#ccc;">—</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php
                    $pBadge = ['pending'=>'badge-pending','paid'=>'badge-completed','refunded'=>'badge-cancelled'];
                    echo "<span class='badge ".($pBadge[$p['status']]?:'badge-pending')."'>".ucfirst($p['status'])."</span>";
                  ?>
                  <?php if ($p['status'] === 'paid'): ?>
                    <a href="<?= APP_URL ?>/download_receipt.php?id=<?= $p['id'] ?>"
   target="_blank"
   style="display:inline-block;margin-top:5px;padding:3px 10px;font-size:12px;font-weight:600;color:#0d9488;background:#dcfce7;border-radius:999px;text-decoration:none;">
  Get Receipt 🧾
</a>
                  <?php endif; ?>
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
</div>

<!-- Submit Payment Modal -->
<div class="modal-overlay" id="payModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Submit Payment</div>
      <button class="modal-close" onclick="closeModal('payModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="?page=submit_payment">
      <div class="modal-body">
        <div class="form-group">
          <label>Select Appointment *</label>
          <div class="input-wrap no-icon">
            <select name="appointment_id" required onchange="fillAmount(this)">
              <option value="">-- Choose appointment --</option>
              <?php foreach ($payable as $a): ?>
                <option value="<?= $a['id'] ?>" data-price="<?= $a['price'] ?>">
                  <?= htmlspecialchars($a['service_name']) ?> – <?= formatDate($a['appointment_date']) ?> (₱<?= number_format($a['price'],2) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Amount (₱) *</label>
          <div class="input-wrap">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <input type="number" name="amount" id="amountField" step="0.01" min="1" required placeholder="0.00">
          </div>
        </div>
        <div class="form-group">
          <label>Payment Method *</label>
          <div class="input-wrap no-icon">
            <select name="method" required onchange="toggleRef(this)">
              <option value="cash">💵 Cash</option>
              <option value="gcash">📱 GCash</option>
              <option value="maya">💳 Maya</option>
              <option value="card">💳 Credit/Debit Card</option>
            </select>
          </div>
        </div>
        <div class="form-group" id="refGroup">
          <label>Reference / Transaction No.</label>
          <div class="input-wrap">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
            <input type="text" name="reference_no" placeholder="e.g. GCash ref number">
          </div>
        </div>
        <div class="form-group">
          <label>Notes (optional)</label>
          <div class="input-wrap no-icon">
            <textarea name="notes" placeholder="Any additional details..."></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('payModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit Payment</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function fillAmount(sel) {
  const opt = sel.options[sel.selectedIndex];
  if (opt.value) document.getElementById('amountField').value = opt.dataset.price;
}
function toggleRef(sel) {
  document.getElementById('refGroup').style.display = sel.value === 'cash' ? 'none' : 'block';
}
document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', function(e) { if(e.target===this) this.classList.remove('open'); });
});
</script>
</body>
</html>