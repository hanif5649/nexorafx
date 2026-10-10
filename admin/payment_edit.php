<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE id = ?");
$stmt->execute([$id]);
$m = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $qr_url = $m['qr_code_url'];

    if (!empty($_FILES['qr_code']['name'])) {
        $upload_dir = "../uploads/qr/";
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
        
        $ext = pathinfo($_FILES["qr_code"]["name"], PATHINFO_EXTENSION);
        $qr_url = "qr_" . time() . "." . $ext;
        move_uploaded_file($_FILES["qr_code"]["tmp_name"], $upload_dir . $qr_url);
        
        // Delete old QR if exists
        if($m['qr_code_url'] && file_exists($upload_dir . $m['qr_code_url'])) { unlink($upload_dir . $m['qr_code_url']); }
    }

    $update = $pdo->prepare("UPDATE payment_methods SET name=?, min_deposit=?, max_deposit=?, wallet_address=?, extra_details=?, qr_code_url=?, status=? WHERE id=?");
    $update->execute([$_POST['name'], $_POST['min_deposit'], $_POST['max_deposit'], $_POST['wallet_address'], $_POST['extra_details'], $qr_url, $_POST['status'], $id]);
    echo "<script>window.location='payment_methods.php';</script>";
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="ledger-container p-4 p-md-5 shadow-sm border-0 rounded-4">
                <h4 class="fw-bold mb-4">Edit <?= htmlspecialchars($m['name'] ?? '') ?></h4>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3 text-center">
                        <?php if($m['qr_code_url']): ?>
                            <img src="../uploads/qr/<?= $m['qr_code_url'] ?>" class="img-thumbnail mb-2" style="width: 150px;">
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Method Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($m['name'] ?? '') ?>">
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="small fw-bold mb-2">Min</label>
                            <input type="number" name="min_deposit" class="form-control" value="<?= $m['min_deposit'] ?>">
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold mb-2">Max</label>
                            <input type="number" name="max_deposit" class="form-control" value="<?= $m['max_deposit'] ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Wallet Address</label>
                        <input type="text" name="wallet_address" class="form-control" value="<?= htmlspecialchars($m['wallet_address'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Update QR Code</label>
                        <input type="file" name="qr_code" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= $m['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $m['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary w-100 py-3 rounded-pill fw-bold mt-3">Update Method</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>