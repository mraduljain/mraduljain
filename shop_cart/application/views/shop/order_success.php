<?php
$icons = ['🎧', '⌚', '🔊', '⌨️', '🔌'];
?>

<div style="padding: 20px 0;">
  <div class="success-card">

    <div class="success-icon">✅</div>

    <h2>Order Placed Successfully!</h2>
    <p>Thank you, <strong><?= htmlspecialchars($order['name']) ?></strong>! Your order has been received.</p>
    <p style="margin-top:6px;">A confirmation will be sent to <strong><?= htmlspecialchars($order['email']) ?></strong></p>

    <div class="order-id-badge">
      Order ID: <?= htmlspecialchars($order['order_number']) ?>
    </div>

    <!-- Order Items Table -->
    <div class="order-detail-table">
      <table style="width:100%; border-collapse:collapse; margin-top:8px;">
        <thead>
          <tr>
            <th>Product</th>
            <th style="text-align:center;">Qty</th>
            <th style="text-align:right;">Price</th>
            <th style="text-align:right;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($order['items'] as $item): ?>
          <tr>
            <td>
              <?= $icons[($item['product_id'] - 1) % count($icons)] ?>
              <?= htmlspecialchars($item['name']) ?>
            </td>
            <td style="text-align:center;"><?= $item['quantity'] ?></td>
            <td style="text-align:right;">₹<?= number_format($item['price'], 2) ?></td>
            <td style="text-align:right; font-weight:600;">₹<?= number_format($item['subtotal'], 2) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" style="text-align:right; font-weight:700; padding-top:14px; font-size:1rem;">
              Grand Total:
            </td>
            <td style="text-align:right; font-weight:700; padding-top:14px; color:var(--primary); font-size:1.05rem;">
              ₹<?= number_format($order['total_amount'], 2) ?>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Customer Info -->
    <div style="margin-top:28px; background:#f8fafc; border-radius:10px; padding:18px; text-align:left;">
      <h4 style="margin-bottom:12px; color:var(--muted); font-size:.85rem; text-transform:uppercase; letter-spacing:.5px;">Customer Details</h4>
      <p><strong>Name:</strong> <?= htmlspecialchars($order['name']) ?></p>
      <p style="margin-top:6px;"><strong>Mobile:</strong> <?= htmlspecialchars($order['mobile']) ?></p>
      <p style="margin-top:6px;"><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
      <p style="margin-top:6px;"><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
    </div>

    <div style="margin-top:28px;">
      <a href="<?= site_url() ?>" class="btn btn-primary">🛍️ Continue Shopping</a>
    </div>

  </div>
</div>
