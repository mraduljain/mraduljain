<?php
// Product emoji icons mapped by index (fallback display since images may not exist)
$icons = ['🎧', '⌚', '🔊', '⌨️', '🔌'];

$flash_success = $this->session->flashdata('success');
$flash_error   = $this->session->flashdata('error');
?>

<?php if ($flash_success): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div>
<?php endif; ?>
<?php if ($flash_error): ?>
  <div class="alert alert-error"><?= htmlspecialchars($flash_error) ?></div>
<?php endif; ?>

<h1 class="page-title">🛍️ Our <span>Products</span></h1>

<?php if (empty($products)): ?>
  <div class="empty-state">
    <div class="icon">📦</div>
    <h3>No products found</h3>
    <p>Please run the database seed script to add products.</p>
  </div>
<?php else: ?>

<div class="product-grid">
  <?php foreach ($products as $i => $product): ?>
  <div class="product-card">
    <div class="thumb"><?= $icons[$i % count($icons)] ?></div>
    <div class="card-body">
      <h3><?= htmlspecialchars($product['name']) ?></h3>
      <p><?= htmlspecialchars($product['description']) ?></p>
      <div class="price">
        ₹<?= number_format($product['price'], 2) ?>
        <span>/ unit</span>
      </div>

      <div class="qty-row">
        <label for="qty-<?= $product['id'] ?>">Qty:</label>
        <input
          type="number"
          id="qty-<?= $product['id'] ?>"
          class="qty-input"
          value="1"
          min="1"
          max="99"
        >
      </div>

      <button
        class="btn btn-primary btn-full add-to-cart-btn"
        data-id="<?= $product['id'] ?>"
      >
        🛒 Add to Cart
      </button>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php endif; ?>
