<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM tradable_assets WHERE id = ?");
$stmt->execute([$id]);
$asset = $stmt->fetch();

if (!$asset) { header("Location: tradable_assets.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $price = $_POST['current_price'];
    $is_active = $_POST['is_active'];
    $name = $_POST['asset_name'];

    $stmt = $pdo->prepare("UPDATE tradable_assets SET current_price = ?, is_active = ?, asset_name = ? WHERE id = ?");
    $stmt->execute([$price, $is_active, $name, $id]);
    echo "<script>window.location='tradable_assets.php?msg=updated';</script>";
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="ledger-container p-4 p-md-5 border-top border-primary border-4 shadow-lg">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Modify <?= htmlspecialchars($asset['symbol']) ?></h4>
                    <span class="text-secondary small">ID: #<?= $asset['id'] ?></span>
                </div>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Display Name</label>
                        <input type="text" name="asset_name" class="form-control border-color" value="<?= htmlspecialchars($asset['asset_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Live Price ($)</label>
                        <input type="number" step="0.00000001" name="current_price" class="form-control border-color form-control-lg fw-bold" value="<?= $asset['current_price'] ?>" required>
                        <div class="form-text x-small">Updating this will instantly change valuation for all open user trades in this asset.</div>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold mb-2">Trading Status</label>
                        <select name="is_active" class="form-select border-color">
                            <option value="1" <?= $asset['is_active'] ? 'selected' : '' ?>>Market Open (Active)</option>
                            <option value="0" <?= !$asset['is_active'] ? 'selected' : '' ?>>Market Closed (Paused)</option>
                        </select>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="tradable_assets.php" class="btn btn-light rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary flex-grow-1 rounded-pill fw-bold shadow">Save Asset Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>