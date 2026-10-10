<?php
// --- EMERGENCY DEBUGGING ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ---------------------------

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. Initial Config & Session
session_start();
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../config/config.php'; // Ensure path is correct

$message = "";
$status = "";

// 2. Handler for Password Reset Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_request'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // We generate the process regardless to prevent user enumeration
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        if ($user) {
            // Clean old resets for this email to keep DB tidy
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            
            // Store new token
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expires]);

            // PHPMailer Configuration
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'mail.fortunextrade.online'; // Change to your host
            $mail->SMTPAuth   = true;
            $mail->Username   = 'support@fortunextrade.online'; 
            $mail->Password   = 'controller30!';   

            // For SSL on Port 465, use ENCRYPTION_SMTPS
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
            $mail->Port       = 465;

            $mail->setFrom('support@fortunextrade.online', 'Global Wealth');
            $mail->addAddress($email);

            $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/user/reset-password.php?token=" . $token;
            
            $mail->isHTML(true);
            $mail->Subject = 'Terminal Access Recovery - Secure Protocol';
            $mail->Body    = "
                <div style='background: #050d1a; color: #ffffff; padding: 40px; font-family: sans-serif; border: 1px solid #2a5a7a;'>
                    <h2 style='color: #4a8aaa;'>SECURE RECOVERY INITIATED</h2>
                    <p style='color: #a0aec0;'>An access reset request was authorized for your account.</p>
                    <a href='$resetLink' style='display: inline-block; padding: 15px 30px; background: #1a3a5c; color: #ffffff; text-decoration: none; font-weight: 800; border-radius: 4px;'>BYPASS ENCRYPTION & RESET</a>
                    <p style='font-size: 10px; color: #4a8aaa; margin-top: 30px;'>LINK VALID FOR 60 MINUTES | SYSTEM ID: ".uniqid()."</p>
                </div>";

            $mail->send();
        }

        // Always show success for security
        $status = "success";
        $message = "Protocol Active: Check your secure inbox for instructions.";

    } catch (Exception $e) {
        $status = "error";
        $message = "Gateway Error: " . $mail->ErrorInfo;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recovery | Global Wealth Terminal</title>
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
        .scanning { position: relative; overflow: hidden; }
        .scanning::after {
            content: ''; position: absolute; top: -100%; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, transparent, rgba(42, 90, 122, 0.15), transparent);
            animation: scan 3s infinite linear;
        }
        @keyframes scan { 0% { top: -100%; } 100% { top: 100%; } }
        .navy-glow { box-shadow: 0 0 30px rgba(26, 58, 92, 0.1); }
    </style>
</head>
<body class="bg-deep-dark text-gray-400 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full">
        <!-- Brand -->
        <div class="text-center mb-10">
            <h1 class="font-['Syne'] text-2xl font-black text-white tracking-[0.2em] uppercase mb-1">Global <span class="text-navy-light">Wealth</span></h1>
            <p class="text-[9px] tracking-[0.5em] text-navy-light/40 font-bold uppercase">Asset Management Terminal</p>
        </div>

        <div class="scanning bg-surface border border-white/5 rounded-3xl p-8 md:p-12 shadow-2xl relative navy-glow">
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-navy-deep/60 rounded-xl flex items-center justify-center text-navy-light">
                        <i class="fa-solid fa-microchip animate-pulse"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-lg leading-tight">Access Recovery</h2>
                        <p class="text-[10px] text-navy-light/40 uppercase font-black tracking-widest">Protocol: 00-Reset</p>
                    </div>
                </div>

                <?php if($message): ?>
                    <div class="mb-6 p-4 rounded-lg text-[10px] font-bold uppercase tracking-wider <?= $status == 'success' ? 'bg-navy-dark/60 text-navy-light border border-navy-mid/30' : 'bg-red-500/5 text-red-500 border border-red-500/20' ?>">
                        <i class="fa-solid <?= $status == 'success' ? 'fa-check-double' : 'fa-triangle-exclamation' ?> mr-2"></i>
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-[0.3em] text-navy-light/40 mb-3 ml-1">Identity Identifier (Email)</label>
                        <div class="relative group">
                            <i class="fa-solid fa-at absolute left-4 top-1/2 -translate-y-1/2 text-navy-light/40 group-focus-within:text-navy-light transition-colors"></i>
                            <input type="email" name="email" required
                                   class="w-full bg-deep-dark border border-white/5 rounded-xl py-4 pl-12 pr-4 text-xs text-white placeholder-gray-700 focus:border-navy-mid/60 focus:ring-0 outline-none transition-all"
                                   placeholder="COMM-ID@WEALTH.SEC">
                        </div>
                    </div>

                    <button type="submit" name="reset_request"
                            class="w-full bg-navy-base text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.3em] shadow-lg shadow-navy-base/20 hover:bg-navy-light hover:text-deep-dark hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Execute Recovery
                    </button>
                </form>

                <div class="mt-10 pt-6 border-t border-white/5 text-center">
                    <a href="login.php" class="text-[9px] font-black uppercase tracking-[0.2em] text-navy-light/40 hover:text-navy-light transition-all">
                        <i class="fa-solid fa-chevron-left mr-2"></i> Return to Authorization
                    </a>
                </div>
            </div>
        </div>

        <p class="mt-8 text-center text-[9px] text-gray-600 font-medium uppercase tracking-[0.2em]">
            &copy; 2026 Capital Institutional Framework. <br> 
            <span class="text-navy-light/40">End-to-End Encryption Active.</span>
        </p>
    </div>

</body>
</html>