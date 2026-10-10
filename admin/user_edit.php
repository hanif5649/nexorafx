<?php
session_start();
if (!isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
require_once('../config/config.php');

$message = "";
$error = "";

// 1. POPULAR CURRENCIES LIST
$currencies = [
    "USD" => "US Dollar ($)",
    "EUR" => "Euro (€)",
    "GBP" => "British Pound (£)",
    "NGN" => "Nigerian Naira (₦)",
    "CAD" => "Canadian Dollar (C$)",
    "AUD" => "Australian Dollar (A$)",
    "ZAR" => "South African Rand (R)",
    "GHS" => "Ghanaian Cedi (₵)",
    "KES" => "Kenyan Shilling (KSh)",
    "INR" => "Indian Rupee (₹)",
    "BTC" => "Bitcoin (฿)",
    "ETH" => "Ethereum (Ξ)",
    "USDT" => "Tether (₮)"
];

// 2. FETCH USER DATA
if (!isset($_GET['id'])) {
    header("Location: users_list.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: users_list.php");
    exit();
}

// 3. UPDATE HANDLER
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
    $phone = $_POST['phone_number'];
    $whatsapp = $_POST['whatsapp_number'];
    $pin = $_POST['pin'];
    $withdrawal_pin = $_POST['withdrawal_pin'];
    
    // Financials & Currency
    $acc_bal = $_POST['account_balance'];
    $inv_bal = $_POST['investment_balance'];
    $pro_bal = $_POST['profit_balance'];
    $trd_bal = $_POST['trading_balance'];
    $vault_bal = $_POST['secure_vault_balance'];
    $currency = $_POST['currency']; 
    
    $rank = $_POST['rank'];
    $trader_account = $_POST['trader_account'];
    $status = $_POST['status'];
    $admin_remark = $_POST['admin_remark'];

    // Update password only if provided
    $pass_query = "";
    $params = [$fullname, $email, $dob, $phone, $whatsapp, $pin, $withdrawal_pin, $acc_bal, $inv_bal, $pro_bal, $trd_bal, $vault_bal, $currency, $rank, $trader_account, $status, $admin_remark];
    
    if (!empty($_POST['new_password'])) {
        $pass_query = ", password = ?";
        $params[] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    }
    
    $params[] = $id;

    try {
        // Fixed SQL - wrapped `rank` in backticks since it's a reserved keyword
        $sql = "UPDATE users SET 
                fullname = ?, email = ?, dob = ?, phone_number = ?, whatsapp_number = ?, 
                pin = ?, withdrawal_pin = ?, account_balance = ?, investment_balance = ?, 
                profit_balance = ?, trading_balance = ?, secure_vault_balance = ?, currency = ?, `rank` = ?, 
                trader_account = ?, status = ?, admin_remark = ? $pass_query 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);

        $result = $stmt->execute($params);
        
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        // Redirect to clear POST data and show popup
        echo "<script>window.location='user_edit.php?id=" . $id . "';</script>";
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['popup'] = [
            'type' => 'error',
            'title' => 'Update Failed!',
            'message' => $e->getMessage()
        ];
        echo "<script>window.location='user_edit.php?id=" . $id . "';</script>";
        exit();
    }
}
include('layout/header.php');

// Check for popup messages
if (isset($_SESSION['popup'])) {
    $popup = $_SESSION['popup'];
    unset($_SESSION['popup']);
}
?>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">Edit Profile: <?= htmlspecialchars($user['fullname']) ?></h4>
                    <p class="text-secondary small">UID: #<?= $user['id'] ?> | Registered: <?= date('d M, Y', strtotime($user['created_at'])) ?></p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <a href="login_as_user.php?id=<?= $user['id'] ?>" target="_blank" class="btn btn-info btn-sm rounded-pill px-3 fw-bold shadow-sm">
                        <i class="fa-solid fa-user-secret me-1"></i> Login as User
                    </a>
                    <a href="users_list.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-chevron-left me-1"></i> Ledger
                    </a>
                </div>
            </div>

            <form action="user_edit.php?id=<?= $id ?>" method="POST" class="row g-4">
                
                <div class="col-12">
                    <div class="ledger-container p-4 border border-primary-subtle" style="background: rgba(var(--bs-primary-rgb), 0.05);">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                            <h6 class="stat-label mb-0 text-primary"><i class="fa-solid fa-money-bill-transfer me-2"></i>Wallet & Currency Management</h6>
                            
                            <div class="d-flex align-items-center gap-2">
                                <span class="small fw-bold text-secondary">Base Currency:</span>
                                <select name="currency" class="form-select form-select-sm bg-body border-color fw-bold text-primary" style="min-width: 180px;">
                                    <?php foreach($currencies as $code => $name): ?>
                                        <option value="<?= $code ?>" <?= ($user['currency'] == $code) ? 'selected' : '' ?>>
                                            <?= $name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4 col-lg-2.5" style="flex: 1 0 20%;">
                                <label class="small fw-bold mb-1">Account Balance</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-color small fw-bold text-secondary"><?= $user['currency'] ?></span>
                                    <input type="number" step="0.01" name="account_balance" class="form-control bg-body border-color fw-bold" value="<?= $user['account_balance'] ?>">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-2.5" style="flex: 1 0 20%;">
                                <label class="small fw-bold mb-1">Investment Pool</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-color small fw-bold text-secondary"><?= $user['currency'] ?></span>
                                    <input type="number" step="0.01" name="investment_balance" class="form-control bg-body border-color fw-bold" value="<?= $user['investment_balance'] ?>">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-2.5" style="flex: 1 0 20%;">
                                <label class="small fw-bold mb-1 text-success">Accrued Profit</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-color small fw-bold text-success"><?= $user['currency'] ?></span>
                                    <input type="number" step="0.01" name="profit_balance" class="form-control bg-body border-color fw-bold text-success" value="<?= $user['profit_balance'] ?>">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-2.5" style="flex: 1 0 20%;">
                                <label class="small fw-bold mb-1 text-primary">Trading Capital</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-color small fw-bold text-primary"><?= $user['currency'] ?></span>
                                    <input type="number" step="0.01" name="trading_balance" class="form-control bg-body border-color fw-bold text-primary" value="<?= $user['trading_balance'] ?>">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-2.5" style="flex: 1 0 20%;">
                                <label class="small fw-bold mb-1 text-warning">Safe Vault Balance</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-color small fw-bold text-warning"><?= $user['currency'] ?></span>
                                    <input type="number" step="0.01" name="secure_vault_balance" class="form-control bg-body border-color fw-bold text-warning" value="<?= $user['secure_vault_balance'] ?? 0.00 ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="ledger-container p-4 h-100 shadow-sm">
                        <h6 class="stat-label mb-4 text-primary"><i class="fa-solid fa-user-tag me-2"></i>Investor Identity</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Full Name</label>
                                <input type="text" name="fullname" class="form-control bg-body border-color" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Email Address</label>
                                <input type="email" name="email" class="form-control bg-body border-color" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control bg-body border-color" value="<?= $user['phone_number'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">WhatsApp No.</label>
                                <input type="text" name="whatsapp_number" class="form-control bg-body border-color" value="<?= $user['whatsapp_number'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Date of Birth</label>
                                <input type="date" name="dob" class="form-control bg-body border-color" value="<?= $user['dob'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1 text-warning">Override Password (Leave blank to keep)</label>
                                <input type="password" name="new_password" class="form-control bg-body border-color" placeholder="Enter new password">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="ledger-container p-4 h-100 shadow-sm">
                        <h6 class="stat-label mb-4 text-primary"><i class="fa-solid fa-shield-halved me-2"></i>Security Controls</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Account PIN</label>
                                <input type="text" name="pin" class="form-control bg-body border-color text-center fw-bold letter-spacing-2" maxlength="6" value="<?= $user['pin'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Withdraw PIN</label>
                                <input type="text" name="withdrawal_pin" class="form-control bg-body border-color text-center fw-bold letter-spacing-2" maxlength="6" value="<?= $user['withdrawal_pin'] ?>">
                            </div>
                            <div class="col-12">
                                <label class="small fw-bold mb-1">MT4/MT5 Trader ID</label>
                                <input type="text" name="trader_account" class="form-control bg-body border-color fw-mono" value="<?= $user['trader_account'] ?>" placeholder="#TRD-0000">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Investor Rank</label>
                                <select name="rank" class="form-select bg-body border-color">
                                    <option value="Beginner" <?= $user['rank'] == 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                                    <option value="Professional" <?= $user['rank'] == 'Professional' ? 'selected' : '' ?>>Professional</option>
                                    <option value="VIP" <?= $user['rank'] == 'VIP' ? 'selected' : '' ?>>VIP</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Account Status</label>
                                <select name="status" class="form-select bg-body border-color">
                                    <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="pending" <?= $user['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="suspended" <?= $user['status'] == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="ledger-container p-4 shadow-sm">
                        <label class="small fw-bold mb-2">Internal Admin Remarks (Private)</label>
                        <textarea name="admin_remark" class="form-control bg-body border-color" rows="3" placeholder="Add notes about this user's activity or verification status..."><?= $user['admin_remark'] ?></textarea>
                    </div>
                </div>

                <div class="col-12 text-end mb-5">
                    <button type="submit" name="update_user" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow">
                        Save Changes & Sync Data
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Popup Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($popup)): ?>
        Swal.fire({
            icon: '<?= $popup['type'] ?>',
            title: '<?= $popup['title'] ?>',
            text: '<?= $popup['message'] ?>',
            confirmButtonColor: '<?= $popup['type'] == 'error' ? '#dc3545' : '#3085d6' ?>',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>
});
</script>

<?php include('layout/footer.php'); ?>