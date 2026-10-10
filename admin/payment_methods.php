<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("SELECT qr_code_url FROM payment_methods WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    $img = $stmt->fetchColumn();
    if($img && file_exists("../uploads/qr/".$img)) { unlink("../uploads/qr/".$img); }

    $stmt = $pdo->prepare("DELETE FROM payment_methods WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    echo "<script>window.location='payment_methods.php?msg=deleted';</script>";
}

// Handle Status Toggle
if (isset($_GET['toggle_id'])) {
    $stmt = $pdo->prepare("UPDATE payment_methods SET status = IF(status='active','inactive','active') WHERE id = ?");
    $stmt->execute([$_GET['toggle_id']]);
    echo "<script>window.location='payment_methods.php';</script>";
}

$methods = $pdo->query("SELECT * FROM payment_methods ORDER BY id DESC")->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Payment Methods</h4>
            <p class="text-secondary small">Configure how users fund their accounts</p>
        </div>
        <a href="payment_add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> Add Gateway
        </a>
    </div>

    <div class="ledger-container p-0 overflow-hidden shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Method Name</th>
                        <th>Limits (Min - Max)</th>
                        <th>Address/Details</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($methods as $m): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-primary"><?= htmlspecialchars($m['name'] ?? '') ?></div>
                            <?php if($m['qr_code_url']): ?>
                                <span class="badge bg-info-subtle text-info x-small">QR Enabled</span>
                            <?php endif; ?>
                        </td>
                        <td class="small fw-bold">
                            $<?= number_format($m['min_deposit'], 2) ?> - $<?= number_format($m['max_deposit'], 2) ?>
                        </td>
                        <td>
                            <code class="x-small text-primary"><?= htmlspecialchars($m['wallet_address'] ?? 'N/A') ?></code>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 <?= $m['status'] == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                                <?= ucfirst($m['status']) ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm">
                                <a href="payment_edit.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-white border-color"><i class="fa-solid fa-pen"></i></a>
                                <a href="?toggle_id=<?= $m['id'] ?>" class="btn btn-sm btn-white border-color"><i class="fa-solid fa-power-off"></i></a>
                                <a href="?delete_id=<?= $m['id'] ?>" class="btn btn-sm btn-white border-color text-danger" onclick="return confirm('Delete this method?')"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>