<?php 
include('layout/header.php'); 

$user_id = $_SESSION['user_id'] ?? null; 
$message = "";

// --- UNIVERSAL WITHDRAWAL HANDLER ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_withdrawal'])) {
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $method_type = htmlspecialchars($_POST['method_type']);
    $instruction_json = htmlspecialchars($_POST['transfer_instructions']);
    $input_pin = $_POST['withdrawal_pin'];

    $final_data = strtoupper($method_type) . " | " . $instruction_json;

    $stmt = $pdo->prepare("SELECT account_balance, withdrawal_pin FROM users WHERE id = ?"); 
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = "Session error.";
    } elseif ($amount > $user['account_balance']) {
        $message = "Insufficient balance.";
    } elseif ($input_pin !== $user['withdrawal_pin']) {
        $message = "Incorrect security PIN.";
    } elseif ($amount <= 0) {
        $message = "Invalid amount.";
    } else {
        $pdo->beginTransaction();
        try {
            $update = $pdo->prepare("UPDATE users SET account_balance = account_balance - ? WHERE id = ?");
            $update->execute([$amount, $user_id]);

            $insert = $pdo->prepare("INSERT INTO withdrawals (user_id, amount, method_name, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
            $insert->execute([$user_id, $amount, $final_data]);

            $pdo->commit();
            $message = "Request submitted successfully.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "System fault. Please contact support.";
        }
    }
}

$user_data = $pdo->prepare("SELECT account_balance FROM users WHERE id = ?");
$user_data->execute([$user_id]);
$current_balance = $user_data->fetchColumn() ?? 0;

$stmt = $pdo->prepare("SELECT * FROM withdrawals WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$my_withdrawals = $stmt->fetchAll();
?>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="max-w-7xl mx-auto pb-24 px-6" x-data="{ 
    step: 1, 
    amount: '', 
    methodType: '',
    instructions: '',
    balance: <?= $current_balance ?>,
    minWithdraw: 10 
}">

    <!-- System Header -->
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h2 class="text-3xl font-black font-['Syne'] text-white uppercase tracking-tighter">Settlement <span class="text-golf-green">Hub</span></h2>
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-[0.4em] mt-2">Asset Liquidation Protocol 2026</p>
        </div>
        <div class="bg-surface border border-white/5 px-8 py-4 rounded-2xl text-right">
            <p class="text-[9px] text-gray-500 font-black uppercase tracking-widest mb-1">Available Vault Balance</p>
            <p class="text-2xl font-black text-white font-mono">$<?= number_format($current_balance, 2) ?></p>
        </div>
    </div>

    <?= $message ? "<div class='bg-golf-green/10 border border-golf-green/20 p-4 rounded-xl mb-8 text-center text-xs font-black uppercase tracking-widest text-golf-green'>$message</div>" : "" ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Protocol Guidelines -->
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-surface border border-white/5 p-6 rounded-[2rem]">
                <p class="text-xs font-black text-golf-green uppercase tracking-widest mb-4">Payout Protocol</p>
                <div class="space-y-5">
                    <div>
                        <p class="text-[9px] text-white font-black uppercase tracking-widest mb-1">1. Verification Audit</p>
                        <p class="text-[11px] text-gray-500 leading-relaxed">Each request is audited by the security department. Payouts are typically processed within 24 hours.</p>
                    </div>
                    <div>
                        <p class="text-[9px] text-white font-black uppercase tracking-widest mb-1">2. Data Accuracy</p>
                        <p class="text-[11px] text-gray-500 leading-relaxed">Ensure Bank or Wallet details are entered correctly in the transfer data field to avoid rejection.</p>
                    </div>
                    <div>
                        <p class="text-[9px] text-white font-black uppercase tracking-widest mb-1">3. Security Lock</p>
                        <p class="text-[11px] text-gray-500 leading-relaxed">Authorization requires your private Withdrawal PIN. Contact support if you need a PIN reset.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Center: Form Section -->
        <div class="lg:col-span-5">
            <div class="bg-surface border border-white/5 rounded-[2.5rem] p-8 md:p-10 shadow-3xl">
                
                <form action="" method="POST">
                    <!-- Step 1 -->
                    <div x-show="step === 1" x-transition>
                        <div class="space-y-6">
                            <div>
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">Withdrawal Channel</label>
                                <select name="method_type" x-model="methodType" required class="w-full mt-2 bg-deep-dark border border-white/10 rounded-2xl px-6 py-4 text-white font-bold outline-none text-xs uppercase tracking-widest appearance-none">
                                    <option value="">Select Method</option>
                                    <option value="Local Bank">Local Bank Transfer</option>
                                    <option value="International Wire">International Wire (Swift)</option>
                                    <option value="Crypto Assets">Digital Assets (BTC/USDT)</option>
                                    <option value="Online Wallet">Online Wallet (PayPal/Skrill)</option>
                                </select>
                            </div>

                            <div x-show="methodType" x-transition>
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">Transfer Instructions (JSON/Details)</label>
                                <textarea name="transfer_instructions" x-model="instructions" required 
                                          placeholder=""
                                          class="w-full mt-2 bg-deep-dark border border-white/10 rounded-2xl px-6 py-4 text-white font-mono text-xs focus:border-golf-green/50 outline-none h-32 resize-none"></textarea>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">Payout Amount ($)</label>
                                <div class="relative mt-2">
                                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-golf-green font-black text-2xl">$</span>
                                    <input type="number" name="amount" x-model="amount" placeholder="0.00" required
                                           class="w-full bg-deep-dark border border-white/10 rounded-2xl pl-12 pr-6 py-5 text-white font-black text-2xl focus:border-golf-green/50 outline-none">
                                </div>
                            </div>

                            <button type="button" @click="if(amount >= minWithdraw && amount <= balance && instructions !== '') { step = 2 } else { alert('Verify instructions and balance') }"
                                    class="w-full bg-white text-deep-dark font-black py-5 rounded-2xl uppercase text-[11px] tracking-[0.4em] hover:bg-golf-green transition-all shadow-xl">
                                Proceed to Auth
                            </button>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div x-show="step === 2" x-transition>
                        <div class="text-center py-6">
    <p class="text-xs font-black text-white uppercase tracking-widest mb-6 italic underline underline-offset-8 decoration-golf-green">Confirm Authorization</p>
    
    <input type="password" name="withdrawal_pin" maxlength="6" placeholder="******" required
           class="w-full bg-black/40 border border-white/10 rounded-2xl py-6 text-center text-white font-black text-3xl tracking-[0.6em] focus:border-golf-green/50 outline-none mb-4">
    
    <!-- PIN Help Notice -->
    <div class="mb-6 p-4 bg-white/[0.02] border border-white/5 rounded-xl">
        <p class="text-[9px] text-gray-500 font-black uppercase tracking-widest mb-2">No Security PIN?</p>
        <p class="text-[10px] text-gray-400 leading-relaxed uppercase tracking-tighter">
            Contact support via <a href="mailto:admin@auchicity.com" class="text-golf-green hover:underline">Webmail</a> 
            or initiate a <a href="#" class="text-golf-green hover:underline">Live Chat</a> 
            session to generate your unique settlement key.
        </p>
    </div>

    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-[0.2em] leading-relaxed">
        You are authorizing a release of <span class="text-white" x-text="'$' + amount"></span> <br>
        to <span class="text-white" x-text="methodType"></span>
    </p>
</div>

                        <div class="grid grid-cols-2 gap-4 mt-8">
                            <button type="button" @click="step = 1" class="bg-white/5 text-white font-black py-4 rounded-xl uppercase text-[10px] tracking-widest border border-white/10">Modify</button>
                            <button type="submit" name="submit_withdrawal" class="bg-golf-green text-deep-dark font-black py-4 rounded-xl uppercase text-[10px] tracking-widest shadow-lg">Confirm</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: History Ledger -->
        <div class="lg:col-span-4">
            <p class="text-xs font-black text-gray-500 uppercase tracking-[0.3em] mb-6 ml-2">Recent Activity</p>
            <div class="space-y-4">
                <?php foreach($my_withdrawals as $w): ?>
                <div class="bg-surface border border-white/5 p-6 rounded-3xl hover:bg-white/5 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xl font-black text-white font-mono tracking-tighter">$<?= number_format($w['amount'], 2) ?></p>
                            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest mt-1"><?= date('M d, Y • H:i', strtotime($w['created_at'])) ?></p>
                        </div>
                        <?php 
                            $st = match($w['status']) {
                                'approved' => 'text-golf-green bg-golf-green/10 border-golf-green/20',
                                'pending'  => 'text-yellow-500 bg-yellow-500/10 border-yellow-500/20',
                                'rejected' => 'text-red-500 bg-red-500/10 border-red-500/20',
                                default => 'text-white bg-white/10'
                            };
                        ?>
                        <span class="px-3 py-1 rounded-lg border text-[9px] font-black uppercase tracking-widest <?= $st ?>">
                            <?= $w['status'] ?>
                        </span>
                    </div>

                    <div class="bg-black/20 p-3 rounded-xl border border-white/5">
                        <p class="text-[8px] text-gray-500 font-black uppercase mb-1">Details:</p>
                        <p class="text-[10px] text-gray-400 font-mono break-all truncate"><?= htmlspecialchars($w['method_name']) ?></p>
                    </div>

                    <?php if(!empty($w['admin_remark'])): ?>
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <div class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-golf-green mt-1"></div>
                            <p class="text-[11px] text-gray-300 italic leading-relaxed">"<?= htmlspecialchars($w['admin_remark']) ?>"</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

                <?php if(empty($my_withdrawals)): ?>
                    <div class="text-center py-12 bg-surface border border-dashed border-white/10 rounded-3xl">
                        <p class="text-xs text-gray-600 font-black uppercase tracking-widest italic">No pending settlements</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>