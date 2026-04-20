<?php $currentPage = 'admin_payments'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Payments – Auza Dental Clinic</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require __DIR__ . '/../shared/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-title">Payments</div>
    </div>
    <div class="page-content">
      <?php require_once __DIR__ . '/../shared/helpers.php'; showFlash(); ?>

      <!-- Filter -->
      <div class="filter-bar">
        <?php
          $filters = ['' => 'All', 'pending' => 'Pending', 'paid' => 'Paid', 'refunded' => 'Refunded'];
          $current = $_GET['status'] ?? '';
          foreach ($filters as $val => $label):
        ?>
          <a href="?page=admin_payments<?= $val ? '&status='.$val : '' ?>" class="filter-btn <?= $current === $val ? 'active' : '' ?>"><?= $label ?></a>
        <?php endforeach; ?>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <?= $filters[$current] ?? 'All' ?> Payments
            <span style="font-size:.85rem;font-weight:400;color:var(--gray);margin-left:8px;">(<?= count($payments) ?>)</span>
          </div>
          <?php
            $total_paid = array_sum(array_column(array_filter($payments ?: [], fn($p) => $p['status'] === 'paid'), 'amount'));
          ?>
          <?php if ($total_paid > 0): ?>
          <div style="font-size:.9rem;color:var(--success);font-weight:700;">
            Total Collected: ₱<?= number_format($total_paid, 2) ?>
          </div>
          <?php endif; ?>
        </div>
        <div class="table-wrap">
          <?php if (empty($payments)): ?>
            <div class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              <h3>No Payments Found</h3>
              <p>Payments will appear here once patients submit them</p>
            </div>
          <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Patient</th>
                <th>Service</th>
                <th>Appointment</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Notes</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($payments as $p): ?>
              <tr>
                <td class="text-sm text-gray">#<?= $p['id'] ?></td>
                <td>
                  <div class="font-bold"><?= htmlspecialchars($p['patient_name']) ?></div>
                  <div class="text-sm text-gray"><?= htmlspecialchars($p['patient_email']) ?></div>
                </td>
                <td><?= htmlspecialchars($p['service_name']) ?></td>
                <td>
                  <div><?= formatDate($p['appointment_date']) ?></div>
                  <div class="text-sm text-gray"><?= formatTime($p['appointment_time']) ?></div>
                </td>
                <td class="font-bold text-primary">₱<?= number_format($p['amount'],2) ?></td>
                <td>
                  <?php
                    $methodIcons = ['cash'=>'💵','gcash'=>'📱','maya'=>'💳','card'=>'💳'];
                    echo ($methodIcons[$p['method']] ?? '💵') . ' ' . strtoupper($p['method']);
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
                    $pBadges = ['pending'=>'badge-pending','paid'=>'badge-completed','refunded'=>'badge-cancelled'];
                    echo "<span class='badge ".($pBadges[$p['status']]?:'badge-pending')."'>".ucfirst($p['status'])."</span>";
                  ?>
                </td>
                <td style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                  <?php if ($p['status'] === 'paid'): ?>
                    <a href="<?= APP_URL ?>/download_receipt.php?id=<?= $p['id'] ?>"
                       target="_blank"
                       class="btn btn-sm"
                       style="background:#0d9488;color:#fff;text-decoration:none;">
                      Receipt
                    </a>
                  <?php endif; ?>
                  <button class="btn btn-ghost btn-sm"
                    data-id="<?= $p['id'] ?>"
                    data-status="<?= htmlspecialchars($p['status']) ?>"
                    data-notes="<?= htmlspecialchars($p['notes'] ?? '') ?>"
                    onclick="openPayModal(this)">
                    Update
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Update Payment Modal -->
<div class="modal-overlay" id="payModal">
  <div class="modal" style="max-width:400px;">
    <div class="modal-header">
      <div class="modal-title">Update Payment Status</div>
      <button class="modal-close" onclick="closeModal('payModal')">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="?page=admin_update_payment">
      <div class="modal-body">
        <input type="hidden" name="id" id="payId">
        <div class="form-group">
          <label>Payment Status</label>
          <div class="input-wrap no-icon">
            <select name="status" id="payStatus">
              <option value="pending">Pending</option>
              <option value="paid">Paid ✓</option>
              <option value="refunded">Refunded</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Admin Notes (optional)</label>
          <div class="input-wrap no-icon">
            <textarea name="notes" id="payNotes" placeholder="e.g. Cash received, GCash confirmed..."></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('payModal')">Cancel</button>
        <button type="submit" class="btn btn-success">Save Status</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function openPayModal(btn) {
  document.getElementById('payId').value     = btn.dataset.id;
  document.getElementById('payStatus').value = btn.dataset.status;
  document.getElementById('payNotes').value  = btn.dataset.notes || '';
  openModal('payModal');
}
document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', function(e) { if(e.target===this) this.classList.remove('open'); });
});
</script>
</body>
</html>