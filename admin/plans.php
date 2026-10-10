<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

// Handle Deletion
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM inv_plans WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    echo "<script>window.location='plans.php?msg=deleted';</script>";
    exit();
}

$stmt = $pdo->query("SELECT * FROM inv_plans ORDER BY created_at DESC");
$plans = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Investment Inventory</h4>
            <p class="text-secondary small">Manage tiered investment packages for your users</p>
        </div>
        <a href="plan_add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> Add New Plan
        </a>
    </div>

    <div class="row g-4">
        <?php foreach($plans as $plan): ?>
        <div class="col-md-4">
            <div class="ledger-container p-4 h-100 border-0 shadow-sm position-relative">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="fw-bold mb-0 text-primary"><?= htmlspecialchars($plan['plan_name']) ?></h5>
                    <span class="badge <?= $plan['status'] == 'active' ? 'bg-success' : 'bg-danger' ?> rounded-pill">
                        <?= ucfirst($plan['status']) ?>
                    </span>
                </div>
                
                <p class="text-secondary small mb-4" style="min-height: 40px;">
                    <?= htmlspecialchars($plan['plan_description']) ?: 'No description provided.' ?>
                </p>

                <div class="mb-4">
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="small text-muted">Limits:</span>
                        <span class="fw-bold">$<?= number_format($plan['min_amount']) ?> - $<?= number_format($plan['max_amount']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="small text-muted">Returns (ROI):</span>
                        <span class="fw-bold text-success">
                            <?= $plan['roi_percentage'] ? $plan['roi_percentage'].'%' : '$'.number_format($plan['roi_amount'], 2) ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="small text-muted">Duration:</span>
                        <span class="fw-bold"><?= $plan['duration_days'] ?> Days</span>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-auto">
                    <a href="plan_edit.php?id=<?= $plan['id'] ?>" class="btn btn-outline-primary btn-sm flex-grow-1 rounded-pill fw-bold">Edit Plan</a>
                    <a href="?delete=<?= $plan['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm('Delete this plan?')">
                        <i class="fa-solid fa-trash-can"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include('layout/footer.php'); ?>