<?php
require_once 'config.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM bills WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $db->close();
    header('Location: list.php?msg=deleted');
    exit;
}

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
            <p>Manage your saved bills</p>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'deleted'): ?>
                <div class="alert alert-success">&#10003; Bill deleted successfully.</div>
            <?php elseif ($_GET['msg'] === 'updated'): ?>
                <div class="alert alert-success">&#10003; Bill updated successfully.</div>
            <?php endif; ?>
        <?php endif; ?>

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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($bill = $bills->fetch_assoc()): ?>
                    <tr>
                        <td><span class="bill-badge"><?= htmlspecialchars($bill['bill_no']) ?></span></td>
                        <td><?= htmlspecialchars($bill['customer_name']) ?></td>
                        <td><?= date('d M Y', strtotime($bill['bill_date'])) ?></td>
                        <td>&#8377; <?= number_format($bill['item_total'], 2) ?></td>
                        <td><?= $bill['gst_percent'] ?>% &nbsp;(&#8377; <?= number_format($bill['gst_amount'], 2) ?>)</td>
                        <td><strong>&#8377; <?= number_format($bill['grand_total'], 2) ?></strong></td>
                        <td>
                            <div class="actions-cell">
                                <a href="view.php?id=<?= $bill['id'] ?>" class="btn-view">View</a>
                                <a href="edit.php?id=<?= $bill['id'] ?>" class="btn-edit">Edit</a>
                                <button class="btn-danger"
                                    onclick="confirmDelete(<?= $bill['id'] ?>, '<?= htmlspecialchars($bill['bill_no'], ENT_QUOTES) ?>')">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div id="modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:10px;padding:28px 32px;max-width:380px;width:90%;text-align:center">
        <div style="font-size:36px;margin-bottom:12px">&#128465;</div>
        <h3 style="margin-bottom:8px;font-size:17px">Delete Bill?</h3>
        <p id="modal-msg" style="color:#64748b;font-size:13px;margin-bottom:24px"></p>
        <div style="display:flex;gap:10px;justify-content:center">
            <button class="btn-secondary" style="padding:8px 20px" onclick="closeModal()">Cancel</button>
            <a id="modal-confirm" href="#" class="btn-primary" style="padding:8px 20px;background:#dc2626">Yes, Delete</a>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, billNo) {
    document.getElementById('modal-msg').textContent = 'Bill "' + billNo + '" and all its items will be permanently deleted.';
    document.getElementById('modal-confirm').href = 'list.php?delete=' + id;
    document.getElementById('modal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('modal').style.display = 'none';
}
document.getElementById('modal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

</body>
</html>
