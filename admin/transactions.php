<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    echo "<script>window.location='transactions.php?msg=deleted';</script>";
}

// Fetch Transactions with User Info
$stmt = $pdo->query("SELECT t.*, u.fullname, u.email FROM transactions t 
                     JOIN users u ON t.user_id = u.id 
                     ORDER BY t.created_at DESC LIMIT 500");
$transactions = $stmt->fetchAll();
?>

<div class="container-fluid py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-0">Master Ledger</h4>
            <p class="text-secondary small">Comprehensive audit trail of all platform movements</p>
        </div>
        <div class="d-flex gap-2">
            <a href="tx_generate.php" class="btn btn-dark rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-wand-sparkles me-2"></i> Auto-Generate History
            </a>
            <a href="tx_add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-2"></i> Add Single Record
            </a>
        </div>
    </div>

    <div class="ledger-container p-0 overflow-hidden shadow-sm border-0 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Date / TXID</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Running Balance</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transactions as $tx): 
                        $typeClass = match($tx['type']) {
                            'deposit', 'profit', 'bonus' => 'text-success',
                            'withdrawal', 'investment', 'penalty' => 'text-danger',
                            default => 'text-primary'
                        };
                    ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold small"><?= date('M d, Y H:i', strtotime($tx['created_at'])) ?></div>
                            <code class="x-small text-muted">#TX-<?= str_pad($tx['id'], 6, '0', STR_PAD_LEFT) ?></code>
                        </td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($tx['fullname']) ?></div>
                            <div class="x-small text-muted"><?= $tx['email'] ?></div>
                        </td>
                        <td>
                            <span class="badge bg-light <?= $typeClass ?> border text-uppercase small px-2">
                                <?= $tx['type'] ?>
                            </span>
                        </td>
                        <td class="fw-bold <?= $typeClass ?>">
                            <?= in_array($tx['type'], ['withdrawal', 'investment', 'penalty']) ? '-' : '+' ?>$<?= number_format($tx['amount'], 2) ?>
                        </td>
                        <td class="text-secondary fw-bold">$<?= number_format($tx['running_balance'], 2) ?></td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm">
                                <a href="tx_edit.php?id=<?= $tx['id'] ?>" class="btn btn-sm btn-white border-color"><i class="fa-solid fa-pen"></i></a>
                                <a href="?delete_id=<?= $tx['id'] ?>" class="btn btn-sm btn-white border-color text-danger" onclick="return confirm('Delete this record? This won\'t affect the user\'s current balance.')"><i class="fa-solid fa-trash"></i></a>
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
    .x-small { font-size: 0.65rem; }
    .btn-white { background: #fff; color: #333; }
</style>

<?php include('layout/footer.php'); ?>