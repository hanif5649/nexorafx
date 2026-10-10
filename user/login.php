<?php session_start(); include('../config/config.php');

$error = "";

// --- PHP LOGIN HANDLER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_btn'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['temp_user_id'] = $user['id'];
            // Password login always goes to PIN
            header("Location: pin.php");
            exit();
        } else {
            $error = "Access Denied: Invalid credentials detected.";
        }
    } catch (PDOException $e) {
        $error = "System Alert: Verification gateway timeout.";
    }
}
?>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Wealth | Secure Access</title>
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
        .glass { background: rgba(255, 255, 255, 0.01); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .scanning { position: relative; overflow: hidden; }
        .scanning::after {
            content: ''; position: absolute; top: -100%; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, transparent, rgba(42, 90, 122, 0.4), transparent);
            animation: scan 2s infinite;
        }
        @keyframes scan { 0% { top: -100%; } 100% { top: 100%; } }
        /* custom navy glow */
        .navy-glow { box-shadow: 0 0 15px rgba(26, 58, 92, 0.3); }
        .navy-border { border-color: rgba(42, 90, 122, 0.3); }
    </style>
</head>
<body class="bg-deep-dark text-gray-400 min-h-screen flex items-center justify-center p-4 md:p-0">

    <!-- MAIN CONTAINER: Split on Desktop, Stacked on Mobile -->
    <div class="flex flex-col md:flex-row w-full max-w-[900px] h-full md:h-[600px] glass rounded-3xl overflow-hidden shadow-2xl">
        
        <!-- LEFT SIDE: Branding & Visuals (Hidden on mobile or stacked) -->
        <div class="w-full md:w-1/2 bg-surface p-12 flex flex-col justify-between border-b md:border-b-0 md:border-r border-white/5">
            <div class="space-y-4">
                <div class="font-['Syne'] text-3xl font-bold text-navy-light italic tracking-tighter uppercase">
                    NEXORA
                </div>
                <div class="h-1 w-12 bg-navy-mid/60"></div>
                <p class="text-[10px] uppercase tracking-[0.4em] text-gray-500 font-black">Authorized Personnel Only</p>
            </div>

            <div class="hidden md:block space-y-6">
                <div class="text-white/20 text-6xl opacity-10">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h2 class="text-white text-lg font-bold leading-tight">Quantum-Encrypted<br>Wealth Management</h2>
                <p class="text-gray-600 leading-relaxed max-w-[200px]">Access the 2026 digital ecosystem for global commerce and secured assets.</p>
            </div>
            
            <div class="text-[9px] text-gray-700 uppercase tracking-widest">
                System Status: <span class="text-navy-light">Optimal</span>
            </div>
        </div>

        <!-- RIGHT SIDE: Auth Options -->
        <div class="w-full md:w-1/2 p-8 md:p-12 overflow-y-auto flex flex-col justify-center">
            
            <?php if($error): ?>
                <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl text-[10px] text-center">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- BIOMETRIC SECTION (Always Displayed as Requested) -->
            <div id="biometric-area" class="text-center space-y-4 mb-8">
                <div class="scanning w-16 h-16 bg-navy-deep/60 border border-navy-mid/40 rounded-2xl mx-auto flex items-center justify-center text-navy-light transition-all hover:bg-navy-dark/80 cursor-pointer" onclick="startPasskeyAuth()">
                    <i id="bio-icon" class="fa-solid fa-fingerprint text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-[10px] uppercase tracking-widest">Biometric Vault</h3>
                    <p id="bio-status" class="text-[9px] text-gray-600 mt-1 italic">Waiting for verification...</p>
                </div>
                <button type="button" onclick="startPasskeyAuth()" class="w-full py-3 rounded-xl bg-navy-dark/60 border border-navy-mid/40 text-navy-light text-[10px] font-black uppercase tracking-tighter hover:bg-navy-base hover:text-white transition-all">
                    Verify Identity
                </button>

                <div class="relative py-2">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-white/5"></div></div>
                    <div class="relative flex justify-center text-[9px] uppercase"><span class="bg-deep-dark px-2 text-gray-700">or manual login</span></div>
                </div>
            </div>

            <!-- MANUAL LOGIN FORM -->
            <form id="loginForm" method="POST" action="" class="space-y-4">
                <div>
                    <label class="text-[9px] uppercase font-black text-gray-600 mb-1 block tracking-widest">Email Address</label>
                    <input type="email" name="email" required placeholder="name@domain.com" class="w-full bg-surface border border-white/5 rounded-xl p-3 text-white text-[12px] outline-none focus:border-navy-mid/70 transition-all">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-[9px] uppercase font-black text-gray-600 block tracking-widest">Password</label>
                        <a href="forgot-password.php" class="text-[9px] text-navy-light/70 hover:text-navy-pale">Reset?</a>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password" required class="w-full bg-surface border border-white/5 rounded-xl p-3 text-white text-[12px] outline-none focus:border-navy-mid/70 transition-all">
                        <button type="button" onclick="togglePass()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-600 hover:text-navy-light">
                            <i id="eye-icon" class="fa-solid fa-eye-slash text-[10px]"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" name="login_btn" class="w-full p-4 rounded-xl bg-navy-base text-white font-black uppercase tracking-tighter shadow-xl transition-all hover:bg-navy-light hover:text-deep-dark active:scale-95">
                    Sign In
                </button>
            </form>

            <p class="mt-8 text-center text-[9px] uppercase tracking-[0.2em] text-gray-600 font-bold">
                No credentials? <a href="register.php" class="text-navy-light ml-1 underline decoration-navy-mid/40">Create Account</a>
            </p>
        </div>
    </div>

    <script>
        function togglePass() {
            const passInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.className = 'fa-solid fa-eye text-[10px]';
            } else {
                passInput.type = 'password';
                eyeIcon.className = 'fa-solid fa-eye-slash text-[10px]';
            }
        }

        async function startPasskeyAuth() {
    const statusText = document.getElementById('bio-status');
    const icon = document.getElementById('bio-icon');
    
    try {
        // 1. Get Challenge from Server (Simplified for this example)
        // In a real app, you'd fetch a random string from the server first
        
        // 2. Trigger the Browser Biometric Prompt
        // This is a simplified WebAuthn call
        const credential = await navigator.credentials.get({
            publicKey: {
                challenge: new Uint8Array([/* challenge from server */]),
                allowCredentials: [{
                    id: new Uint8Array([/* user's credential id */]),
                    type: 'public-key'
                }],
                userVerification: "required"
            }
        });

        // 3. Send the result to your new PHP endpoint
        icon.className = 'fa-solid fa-circle-notch fa-spin text-3xl';
        statusText.innerText = "Verifying with Secure Gateway...";

        const response = await fetch('verify-webauthn.php', {
            method: 'POST',
            body: JSON.stringify({
                id: credential.id,
                rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))),
                type: credential.type,
                response: {
                    authenticatorData: btoa(String.fromCharCode(...new Uint8Array(credential.response.authenticatorData))),
                    clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))),
                    signature: btoa(String.fromCharCode(...new Uint8Array(credential.response.signature)))
                }
            }),
            headers: { 'Content-Type': 'application/json' }
        });

        const result = await response.json();

        if (result.success) {
            statusText.innerText = "Identity Confirmed.";
            window.location.href = result.redirect;
        } else {
            statusText.innerText = result.message;
            icon.className = 'fa-solid fa-fingerprint text-3xl text-red-500';
        }

    } catch (err) {
        console.error(err);
        statusText.innerText = "Biometric session cancelled or failed.";
        icon.className = 'fa-solid fa-fingerprint text-3xl text-gray-600';
    }
}
    </script>
</body>
</html>