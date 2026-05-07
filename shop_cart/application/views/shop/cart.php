<?php
$icons = ['🎧', '⌚', '🔊', '⌨️', '🔌'];
$icon_map = [];
// Build icon map using product_id mod
foreach ($cart_items as $item) {
    $icon_map[$item['product_id']] = $icons[($item['product_id'] - 1) % count($icons)];
}
?>

<h1 class="page-title">🛒 Your <span>Cart</span></h1>

<?php if (empty($cart_items)): ?>
  <div class="empty-state">
    <div class="icon">🛒</div>
    <h3>Your cart is empty</h3>
    <p>Browse our products and add something!</p>
    <a href="<?= base_url() ?>" class="btn btn-primary">🛍️ Shop Now</a>
  </div>

<?php else: ?>

<div class="cart-wrapper">

  <!-- Cart Items Table -->
  <div class="table-card">
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Subtotal</th>
          <th>Remove</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cart_items as $item): ?>
        <tr id="cart-row-<?= $item['product_id'] ?>">
          <td>
            <div style="display:flex; align-items:center; gap:12px;">
              <div class="product-thumb-sm"><?= $icon_map[$item['product_id']] ?></div>
              <strong><?= htmlspecialchars($item['name']) ?></strong>
            </div>
          </td>
          <td>₹<?= number_format($item['price'], 2) ?></td>
          <td>
            <div class="qty-control">
              <button class="qty-dec" data-id="<?= $item['product_id'] ?>">−</button>
              <span class="qty-val" id="qty-val-<?= $item['product_id'] ?>"><?= $item['qty'] ?></span>
              <button class="qty-inc" data-id="<?= $item['product_id'] ?>">+</button>
            </div>
          </td>
          <td id="subtotal-<?= $item['product_id'] ?>">
            ₹<?= number_format($item['subtotal'], 2) ?>
          </td>
          <td>
            <button
              class="btn btn-danger btn-sm remove-item-btn"
              data-id="<?= $item['product_id'] ?>"
              title="Remove"
            >🗑️</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Order Summary -->
  <div class="summary-card">
    <h3>📋 Order Summary</h3>

    <div class="summary-row">
      <span>Items (<?= array_sum(array_column($cart_items, 'qty')) ?>)</span>
      <span id="cart-total">₹<?= number_format($cart_total, 2) ?></span>
    </div>
    <div class="summary-row">
      <span>Shipping</span>
      <span style="color:var(--success)">FREE</span>
    </div>
    <div class="summary-row total">
      <span>Total</span>
      <span id="summary-total">₹<?= number_format($cart_total, 2) ?></span>
    </div>

    <a href="<?= base_url('checkout') ?>" class="btn btn-success btn-full">
      ✅ Proceed to Checkout
    </a>
    <a href="<?= base_url() ?>" class="btn btn-outline btn-full" style="margin-top:10px;">
      ← Continue Shopping
    </a>
  </div>

</div><!-- /.cart-wrapper -->

<?php endif; ?>
