<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['plan_name'];
    $desc = $_POST['plan_description'];
    $min = $_POST['min_amount'];
    $max = $_POST['max_amount'];
    $roi_p = $_POST['roi_percentage'] ?: null;
    $roi_a = $_POST['roi_amount'] ?: null;
    $days = $_POST['duration_days'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO inv_plans (plan_name, plan_description, min_amount, max_amount, roi_percentage, roi_amount, duration_days, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $desc, $min, $max, $roi_p, $roi_a, $days, $status]);
    echo "<script>window.location='plans.php?msg=added';</script>";
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="ledger-container p-5">
                <h4 class="fw-bold mb-4">Create Investment Tier</h4>
                <form method="POST" class="row g-3">
                    <div class="col-12">
                        <label class="small fw-bold">Plan Name</label>
                        <input type="text" name="plan_name" class="form-control border-color" placeholder="e.g. Diamond Real Estate" required>
                    </div>
                    <div class="col-12">
                        <label class="small fw-bold">Description</label>
                        <textarea name="plan_description" class="form-control border-color" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Min Amount ($)</label>
                        <input type="number" step="0.01" name="min_amount" class="form-control border-color" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Max Amount ($)</label>
                        <input type="number" step="0.01" name="max_amount" class="form-control border-color" required>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold">ROI (%)</label>
                        <input type="number" step="0.01" name="roi_percentage" class="form-control border-color" placeholder="e.g. 15.5">
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold">ROI Fixed ($)</label>
                        <input type="number" step="0.01" name="roi_amount" class="form-control border-color" placeholder="Optional">
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold">Duration (Days)</label>
                        <input type="number" name="duration_days" class="form-control border-color" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Status</label>
                        <select name="status" class="form-select border-color">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-12 pt-3">
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">Launch Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('layout/footer.php'); ?>