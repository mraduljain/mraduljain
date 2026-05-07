<?php
$icons = ['🎧', '⌚', '🔊', '⌨️', '🔌'];
$flash_error = $this->session->flashdata('error');
?>

<?php if ($flash_error): ?>
  <div class="alert alert-error"><?= htmlspecialchars($flash_error) ?></div>
<?php endif; ?>

<h1 class="page-title">✅ <span>Checkout</span></h1>

<div class="checkout-wrapper">

  <!-- Customer Details Form -->
  <div class="form-card">
    <h3>📋 Your Details</h3>

    <?php echo form_open('place-order'); ?>

      <div class="form-group">
        <label for="name">Full Name *</label>
        <input
          type="text"
          name="name"
          id="name"
          class="form-control"
          value="<?= set_value('name') ?>"
          placeholder="Enter your full name"
          required
        >
        <?php if (form_error('name')): ?>
          <div class="form-error"><?= form_error('name') ?></div>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="mobile">Mobile Number *</label>
        <input
          type="tel"
          name="mobile"
          id="mobile"
          class="form-control"
          value="<?= set_value('mobile') ?>"
          placeholder="10-digit mobile number"
          maxlength="10"
          required
        >
        <?php if (form_error('mobile')): ?>
          <div class="form-error"><?= form_error('mobile') ?></div>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="email">Email ID *</label>
        <input
          type="email"
          name="email"
          id="email"
          class="form-control"
          value="<?= set_value('email') ?>"
          placeholder="you@example.com"
          required
        >
        <?php if (form_error('email')): ?>
          <div class="form-error"><?= form_error('email') ?></div>
        <?php endif; ?>
      </div>

      <button type="submit" class="btn btn-success btn-full" style="margin-top:8px;">
        🛍️ Place Order
      </button>
      <a href="<?= site_url('cart') ?>" class="btn btn-outline btn-full" style="margin-top:10px;">
        ← Back to Cart
      </a>

    <?php echo form_close(); ?>
  </div>

  <!-- Order Summary -->
  <div class="summary-card">
    <h3>🧾 Order Summary</h3>

    <table style="width:100%; border-collapse:collapse; margin-bottom:16px;">
      <thead>
        <tr>
          <th style="text-align:left; padding:8px; background:var(--primary); color:#fff; border-radius:6px 0 0 0; font-size:.8rem;">Item</th>
          <th style="text-align:center; padding:8px; background:var(--primary); color:#fff; font-size:.8rem;">Qty</th>
          <th style="text-align:right; padding:8px; background:var(--primary); color:#fff; border-radius:0 6px 0 0; font-size:.8rem;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cart_items as $item): ?>
        <tr>
          <td style="padding:8px; border-bottom:1px solid var(--border); font-size:.85rem;">
            <?= $icons[($item['product_id'] - 1) % count($icons)] ?>
            <?= htmlspecialchars($item['name']) ?>
          </td>
          <td style="padding:8px; border-bottom:1px solid var(--border); text-align:center; font-size:.85rem;">
            <?= $item['qty'] ?>
          </td>
          <td style="padding:8px; border-bottom:1px solid var(--border); text-align:right; font-size:.85rem; font-weight:600;">
            ₹<?= number_format($item['subtotal'], 2) ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="summary-row">
      <span>Subtotal</span>
      <span>₹<?= number_format($cart_total, 2) ?></span>
    </div>
    <div class="summary-row">
      <span>Shipping</span>
      <span style="color:var(--success);">FREE</span>
    </div>
    <div class="summary-row total">
      <span>Total Payable</span>
      <span>₹<?= number_format($cart_total, 2) ?></span>
    </div>
  </div>

</div>
