<?php
session_start();
include('../config/config.php'); 

// Check if config loaded $pdo correctly
if (!isset($pdo)) {
    die("Critical Error: Database variable initialization failed.");
}

$error = "";
$success = "";
$trader_account = "";

// 1. Handle Registration Post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_btn'])) {
    
    $fullname = trim($_POST['fullname']);
    $email    = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $dob      = $_POST['dob'];
    $phone    = $_POST['phone'];
    $whatsapp = $_POST['whatsapp'];
    $currency = $_POST['currency'];
    $pin      = $_POST['pin'];
    $wants_bio = isset($_POST['enable_biometrics']) ? 1 : 0;
    
    $trader_account = "GW-" . strtoupper(bin2hex(random_bytes(3)));

    try {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $check->execute([':email' => $email]);

        if ($check->fetch()) {
            $error = "Infrastructure Alert: Email already exists in our cluster.";
        } else {
            $sql = "INSERT INTO users (
                        fullname, email, password, dob, phone_number, 
                        whatsapp_number, currency, pin, trader_account, 
                        two_factor_enabled, status, `rank`
                    ) VALUES (
                        :name, :email, :pass, :dob, :phone, 
                        :whatsapp, :curr, :pin, :trader, 
                        :twofa, 'active', 'Beginner'
                    )";
            
            $stmt = $pdo->prepare($sql);
            $exec = $stmt->execute([
                ':name'     => $fullname,
                ':email'    => $email,
                ':pass'     => $password,
                ':dob'      => $dob,
                ':phone'    => $phone,
                ':whatsapp' => $whatsapp,
                ':curr'     => $currency,
                ':pin'      => $pin,
                ':trader'   => $trader_account,
                ':twofa'    => 0
            ]);

            if ($exec) {
                $_SESSION['temp_user_id'] = $pdo->lastInsertId();
                $_SESSION['temp_user_email'] = $email;
                $_SESSION['temp_user_name'] = $fullname;
                $success = "Account successfully created. Your Trader ID is: " . $trader_account;
            } else {
                $error = "System failure: The secure vault rejected the entry.";
            }
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}

// Fetch Site Settings
$stmt_settings = $pdo->query("SELECT * FROM site_settings LIMIT 1");
$site = $stmt_settings->fetch();

// Fallback values if table is empty
$site_name = $site['site_name'] ?? "GLOBAL WEALTH";
$site_address = $site['site_address'] ?? "Global Wealth";
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Wealth | Secure Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Syne:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Navy blue sequence: deeper and lighter navy blues
                        'navy-deep': '#0a1a2f',
                        'navy-dark': '#0f2847',
                        'navy-base': '#1a3a5c',
                        'navy-mid': '#2a5a7a',
                        'navy-light': '#4a8aaa',
                        'navy-pale': '#7ab8d4',
                        'surface': '#0c1a2b',
                        'deep-dark': '#050d1a'
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; font-size: 11px; }
        .step-content { display: none; }
        .step-content.active { display: block; animation: slideUp 0.3s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        .glass { background: rgba(255, 255, 255, 0.01); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .scanning { position: relative; overflow: hidden; }
        .scanning::after {
            content: ''; position: absolute; top: -100%; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, transparent, rgba(42, 90, 122, 0.3), transparent);
            animation: scan 2s infinite;
        }
        @keyframes scan { 0% { top: -100%; } 100% { top: 100%; } }
        .navy-glow { box-shadow: 0 0 20px rgba(26, 58, 92, 0.3); }
    </style>
</head>
<body class="bg-deep-dark text-gray-400 min-h-screen flex flex-col md:flex-row">

    <!-- LEFT PANEL: Branding -->
    <div class="w-full md:w-1/3 bg-surface p-10 border-b md:border-b-0 md:border-r border-white/5 flex flex-col justify-between">
        <div class="space-y-12">
            <div class="font-['Syne'] text-xl font-bold text-navy-light italic tracking-tighter uppercase">
                <?= htmlspecialchars($site_name) ?> <span class="text-white/30 text-[9px] block uppercase font-sans tracking-[0.3em] mt-1">Institutional Terminal</span>
            </div>
            
            <div class="space-y-6">
                <h1 class="text-3xl font-bold text-white font-['Syne'] leading-none">THE FUTURE OF <span class="text-navy-light italic underline decoration-navy-mid/30">WEALTH</span>.</h1>
                <p class="text-[11px] text-gray-500 leading-relaxed">Join 50,000+ traders utilizing high-frequency infrastructure and AI-driven portfolio management.</p>
                
                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-[10px] text-white/70 uppercase tracking-widest font-bold">
                        <i class="fa-solid fa-check-double text-navy-light"></i> 256-bit AES Encryption
                    </div>
                    <div class="flex items-center gap-3 text-[10px] text-white/70 uppercase tracking-widest font-bold">
                        <i class="fa-solid fa-check-double text-navy-light"></i> Biometric Auth Ready
                    </div>
                </div>
            </div>
        </div>

       <div class="mt-10 md:mt-0 pt-8 border-t border-white/5 opacity-40">
    <p class="text-[9px] uppercase font-black tracking-widest mb-2">Account Verified</p>
    <p class="text-[10px]"><?= htmlspecialchars($site_address) ?></p>
</div>
    </div>

    <!-- RIGHT PANEL: Registration Engine -->
    <div class="w-full md:w-2/3 p-6 md:p-20 flex flex-col justify-center items-center relative overflow-hidden">
        <div class="w-full max-w-sm relative z-10">

            <!-- Alerts -->
            <?php if($error): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl text-[10px] mb-8 flex items-center gap-3">
                    <i class="fa-solid fa-bolt-lightning"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div id="success-module" class="bg-navy-dark/60 border border-navy-mid/30 text-navy-light p-5 rounded-xl text-[11px] mb-8">
                    <p class="font-bold mb-2 uppercase tracking-widest">Account Accepted</p>
                    <p id="success-text" class="text-gray-400 mb-4"><?= $success ?></p>
                    <div id="bio-enrollment-ui" class="hidden flex items-center gap-4 p-4 glass rounded-xl mb-4 border-navy-mid/30">
                         <div class="scanning w-10 h-10 bg-navy-deep/60 rounded-lg flex items-center justify-center text-navy-light">
                             <i class="fa-solid fa-fingerprint"></i>
                         </div>
                         <p class="text-[9px] uppercase font-bold">Initializing Biometric Enrollment...</p>
                    </div>
                    <a href="login.php" class="inline-block px-4 py-2 bg-navy-base text-white font-black rounded-lg uppercase tracking-tighter hover:bg-navy-light hover:text-deep-dark transition">Enter Terminal →</a>
                </div>
            <?php endif; ?>

            <!-- Progress Bar -->
            <div class="flex items-center gap-2 mb-12">
                <div id="bar-1" class="h-1 flex-1 bg-navy-mid rounded-full shadow-[0_0_10px_rgba(42,90,122,0.3)]"></div>
                <div id="bar-2" class="h-1 flex-1 bg-white/5 rounded-full"></div>
                <div id="bar-3" class="h-1 flex-1 bg-white/5 rounded-full"></div>
            </div>

            <form id="regForm" method="POST" action="">
                
                <!-- STEP 1: IDENTITY -->
                <div class="step-content active" id="step-1">
                    <div class="mb-8">
                        <h2 class="text-white font-black text-xl font-['Syne']">01. IDENTITY</h2>
                        <p class="text-gray-500 mt-1 uppercase tracking-[0.2em] text-[9px]">Legal Entity Verification</p>
                    </div>
                    <div class="space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="text-[9px] uppercase font-black text-gray-600 mb-2 block tracking-widest">Full Legal Name</label>
                                <input type="text" name="fullname" required class="w-full bg-surface border border-white/5 rounded-xl p-4 text-white outline-none focus:border-navy-mid/60">
                            </div>
                            <div class="col-span-2">
                                <label class="text-[9px] uppercase font-black text-gray-600 mb-2 block tracking-widest">Date of Birth</label>
                                <input type="date" name="dob" required class="w-full bg-surface border border-white/5 rounded-xl p-4 text-white outline-none focus:border-navy-mid/60">
                            </div>
                            <div>
                                <label class="text-[9px] uppercase font-black text-gray-600 mb-2 block tracking-widest">Phone</label>
                                <input type="text" name="phone" required class="w-full bg-surface border border-white/5 rounded-xl p-4 text-white outline-none focus:border-navy-mid/60" placeholder="+44...">
                            </div>
                            <div>
                                <label class="text-[9px] uppercase font-black text-gray-600 mb-2 block tracking-widest">WhatsApp</label>
                                <input type="text" name="whatsapp" required class="w-full bg-surface border border-white/5 rounded-xl p-4 text-white outline-none focus:border-navy-mid/60">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: SECURITY -->
                <div class="step-content" id="step-2">
                    <div class="mb-8">
                        <h2 class="text-white font-black text-xl font-['Syne']">02. ACCESS</h2>
                        <p class="text-gray-500 mt-1 uppercase tracking-[0.2em] text-[9px]">Encryption Configuration</p>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label class="text-[9px] uppercase font-black text-gray-600 mb-2 block tracking-widest">Email Address</label>
                            <input type="email" name="email" required class="w-full bg-surface border border-white/5 rounded-xl p-4 text-white outline-none focus:border-navy-mid/60">
                        </div>
                        <div>
                            <label class="text-[9px] uppercase font-black text-gray-600 mb-2 block tracking-widest">Password</label>
                            <div class="relative">
                                <input type="password" name="password" id="password" required class="w-full bg-surface border border-white/5 rounded-xl p-4 text-white outline-none focus:border-navy-mid/60">
                                <button type="button" onclick="togglePass()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-600 hover:text-navy-light">
                                    <i id="eye-icon" class="fa-solid fa-eye-slash text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="text-[9px] uppercase font-black text-gray-600 mb-2 block tracking-widest">Master PIN (6-Digits)</label>
                            <input type="text" name="pin" maxlength="6" required class="w-full bg-surface border border-white/5 rounded-xl p-4 text-white text-center tracking-[1.5em] font-bold outline-none focus:border-navy-mid/60" placeholder="000000">
                        </div>
                    </div>
                </div>

                <!-- STEP 3: PREFERENCES -->
                <div class="step-content" id="step-3">
                    <div class="mb-8">
                        <h2 class="text-white font-black text-xl font-['Syne']">03. TERMINAL</h2>
                        <p class="text-gray-500 mt-1 uppercase tracking-[0.2em] text-[9px]">Account Finalization</p>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="text-[9px] uppercase font-black text-gray-600 mb-2 block tracking-widest">Operating Currency</label>
                            <select name="currency" class="w-full bg-surface border border-white/5 rounded-xl p-4 text-white outline-none appearance-none cursor-pointer">
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                
                            </select>
                        </div>
                        
                        <!-- Biometric Switch -->
                        <div id="biometric-box" class="glass p-5 rounded-2xl border border-navy-mid/20 transition-all cursor-pointer group hover:border-navy-mid/50">
                            <div class="flex items-center gap-4">
                                <div id="bio-icon-wrap" class="w-12 h-12 bg-navy-deep/60 rounded-full flex items-center justify-center text-navy-light transition-all group-hover:scale-110">
                                    <i class="fa-solid fa-fingerprint text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-white font-bold text-[10px] uppercase tracking-wider">Passkey Protection</p>
                                    <p class="text-[9px] text-gray-500 mt-1 leading-tight">Enable FaceID or Fingerprint login for this device.</p>
                                </div>
                                <div class="relative inline-flex items-center">
                                    <input type="checkbox" name="enable_biometrics" id="bio-toggle" class="sr-only peer">
                                    <div class="w-9 h-5 bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-navy-mid after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-gray-400 after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex gap-4 mt-12">
                    <button type="button" id="prevBtn" onclick="move(-1)" class="hidden w-1/3 p-4 rounded-xl border border-white/5 text-gray-500 font-black uppercase tracking-tighter hover:bg-white/5 transition">Back</button>
                    <button type="button" id="nextBtn" onclick="move(1)" class="w-full p-4 rounded-xl bg-white text-deep-dark font-black uppercase tracking-tighter shadow-xl transition-all hover:scale-[1.02]">Next Step</button>
                    <button type="submit" name="register_btn" id="submitBtn" class="hidden w-full p-4 rounded-xl bg-navy-base text-white font-black uppercase tracking-tighter shadow-[0_0_20px_rgba(26,58,92,0.4)] transition-all hover:bg-navy-light hover:text-deep-dark hover:scale-[1.02]">Initialize Account</button>
                </div>
            </form>

            <p class="mt-10 text-center text-[9px] uppercase tracking-[0.3em] text-gray-600 font-bold">
                Already Secured? <a href="login.php" class="text-navy-light ml-2">Sign In</a>
            </p>
        </div>
    </div>

    <script>
        let current = 1;
        const total = 3;

        function move(n) {
            const steps = document.querySelectorAll('.step-content');
            steps[current - 1].classList.remove('active');
            current += n;
            steps[current - 1].classList.add('active');

            for(let i = 1; i <= total; i++) {
                const bar = document.getElementById(`bar-${i}`);
                bar.className = i <= current ? `h-1 flex-1 bg-navy-mid rounded-full transition-all shadow-[0_0_10px_rgba(42,90,122,0.3)]` : `h-1 flex-1 bg-white/5 rounded-full transition-all`;
            }

            document.getElementById('prevBtn').classList.toggle('hidden', current === 1);
            document.getElementById('nextBtn').classList.toggle('hidden', current === total);
            document.getElementById('submitBtn').classList.toggle('hidden', current !== total);
        }

        function togglePass() {
            const pass = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            pass.type = pass.type === 'password' ? 'text' : 'password';
            icon.className = pass.type === 'password' ? 'fa-solid fa-eye-slash text-xs' : 'fa-solid fa-eye text-xs text-navy-light';
        }

        // Biometric UI Toggle
        document.getElementById('biometric-box').addEventListener('click', () => {
            const t = document.getElementById('bio-toggle');
            t.checked = !t.checked;
            document.getElementById('bio-icon-wrap').className = t.checked ? 'w-12 h-12 bg-navy-mid rounded-full flex items-center justify-center text-deep-dark transition-all' : 'w-12 h-12 bg-navy-deep/60 rounded-full flex items-center justify-center text-navy-light transition-all';
        });

        // AUTO-ENROLLMENT LOGIC (Runs after PHP Success)
        <?php if($success && isset($_POST['enable_biometrics'])): ?>
        window.onload = async () => {
            const bioUI = document.getElementById('bio-enrollment-ui');
            const statusTxt = document.getElementById('success-text');
            bioUI.classList.remove('hidden');

            try {
                const credential = await navigator.credentials.create({
                    publicKey: {
                        challenge: crypto.getRandomValues(new Uint8Array(32)),
                        rp: { name: "Global Wealth" },
                        user: {
                            id: Uint8Array.from("<?= $_SESSION['temp_user_id'] ?>", c => c.charCodeAt(0)),
                            name: "<?= $_SESSION['temp_user_email'] ?>",
                            displayName: "<?= $_SESSION['temp_user_name'] ?>"
                        },
                        pubKeyCredParams: [{alg: -7, type: "public-key"}],
                        authenticatorSelection: { authenticatorAttachment: "platform" },
                        timeout: 60000
                    }
                });

                // Send to Handler
                const response = await fetch('store-webauthn.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        id: credential.id,
                        publicKey: btoa(String.fromCharCode(...new Uint8Array(credential.response.getPublicKey())))
                    }),
                    headers: {'Content-Type': 'application/json'}
                });
 
                const result = await response.json();
                if(result.success) {
                    statusTxt.innerText = "Biometrics Successfully Linked. You can now login using FaceID/Fingerprint.";
                    bioUI.innerHTML = '<div class="w-10 h-10 bg-navy-mid rounded-lg flex items-center justify-center text-deep-dark"><i class="fa-solid fa-check"></i></div><p class="text-[9px] uppercase font-bold">Biometrics Secured</p>';
                }
            } catch (err) {
                console.error(err);
                bioUI.innerHTML = '<div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center text-white"><i class="fa-solid fa-xmark"></i></div><p class="text-[9px] uppercase font-bold text-red-500">Enrollment Cancelled</p>';
            }
        };
        <?php endif; ?>
    </script>
</body>
</html>