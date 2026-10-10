<?php 
include('layout/header.php'); 

$user_id = $_SESSION['user_id'] ?? null; 
$message = "";

// --- HANDLER (Kept exactly as requested) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_deposit'])) {
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $method_name = htmlspecialchars($_POST['method_name']);
    
    $target_dir = "uploads/receipts/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    if (!empty($_FILES["proof_image"]["name"])) {
        $file_ext = strtolower(pathinfo($_FILES["proof_image"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        
        if (in_array($file_ext, $allowed_extensions)) {
            $file_name = "DEP_" . time() . "_" . $user_id . "." . $file_ext;
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["proof_image"]["tmp_name"], $target_file)) {
                $stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, method_details, proof_image_url, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
                if ($stmt->execute([$user_id, $amount, "Gateway: $method_name", $target_file])) {
                    $message = "<div class='bg-golf-green/10 border border-golf-green/20 text-golf-green p-4 rounded-2xl mb-6 text-center text-[10px] font-black uppercase tracking-widest'>Deposit submitted. Awaiting approval.</div>";
                }
            } else {
                $message = "<div class='bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-2xl mb-6 text-center text-[10px] font-black uppercase tracking-widest'>Upload failed. Check folder permissions.</div>";
            }
        } else {
            $message = "<div class='bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-2xl mb-6 text-center text-[10px] font-black uppercase tracking-widest'>Use JPG, PNG or PDF only.</div>";
        }
    }
}

$methods = $pdo->query("SELECT * FROM payment_methods WHERE status = 'active'")->fetchAll();
$stmt = $pdo->prepare("SELECT * FROM deposits WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$my_deposits = $stmt->fetchAll();
?>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="max-w-6xl mx-auto pb-20 px-4" x-data="{ 
    step: 1, 
    amount: '', 
    selectedMethod: '', 
    walletAddr: '', 
    qrUrl: '', 
    minAmt: 0,
    
    updateMethod(e) {
        const option = e.target.options[e.target.selectedIndex];
        if(!option.value) return;
        this.selectedMethod = option.value;
        this.walletAddr = option.dataset.addr;
        this.qrUrl = option.dataset.qr;
        this.minAmt = parseFloat(option.dataset.min);
    }
}">

    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-black font-['Syne'] text-white uppercase tracking-tighter leading-none">Add <span class="text-golf-green">Funds</span></h2>
        <p class="text-gray-500 text-[9px] font-black uppercase tracking-[0.3em] mt-2">Secure Wallet Deposit</p>
    </div>

    <?= $message ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Main Form -->
        <div class="lg:col-span-7">
            <div class="bg-surface border border-white/5 rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative">
                
                <!-- Step 1: Select & Amount -->
                <div x-show="step === 1" x-transition>
                    <div class="space-y-6">
                        <div>
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-2">Payment Method</label>
                            <div class="relative mt-2">
                                <select @change="updateMethod" class="w-full bg-deep-dark border border-white/10 rounded-2xl px-6 py-4 text-white font-bold appearance-none focus:border-golf-green/50 outline-none transition-all text-xs uppercase tracking-widest">
                                    <option value="">Select Method</option>
                                    <?php foreach($methods as $m): ?>
                                    <option value="<?= $m['name'] ?>" data-addr="<?= $m['wallet_address'] ?>" data-qr="<?= $m['qr_code_url'] ?>" data-min="<?= $m['min_deposit'] ?>">
                                        <?= htmlspecialchars($m['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-golf-green text-[10px]"></i>
                            </div>
                        </div>

                        <div x-show="selectedMethod" x-transition>
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-2">Amount to Deposit ($)</label>
                            <div class="relative mt-2">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-golf-green font-black text-xl">$</span>
                                <input type="number" x-model="amount" placeholder="0.00" 
                                       class="w-full bg-deep-dark border border-white/10 rounded-2xl pl-12 pr-6 py-5 text-white font-black text-2xl focus:border-golf-green/50 outline-none transition-all">
                            </div>
                            <p class="text-[9px] text-gray-600 font-bold uppercase mt-2 ml-2" x-text="'Min: $' + minAmt"></p>
                        </div>

                        <button @click="if(amount >= minAmt) { step = 2 } else { alert('Minimum is $' + minAmt) }"
                                x-show="selectedMethod && amount > 0"
                                class="w-full bg-white text-deep-dark font-black py-5 rounded-2xl uppercase text-[10px] tracking-[0.3em] hover:bg-golf-green transition-all shadow-lg">
                            Continue
                        </button>
                    </div>
                </div>

                <!-- Step 2: Pay Info -->
                <div x-show="step === 2" x-transition>
                    <div class="flex items-center justify-between mb-8">
                        <p class="text-[10px] font-black text-white uppercase tracking-widest">Payment Details</p>
                        <button @click="step = 1" class="text-gray-500 text-[9px] font-black uppercase underline underline-offset-4">Back</button>
                    </div>

                    <div class="flex flex-col md:flex-row gap-8 items-center bg-deep-dark p-6 rounded-3xl border border-white/5">
                        <div class="w-32 h-32 bg-white p-3 rounded-2xl shadow-lg flex-shrink-0">
                            <img :src="qrUrl" class="w-full h-full object-contain" alt="QR">
                        </div>
                        <div class="space-y-4 w-full">
                            <div>
                                <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Send Exactly</p>
                                <p class="text-2xl font-black text-white" x-text="'$' + amount"></p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1" x-text="selectedMethod + ' Address'"></p>
                                <div class="flex items-center justify-between bg-black/40 px-4 py-3 rounded-xl border border-white/5">
                                    <p class="text-[10px] font-mono text-gray-300 break-all pr-4" x-text="walletAddr"></p>
                                    <button @click="navigator.clipboard.writeText(walletAddr); alert('Copied!')" class="text-golf-green hover:text-white">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button @click="step = 3" class="w-full mt-8 bg-white text-deep-dark font-black py-5 rounded-2xl uppercase text-[10px] tracking-[0.3em] hover:bg-golf-green transition-all">
                        I've made the payment
                    </button>
                </div>

                <!-- Step 3: Upload -->
                <div x-show="step === 3" x-transition>
                    <div class="flex items-center justify-between mb-8">
                        <p class="text-[10px] font-black text-white uppercase tracking-widest">Submit Proof</p>
                        <button @click="step = 2" class="text-gray-500 text-[9px] font-black uppercase underline underline-offset-4">Back</button>
                    </div>

                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="amount" :value="amount">
                        <input type="hidden" name="method_name" :value="selectedMethod">

                        <div class="relative group cursor-pointer">
                            <input type="file" name="proof_image" id="proof_image" required 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-30">
                            <div class="bg-deep-dark border-2 border-dashed border-white/10 rounded-3xl py-12 text-center group-hover:border-golf-green/40 transition-all">
                                <i class="fa-solid fa-camera text-2xl text-golf-green mb-4 opacity-50"></i>
                                <p class="text-white font-black text-[10px] uppercase tracking-widest">Select Screenshot</p>
                                <div id="fileNameDisplay" class="mt-2 text-golf-green text-[9px] font-black uppercase truncate px-4"></div>
                            </div>
                        </div>

                        <button type="submit" name="submit_deposit" class="w-full bg-golf-green text-deep-dark font-black py-5 rounded-2xl uppercase text-[10px] tracking-[0.3em] shadow-lg">
                            Finish Deposit
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Sidebar -->
        <div class="lg:col-span-5">
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-4 ml-2">Recent Deposits</p>
            <div class="space-y-3">
                <?php foreach($my_deposits as $dep): ?>
                <div class="bg-surface border border-white/5 p-5 rounded-3xl hover:bg-white/5 transition-all">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-lg font-black text-white">$<?= number_format($dep['amount'], 2) ?></p>
                            <p class="text-[8px] text-gray-500 font-bold uppercase tracking-widest"><?= date('M d, H:i', strtotime($dep['created_at'])) ?></p>
                        </div>
                        <?php 
                            $c = match($dep['status']) {
                                'approved' => 'text-golf-green bg-golf-green/10',
                                'pending'  => 'text-yellow-500 bg-yellow-500/10',
                                'rejected' => 'text-red-500 bg-red-500/10',
                                default => 'text-white bg-white/10'
                            };
                        ?>
                        <span class="px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-tighter <?= $c ?>">
                            <?= $dep['status'] ?>
                        </span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-white/5 flex justify-between items-center">
                        <span class="text-[8px] text-gray-600 font-black uppercase tracking-widest"><?= htmlspecialchars($dep['method_details']) ?></span>
                        <a href="<?= $dep['proof_image_url'] ?>" target="_blank" class="text-[8px] text-white/40 uppercase font-black hover:text-golf-green">View File</a>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if(empty($my_deposits)): ?>
                    <div class="text-center py-10 bg-surface/50 border border-dashed border-white/5 rounded-3xl">
                        <p class="text-[9px] text-gray-600 font-black uppercase tracking-widest italic">No history yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('proof_image').onchange = function () {
        document.getElementById('fileNameDisplay').innerHTML = this.files[0].name;
    };
</script>

<?php include('layout/footer.php'); ?>