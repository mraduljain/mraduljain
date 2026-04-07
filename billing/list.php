<?php
// list.php - View all saved bills
require_once 'config.php';

$db = getDB();
$bills = $db->query("SELECT * FROM bills ORDER BY created_at DESC");
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bills List</title>
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
            <h2>All Bills</h2>
            <p>Click on a bill to view its details</p>
        </div>

        <?php if ($bills->num_rows === 0): ?>
            <div class="empty-state">
                <div class="empty-icon">&#128196;</div>
                <p>No bills found. <a href="index.php">Create your first bill &rarr;</a></p>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table class="list-table">
                <thead>
                    <tr>
                        <th>Bill No</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Item Total</th>
                        <th>GST</th>
                        <th>Grand Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($bill = $bills->fetch_assoc()): ?>
                    <tr>
                        <td><span class="bill-badge"><?= htmlspecialchars($bill['bill_no']) ?></span></td>
                        <td><?= htmlspecialchars($bill['customer_name']) ?></td>
                        <td><?= date('d M Y', strtotime($bill['bill_date'])) ?></td>
                        <td>₹ <?= number_format($bill['item_total'], 2) ?></td>
                        <td><?= $bill['gst_percent'] ?>% &nbsp; (₹ <?= number_format($bill['gst_amount'], 2) ?>)</td>
                        <td><strong>₹ <?= number_format($bill['grand_total'], 2) ?></strong></td>
                        <td><a href="view.php?id=<?= $bill['id'] ?>" class="btn-view">View</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
