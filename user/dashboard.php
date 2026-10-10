<?php include('layout/header.php'); 

// 1. Fetch Latest Notification
$notif_query = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 1");
$notif_query->execute([$user_id]);
$notification = $notif_query->fetch();

// 2. Fetch Recent Transactions
$trans_query = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$trans_query->execute([$user_id]);
$transactions = $trans_query->fetchAll();

// 3. Count Active Stakes/Trades (FIXED: Switched from $conn to $pdo)
$stmt_inv = $pdo->prepare("SELECT COUNT(*) as total FROM investments WHERE user_id = ? AND status = 'running'");
$stmt_inv->execute([$user_id]);
$active_inv = $stmt_inv->fetch();

$stmt_trades = $pdo->prepare("SELECT COUNT(*) as total FROM asset_investments WHERE user_id = ? AND status = 'open'");
$stmt_trades->execute([$user_id]);
$active_trades = $stmt_trades->fetch();

$currency = $user['currency'] ?? 'USD';
?>
<div class="mb-10">
    <h2 class="text-2xl font-black font-['Syne'] text-white uppercase tracking-tighter">
        Hello, <?= explode(' ', htmlspecialchars($user['fullname']))[0] ?>
    </h2>
    <p class="text-gray-500 text-xs font-semibold uppercase tracking-widest mt-1">
        Here is your portfolio performance for today.
    </p>
</div>

<?php if($notification): ?>
<div class="mb-8 bg-golf-green/10 border border-golf-green/20 p-4 rounded-2xl flex items-center gap-4">
    <div class="w-10 h-10 bg-golf-green text-deep-dark rounded-xl flex items-center justify-center shrink-0">
        <i class="fa-solid fa-envelope-open-text text-lg"></i>
    </div>
    <div class="flex-1">
        <h4 class="text-white text-[11px] font-black uppercase tracking-tighter"><?= $notification['title'] ?></h4>
        <p class="text-gray-400 text-[10px]"><?= substr($notification['message'], 0, 80) ?>...</p>
    </div>
    <a href="notifications.php" class="text-golf-green text-[10px] font-bold uppercase underline">View</a>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-10">
    <div class="bg-surface border border-white/5 p-6 rounded-3xl">
        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2">Account Balance</p>
        <h3 class="text-3xl font-black text-white font-['Syne'] tracking-tighter">
            <span class="text-golf-green"><?= $currency ?></span> <?= number_format($user['account_balance'], 2) ?>
        </h3>
    </div>
    
    <div class="bg-surface border border-white/5 p-6 rounded-3xl">
        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2">Total Staked</p>
        <h3 class="text-3xl font-black text-white font-['Syne'] tracking-tighter">
             <span class="text-golf-green"><?= $currency ?></span> <?= number_format($user['investment_balance'], 2) ?>
        </h3>
    </div>

    <div class="bg-surface border border-white/5 p-6 rounded-3xl">
        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2">Trading Profits</p>
        <h3 class="text-3xl font-black text-golf-green font-['Syne'] tracking-tighter">
            +<span class="text-golf-green"><?= $currency ?></span> <?= number_format($user['profit_balance'], 2) ?>
        </h3>
    </div>

    <div class="bg-surface border border-white/5 p-6 rounded-3xl">
        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2">Safe Vault</p>
        <h3 class="text-3xl font-black text-white font-['Syne'] tracking-tighter">
            <span class="text-golf-green"><?= $currency ?></span> <?= number_format($user['secure_vault_balance'] ?? 0.00, 2) ?>
        </h3>
    </div>

    <div class="bg-army-green text-white p-6 rounded-3xl flex items-center justify-between">
        <div>
            <p class="text-[10px] font-black text-light-army uppercase tracking-[0.2em] mb-1">Active Positions</p>
            <h3 class="text-2xl font-black font-['Syne']"><?= $active_inv['total'] + $active_trades['total'] ?></h3>
        </div>
        <i class="fa-solid fa-chart-line text-3xl opacity-30"></i>
    </div>
</div>

<div class="mb-12">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xs font-black uppercase tracking-widest text-white">Direct Access</h3>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="deposit.php" class="group bg-surface hover:bg-golf-green border border-white/5 p-4 rounded-2xl flex items-center gap-4 transition-all">
            <div class="w-10 h-10 bg-army-green group-hover:bg-deep-dark rounded-xl flex items-center justify-center text-golf-green transition-colors">
                <i class="fa-solid fa-arrow-down"></i>
            </div>
            <span class="text-[11px] font-black text-white uppercase group-hover:text-deep-dark transition-colors">Deposit</span>
        </a>
        <a href="withdraw.php" class="group bg-surface hover:bg-white border border-white/5 p-4 rounded-2xl flex items-center gap-4 transition-all">
            <div class="w-10 h-10 bg-army-green group-hover:bg-deep-dark rounded-xl flex items-center justify-center text-golf-green transition-colors">
                <i class="fa-solid fa-arrow-up"></i>
            </div>
            <span class="text-[11px] font-black text-white uppercase group-hover:text-deep-dark transition-colors">Withdraw</span>
        </a>
        <a href="plans.php" class="group bg-surface hover:border-golf-green border border-white/5 p-4 rounded-2xl flex items-center gap-4 transition-all">
            <div class="w-10 h-10 bg-army-green rounded-xl flex items-center justify-center text-golf-green">
                <i class="fa-solid fa-gem"></i>
            </div>
            <span class="text-[11px] font-black text-white uppercase">New Plan</span>
        </a>
        <a href="kyc.php" class="group bg-surface hover:border-golf-green border border-white/5 p-4 rounded-2xl flex items-center gap-4 transition-all">
            <div class="w-10 h-10 bg-army-green rounded-xl flex items-center justify-center text-golf-green">
                <i class="fa-solid fa-id-card"></i>
            </div>
            <span class="text-[11px] font-black text-white uppercase">Identity</span>
        </a>
    </div>
</div>

<div class="bg-gradient-to-r from-surface to-army-green/20 border border-white/5 p-8 rounded-[2.5rem] mb-12 flex flex-col md:flex-row items-center justify-between gap-6">
    <div class="text-center md:text-left">
        <h2 class="text-xl font-black font-['Syne'] text-white uppercase mb-2">Ready to grow your wealth?</h2>
        <p class="text-gray-400 text-xs">Our 2026 Asset Portfolios are generating up to 25% monthly returns.</p>
    </div>
    <a href="plans.php" class="bg-golf-green text-deep-dark px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-golf-green/20 hover:scale-105 transition-transform">
        Browse Investment Plans
    </a>
</div>

<div class="mb-10">
    <div class="flex items-center justify-between mb-6 px-2">
        <h3 class="text-xs font-black uppercase tracking-widest text-white">Recent Activity</h3>
        <a href="transactions.php" class="text-golf-green text-[10px] font-black uppercase tracking-tighter">View All <i class="fa-solid fa-chevron-right ml-1"></i></a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-separate border-spacing-y-2">
            <thead>
                <tr class="text-gray-600 text-[10px] uppercase tracking-widest">
                    <th class="px-6 py-2">Details</th>
                    <th class="px-6 py-2 text-right">Amount</th>
                    <th class="px-6 py-2 text-center">Status</th>
                    <th class="px-6 py-2 text-right">Date</th>
                </tr>
            </thead>
            <tbody>
    <?php if (count($transactions) > 0): ?>
        <?php foreach($transactions as $row): ?>
        <tr class="bg-surface/50 border border-white/5">
            <td class="px-6 py-4 rounded-l-2xl border-l border-t border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-deep-dark flex items-center justify-center text-golf-green border border-white/5">
                        <i class="fa-solid fa-receipt text-[10px]"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-white"><?= htmlspecialchars($row['type']) ?></p>
                        <p class="text-[9px] text-gray-500 truncate max-w-[150px]"><?= htmlspecialchars($row['description']) ?></p>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 text-right border-t border-b border-white/5">
                <span class="text-[11px] font-black text-white">
                    <?= ($row['amount'] < 0) ? '-' : '+' ?> <?= $currency ?> <?= number_format(abs($row['amount']), 2) ?>
                </span>
            </td>
            <td class="px-6 py-4 text-center border-t border-b border-white/5">
                <span class="bg-golf-green/10 text-golf-green text-[9px] font-black uppercase px-3 py-1 rounded-full">Success</span>
            </td>
            <td class="px-6 py-4 text-right rounded-r-2xl border-r border-t border-b border-white/5 text-[10px] text-gray-500 font-mono">
                <?= date('d M, Y', strtotime($row['created_at'])) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" class="text-center py-10 text-gray-600 text-xs uppercase tracking-widest">No recent transactions found</td>
        </tr>
    <?php endif; ?>
</tbody>
        </table>
    </div>
</div>

<?php include('layout/footer.php'); ?>