<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'ShopCart') ?> — ShopCart</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <script>
    // site_url() always includes index.php if mod_rewrite is OFF
    // This ensures fetch() calls go to the correct URL in all cases
    window.BASE_URL = '<?= rtrim(site_url(), '/') ?>/';
  </script>
</head>
<body>

<nav class="navbar">
  <a href="<?= base_url() ?>" class="brand">🛍️ Shop<span>Cart</span></a>
  <a href="<?= site_url('cart') ?>" class="cart-btn">
    🛒 Cart
    <span class="cart-badge" id="cart-badge"><?= (int)($cart_count ?? 0) ?></span>
  </a>
</nav>

<div id="toast"></div>

<div class="container">
