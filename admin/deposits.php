<?php
session_start();
require_once('../config/config.php');
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }


// --- 1. HANDLE APPROVAL ---
if (isset($_GET['approve_id'])) {
    $dep_id = (int)$_GET['approve_id'];
    
    try {
        $pdo->beginTransaction();
        
        // Get deposit details
        $stmt = $pdo->prepare("SELECT user_id, amount, status FROM deposits WHERE id = ?");
        $stmt->execute([$dep_id]);
        $deposit = $stmt->fetch();
        
        if ($deposit && $deposit['status'] == 'pending') {
            // Update User Balance
            $upUser = $pdo->prepare("UPDATE users SET account_balance = account_balance + ? WHERE id = ?");
            $upUser->execute([$deposit['amount'], $deposit['user_id']]);
            
            // Update Deposit Status
            $upDep = $pdo->prepare("UPDATE deposits SET status = 'approved' WHERE id = ?");
            $upDep->execute([$dep_id]);
            
            // Get user's updated balance
            $getBal = $pdo->prepare("SELECT account_balance FROM users WHERE id = ?");
            $getBal->execute([$deposit['user_id']]);
            $user = $getBal->fetch();

            $tx = $pdo->prepare("
            INSERT INTO transactions
            (user_id, amount, type, description, running_balance)
            VALUES
            (?, ?, 'deposit', ?, ?)
            ");

            $tx->execute([
                $deposit['user_id'],
                $deposit['amount'],
                'Wallet Funding via Direct Deposit',
                $user['account_balance']
        ]);
            
            $_SESSION['popup'] = [
                'type' => 'success',
                'title' => 'Approved Successfully!',
                'message' => 'Deposit of $' . number_format($deposit['amount'], 2) . ' has been approved and credited to user\'s wallet.'
            ];
        }
        
        $pdo->commit();
        header("Location: deposits.php"); exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['popup'] = [ 
            'type' => 'error',
            'title' => 'Approval Failed!',
            'message' => 'Error: ' . $e->getMessage()
        ];
        header("Location: deposits.php"); exit();
    }
}

// --- 2. HANDLE REJECTION ---
if (isset($_GET['reject_id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE deposits SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$_GET['reject_id']]);
        
        $_SESSION['popup'] = [
            'type' => 'warning',
            'title' => 'Rejected Successfully!',
            'message' => 'The deposit has been rejected.'
        ];
    } catch (Exception $e) {
        $_SESSION['popup'] = [
            'type' => 'error',
            'title' => 'Rejection Failed!',
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
    header("Location: deposits.php"); exit();
}

// --- 3. HANDLE DELETION ---
if (isset($_GET['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM deposits WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        
        $_SESSION['popup'] = [
            'type' => 'info',
            'title' => 'Deleted Successfully!',
            'message' => 'The deposit record has been permanently deleted.'
        ];
    } catch (Exception $e) {
        $_SESSION['popup'] = [
            'type' => 'error',
            'title' => 'Deletion Failed!',
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
    header("Location: deposits.php"); exit();
}
include('layout/header.php');

// Fetch all deposits with user names
$stmt = $pdo->query("SELECT d.*, u.fullname, u.email FROM deposits d JOIN users u ON d.user_id = u.id ORDER BY d.created_at DESC");
$deposits = $stmt->fetchAll();
?>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-5">
    <div class="mb-4">
        <h4 class="fw-bold mb-0">Deposit Management</h4>
        <p class="text-secondary small">Verify and approve incoming fund requests</p>
    </div>

    <div class="ledger-container p-0 overflow-hidden border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">User</th>
                        <th>Amount</th>
                        <th>Payment Proof</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($deposits as $dep): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-secondary"><?= htmlspecialchars($dep['fullname']) ?></div>
                            <div class="small text-muted"><?= $dep['email'] ?></div>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">$<?= number_format($dep['amount'], 2) ?></div>
                            <div class="x-small text-uppercase text-secondary"><?= date('M d, H:i', strtotime($dep['created_at'])) ?></div>
                        </td>
                        <td>
                            <?php if($dep['proof_image_url']): ?>
                                <a href="../user/<?= $dep['proof_image_url'] ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fa-solid fa-image me-1"></i> View Proof
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">No Proof</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($dep['status'] == 'pending'): ?>
                                <span class="badge bg-warning text-dark px-3 rounded-pill">Pending</span>
                            <?php elseif($dep['status'] == 'approved'): ?>
                                <span class="badge bg-success px-3 rounded-pill">Approved</span>
                            <?php else: ?>
                                <span class="badge bg-danger px-3 rounded-pill">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <?php if($dep['status'] == 'pending'): ?>
                                <div class="btn-group shadow-sm">
                                    <a href="#" class="btn btn-sm btn-white border-color text-success approve-btn" data-id="<?= $dep['id'] ?>" data-amount="$<?= number_format($dep['amount'], 2) ?>">
                                        <i class="fa-solid fa-check"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-white border-color text-warning reject-btn" data-id="<?= $dep['id'] ?>">
                                        <i class="fa-solid fa-xmark"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-white border-color text-danger delete-btn" data-id="<?= $dep['id'] ?>">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                <a href="#" class="btn btn-sm btn-white border-color text-danger delete-btn" data-id="<?= $dep['id'] ?>">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .btn-white { background: #fff; border: 1px solid #e9ecef; }
    .btn-white:hover { background: #f8f9fa; }
    .x-small { font-size: 0.65rem; font-weight: 800; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for popup from session
    <?php if(isset($_SESSION['popup'])): ?>
        Swal.fire({
            icon: '<?= $_SESSION['popup']['type'] ?>',
            title: '<?= $_SESSION['popup']['title'] ?>',
            text: '<?= $_SESSION['popup']['message'] ?>',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['popup']); ?>
    <?php endif; ?>

    // Approve Button Handler
    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const amount = this.dataset.amount;
            
            Swal.fire({
                title: 'Approve Deposit?',
                html: `Are you sure you want to approve this deposit of <strong>${amount}</strong>?<br><br>This will credit the user's wallet.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Approve!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?approve_id=${id}`;
                }
            });
        });
    });

    // Reject Button Handler
    document.querySelectorAll('.reject-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            
            Swal.fire({
                title: 'Reject Deposit?',
                text: 'Are you sure you want to reject this deposit?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reject!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?reject_id=${id}`;
                }
            });
        });
    });

    // Delete Button Handler
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            
            Swal.fire({
                title: 'Delete Record?',
                text: 'This action cannot be undone. Are you sure you want to delete this deposit record?',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?delete_id=${id}`;
                }
            });
        });
    });
});
</script>

<?php include('layout/footer.php'); ?>