/* =============================================
   ShopCart — Main JS
   ============================================= */

// BASE_URL is injected by layout_header.php via PHP site_url()
// e.g. http://localhost/shop_cart/index.php/  (mod_rewrite OFF)
//      http://localhost/shop_cart/            (mod_rewrite ON)
const BASE_URL = (window.BASE_URL || window.location.origin + '/').replace(/\/+$/, '/');

// Always build route URLs via this helper
function url(path) {
  return BASE_URL + path;
}

// ── TOAST NOTIFICATION ──────────────────────
function showToast(msg, type = 'success') {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.className   = 'show ' + type;
  clearTimeout(t._timer);
  t._timer = setTimeout(() => { t.className = ''; }, 3000);
}

// ── UPDATE CART BADGE ────────────────────────
function updateCartBadge(count) {
  const badge = document.getElementById('cart-badge');
  if (badge) badge.textContent = count;
}

// ── ADD TO CART ──────────────────────────────
document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
  btn.addEventListener('click', async function () {
    const productId = this.dataset.id;
    const qtyInput  = document.getElementById('qty-' + productId);
    const qty       = qtyInput ? parseInt(qtyInput.value, 10) : 1;

    const originalText = this.innerHTML;
    this.innerHTML = '⏳ Adding…';
    this.disabled  = true;

    try {
      const fd = new FormData();
      fd.append('product_id', productId);
      fd.append('qty', qty);

      console.log('Posting to:', url('cart/add')); // debug

      const res = await fetch(url('cart/add'), { method: 'POST', body: fd });

      if (!res.ok) throw new Error('HTTP ' + res.status + ' — ' + res.url);

      const data = await res.json();
      showToast(data.message, data.success ? 'success' : 'error');
      if (data.success) updateCartBadge(data.cart_count);

    } catch (e) {
      console.error('Add to cart failed:', e);
      showToast('Error: ' + e.message, 'error');
    } finally {
      this.innerHTML = originalText;
      this.disabled  = false;
    }
  });
});

// ── CART QTY CONTROLS ───────────────────────
document.querySelectorAll('.qty-inc, .qty-dec').forEach(btn => {
  btn.addEventListener('click', async function () {
    const productId = this.dataset.id;
    const valEl     = document.getElementById('qty-val-' + productId);
    let   qty       = parseInt(valEl.textContent, 10);

    if (this.classList.contains('qty-inc')) qty++;
    else qty = Math.max(0, qty - 1);

    const fd = new FormData();
    fd.append('product_id', productId);
    fd.append('qty', qty);

    try {
      const res  = await fetch(url('cart/update'), { method: 'POST', body: fd });
      const data = await res.json();

      if (data.success) {
        if (qty === 0) {
          const row = document.getElementById('cart-row-' + productId);
          if (row) row.remove();
          const tbody = document.querySelector('tbody');
          if (!tbody || tbody.children.length === 0) location.reload();
        } else {
          valEl.textContent = qty;
          const subtotalEl = document.getElementById('subtotal-' + productId);
          if (subtotalEl) subtotalEl.textContent = '₹' + data.item_total;
        }
        updateCartBadge(data.cart_count);
        const totalEl = document.getElementById('cart-total');
        if (totalEl) totalEl.textContent = '₹' + data.cart_total;
        const summEl = document.getElementById('summary-total');
        if (summEl) summEl.textContent = '₹' + data.cart_total;
      }
    } catch (e) {
      showToast('Could not update cart.', 'error');
    }
  });
});

// ── REMOVE ITEM (AJAX) ───────────────────────
document.querySelectorAll('.remove-item-btn').forEach(btn => {
  btn.addEventListener('click', async function () {
    const productId = this.dataset.id;
    if (!confirm('Remove this item from cart?')) return;

    try {
      const res  = await fetch(url('cart/remove/' + productId), {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const data = await res.json();

      if (data.success) {
        const row = document.getElementById('cart-row-' + productId);
        if (row) row.remove();
        updateCartBadge(data.cart_count);
        const totalEl = document.getElementById('cart-total');
        if (totalEl) totalEl.textContent = '₹' + data.cart_total;
        const summEl = document.getElementById('summary-total');
        if (summEl) summEl.textContent = '₹' + data.cart_total;
        showToast('Item removed.', 'success');
        const tbody = document.querySelector('tbody');
        if (!tbody || tbody.children.length === 0) location.reload();
      }
    } catch (e) {
      showToast('Could not remove item.', 'error');
    }
  });
});
