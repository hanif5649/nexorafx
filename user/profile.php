<?php 
include('layout/header.php'); 

$user_id = $_SESSION['user_id'] ?? null; 
$message = "";

// --- UPDATE HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Update Personal Info
    if (isset($_POST['update_profile'])) {
        $fullname = htmlspecialchars($_POST['fullname']);
        $phone = htmlspecialchars($_POST['phone_number']);
        $whatsapp = htmlspecialchars($_POST['whatsapp_number']);
        $dob = $_POST['dob'];

        $stmt = $pdo->prepare("UPDATE users SET fullname = ?, phone_number = ?, whatsapp_number = ?, dob = ? WHERE id = ?");
        if ($stmt->execute([$fullname, $phone, $whatsapp, $dob, $user_id])) {
            $message = "PROFILE_SYNC: Identity updated successfully.";
        }
    }

    // 2. Update Password
    if (isset($_POST['update_password'])) {
        $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$new_pass, $user_id]);
        $message = "SECURITY_ALERT: Password has been re-encrypted.";
    }

    // 3. Update PIN
    if (isset($_POST['update_pin'])) {
        $pin = htmlspecialchars($_POST['pin']);
        if(strlen($pin) == 6) {
            $stmt = $pdo->prepare("UPDATE users SET withdrawal_pin = ?, pin_status = 1 WHERE id = ?");
            $stmt->execute([$pin, $user_id]);
            $message = "VAULT_UPDATE: Authorization PIN active.";
        }
    }
}

// Fetch Fresh User Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$u = $stmt->fetch();

// Check if Biometrics/Passkey is already registered
$passkey_check = $pdo->prepare("SELECT id FROM user_passkeys WHERE user_id = ? LIMIT 1");
$passkey_check->execute([$user_id]);
$has_passkey = $passkey_check->fetch();
?>

<div class="max-w-7xl mx-auto pb-24 px-6" x-data="{ tab: 'identity' }">

    <!-- Header -->
    <div class="mb-10 border-b border-white/5 pb-8">
        <h2 class="text-3xl font-black font-['Syne'] text-white uppercase tracking-tighter italic">Account<span class="text-golf-green">Matrix</span></h2>
        <div class="flex gap-6 mt-6">
            <button @click="tab = 'identity'" :class="tab == 'identity' ? 'text-golf-green border-golf-green' : 'text-gray-500 border-transparent'" class="pb-2 border-b-2 font-black uppercase text-[10px] tracking-widest transition-all">Personal Info</button>
            <button @click="tab = 'security'" :class="tab == 'security' ? 'text-golf-green border-golf-green' : 'text-gray-500 border-transparent'" class="pb-2 border-b-2 font-black uppercase text-[10px] tracking-widest transition-all">Security & Vault</button>
        </div>
    </div>

    <?= $message ? "<div class='bg-golf-green/10 border border-golf-green/20 p-4 rounded-xl mb-8 text-center text-[10px] font-black uppercase tracking-widest text-golf-green'>$message</div>" : "" ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <!-- Left: Static Stats -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-surface border border-white/5 rounded-[2.5rem] p-8 text-center">
                <div class="w-24 h-24 bg-deep-dark border-4 border-golf-green/20 rounded-full mx-auto mb-6 flex items-center justify-center relative">
                    <span class="text-2xl font-black text-white"><?= strtoupper(substr($u['fullname'], 0, 1)) ?></span>
                    <div class="absolute bottom-0 right-0 w-6 h-6 bg-golf-green rounded-full border-4 border-surface flex items-center justify-center">
                        <i class="fa-solid fa-check text-[8px] text-deep-dark"></i>
                    </div>
                </div>
                <h3 class="text-white font-black uppercase tracking-tight"><?= $u['fullname'] ?></h3>
                <p class="text-[9px] text-gray-500 font-black uppercase tracking-[0.3em] mt-1"><?= $u['rank'] ?> Account</p>
                
                <div class="mt-8 grid grid-cols-2 gap-2">
                    <div class="bg-deep-dark p-3 rounded-xl border border-white/5">
                        <p class="text-[8px] text-gray-600 font-black uppercase">Status</p>
                        <p class="text-[10px] text-golf-green font-black uppercase"><?= $u['status'] ?></p>
                    </div>
                    <div class="bg-deep-dark p-3 rounded-xl border border-white/5">
                        <p class="text-[8px] text-gray-600 font-black uppercase">Currency</p>
                        <p class="text-[10px] text-white font-black uppercase"><?= $u['currency'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Biometric Quick Toggle -->
            <div class="bg-surface border border-white/5 rounded-[2.5rem] p-8">
                <div class="flex items-center justify-between mb-6">
                    <p class="text-[10px] font-black text-white uppercase tracking-widest">Biometric Login</p>
                    <i class="fa-solid fa-fingerprint text-golf-green"></i>
                </div>
                
                <?php if($has_passkey): ?>
                    <div class="flex items-center justify-between bg-deep-dark p-4 rounded-2xl border border-golf-green/20">
                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Status: Registered</span>
                        <a href="manage_biometrics.php?action=disable" class="text-[8px] font-black text-red-500 uppercase underline">Disable</a>
                    </div>
                <?php else: ?>
                    <a href="register_biometric.php" class="block w-full bg-white/5 border border-dashed border-white/20 text-white py-4 rounded-2xl text-center text-[10px] font-black uppercase tracking-widest hover:border-golf-green transition-all">
                        Setup FaceID / TouchID
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Forms -->
        <div class="lg:col-span-8">
            
            <!-- Tab: Identity -->
            <div x-show="tab == 'identity'" x-transition>
                <div class="bg-surface border border-white/5 rounded-[2.5rem] p-8 md:p-12 shadow-2xl">
                    <form action="" method="POST" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-1">Full Legal Name</label>
                                <input type="text" name="fullname" value="<?= $u['fullname'] ?>" required
                                       class="w-full mt-2 bg-deep-dark border border-white/10 rounded-2xl px-5 py-4 text-white font-bold text-xs outline-none focus:border-golf-green/50">
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-1">Email Address (Locked)</label>
                                <input type="email" value="<?= $u['email'] ?>" disabled
                                       class="w-full mt-2 bg-deep-dark/50 border border-white/5 rounded-2xl px-5 py-4 text-gray-600 font-bold text-xs cursor-not-allowed">
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-1">Phone Number</label>
                                <input type="text" name="phone_number" value="<?= $u['phone_number'] ?>"
                                       class="w-full mt-2 bg-deep-dark border border-white/10 rounded-2xl px-5 py-4 text-white font-bold text-xs outline-none focus:border-golf-green/50">
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-1">WhatsApp Line</label>
                                <input type="text" name="whatsapp_number" value="<?= $u['whatsapp_number'] ?>"
                                       class="w-full mt-2 bg-deep-dark border border-white/10 rounded-2xl px-5 py-4 text-white font-bold text-xs outline-none focus:border-golf-green/50">
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-1">Date of Birth</label>
                                <input type="date" name="dob" value="<?= $u['dob'] ?>"
                                       class="w-full mt-2 bg-deep-dark border border-white/10 rounded-2xl px-5 py-4 text-white font-bold text-xs outline-none focus:border-golf-green/50">
                            </div>
                        </div>

                        <button type="submit" name="update_profile" 
                                class="bg-white text-deep-dark font-black px-10 py-5 rounded-2xl uppercase text-[10px] tracking-[0.4em] hover:bg-golf-green transition-all shadow-xl">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tab: Security -->
            <div x-show="tab == 'security'" x-transition class="space-y-8">
                <!-- Password Change -->
                <div class="bg-surface border border-white/5 rounded-[2.5rem] p-10 shadow-2xl">
                    <h4 class="text-white font-black text-xs uppercase tracking-widest mb-8 flex items-center gap-3">
                        <i class="fa-solid fa-shield-halved text-golf-green"></i> System Password
                    </h4>
                    <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                        <div>
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-1">New Password</label>
                            <input type="password" name="password" required placeholder="••••••••"
                                   class="w-full mt-2 bg-deep-dark border border-white/10 rounded-2xl px-5 py-4 text-white font-bold text-xs outline-none focus:border-golf-green/50">
                        </div>
                        <button type="submit" name="update_password" 
                                class="bg-deep-dark border border-white/10 text-white font-black py-5 rounded-2xl uppercase text-[9px] tracking-[0.3em] hover:bg-white hover:text-black transition-all">
                            Refresh Password
                        </button>
                    </form>
                </div>

                <!-- Transaction PIN -->
                <div class="bg-surface border border-white/5 rounded-[2.5rem] p-10 shadow-2xl">
                    <h4 class="text-white font-black text-xs uppercase tracking-widest mb-8 flex items-center gap-3">
                        <i class="fa-solid fa-key text-golf-green"></i> Withdrawal PIN
                    </h4>
                    <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                        <div>
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest ml-1">Set 6-Digit PIN</label>
                            <input type="password" name="pin" maxlength="6" pattern="\d{6}" placeholder="000000" required
                                   class="w-full mt-2 bg-deep-dark border border-white/10 rounded-2xl px-5 py-4 text-white font-black text-xl tracking-[0.5em] outline-none focus:border-golf-green/50">
                        </div>
                        <button type="submit" name="update_pin" 
                                class="bg-golf-green text-deep-dark font-black py-5 rounded-2xl uppercase text-[9px] tracking-[0.3em] hover:bg-white transition-all shadow-xl">
                            Update Security PIN
                        </button>
                    </form>
                    <p class="mt-6 text-[9px] text-gray-500 uppercase font-bold tracking-widest">Note: This PIN is required for all fund transfers and settlement requests.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>