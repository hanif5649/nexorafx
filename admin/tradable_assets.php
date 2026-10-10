<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

// Handle Activation/Deactivation
if (isset($_GET['toggle_id'])) {
    $stmt = $pdo->prepare("UPDATE tradable_assets SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$_GET['toggle_id']]);
    echo "<script>window.location='tradable_assets.php';</script>";
    exit();
}

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM tradable_assets WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    echo "<script>window.location='tradable_assets.php?msg=deleted';</script>";
    exit();
}

// Fetch all assets
$stmt = $pdo->query("SELECT * FROM tradable_assets ORDER BY asset_type ASC, asset_name ASC");
$assets = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Market Assets</h4>
            <p class="text-secondary small">Manage symbols and live pricing for the trading terminal</p>
        </div>
        <a href="asset_add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-plus-circle me-2"></i> Add New Asset
        </a>
    </div>

    <!-- Alert for Deletion -->
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4">
            <i class="fa-solid fa-trash me-2"></i> Asset has been removed from the registry.
        </div>
    <?php endif; ?>

    <div class="ledger-container p-0 overflow-hidden shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Asset / Type</th>
                        <th>Symbol</th>
                        <th>Current Price</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($assets as $asset): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold"><?= htmlspecialchars($asset['asset_name']) ?></div>
                            <span class="badge bg-secondary-subtle text-secondary x-small py-1"><?= strtoupper($asset['asset_type']) ?></span>
                        </td>
                        <td><code class="fw-bold text-primary"><?= htmlspecialchars($asset['symbol']) ?></code></td>
                        <td class="fw-bold font-monospace text-primary">
                            $<?= number_format($asset['current_price'], 8) ?>
                        </td>
                        <td>
                            <?php if($asset['is_active']): ?>
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Live</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Paused</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm">
                                <!-- Edit -->
                                <a href="asset_edit.php?id=<?= $asset['id'] ?>" class="btn btn-sm btn-white border-color" title="Edit Settings">
                                    <i class="fa-solid fa-pen-to-square text-primary"></i>
                                </a>
                                <!-- Toggle Status -->
                                <a href="?toggle_id=<?= $asset['id'] ?>" class="btn btn-sm btn-white border-color <?= $asset['is_active'] ? 'text-danger' : 'text-success' ?>" title="Toggle Status">
                                    <i class="fa-solid fa-power-off"></i>
                                </a>
                                <!-- Delete Button -->
                                <a href="?delete_id=<?= $asset['id'] ?>" 
                                   class="btn btn-sm btn-white border-color text-danger" 
                                   title="Delete Asset"
                                   onclick="return confirm('Warning: Deleting this asset may affect users with open positions. Are you sure?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .btn-white { background: #fff; border: 1px solid #dee2e6; }
    .btn-white:hover { background: #f8f9fa; }
    .x-small { font-size: 0.7rem; letter-spacing: 0.5px; font-weight: 700; }
</style>

<?php include('layout/footer.php'); ?>