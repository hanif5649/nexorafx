<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

$id = $_GET['id'];
$tx = $pdo->prepare("SELECT * FROM transactions WHERE id = ?");
$tx->execute([$id]);
$data = $tx->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("UPDATE transactions SET amount = ?, type = ?, description = ? WHERE id = ?");
    $stmt->execute([$_POST['amount'], $_POST['type'], $_POST['description'], $id]);
    echo "<script>window.location='transactions.php?msg=updated';</script>";
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="ledger-container p-4 p-md-5 shadow-lg border-0">
                <h5 class="fw-bold mb-4">Edit Transaction Log</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold">Amount ($)</label>
                        <input type="number" name="amount" step="0.01" class="form-control" value="<?= $data['amount'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Type</label>
                        <select name="type" class="form-select">
                            <option value="deposit" <?= $data['type']=='deposit'?'selected':'' ?>>Deposit</option>
                            <option value="withdrawal" <?= $data['type']=='withdrawal'?'selected':'' ?>>Withdrawal</option>
                            <option value="profit" <?= $data['type']=='profit'?'selected':'' ?>>Profit</option>
                            <option value="investment" <?= $data['type']=='investment'?'selected':'' ?>>Investment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($data['description']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Update Log</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>