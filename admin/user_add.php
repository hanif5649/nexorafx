<?php
session_start();
if (!isset($_SESSION['admin_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
include('layout/header.php');

$message = "";
$error = "";

// --- FORM HANDLER ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['provision_user'])) {
    
    // Collect and sanitize input
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
    $phone = $_POST['phone_number'];
    $whatsapp = $_POST['whatsapp_number'];
    
    $pin = $_POST['pin'];
    $withdrawal_pin = $_POST['withdrawal_pin'];
    
    $acc_bal = $_POST['account_balance'] ?? 0.00;
    $inv_bal = $_POST['investment_balance'] ?? 0.00;
    $pro_bal = $_POST['profit_balance'] ?? 0.00;
    $trd_bal = $_POST['trading_balance'] ?? 0.00;
    
    $currency = $_POST['currency'] ?? 'USD';
    $rank = $_POST['rank'] ?? 'Beginner';
    $trader_account = $_POST['trader_account'];
    $status = $_POST['status'] ?? 'active';
    $admin_remark = $_POST['admin_remark'];

    try {
        $sql = "INSERT INTO users (
                    fullname, email, password, dob, phone_number, whatsapp_number, 
                    pin, withdrawal_pin, account_balance, investment_balance, 
                    profit_balance, trading_balance, currency, rank, 
                    trader_account, status, admin_remark
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $fullname, $email, $password, $dob, $phone, $whatsapp,
            $pin, $withdrawal_pin, $acc_bal, $inv_bal,
            $pro_bal, $trd_bal, $currency, $rank,
            $trader_account, $status, $admin_remark
        ]);
        
        $message = "Account for $fullname has been successfully provisioned!";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "Error: This email address is already registered.";
        } else {
            $error = "System Error: " . $e->getMessage();
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Header & Back Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">Provision New Investor</h4>
                    <p class="text-secondary small">Fill in the details to create a new managed account.</p>
                </div>
                <a href="users_list.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-2"></i> Back to Ledger
                </a>
            </div>

            <!-- Alerts -->
            <?php if($message): ?>
                <div class="alert alert-success border-0 bg-success-subtle text-success rounded-4 mb-4">
                    <i class="fa-solid fa-circle-check me-2"></i> <?= $message ?>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-danger border-0 bg-danger-subtle text-danger rounded-4 mb-4">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="user_add.php" method="POST" class="row g-4">
                
                <!-- Primary Identity -->
                <div class="col-md-8">
                    <div class="ledger-container p-4 h-100">
                        <h6 class="stat-label mb-4 text-primary">Identity Details</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="small fw-bold mb-1">Full Legal Name</label>
                                <input type="text" name="fullname" class="form-control bg-body border-color" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Email Address</label>
                                <input type="email" name="email" class="form-control bg-body border-color" placeholder="john@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Temporary Password</label>
                                <input type="text" name="password" class="form-control bg-body border-color" value="Invest2026!" required>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold mb-1">Date of Birth</label>
                                <input type="date" name="dob" class="form-control bg-body border-color">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold mb-1">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control bg-body border-color" placeholder="+1...">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold mb-1">WhatsApp</label>
                                <input type="text" name="whatsapp_number" class="form-control bg-body border-color">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security & Rank -->
                <div class="col-md-4">
                    <div class="ledger-container p-4 h-100">
                        <h6 class="stat-label mb-4 text-primary">Security & Status</h6>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Account PIN (6 Digits)</label>
                            <input type="text" name="pin" class="form-control bg-body border-color" maxlength="6" placeholder="000000">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Withdrawal PIN</label>
                            <input type="text" name="withdrawal_pin" class="form-control bg-body border-color" maxlength="6" placeholder="123456">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Investment Rank</label>
                            <select name="rank" class="form-select bg-body border-color">
                                <option value="Beginner">Beginner</option>
                                <option value="Professional">Professional</option>
                                <option value="VIP">VIP</option>
                                <option value="Whale">Whale</option>
                            </select>
                        </div>
                        <div class="">
                            <label class="small fw-bold mb-1">Account Status</label>
                            <select name="status" class="form-select bg-body border-color">
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Financial Configuration -->
                <div class="col-12">
                    <div class="ledger-container p-4">
                        <h6 class="stat-label mb-4 text-primary">Liquidity & Wallet Management</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="small fw-bold mb-1">Account Balance</label>
                                <input type="number" step="0.01" name="account_balance" class="form-control bg-body border-color fw-bold" value="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold mb-1">Investment Balance</label>
                                <input type="number" step="0.01" name="investment_balance" class="form-control bg-body border-color fw-bold" value="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold mb-1">Profit Balance</label>
                                <input type="number" step="0.01" name="profit_balance" class="form-control bg-body border-color fw-bold text-success" value="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold mb-1">Trading Balance</label>
                                <input type="number" step="0.01" name="trading_balance" class="form-control bg-body border-color fw-bold text-primary" value="0.00">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold mb-1">Base Currency</label>
                                <input type="text" name="currency" class="form-control bg-body border-color" value="USD">
                            </div>
                            <div class="col-md-8">
                                <label class="small fw-bold mb-1">Trader Account ID (MT4/MT5)</label>
                                <input type="text" name="trader_account" class="form-control bg-body border-color" placeholder="e.g. #ACC-990821">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Notes -->
                <div class="col-12">
                    <div class="ledger-container p-4">
                        <label class="small fw-bold mb-2">Admin Remarks (Internal Only)</label>
                        <textarea name="admin_remark" class="form-control bg-body border-color" rows="3" placeholder="Notes about this investor..."></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12 text-end">
                    <button type="submit" name="provision_user" class="btn btn-primary rounded-pill px-5 py-2 fw-bold">
                        Confirm & Create User
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>