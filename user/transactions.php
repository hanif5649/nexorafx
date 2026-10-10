<?php 
include('layout/header.php'); 

// Pagination logic
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total count for pagination
$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = ?");
$total_stmt->execute([$user_id]);
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Fetch transactions
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll();
?>

<div class="max-w-6xl mx-auto pb-20">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            <h2 class="text-4xl font-black font-['Syne'] text-white uppercase tracking-tighter">Activity Ledger</h2>
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-[0.3em] mt-2">A complete record of your financial movement</p>
        </div>
        <div class="bg-surface border border-white/5 px-6 py-3 rounded-2xl">
            <p class="text-[9px] font-black text-gray-500 uppercase mb-1">Current Liquid Balance</p>
            <p class="text-xl font-black text-golf-green">$<?= number_format($user['account_balance'], 2) ?></p>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-surface border border-white/5 rounded-[2.5rem] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date & Time</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Type</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Description</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Amount</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (count($transactions) > 0): ?>
                        <?php foreach ($transactions as $tx): ?>
                            <?php 
                                $is_positive = $tx['amount'] >= 0;
                                $type_class = $is_positive ? 'text-golf-green bg-golf-green/10' : 'text-red-400 bg-red-400/10';
                            ?>
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-8 py-6">
                                    <p class="text-white text-xs font-bold"><?= date('M d, Y', strtotime($tx['created_at'])) ?></p>
                                    <p class="text-gray-500 text-[10px]"><?= date('h:i A', strtotime($tx['created_at'])) ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter <?= $type_class ?>">
                                        <?= htmlspecialchars($tx['type']) ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-gray-400 text-xs italic leading-tight max-w-xs">
                                        <?= htmlspecialchars($tx['description']) ?>
                                    </p>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="text-sm font-black font-['Syne'] <?= $is_positive ? 'text-golf-green' : 'text-white' ?>">
                                        <?= $is_positive ? '+' : '' ?><?= number_format($tx['amount'], 2) ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="text-gray-500 text-xs font-mono">
                                        $<?= number_format($tx['running_balance'], 2) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <i class="fa-solid fa-receipt text-4xl text-white/10 mb-4"></i>
                                <p class="text-gray-500 text-xs uppercase font-black tracking-widest">No transactions found</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="px-8 py-6 border-t border-white/5 flex items-center justify-between">
            <p class="text-[10px] text-gray-500 font-bold uppercase">Page <?= $page ?> of <?= $total_pages ?></p>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="bg-deep-dark border border-white/10 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:border-golf-green transition-all">Prev</a>
                <?php endif; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="bg-deep-dark border border-white/10 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:border-golf-green transition-all">Next</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include('layout/footer.php'); ?>