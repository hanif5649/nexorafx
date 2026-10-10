<?php
session_start();
if (!isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

include('layout/header.php');

if (!isset($_GET['id'])) {
    echo "<script>window.location='plans.php';</script>";
    exit();
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM inv_plans WHERE id = ?");
$stmt->execute([$id]);
$plan = $stmt->fetch();

if (!$plan) {
    echo "<script>window.location='plans.php?error=notfound';</script>";
    exit();
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_plan'])) {

    $plan_name        = trim($_POST['plan_name']);
    $plan_description = trim($_POST['plan_description']);
    $min_amount       = floatval($_POST['min_amount']);
    $max_amount       = floatval($_POST['max_amount']);
    $roi_percentage   = ($_POST['roi_percentage'] !== "") ? floatval($_POST['roi_percentage']) : null;
    $roi_amount       = ($_POST['roi_amount'] !== "") ? floatval($_POST['roi_amount']) : null;
    $duration_days    = intval($_POST['duration_days']);
    $status           = $_POST['status'];

    try {
        $update_sql = "UPDATE inv_plans SET 
                        plan_name = ?, 
                        plan_description = ?, 
                        min_amount = ?, 
                        max_amount = ?, 
                        roi_percentage = ?, 
                        roi_amount = ?, 
                        duration_days = ?, 
                        status = ? 
                       WHERE id = ?";

        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([
            $plan_name,
            $plan_description,
            $min_amount,
            $max_amount,
            $roi_percentage,
            $roi_amount,
            $duration_days,
            $status,
            $id
        ]);

        $success = "Investment plan updated successfully.";

        $stmt->execute([$id]);
        $plan = $stmt->fetch();

    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}
?>

<style>
    .edit-header-card {
        border-radius: 20px;
        border: 1px solid var(--bs-border-color);
        background: linear-gradient(135deg, var(--bs-tertiary-bg), var(--bs-secondary-bg));
        box-shadow: 0 12px 35px rgba(0,0,0,0.10);
        padding: 1.6rem;
    }

    .edit-header-card h3 {
        font-weight: 900;
        letter-spacing: -0.7px;
        margin-bottom: 0.3rem;
    }

    .edit-header-card p {
        margin-bottom: 0;
        color: var(--bs-secondary-color);
        font-size: 0.85rem;
    }

    .form-card {
        border-radius: 20px;
        border: 1px solid var(--bs-border-color);
        background: var(--bs-tertiary-bg);
        box-shadow: 0 12px 35px rgba(0,0,0,0.10);
        overflow: hidden;
    }

    .form-card-header {
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid var(--bs-border-color);
        background: rgba(255,255,255,0.02);
    }

    .form-card-header h6 {
        font-weight: 900;
        margin-bottom: 0.15rem;
        letter-spacing: -0.4px;
    }

    .form-card-header span {
        font-size: 0.8rem;
        color: var(--bs-secondary-color);
    }

    .form-card-body {
        padding: 1.6rem;
    }

    .form-label {
        font-weight: 800;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--bs-secondary-color);
    }

    .form-control, .form-select {
        border-radius: 14px;
        padding: 0.85rem 1rem;
        font-weight: 600;
        border: 1px solid var(--bs-border-color);
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
        box-shadow: none !important;
    }

    .form-control:focus, .form-select:focus {
        border-color: rgba(13,110,253,0.5);
        box-shadow: 0 0 0 0.15rem rgba(13,110,253,0.15) !important;
    }

    .input-group-text {
        border-radius: 14px;
        font-weight: 800;
        border: 1px solid var(--bs-border-color);
        background: var(--bs-secondary-bg);
        color: var(--bs-secondary-color);
    }

    .divider-title {
        font-weight: 900;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--bs-primary);
        margin-bottom: 1rem;
    }

    .hint-box {
        border-radius: 18px;
        padding: 1rem 1.2rem;
        background: rgba(13,110,253,0.08);
        border: 1px solid rgba(13,110,253,0.18);
        font-size: 0.83rem;
        color: var(--bs-secondary-color);
    }

    .btn-save {
        border-radius: 50px;
        font-weight: 900;
        padding: 1rem;
        letter-spacing: 0.03em;
        box-shadow: 0 10px 25px rgba(13,110,253,0.25);
    }
</style>


<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="edit-header-card mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div>
            <a href="plans.php" class="text-decoration-none text-secondary small fw-semibold">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to Plans
            </a>
            <h3 class="mt-2">
                Edit Investment Plan
                <span class="text-primary"><?= htmlspecialchars($plan['plan_name']) ?></span>
            </h3>
            <p>Modify plan parameters and update platform investment terms.</p>
        </div>

        <div class="mt-3 mt-md-0 text-md-end">
            <div class="small text-secondary fw-semibold">Created Date</div>
            <div class="fw-bold text-emphasis"><?= date('M d, Y', strtotime($plan['created_at'])) ?></div>
        </div>
    </div>

    <!-- ALERTS -->
    <?php if($success): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm py-3">
            <i class="fa-solid fa-circle-check me-2"></i> <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if($error): ?>
        <div class="alert alert-danger border-0 rounded-4 shadow-sm py-3">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- FORM CARD -->
    <div class="form-card">

        <div class="form-card-header d-flex justify-content-between align-items-center">
            <div>
                <h6>Plan Configuration</h6>
                <span>Update pricing range, profit logic, and duration.</span>
            </div>

            <span class="badge rounded-pill px-3 py-2 
                <?= $plan['status'] === 'active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' ?>">
                <i class="fa-solid fa-circle me-2" style="font-size: 9px;"></i>
                <?= strtoupper($plan['status']) ?>
            </span>
        </div>

        <div class="form-card-body">
            <form method="POST" action="">
                <div class="row g-4">

                    <div class="col-12">
                        <div class="divider-title">General Setup</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Plan Name</label>
                        <input type="text" name="plan_name" class="form-control"
                            value="<?= htmlspecialchars($plan['plan_name']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Availability</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= ($plan['status'] == 'active') ? 'selected' : '' ?>>Active (Public)</option>
                            <option value="inactive" <?= ($plan['status'] == 'inactive') ? 'selected' : '' ?>>Inactive (Hidden)</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="plan_description" class="form-control" rows="3"
                            placeholder="Describe the plan strategy, risk, or investor expectation..."><?= htmlspecialchars($plan['plan_description']) ?></textarea>
                    </div>

                    <div class="col-12 mt-2">
                        <hr class="opacity-25">
                    </div>

                    <div class="col-12">
                        <div class="divider-title">Financial Parameters</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Minimum Deposit</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="min_amount" class="form-control"
                                value="<?= htmlspecialchars($plan['min_amount']) ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Maximum Deposit</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="max_amount" class="form-control"
                                value="<?= htmlspecialchars($plan['max_amount']) ?>" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Profit Percentage</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="roi_percentage" class="form-control"
                                value="<?= htmlspecialchars($plan['roi_percentage']) ?>" placeholder="e.g 10">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Fixed Profit Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="roi_amount" class="form-control"
                                value="<?= htmlspecialchars($plan['roi_amount']) ?>" placeholder="e.g 500">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Duration</label>
                        <div class="input-group">
                            <input type="number" name="duration_days" class="form-control"
                                value="<?= htmlspecialchars($plan['duration_days']) ?>" required>
                            <span class="input-group-text">Days</span>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="hint-box mt-2">
                            <i class="fa-solid fa-circle-info me-2 text-primary"></i>
                            If <strong>percentage profit</strong> is set, it will override fixed amount.  
                            If percentage is empty, system will apply fixed ROI amount.
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" name="update_plan" class="btn btn-primary btn-lg w-100 btn-save">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Save Plan Changes
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="mt-4 text-center">
        <p class="small text-secondary">
            <i class="fa-solid fa-shield-halved me-1"></i>
            Updates affect new investments only. Existing running investments remain unchanged.
        </p>
    </div>

</div>

<?php include('layout/footer.php'); ?>