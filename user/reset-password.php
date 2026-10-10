<?php
// --- ERROR REPORTING FOR DEVELOPMENT ---
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require '../config/config.php';

$message = "";
$status = "";
$is_token_valid = false;

// 1. VERIFY THE TOKEN IMMEDIATELY ON PAGE LOAD
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        // Find a token that hasn't expired yet
        $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1");
        $stmt->execute([$token]);
        $reset_data = $stmt->fetch();

        if ($reset_data) {
            $is_token_valid = true;
            $email = $reset_data['email'];
        } else {
            $status = "error";
            $message = "AUTHORIZATION EXPIRED: TOKEN INVALID OR TIMED OUT.";
        }
    } catch (PDOException $e) {
        $status = "error";
        $message = "SYSTEM ERROR: DATABASE HANDSHAKE FAILED.";
    }
} else {
    // No token provided, redirect to forgot password
    header("Location: forgot-password.php");
    exit();
}

// 2. HANDLE THE PASSWORD UPDATE SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_btn'])) {
    $new_pass = $_POST['new_password'];
    $conf_pass = $_POST['confirm_password'];
    $form_token = $_POST['token'];

    if ($new_pass !== $conf_pass) {
        $status = "error";
        $message = "VALIDATION FAILED: PASSWORDS DO NOT MATCH.";
    } elseif (strlen($new_pass) < 8) {
        $status = "error";
        $message = "SECURITY POLICY: MINIMUM 8 CHARACTERS REQUIRED.";
    } else {
        try {
            // Re-verify email from token for maximum security
            $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? LIMIT 1");
            $stmt->execute([$form_token]);
            $user_info = $stmt->fetch();

            if ($user_info) {
                $target_email = $user_info['email'];
                $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

                // Update the user record
                $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                $update->execute([$hashed_password, $target_email]);

                // CRITICAL: Purge the token so it can't be used again
                $delete = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
                $delete->execute([$target_email]);

                $status = "success";
                $message = "ACCESS RESTORED. REDIRECTING TO PORTAL...";
                header("refresh:3;url=login.php");
            }
        } catch (PDOException $e) {
            $status = "error";
            $message = "PROCESSOR ERROR: FAILED TO COMMIT CHANGES.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credentials Update | Global Wealth</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;900&family=Syne:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Navy blue sequence matching the theme
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
        body { font-family: 'Inter', sans-serif; }
        .navy-glow { box-shadow: 0 0 30px rgba(26, 58, 92, 0.15); }
    </style>
</head>
<body class="bg-deep-dark text-gray-400 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <h1 class="font-['Syne'] text-xl font-black text-white tracking-[0.3em] uppercase">SYSTEM <span class="text-navy-light">RECOVERY</span></h1>
        </div>

        <div class="bg-surface border border-white/5 rounded-[2.5rem] p-10 shadow-2xl relative overflow-hidden navy-glow">
            <!-- Decorative Scanline -->
            <div class="absolute top-0 left-0 w-full h-[2px] bg-navy-mid/30 animate-[bounce_3s_infinite]"></div>

            <div class="mb-8">
                <h2 class="text-white font-bold text-lg">Update Credentials</h2>
                <p class="text-[9px] text-navy-light/60 uppercase font-black tracking-widest mt-1">Identity Confirmed via Secure Token</p>
            </div>

            <?php if($message): ?>
                <div class="mb-6 p-4 rounded-xl text-[10px] font-bold uppercase tracking-wider <?= $status == 'success' ? 'bg-navy-dark/60 text-navy-light border border-navy-mid/30' : 'bg-red-500/10 text-red-500 border border-red-500/20' ?>">
                    <i class="fa-solid <?= $status == 'success' ? 'fa-check-double' : 'fa-triangle-exclamation' ?> mr-2"></i>
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <?php if($is_token_valid && $status !== 'success'): ?>
                <form action="" method="POST" class="space-y-6">
                    <!-- Hidden Token Transfer -->
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">

                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-[0.3em] text-navy-light/60 mb-3 ml-1">New Access Key</label>
                        <div class="relative group">
                            <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-navy-light/40 group-focus-within:text-navy-light transition-colors"></i>
                            <input type="password" name="new_password" required
                                   class="w-full bg-deep-dark border border-white/5 rounded-xl py-4 pl-12 pr-4 text-xs text-white focus:border-navy-mid/60 outline-none transition-all"
                                   placeholder="••••••••••••">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-[0.3em] text-navy-light/60 mb-3 ml-1">Confirm Security Password</label>
                        <div class="relative group">
                            <i class="fa-solid fa-shield-halved absolute left-4 top-1/2 -translate-y-1/2 text-navy-light/40 group-focus-within:text-navy-light transition-colors"></i>
                            <input type="password" name="confirm_password" required
                                   class="w-full bg-deep-dark border border-white/5 rounded-xl py-4 pl-12 pr-4 text-xs text-white focus:border-navy-mid/60 outline-none transition-all"
                                   placeholder="••••••••••••">
                        </div>
                    </div>

                    <button type="submit" name="update_btn"
                            class="w-full bg-navy-base text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.4em] shadow-lg shadow-navy-base/20 hover:bg-navy-light hover:text-deep-dark hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Finalize Reset
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-[10px] text-slate-500 mb-6 italic">Session closed or token expired. Please re-request access.</p>
                    <a href="forgot-password.php" class="inline-block text-[9px] font-black uppercase tracking-widest text-navy-light border border-navy-mid/30 px-6 py-3 rounded-lg hover:bg-navy-base hover:text-white transition-all">
                        Request New Link
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-[9px] text-navy-light/40 font-bold uppercase tracking-widest">
                <i class="fa-solid fa-lock mr-2"></i> End-to-End Secure Reset
            </p>
        </div>
    </div>

</body>
</html>