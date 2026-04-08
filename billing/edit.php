<?php
require_once 'config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: list.php'); exit; }

$db = getDB();

// Load bill
$stmt = $db->prepare("SELECT * FROM bills WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$bill = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$bill) { header('Location: list.php'); exit; }

// Load items
$istmt = $db->prepare("SELECT * FROM bill_items WHERE bill_id = ?");
$istmt->bind_param("i", $id);
$istmt->execute();
$items = $istmt->get_result()->fetch_all(MYSQLI_ASSOC);
$istmt->close();

$error = '';

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name']);
    $bill_date     = trim($_POST['bill_date']);
    $gst_percent   = floatval($_POST['gst_percent']);
    $item_names    = $_POST['item_name'] ?? [];
    $quantities    = $_POST['quantity'] ?? [];
    $prices        = $_POST['price'] ?? [];

    if (!$customer_name || !$bill_date) {
        $error = 'Please fill all required fields.';
    } elseif (empty($item_names)) {
        $error = 'Please add at least one item.';
    } else {
        $item_total = 0;
        $new_items  = [];
        $valid = true;

        foreach ($item_names as $i => $name) {
            $name  = trim($name);
            $qty   = intval($quantities[$i] ?? 0);
            $price = floatval($prices[$i] ?? 0);
            if (!$name || $qty <= 0 || $price <= 0) { $valid = false; break; }
            $amount = $qty * $price;
            $item_total += $amount;
            $new_items[] = compact('name', 'qty', 'price', 'amount');
        }

        if (!$valid) {
            $error = 'Please fill all item fields with valid values.';
        } else {
            $gst_amount  = round($item_total * $gst_percent / 100, 2);
            $grand_total = $item_total + $gst_amount;

            // Update bill
            $ustmt = $db->prepare("UPDATE bills SET customer_name=?, bill_date=?, item_total=?, gst_percent=?, gst_amount=?, grand_total=? WHERE id=?");
            $ustmt->bind_param("ssddddi", $customer_name, $bill_date, $item_total, $gst_percent, $gst_amount, $grand_total, $id);
            $ustmt->execute();
            $ustmt->close();

            // Delete old items and re-insert
            $db->query("DELETE FROM bill_items WHERE bill_id = $id");

            $ins = $db->prepare("INSERT INTO bill_items (bill_id, item_name, quantity, price, amount) VALUES (?, ?, ?, ?, ?)");
            foreach ($new_items as $item) {
                $ins->bind_param("isidd", $id, $item['name'], $item['qty'], $item['price'], $item['amount']);
                $ins->execute();
            }
            $ins->close();
            $db->close();

            header('Location: list.php?msg=updated');
            exit;
        }
    }
}
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bill</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="page-header">
    <div class="header-inner">
        <div class="logo">&#9688; BillManager</div>
        <nav>
            <a href="index.php" class="nav-link">New Bill</a>
            <a href="list.php" class="nav-link active">Bills List</a>
        </nav>
    </div>
</div>

<div class="container">
    <div class="form-card">
        <div class="form-title">
            <h2>Edit Bill &mdash; <?= htmlspecialchars($bill['bill_no']) ?></h2>
            <p>Update bill details and items</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">&#10005; <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="billForm">

            <div class="section-label">Bill Information</div>
            <div class="row-3">
                <div class="field">
                    <label>Bill No</label>
                    <input type="text" value="<?= htmlspecialchars($bill['bill_no']) ?>" disabled style="background:var(--g50);color:var(--g400)">
                </div>
                <div class="field">
                    <label>Customer Name <span class="req">*</span></label>
                    <input type="text" name="customer_name" required value="<?= htmlspecialchars($_POST['customer_name'] ?? $bill['customer_name']) ?>">
                </div>
                <div class="field">
                    <label>Bill Date <span class="req">*</span></label>
                    <input type="date" name="bill_date" required value="<?= htmlspecialchars($_POST['bill_date'] ?? $bill['bill_date']) ?>">
                </div>
            </div>

            <div class="section-label">
                Items
                <button type="button" class="btn-add" onclick="addRow()">+ Add Item</button>
            </div>

            <div class="table-wrap">
                <table id="itemTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Name <span class="req">*</span></th>
                            <th>Quantity <span class="req">*</span></th>
                            <th>Price (&#8377;) <span class="req">*</span></th>
                            <th>Amount (&#8377;)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="itemBody">
                        <?php foreach ($items as $i => $item): ?>
                        <tr class="item-row">
                            <td class="row-num"><?= $i + 1 ?></td>
                            <td><input type="text" name="item_name[]" required value="<?= htmlspecialchars($item['item_name']) ?>"></td>
                            <td><input type="number" name="quantity[]" min="1" required value="<?= $item['quantity'] ?>" oninput="calcRow(this)"></td>
                            <td><input type="number" name="price[]" min="0.01" step="0.01" required value="<?= $item['price'] ?>" oninput="calcRow(this)"></td>
                            <td class="amount-cell"><span class="amount-val"><?= number_format($item['amount'], 2) ?></span></td>
                            <td><button type="button" class="btn-del" onclick="removeRow(this)">&#10005;</button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="totals-block">
                <div class="totals-inner">
                    <div class="total-row">
                        <span>Item Total</span>
                        <strong id="itemTotalDisplay">&#8377; 0.00</strong>
                    </div>
                    <div class="total-row gst-row">
                        <span>GST %</span>
                        <div class="gst-inputs">
                            <input type="number" name="gst_percent" id="gstPercent" min="0" max="100" step="0.01" value="<?= htmlspecialchars($_POST['gst_percent'] ?? $bill['gst_percent']) ?>" oninput="calcTotals()">
                            <strong id="gstAmountDisplay">&#8377; 0.00</strong>
                        </div>
                    </div>
                    <div class="total-row grand">
                        <span>Grand Total</span>
                        <strong id="grandTotalDisplay">&#8377; 0.00</strong>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="list.php" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Update Bill</button>
            </div>

        </form>
    </div>
</div>

<script>
let rowCount = <?= count($items) ?>;

function addRow() {
    rowCount++;
    const tbody = document.getElementById('itemBody');
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td class="row-num">${rowCount}</td>
        <td><input type="text" name="item_name[]" required placeholder="Item name"></td>
        <td><input type="number" name="quantity[]" min="1" required placeholder="0" oninput="calcRow(this)"></td>
        <td><input type="number" name="price[]" min="0.01" step="0.01" required placeholder="0.00" oninput="calcRow(this)"></td>
        <td class="amount-cell"><span class="amount-val">0.00</span></td>
        <td><button type="button" class="btn-del" onclick="removeRow(this)">&#10005;</button></td>
    `;
    tbody.appendChild(tr);
    tr.querySelector('input').focus();
    calcTotals();
}

function removeRow(btn) {
    if (document.querySelectorAll('.item-row').length === 1) { alert('At least one item required.'); return; }
    btn.closest('tr').remove();
    renumber();
    calcTotals();
}

function renumber() {
    document.querySelectorAll('.item-row').forEach((tr, i) => tr.querySelector('.row-num').textContent = i + 1);
    rowCount = document.querySelectorAll('.item-row').length;
}

function calcRow(input) {
    const tr  = input.closest('tr');
    const qty = parseFloat(tr.querySelector('input[name="quantity[]"]').value) || 0;
    const prc = parseFloat(tr.querySelector('input[name="price[]"]').value) || 0;
    tr.querySelector('.amount-val').textContent = (qty * prc).toFixed(2);
    calcTotals();
}

function calcTotals() {
    let total = 0;
    document.querySelectorAll('.amount-val').forEach(el => total += parseFloat(el.textContent) || 0);
    const gstPct = parseFloat(document.getElementById('gstPercent').value) || 0;
    const gstAmt = total * gstPct / 100;
    document.getElementById('itemTotalDisplay').textContent  = '₹ ' + total.toFixed(2);
    document.getElementById('gstAmountDisplay').textContent  = '₹ ' + gstAmt.toFixed(2);
    document.getElementById('grandTotalDisplay').textContent = '₹ ' + (total + gstAmt).toFixed(2);
}

calcTotals();
</script>

</body>
</html>
