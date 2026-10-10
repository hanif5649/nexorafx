<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $min = $_POST['min_deposit'];
    $max = $_POST['max_deposit'];
    $address = $_POST['wallet_address'];
    $details = $_POST['extra_details'];
    $qr_url = null;

    // Auto-create folder if not exists
    $upload_dir = "../uploads/qr/";
    if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }

    if (!empty($_FILES['qr_code']['name'])) {
        $ext = pathinfo($_FILES["qr_code"]["name"], PATHINFO_EXTENSION);
        $qr_url = "qr_" . time() . "." . $ext;
        move_uploaded_file($_FILES["qr_code"]["tmp_name"], $upload_dir . $qr_url);
    }

    $stmt = $pdo->prepare("INSERT INTO payment_methods (name, min_deposit, max_deposit, wallet_address, extra_details, qr_code_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $min, $max, $address, $details, $qr_url]);
    echo "<script>window.location='payment_methods.php';</script>";
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="ledger-container p-4 p-md-5 shadow-sm border-0 rounded-4">
                <h4 class="fw-bold mb-4">Add Payment Gateway</h4>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Method Name (e.g. Bitcoin, USDT TRC20)</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="small fw-bold mb-2">Min Deposit ($)</label>
                            <input type="number" name="min_deposit" class="form-control" value="0.00">
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold mb-2">Max Deposit ($)</label>
                            <input type="number" name="max_deposit" class="form-control" value="10000.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Wallet Address / Account Info</label>
                        <input type="text" name="wallet_address" class="form-control" placeholder="Paste address here">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">QR Code Image (Optional)</label>
                        <input type="file" name="qr_code" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold mb-2">Instructions for User</label>
                        <textarea name="extra_details" class="form-control" rows="3" placeholder="e.g. Send only BTC to this address..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">Save Payment Method</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>