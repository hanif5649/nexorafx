<?php include('layout/header.php'); 

// --- PLAN PURCHASE HANDLER ---
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_plan'])) {
    $plan_id = filter_var($_POST['plan_id'], FILTER_SANITIZE_NUMBER_INT);
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $source_wallet = $_POST['wallet_type']; // account_balance, profit_balance, or trading_balance

    // Validate Wallet Type to prevent SQL injection
    $allowed_wallets = ['account_balance', 'profit_balance', 'trading_balance'];
    if (!in_array($source_wallet, $allowed_wallets)) {
        $message = "<div class='bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-xl mb-6'>Invalid wallet selected.</div>";
    } else {
        // Fetch Plan Details
        $stmt = $pdo->prepare("SELECT * FROM inv_plans WHERE id = ? AND status = 'active'");
        $stmt->execute([$plan_id]);
        $plan = $stmt->fetch();

        if (!$plan) {
            $message = "<div class='bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-xl mb-6'>Investment plan not found.</div>";
        } elseif ($amount < $plan['min_amount'] || $amount > $plan['max_amount']) {
            $message = "<div class='bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-xl mb-6'>Amount must be between ".number_format($plan['min_amount'])." and ".number_format($plan['max_amount'])."</div>";
        } elseif ($user[$source_wallet] < $amount) {
            $message = "<div class='bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-xl mb-6'>Insufficient funds in your ".str_replace('_', ' ', $source_wallet)."</div>";
        } else {
            try {
                $pdo->beginTransaction();

                // 1. Deduct from selected wallet
                $pdo->prepare("UPDATE users SET $source_wallet = $source_wallet - ? WHERE id = ?")->execute([$amount, $user_id]);

                // 2. Add to investment_balance
                $pdo->prepare("UPDATE users SET investment_balance = investment_balance + ? WHERE id = ?")->execute([$amount, $user_id]);

                // 3. Create Investment Record
                $end_date = date('Y-m-d H:i:s', strtotime("+{$plan['duration_days']} days"));
                $ins_inv = $pdo->prepare("INSERT INTO investments (user_id, plan_id, amount, start_date, end_date, status) VALUES (?, ?, ?, NOW(), ?, 'running')");
                $ins_inv->execute([$user_id, $plan_id, $amount, $end_date]);

                // 4. Record Transaction
                $new_balance = $user[$source_wallet] - $amount;
                $desc = "Purchased " . $plan['plan_name'] . " Plan via " . str_replace('_', ' ', $source_wallet);
                $ins_trans = $pdo->prepare("INSERT INTO transactions (user_id, amount, type, description, running_balance) VALUES (?, ?, 'Investment', ?, ?)");
                $ins_trans->execute([$user_id, -$amount, $desc, $new_balance]);

                $pdo->commit();
                $message = "<div class='bg-golf-green/10 border border-golf-green text-golf-green p-4 rounded-xl mb-6 font-bold uppercase text-xs tracking-widest'>Investment Successful! Your plan is now active.</div>";
                
                // Refresh user data for the display
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "<div class='bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-xl mb-6'>Transaction failed. Please try again.</div>";
            }
        }
    }
}

// Fetch all active plans
$plans = $pdo->query("SELECT * FROM inv_plans WHERE status = 'active' ORDER BY min_amount ASC")->fetchAll();
?>

<div class="max-w-6xl mx-auto">
    <!-- Header & Tutorial -->
    <div class="mb-10">
        <h2 class="text-3xl font-black font-['Syne'] text-white uppercase tracking-tighter mb-2">Investment Plans</h2>
        <div class="bg-army-green/20 border-l-4 border-golf-green p-4 rounded-r-2xl">
            <p class="text-golf-green text-[10px] font-black uppercase tracking-widest mb-1">How it works</p>
            <p class="text-gray-400 text-xs leading-relaxed">
                Choose a plan below, enter your desired investment amount, and select the wallet you wish to fund from. 
                Once confirmed, your funds will be moved to your <span class="text-white font-bold">Staked Balance</span> and start accruing ROI immediately.
            </p>
        </div>
    </div>

    <?= $message ?>

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($plans as $plan): ?>
        <div class="bg-surface border border-white/5 rounded-[2rem] p-8 flex flex-col relative overflow-hidden group">
            <!-- Decorative Background Element -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-golf-green/5 rounded-full blur-3xl group-hover:bg-golf-green/10 transition-all"></div>

            <div class="mb-6">
                <span class="bg-army-green text-golf-green text-[10px] font-black uppercase px-4 py-1 rounded-full tracking-widest">
                    <?= $plan['duration_days'] ?> Days Duration
                </span>
                <h3 class="text-2xl font-black text-white font-['Syne'] mt-4 uppercase"><?= htmlspecialchars($plan['plan_name']) ?></h3>
                <p class="text-gray-500 text-[11px] mt-2 leading-relaxed"><?= htmlspecialchars($plan['plan_description']) ?></p>
            </div>

            <div class="space-y-3 mb-8">
                <div class="flex justify-between items-center border-b border-white/5 pb-2">
                    <span class="text-gray-500 text-[10px] uppercase font-bold">Min Deposit</span>
                    <span class="text-white font-bold text-sm">$<?= number_format($plan['min_amount']) ?></span>
                </div>
                <div class="flex justify-between items-center border-b border-white/5 pb-2">
                    <span class="text-gray-500 text-[10px] uppercase font-bold">Max Deposit</span>
                    <span class="text-white font-bold text-sm">$<?= number_format($plan['max_amount']) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-[10px] uppercase font-bold">Return (ROI)</span>
                    <span class="text-golf-green font-black text-lg"><?= $plan['roi_percentage'] ?>%</span>
                </div>
            </div>

            <!-- Purchase Form -->
            <form method="POST" class="mt-auto space-y-4">
                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-gray-500 uppercase ml-2">Investment Amount</label>
                    <input type="number" name="amount" step="0.01" min="<?= $plan['min_amount'] ?>" max="<?= $plan['max_amount'] ?>" 
                           placeholder="Enter Amount" required
                           class="w-full bg-deep-dark border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-golf-green outline-none transition-all">
                </div>

                <div class="space-y-1">
                    <label class="text-[9px] font-black text-gray-500 uppercase ml-2">Select Source Wallet</label>
                    <select name="wallet_type" required
                            class="w-full bg-deep-dark border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-golf-green outline-none transition-all appearance-none">
                        <option value="account_balance">Main Wallet ($<?= number_format($user['account_balance'], 2) ?>)</option>
                        <option value="profit_balance">Profit Wallet ($<?= number_format($user['profit_balance'], 2) ?>)</option>
                        <option value="trading_balance">Trading Wallet ($<?= number_format($user['trading_balance'], 2) ?>)</option>
                    </select>
                </div>

                <button type="submit" name="buy_plan" 
                        class="w-full bg-golf-green text-deep-dark font-black text-[11px] uppercase tracking-widest py-4 rounded-2xl hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-golf-green/10">
                    Invest Now
                </button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include('layout/footer.php'); ?>