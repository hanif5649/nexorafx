<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
include('layout/header.php');

// --- 1. HANDLE PROCESSING (New Handler) ---
if (isset($_POST['set_processing'])) {
    $id = $_POST['withdrawal_id'];
    $remark = $_POST['admin_remark'];

    $stmt = $pdo->prepare("UPDATE withdrawals SET status = 'processing', admin_remark = ? WHERE id = ?");
    $stmt->execute([$remark, $id]);
    echo "<script>window.location='withdrawals.php?msg=processing';</script>";
}

// --- 2. HANDLE APPROVAL ---
if (isset($_POST['approve_withdrawal'])) {
    $id = $_POST['withdrawal_id'];
    $remark = $_POST['admin_remark'];

    $stmt = $pdo->prepare("UPDATE withdrawals SET status = 'approved', admin_remark = ? WHERE id = ?");
    $stmt->execute([$remark, $id]);
    echo "<script>window.location='withdrawals.php?msg=approved';</script>";
}

// --- 3. HANDLE REJECTION (With Refund Logic) ---
if (isset($_POST['reject_withdrawal'])) {
    $id = $_POST['withdrawal_id'];
    $user_id = $_POST['user_id'];
    $amount = $_POST['amount'];
    $remark = $_POST['admin_remark'];

    try {
        $pdo->beginTransaction();

        // Update Status
        $stmt = $pdo->prepare("UPDATE withdrawals SET status = 'rejected', admin_remark = ? WHERE id = ?");
        $stmt->execute([$remark, $id]);

        // Refund User Balance
        $refund = $pdo->prepare("UPDATE users SET account_balance = account_balance + ? WHERE id = ?");
        $refund->execute([$amount, $user_id]);

        // Log Refund Transaction
        $tx = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, description) VALUES (?, 'refund', ?, 'approved', 'Withdrawal Rejected: Funds returned to wallet')");
        $tx->execute([$user_id, $amount]);

        $pdo->commit();
        echo "<script>window.location='withdrawals.php?msg=rejected';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Rejection Error: " . $e->getMessage());
    }
}

// --- 4. HANDLE DELETION ---
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM withdrawals WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    echo "<script>window.location='withdrawals.php?msg=deleted';</script>";
}

// Fetch all withdrawals
$stmt = $pdo->query("SELECT w.*, u.fullname, u.email, u.account_balance FROM withdrawals w JOIN users u ON w.user_id = u.id ORDER BY w.created_at DESC");
$withdrawals = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Withdrawal Requests</h4>
            <p class="text-secondary small">Review and process user payout requests</p>
        </div>
    </div>

    <div class="ledger-container p-0 overflow-hidden shadow-sm border-0 bg-white rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">User Details</th>
                        <th>Amount / Method</th>
                        <th>Status</th>
                        <th>Admin Remark</th>
                        <th class="text-end pe-4">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($withdrawals as $w): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark"><?= htmlspecialchars($w['fullname']) ?></div>
                            <div class="small text-muted"><?= htmlspecialchars($w['email']) ?></div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">-$<?= number_format($w['amount'], 2) ?></div>
                            <div class="x-small text-uppercase fw-bold text-primary"><?= htmlspecialchars($w['method_name']) ?></div>
                        </td>
                        <td>
                            <?php if($w['status'] == 'pending'): ?>
                                <span class="badge bg-warning text-dark rounded-pill px-3">Pending</span>
                            <?php elseif($w['status'] == 'processing'): ?>
                                <span class="badge bg-info text-dark rounded-pill px-3">Processing</span>
                            <?php elseif($w['status'] == 'approved'): ?>
                                <span class="badge bg-success rounded-pill px-3">Paid</span>
                            <?php else: ?>
                                <span class="badge bg-danger rounded-pill px-3">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="small text-muted italic">
                                <?= $w['admin_remark'] ? htmlspecialchars($w['admin_remark']) : '<span class="opacity-50">No notes yet</span>' ?>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <?php if($w['status'] == 'pending' || $w['status'] == 'processing'): ?>
                                <button class="btn btn-sm btn-dark rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#actionModal<?= $w['id'] ?>">
                                    <?= $w['status'] == 'processing' ? 'Update' : 'Process' ?>
                                </button>
                            <?php else: ?>
                                <a href="?delete_id=<?= $w['id'] ?>" class="btn btn-sm btn-white border-color text-danger" onclick="return confirm('Delete this record?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- MODAL FOR PROCESSING -->
                    <div class="modal fade" id="actionModal<?= $w['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <form method="POST">
                                    <div class="modal-body p-4">
                                        <h5 class="fw-bold mb-3">Update Withdrawal Status</h5>
                                        <div class="p-3 rounded-3 mb-3 border">
                                            <p class="small mb-1 text-muted">Requesting Amount:</p>
                                            <h4 class="fw-bold text-light mb-0">$<?= number_format($w['amount'], 2) ?></h4>
                                            <p class="x-small text-uppercase fw-bold text-primary mb-0 mt-2"><?= htmlspecialchars($w['method_name']) ?></p>
                                        </div>
                                        
                                        <input type="hidden" name="withdrawal_id" value="<?= $w['id'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $w['user_id'] ?>">
                                        <input type="hidden" name="amount" value="<?= $w['amount'] ?>">

                                        <div class="mb-3">
                                            <label class="small fw-bold mb-2">Admin Remark (Visible to user)</label>
                                            <textarea name="admin_remark" class="form-control border-color shadow-sm" rows="3" placeholder="e.g. Transaction ID or processing time..."><?= htmlspecialchars($w['admin_remark'] ?? '') ?></textarea>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <!-- Blue Button: Update and set to Processing -->
                                            <button type="submit" name="set_processing" class="btn btn-info py-2 rounded-pill fw-bold text-dark shadow-sm">
                                                <i class="fa-solid fa-spinner fa-spin me-1"></i> Update Remark & Keep Processing
                                            </button>
                                            
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <button type="submit" name="approve_withdrawal" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm">
                                                        Approve (Paid)
                                                    </button>
                                                </div>
                                                <div class="col-6">
                                                    <button type="submit" name="reject_withdrawal" class="btn btn-outline-danger w-100 py-2 rounded-pill fw-bold shadow-sm">
                                                        Reject (Refund)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .btn-white { background: #fff; border: 1px solid #dee2e6; }
    .x-small { font-size: 0.65rem; }
    .italic { font-style: italic; }
    .border-color { border-color: #e2e8f0; }
    .bg-info { background-color: #0dcaf0 !important; }
    .ledger-container { border: 1px solid #edf2f7; }
</style>

<?php include('layout/footer.php'); ?>