<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $symbol = strtoupper($_POST['symbol']);
    $name = $_POST['asset_name'];
    $type = $_POST['asset_type'];
    $price = $_POST['current_price'];

    try {
        $stmt = $pdo->prepare("INSERT INTO tradable_assets (symbol, asset_name, asset_type, current_price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$symbol, $name, $type, $price]);
        echo "<script>window.location='tradable_assets.php?msg=added';</script>";
    } catch (PDOException $e) {
        $error = "Symbol already exists or data error: " . $e->getMessage();
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="ledger-container p-4 p-md-5">
                <h4 class="fw-bold mb-4">Register Asset</h4>
                <?php if(isset($error)): ?> <div class="alert alert-danger small"><?= $error ?></div> <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Asset Name</label>
                        <input type="text" name="asset_name" class="form-control border-color" placeholder="e.g. Bitcoin" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small fw-bold mb-2">Symbol (Ticker)</label>
                            <input type="text" name="symbol" class="form-control border-color" placeholder="BTC" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold mb-2">Asset Type</label>
                            <select name="asset_type" class="form-select border-color">
                                <option value="crypto">Cryptocurrency</option>
                                <option value="stock">Stock</option>
                                <option value="forex">Forex</option>
                                <option value="commodity">Commodity</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold mb-2">Current Price (USD)</label>
                        <input type="number" step="0.00000001" name="current_price" class="form-control border-color fw-bold text-primary" placeholder="0.00000000" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">Initialize Asset</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>