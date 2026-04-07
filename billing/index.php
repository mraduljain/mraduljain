<?php
// index.php - Billing Entry Form
require_once 'config.php';

$success = '';
$error = '';

// Generate next Bill No on load
$db = getDB();
$res = $db->query("SELECT MAX(id) as max_id FROM bills");
$row = $res->fetch_assoc();
$next_id = ($row['max_id'] ?? 0) + 1;
$bill_no = 'BILL-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
$db->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();

    $bill_no_post     = trim($_POST['bill_no']);
    $customer_name    = trim($_POST['customer_name']);
    $bill_date        = trim($_POST['bill_date']);
    $gst_percent      = floatval($_POST['gst_percent']);
    $item_names       = $_POST['item_name'] ?? [];
    $quantities       = $_POST['quantity'] ?? [];
    $prices           = $_POST['price'] ?? [];

    // Validate
    if (!$bill_no_post || !$customer_name || !$bill_date) {
        $error = 'Please fill all required fields.';
    } elseif (empty($item_names)) {
        $error = 'Please add at least one item.';
    } else {
        // Calculate totals
        $item_total = 0;
        $items = [];
        $valid = true;

        foreach ($item_names as $i => $name) {
            $name = trim($name);
            $qty  = intval($quantities[$i] ?? 0);
            $price = floatval($prices[$i] ?? 0);

            if (!$name || $qty <= 0 || $price <= 0) {
                $valid = false;
                break;
            }
            $amount = $qty * $price;
            $item_total += $amount;
            $items[] = compact('name', 'qty', 'price', 'amount');
        }

        if (!$valid) {
            $error = 'Please fill all item fields with valid values.';
        } else {
            $gst_amount  = round($item_total * $gst_percent / 100, 2);
            $grand_total = $item_total + $gst_amount;

            // Insert bill
            $stmt = $db->prepare("INSERT INTO bills (bill_no, customer_name, bill_date, item_total, gst_percent, gst_amount, grand_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssdddd", $bill_no_post, $customer_name, $bill_date, $item_total, $gst_percent, $gst_amount, $grand_total);

            if ($stmt->execute()) {
                $bill_id = $db->insert_id;

                // Insert items
                $istmt = $db->prepare("INSERT INTO bill_items (bill_id, item_name, quantity, price, amount) VALUES (?, ?, ?, ?, ?)");
                foreach ($items as $item) {
                    $istmt->bind_param("isidd", $bill_id, $item['name'], $item['qty'], $item['price'], $item['amount']);
                    $istmt->execute();
                }
                $istmt->close();

                $success = 'Bill <strong>' . htmlspecialchars($bill_no_post) . '</strong> saved successfully!';

                // Regenerate bill number for next entry
                $res2 = $db->query("SELECT MAX(id) as max_id FROM bills");
                $row2 = $res2->fetch_assoc();
                $next_id2 = ($row2['max_id'] ?? 0) + 1;
                $bill_no = 'BILL-' . str_pad($next_id2, 4, '0', STR_PAD_LEFT);
            } else {
                if ($db->errno == 1062) {
                    $error = 'Bill number already exists. Please use a unique bill number.';
                } else {
                    $error = 'Failed to save bill. ' . $db->error;
                }
            }
            $stmt->close();
        }
    }
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Entry</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="page-header">
    <div class="header-inner">
        <div class="logo">&#9688; BillManager</div>
        <nav>
            <a href="index.php" class="nav-link active">New Bill</a>
            <a href="list.php" class="nav-link">Bills List</a>
        </nav>
    </div>
</div>

<div class="container">
    <div class="form-card">
        <div class="form-title">
            <h2>New Billing Entry</h2>
            <p>Fill in the details to create a new bill</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">&#10003; <?= $success ?> &nbsp; <a href="list.php">View Bills &rarr;</a></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error">&#10005; <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="billForm">

            <!-- Bill Info -->
            <div class="section-label">Bill Information</div>
            <div class="row-3">
                <div class="field">
                    <label>Bill No <span class="req">*</span></label>
                    <input type="text" name="bill_no" value="<?= htmlspecialchars($bill_no) ?>" required placeholder="BILL-0001">
                </div>
                <div class="field">
                    <label>Customer Name <span class="req">*</span></label>
                    <input type="text" name="customer_name" required placeholder="Enter customer name" value="<?= isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : '' ?>">
                </div>
                <div class="field">
                    <label>Bill Date <span class="req">*</span></label>
                    <input type="date" name="bill_date" required value="<?= isset($_POST['bill_date']) ? htmlspecialchars($_POST['bill_date']) : date('Y-m-d') ?>">
                </div>
            </div>

            <!-- Items Table -->
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
                            <th>Price (₹) <span class="req">*</span></th>
                            <th>Amount (₹)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="itemBody">
                        <tr class="item-row">
                            <td class="row-num">1</td>
                            <td><input type="text" name="item_name[]" required placeholder="Item name"></td>
                            <td><input type="number" name="quantity[]" min="1" required placeholder="0" oninput="calcRow(this)"></td>
                            <td><input type="number" name="price[]" min="0.01" step="0.01" required placeholder="0.00" oninput="calcRow(this)"></td>
                            <td class="amount-cell"><span class="amount-val">0.00</span></td>
                            <td><button type="button" class="btn-del" onclick="removeRow(this)" title="Remove">&#10005;</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="totals-block">
                <div class="totals-inner">
                    <div class="total-row">
                        <span>Item Total</span>
                        <strong id="itemTotalDisplay">₹ 0.00</strong>
                    </div>
                    <div class="total-row gst-row">
                        <span>GST %</span>
                        <div class="gst-inputs">
                            <input type="number" name="gst_percent" id="gstPercent" min="0" max="100" step="0.01" value="<?= isset($_POST['gst_percent']) ? htmlspecialchars($_POST['gst_percent']) : '18' ?>" placeholder="18" oninput="calcTotals()">
                            <strong id="gstAmountDisplay">₹ 0.00</strong>
                        </div>
                    </div>
                    <div class="total-row grand">
                        <span>Grand Total</span>
                        <strong id="grandTotalDisplay">₹ 0.00</strong>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <button type="reset" class="btn-secondary" onclick="resetForm()">Clear</button>
                <button type="submit" class="btn-primary">Save Bill</button>
            </div>

        </form>
    </div>
</div>

<script>
let rowCount = 1;

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
        <td><button type="button" class="btn-del" onclick="removeRow(this)" title="Remove">&#10005;</button></td>
    `;
    tbody.appendChild(tr);
    tr.querySelector('input').focus();
    calcTotals();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length === 1) { alert('At least one item is required.'); return; }
    btn.closest('tr').remove();
    renumber();
    calcTotals();
}

function renumber() {
    document.querySelectorAll('.item-row').forEach((tr, i) => {
        tr.querySelector('.row-num').textContent = i + 1;
    });
    rowCount = document.querySelectorAll('.item-row').length;
}

function calcRow(input) {
    const tr = input.closest('tr');
    const qty   = parseFloat(tr.querySelector('input[name="quantity[]"]').value) || 0;
    const price = parseFloat(tr.querySelector('input[name="price[]"]').value) || 0;
    tr.querySelector('.amount-val').textContent = (qty * price).toFixed(2);
    calcTotals();
}

function calcTotals() {
    let total = 0;
    document.querySelectorAll('.amount-val').forEach(el => {
        total += parseFloat(el.textContent) || 0;
    });
    const gstPct = parseFloat(document.getElementById('gstPercent').value) || 0;
    const gstAmt = total * gstPct / 100;
    const grand  = total + gstAmt;

    document.getElementById('itemTotalDisplay').textContent  = '₹ ' + total.toFixed(2);
    document.getElementById('gstAmountDisplay').textContent  = '₹ ' + gstAmt.toFixed(2);
    document.getElementById('grandTotalDisplay').textContent = '₹ ' + grand.toFixed(2);
}

function resetForm() {
    setTimeout(() => {
        document.querySelectorAll('.item-row').forEach((tr, i) => { if (i > 0) tr.remove(); });
        document.querySelectorAll('.amount-val').forEach(el => el.textContent = '0.00');
        rowCount = 1;
        document.querySelector('.row-num').textContent = '1';
        calcTotals();
    }, 10);
}

// Init totals
calcTotals();
</script>

</body>
</html>
