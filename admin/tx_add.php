<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

// Fetch users for the dropdown
$users = $pdo->query("SELECT id, fullname, email, account_balance FROM users ORDER BY fullname ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_transaction'])) {
    $user_id = $_POST['user_id'];
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $update_balance = isset($_POST['update_balance']); // Checkbox to decide if we affect the actual wallet

    try {
        $pdo->beginTransaction();

        // 1. Get current balance if we need to update it or calculate running balance
        $uStmt = $pdo->prepare("SELECT account_balance FROM users WHERE id = ?");
        $uStmt->execute([$user_id]);
        $current_balance = $uStmt->fetchColumn();

        // 2. Determine the new running balance
        // If it's a "negative" type, subtract from running balance
        $is_negative = in_array($type, ['withdrawal', 'investment', 'penalty']);
        $new_running = $is_negative ? ($current_balance - $amount) : ($current_balance + $amount);

        // 3. Insert Transaction Record
        $ins = $pdo->prepare("INSERT INTO transactions (user_id, amount, type, description, running_balance) VALUES (?, ?, ?, ?, ?)");
        $ins->execute([$user_id, $amount, $type, $description, $new_running]);

        // 4. Update the actual User Account Balance (only if checked)
        if ($update_balance) {
            $upUser = $pdo->prepare("UPDATE users SET account_balance = ? WHERE id = ?");
            $upUser->execute([$new_running, $user_id]);
        }

        $pdo->commit();
        echo "<script>window.location='transactions.php?msg=added';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "System Error: " . $e->getMessage();
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="ledger-container p-4 p-md-5 shadow-lg border-0">
                <div class="mb-4">
                    <h4 class="fw-bold text-dark">Create Manual Entry</h4>
                    <p class="text-secondary small">Add a single transaction record to a user's ledger.</p>
                </div>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger border-0 small"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Select User</label>
                        <select name="user_id" class="form-select border-color" required>
                            <option value="">-- Choose User --</option>
                            <?php foreach($users as $u): ?>
                                <option value="<?= $u['id'] ?>">
                                    <?= htmlspecialchars($u['fullname']) ?> (Bal: $<?= number_format($u['account_balance'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small fw-bold mb-2">Transaction Type</label>
                            <select name="type" class="form-select border-color" required>
                                <option value="deposit">Deposit (Credit)</option>
                                <option value="profit">Profit (Credit)</option>
                                <option value="bonus">Bonus (Credit)</option>
                                <option value="withdrawal">Withdrawal (Debit)</option>
                                <option value="investment">Investment (Debit)</option>
                                <option value="penalty">Penalty (Debit)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold mb-2">Amount ($)</label>
                            <input type="number" name="amount" step="0.01" class="form-control border-color" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-2">Description / Note</label>
                        <textarea name="description" class="form-control border-color" rows="3" placeholder="Explain the reason for this entry..."></textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="update_balance" id="upBal" checked>
                            <label class="form-check-label small fw-bold" for="upBal">
                                Update user's actual account balance?
                            </label>
                            <div class="x-small text-muted">If unchecked, this only adds a log without changing their spendable funds.</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="add_transaction" class="btn btn-primary py-3 rounded-pill fw-bold shadow-sm">
                            Process Transaction
                        </button>
                        <a href="transactions.php" class="btn btn-link text-secondary small">Cancel and Return</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .border-color { border-color: #e9ecef ! from; }
    .x-small { font-size: 0.75rem; }
    .form-select:focus, .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }
</style>

<?php include('layout/footer.php'); ?>