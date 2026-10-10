<?php 
include('layout/header.php');
$user_id = $_SESSION['user_id']; 

$message = "";
$currency = $user['currency'] ?? 'USD';

// --- TRANSFER HANDLER ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transfer_funds'])) {
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $from_wallet = $_POST['from_wallet'];
    $to_wallet = $_POST['to_wallet'];
    $fee = 10.00;
    $total_deduction = $amount + $fee;

    // List of wallets allowed for transfers (Included secure_vault_balance)
    $allowed_wallets = ['account_balance', 'profit_balance', 'trading_balance', 'secure_vault_balance'];

    if (!in_array($from_wallet, $allowed_wallets) || !in_array($to_wallet, $allowed_wallets)) {
        $message = "<div class='bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-xl mb-6 text-xs uppercase tracking-wider font-bold'>Invalid wallet selection.</div>";
    } elseif ($from_wallet === $to_wallet) {
        $message = "<div class='bg-yellow-500/10 border border-yellow-500 text-yellow-500 p-4 rounded-xl mb-6 text-xs uppercase tracking-wider font-bold'>Source and destination wallets cannot be the same.</div>";
    } elseif ($amount <= 0) {
        $message = "<div class='bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-xl mb-6 text-xs uppercase tracking-wider font-bold'>Please enter a valid amount.</div>";
    } elseif ($user[$from_wallet] < $total_deduction) {
        $message = "<div class='bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-xl mb-6 text-xs uppercase tracking-wider font-bold'>Insufficient funds. You need " . $currency . " " . number_format($total_deduction, 2) . " (including " . $currency . " 10 fee).</div>";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Deduct from source (Amount + Fee)
            $pdo->prepare($sql = "UPDATE users SET {$from_wallet} = {$from_wallet} - ? WHERE id = ?")->execute([$total_deduction, $user_id]);

            // 2. Add to destination (Only Amount)
            $pdo->prepare($sql = "UPDATE users SET {$to_wallet} = {$to_wallet} + ? WHERE id = ?")->execute([$amount, $user_id]);

            // 3. Record Transaction for the Transfer
            $desc = "Internal Transfer: " . str_replace('_', ' ', $from_wallet) . " to " . str_replace('_', ' ', $to_wallet);
            $pdo->prepare("INSERT INTO transactions (user_id, amount, type, description, running_balance) VALUES (?, ?, 'Transfer', ?, ?)")
                ->execute([$user_id, -$amount, $desc, ($user[$from_wallet] - $total_deduction)]);

            // 4. Record Transaction for the Fee
            $pdo->prepare("INSERT INTO transactions (user_id, amount, type, description, running_balance) VALUES (?, ?, 'Fee', 'Transfer Fee', ?)")
                ->execute([$user_id, -$fee, ($user[$from_wallet] - $total_deduction)]);

            $pdo->commit();
            $message = "<div class='bg-golf-green/10 border border-golf-green text-golf-green p-4 rounded-xl mb-6 font-black uppercase text-[10px] tracking-widest text-center'>Transfer Successful! " . $currency . " 10 flat fee applied.</div>";
            
            // Refresh User Data for Display
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "<div class='bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-xl mb-6 text-xs uppercase tracking-wider font-bold'>System error. Please try again later.</div>";
        }
    }
}
?>

<div class="max-w-5xl mx-auto pb-20">
    <div class="mb-10 text-center md:text-left">
        <h2 class="text-4xl font-black font-['Syne'] text-white uppercase tracking-tighter">My Wallets</h2>
        <p class="text-gray-500 text-[10px] font-black uppercase tracking-[0.3em] mt-2">Manage your assets and move liquidity between accounts</p>
    </div>

    <?= $message ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-12">
        <div class="bg-surface border border-white/5 p-6 rounded-[2rem] relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3">Main Balance</p>
                <h3 class="text-2xl font-black text-white font-['Syne']"><span class="text-golf-green text-lg font-bold mr-0.5"><?= $currency ?></span><?= number_format($user['account_balance'], 2) ?></h3>
            </div>
            <i class="fa-solid fa-wallet absolute -bottom-2 -right-2 text-4xl opacity-5 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-army-green border border-white/10 p-6 rounded-[2rem] relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-light-army uppercase tracking-widest mb-3">Active Investments</p>
                <h3 class="text-2xl font-black text-white font-['Syne']"><span class="text-white/60 text-lg font-bold mr-0.5"><?= $currency ?></span><?= number_format($user['investment_balance'], 2) ?></h3>
            </div>
            <i class="fa-solid fa-lock absolute -bottom-2 -right-2 text-4xl opacity-10 group-hover:rotate-12 transition-transform"></i>
        </div>

        <div class="bg-surface border border-white/5 p-6 rounded-[2rem] relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3">Profit Balance</p>
                <h3 class="text-2xl font-black text-golf-green font-['Syne']"><span class="text-golf-green text-lg font-bold mr-0.5"><?= $currency ?></span><?= number_format($user['profit_balance'], 2) ?></h3>
            </div>
            <i class="fa-solid fa-chart-line absolute -bottom-2 -right-2 text-4xl opacity-5 group-hover:translate-x-1 transition-transform"></i>
        </div>

        <div class="bg-surface border border-white/5 p-6 rounded-[2rem] relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3">Trading Balance</p>
                <h3 class="text-2xl font-black text-white font-['Syne']"><span class="text-golf-green text-lg font-bold mr-0.5"><?= $currency ?></span><?= number_format($user['trading_balance'], 2) ?></h3>
            </div>
            <i class="fa-solid fa-arrow-trend-up absolute -bottom-2 -right-2 text-4xl opacity-5 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-surface border border-white/5 p-6 rounded-[2rem] relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3">Secure Vault</p>
                <h3 class="text-2xl font-black text-white font-['Syne']"><span class="text-golf-green text-lg font-bold mr-0.5"><?= $currency ?></span><?= number_format($user['secure_vault_balance'] ?? 0.00, 2) ?></h3>
            </div>
            <i class="fa-solid fa-shield-halved absolute -bottom-2 -right-2 text-4xl opacity-5 group-hover:scale-110 transition-transform"></i>
        </div>
    </div>

    <div class="bg-surface border border-white/5 rounded-[3rem] p-8 lg:p-14 relative overflow-hidden">
        <div class="max-w-2xl mx-auto relative z-10">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-army-green rounded-2xl text-golf-green mb-4">
                    <i class="fa-solid fa-right-left text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-white font-['Syne'] uppercase">Instant Internal Transfer</h3>
                <p class="text-gray-500 text-xs mt-2 italic">Move funds instantly between your liquid wallets for a flat <?= $currency ?> 10 fee.</p>
            </div>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-4">Source Wallet</label>
                        <select name="from_wallet" class="w-full bg-deep-dark border border-white/10 rounded-2xl px-6 py-4 text-white text-sm focus:border-golf-green outline-none transition-all appearance-none cursor-pointer">
                            <option value="account_balance">Main Wallet (<?= $currency ?> <?= number_format($user['account_balance'], 2) ?>)</option>
                            <option value="profit_balance">Profit Wallet (<?= $currency ?> <?= number_format($user['profit_balance'], 2) ?>)</option>
                            <option value="trading_balance">Trading Wallet (<?= $currency ?> <?= number_format($user['trading_balance'], 2) ?>)</option>
                            <option value="secure_vault_balance">Secure Vault (<?= $currency ?> <?= number_format($user['secure_vault_balance'] ?? 0.00, 2) ?>)</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-4">Destination Wallet</label>
                        <select name="to_wallet" class="w-full bg-deep-dark border border-white/10 rounded-2xl px-6 py-4 text-white text-sm focus:border-golf-green outline-none transition-all appearance-none cursor-pointer">
                            <option value="trading_balance">Trading Wallet</option>
                            <option value="account_balance">Main Wallet</option>
                            <option value="profit_balance">Profit Wallet</option>
                            <option value="secure_vault_balance">Secure Vault</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-4">Amount to Transfer</label>
                    <div class="relative">
                        <span class="absolute left-6 top-1/2 -translate-y-1/2 text-golf-green font-bold"><?= $currency ?></span>
                        <input type="number" name="amount" step="0.01" required placeholder="0.00" 
                               class="w-full bg-deep-dark border border-white/10 rounded-2xl pl-16 pr-6 py-5 text-white font-black text-lg focus:border-golf-green outline-none transition-all">
                    </div>
                    <div class="flex items-center justify-between px-2 pt-2">
                        <p class="text-[9px] text-gray-600 uppercase font-black tracking-widest">Processing Fee: <span class="text-white"><?= $currency ?> 10.00</span></p>
                        <p class="text-[9px] text-gray-600 uppercase font-black tracking-widest">Network: <span class="text-golf-green">Instant</span></p>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" name="transfer_funds" 
                            class="w-full bg-white text-deep-dark font-black text-[11px] uppercase tracking-[0.25em] py-5 rounded-2xl hover:bg-golf-green transition-all shadow-2xl shadow-black/40">
                        Authorize Transfer
                    </button>
                </div>
            </form>
        </div>

        <div class="absolute -bottom-10 -left-10 opacity-[0.02] pointer-events-none">
            <h1 class="text-[200px] font-black font-['Syne'] leading-none">TRANSFER</h1>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>