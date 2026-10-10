<?php session_start();
include('../config/config.php');

// 1. Session Continuity Check
if (!isset($_SESSION['temp_user_id'])) {
    // If no session, redirect to login
    header("Location: login.php");
    exit();
}

$error = "";
$user_id = $_SESSION['temp_user_id'];

// 2. Fetch User Data for Welcome Message & PIN Verification
try {
    $stmt = $pdo->prepare("SELECT fullname, pin FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header("Location: login.php?error=node_not_found");
        exit();
    }
    
    $fullname = $user['fullname'];
} catch (PDOException $e) {
    $error = "Core Error: " . $e->getMessage();
}

// 3. Handle PIN Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the array exists and combine it
    if (isset($_POST['pin_digits']) && is_array($_POST['pin_digits'])) {
        $entered_pin = trim(implode('', $_POST['pin_digits']));

        if ($entered_pin === trim($user['pin'])) {
            // Upgrade Session
            $_SESSION['user_authenticated'] = true;
            $_SESSION['user_id'] = $user_id;
            unset($_SESSION['temp_user_id']); // Clean up temp session

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid Security Credentials. Access Denied.";
        }
    }
}

// 4. Fetch Site Settings
$stmt_settings = $pdo->query("SELECT site_name, address FROM site_settings LIMIT 1");
$site = $stmt_settings->fetch();
$site_name = $site['site_name'] ?? "GLOBAL WEALTH";
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Identity | <?= htmlspecialchars($site_name) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Syne:wght@700&display=swap" rel="stylesheet">
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
        body { font-family: 'Inter', sans-serif; font-size: 11px; }
        .glass { background: rgba(255, 255, 255, 0.01); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .pin-input:focus { border-color: #4a8aaa; box-shadow: 0 0 15px rgba(74, 138, 170, 0.2); }
        @keyframes pulse-soft { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .pulse { animation: pulse-soft 2s infinite; }
    </style>
</head>
<body class="bg-deep-dark text-gray-400 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        <!-- Brand & Personal Welcome -->
        <div class="text-center mb-10">
            <div class="font-['Syne'] text-[10px] font-bold text-navy-light italic uppercase tracking-[0.3em] mb-4">
                <i class="fa-solid fa-shield-halved mr-2"></i> Identity Secured
            </div>
            <h1 class="text-white text-2xl font-black font-['Syne'] uppercase leading-none">
                Welcome, <span class="text-navy-light"><?= explode(' ', htmlspecialchars($fullname))[0] ?></span>
            </h1>
            <p class="text-[10px] uppercase tracking-widest text-gray-600 mt-3">Finalize encryption to enter</p>
        </div>

        <div class="glass p-8 rounded-3xl relative overflow-hidden">
            <!-- Decorative Scanner Line -->
            <div class="absolute top-0 left-0 w-full h-[2px] bg-navy-mid shadow-[0_0_15px_#2a5a7a] opacity-20"></div>

            <?php if($error): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl text-[10px] mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-sm"></i>
                    <span class="font-bold uppercase tracking-tighter"><?= $error ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" id="pinForm">
                <div class="flex justify-between gap-2 mb-10">
                    <?php for($i=0; $i<6; $i++): ?>
                        <input type="text" 
                               name="pin_digits[]" 
                               maxlength="1" 
                               inputmode="numeric"
                               pattern="[0-9]*"
                               required 
                               class="pin-input w-12 h-16 bg-surface border border-white/5 rounded-2xl text-center text-white text-2xl font-black outline-none transition-all">
                    <?php endfor; ?>
                </div>

                <button type="submit" class="w-full p-5 rounded-2xl bg-navy-base text-white font-black uppercase tracking-widest text-[10px] shadow-[0_10px_30px_rgba(26,58,92,0.3)] hover:bg-navy-light hover:text-deep-dark hover:scale-[1.02] active:scale-95 transition-all">
                    Authorize Access →
                </button>
            </form>

            <div class="mt-10 flex items-center justify-between border-t border-white/5 pt-6">
                <div class="flex flex-col">
                    <span class="text-[8px] uppercase text-gray-600 font-bold tracking-widest">Active Session</span>
                    <span class="text-[9px] text-white/50 font-mono italic"><?= htmlspecialchars($site_name) ?></span>
                </div>
                <a href="logout.php" class="text-[9px] uppercase font-black text-red-500/50 hover:text-red-500 transition">Terminate</a>
            </div>
        </div>

        <!-- Footer Data -->
        <p class="text-center mt-8 text-[9px] text-gray-700 uppercase tracking-[0.4em] font-bold pulse">
            System Online // 2026 Secured Protocol
        </p>
    </div>

    <script>
        const inputs = document.querySelectorAll('.pin-input');
        
        // Initial focus
        window.onload = () => inputs[0].focus();

        inputs.forEach((input, index) => {
            // Typing logic
            input.addEventListener('input', (e) => {
                // Ensure only numbers
                if (e.data && !/[0-9]/.test(e.data)) {
                    e.target.value = "";
                    return;
                }

                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            // Backspace logic
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Auto-submit on last digit
            if (index === 5) {
                input.addEventListener('input', () => {
                    if (input.value.length === 1) {
                        setTimeout(() => {
                            document.getElementById('pinForm').submit();
                        }, 150);
                    }
                });
            }
        });
    </script>
</body>
</html>