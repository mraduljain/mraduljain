<?php
// view.php - View a single bill
require_once 'config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: list.php'); exit; }

$db = getDB();

$stmt = $db->prepare("SELECT * FROM bills WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$bill = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$bill) { header('Location: list.php'); exit; }

$istmt = $db->prepare("SELECT * FROM bill_items WHERE bill_id = ?");
$istmt->bind_param("i", $id);
$istmt->execute();
$items = $istmt->get_result();
$istmt->close();
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($bill['bill_no']) ?></title>
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

        <div class="bill-view-header">
            <div>
                <h2><?= htmlspecialchars($bill['bill_no']) ?></h2>
                <p>Date: <?= date('d M Y', strtotime($bill['bill_date'])) ?></p>
            </div>
            <div class="bill-customer">
                <div class="label">Bill To</div>
                <div class="name"><?= htmlspecialchars($bill['customer_name']) ?></div>
            </div>
        </div>

        <div class="table-wrap">
            <table class="list-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price (₹)</th>
                        <th>Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>₹ <?= number_format($item['price'], 2) ?></td>
                        <td>₹ <?= number_format($item['amount'], 2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="totals-block">
            <div class="totals-inner">
                <div class="total-row">
                    <span>Item Total</span>
                    <strong>₹ <?= number_format($bill['item_total'], 2) ?></strong>
                </div>
                <div class="total-row">
                    <span>GST (<?= $bill['gst_percent'] ?>%)</span>
                    <strong>₹ <?= number_format($bill['gst_amount'], 2) ?></strong>
                </div>
                <div class="total-row grand">
                    <span>Grand Total</span>
                    <strong>₹ <?= number_format($bill['grand_total'], 2) ?></strong>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <a href="list.php" class="btn-secondary">← Back to List</a>
            <button onclick="window.print()" class="btn-primary">Print Bill</button>
        </div>

    </div>
</div>

</body>
</html>
