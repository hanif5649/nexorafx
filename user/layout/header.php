<?php include('../config/config.php');
session_start();

// Authentication Guard
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// 1. Fetch Site Settings 
$site_query = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
$settings = $site_query->fetch();

// 2. Fetch User Data
$user_query = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_query->execute([$user_id]);
$user = $user_query->fetch();

// Admin-to-User Impersonation Check
$is_admin_ghost = isset($_SESSION['admin_as_user']);
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $settings['site_name'] ?> | Investment Portal</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Syne:wght@700&display=swap" rel="stylesheet">
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
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .glass { background: rgba(12, 26, 43, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(42, 90, 122, 0.15); }
        .nav-active { color: #4a8aaa !important; position: relative; }
        .nav-active::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 100%; height: 2px; background: #4a8aaa; border-radius: 10px; }
    </style>
</head>
<body class="bg-deep-dark text-gray-200 font-sans pb-24 md:pb-0">
<!-- Modern Pulse Preloader -->
<div id="preloader" class="fixed inset-0 z-[9999] bg-deep-dark flex flex-col items-center justify-center transition-opacity duration-700">
    <div class="relative">
        <!-- Outer Pulsating Ring -->
        <div class="absolute inset-[-20px] border-2 border-navy-mid/20 rounded-full animate-[ping_2s_linear_infinite]"></div>
        
        <!-- Middle Rotating Scanner -->
        <div class="w-20 h-20 border-t-2 border-r-2 border-navy-light rounded-full animate-spin"></div>
        
        <!-- Core Icon -->
        <div class="absolute inset-0 flex items-center justify-center">
            <i class="fa-solid fa-shield-halved text-navy-light text-2xl animate-pulse"></i>
        </div>
    </div>
    
    <!-- Loading Text -->
    <div class="mt-12 text-center">
        <p class="text-white font-black text-[10px] uppercase tracking-[0.6em] animate-pulse">Initializing</p>
        <div class="flex gap-1 justify-center mt-2">
            <span class="w-1 h-1 bg-navy-light rounded-full animate-bounce [animation-delay:-0.3s]"></span>
            <span class="w-1 h-1 bg-navy-light rounded-full animate-bounce [animation-delay:-0.15s]"></span>
            <span class="w-1 h-1 bg-navy-light rounded-full animate-bounce"></span>
        </div>
    </div>
</div>

<style>
    /* Prevent scrolling while loading */
    body.loading {
        overflow: hidden;
    }
</style>
    <!-- Admin Ghost Banner -->
    <?php if($is_admin_ghost): ?>
    <div class="bg-amber-500 text-black text-[10px] font-black text-center py-1 uppercase tracking-tighter sticky top-0 z-[100]">
        Control Mode Active: Viewing as <?= $user['fullname'] ?> | <a href="../admin/exit_user.php" class="underline">Return to Admin</a>
    </div>
    <?php endif; ?>

    <!-- Header -->
    <header class="glass sticky top-0 z-50 px-4 md:px-8 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4 md:gap-8">
                <a href="dashboard.php" class="font-['Syne'] text-xl font-bold tracking-tighter text-navy-light uppercase">
                    <?= substr($settings['site_name'], 0, 3) ?><span class="text-white"><?= substr($settings['site_name'], 3, 6) ?>.</span>
                </a>

                <!-- Mobile Instant Balance -->
                <div class="md:hidden bg-white/5 px-3 py-1.5 rounded-lg border border-white/5">
                    <p class="text-[7px] uppercase font-black text-gray-500 leading-none mb-0.5 tracking-widest">Available</p>
                    <p class="text-[11px] font-black text-white">$<?= number_format($user['account_balance'], 2) ?></p>
                </div>
                
                <!-- Desktop Menu -->
                <nav class="hidden lg:flex gap-6 items-center text-[13px] font-semibold text-gray-400">
                    <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'nav-active' : '' ?> hover:text-white transition">Overview</a>
                    <a href="plans.php" class="<?= $current_page == 'plans.php' ? 'nav-active' : '' ?> hover:text-white transition">Trading Plans</a>
                    <a href="wallet.php" class="<?= $current_page == 'wallet.php' ? 'nav-active' : '' ?> hover:text-white transition">My Wallet</a>
                    <a href="transactions.php" class="<?= $current_page == 'transactions.php' ? 'nav-active' : '' ?> hover:text-white transition">History</a>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                <!-- Desktop Balance & Rank -->
                <div class="hidden md:flex items-center gap-3 bg-surface border border-white/5 p-2 rounded-xl">
                    <div class="text-right">
                        <p class="text-[9px] uppercase font-bold text-navy-light/60 leading-none tracking-widest"><?= $user['rank'] ?></p>
                        <p class="text-sm font-bold text-white">$<?= number_format($user['account_balance'], 2) ?></p>
                    </div>
                    <div class="h-8 w-[1px] bg-white/10"></div>
                    <a href="notifications.php" class="relative text-gray-400 hover:text-navy-light px-2">
                        <i class="fa-solid fa-bell"></i>
                    </a>
                </div>
                
                <!-- Profile/Notification (Mobile View) -->
                <div class="flex items-center gap-3 md:hidden">
                    <a href="notifications.php" class="w-9 h-9 rounded-full bg-surface border border-white/10 flex items-center justify-center text-gray-400">
                        <i class="fa-solid fa-bell text-xs"></i>
                    </a>
                    <a href="profile.php" class="w-9 h-9 rounded-full bg-navy-dark flex items-center justify-center border border-navy-mid/30">
                        <i class="fa-solid fa-user-ninja text-navy-light text-sm"></i>
                    </a>
                </div>

                <!-- Desktop Profile -->
                <a href="profile.php" class="hidden md:flex items-center gap-2 group">
                    <div class="w-9 h-9 rounded-full bg-navy-dark flex items-center justify-center border border-navy-mid/30 group-hover:border-navy-light transition">
                        <i class="fa-solid fa-user-ninja text-navy-light text-sm"></i>
                    </div>
                </a>
            </div>
        </div>
    </header>

    <!-- Content Scroller (Sub-Nav) -->
    <div class="md:hidden overflow-x-auto no-scrollbar bg-surface border-b border-white/5 py-3 px-4 flex gap-5 text-[11px] font-bold uppercase tracking-wider text-gray-500 sticky top-[68px] z-40">
        <a href="running_investments.php" class="<?= $current_page == 'running_investments.php' ? 'text-navy-light' : '' ?> whitespace-nowrap">Running Stakes</a>
        <a href="running_trading.php" class="<?= $current_page == 'running_trading.php' ? 'text-navy-light' : '' ?> whitespace-nowrap">Live Trades</a>
        <a href="kyc.php" class="<?= $current_page == 'kyc.php' ? 'text-navy-light' : '' ?> whitespace-nowrap">KYC Status</a>
        <a href="notifications.php" class="<?= $current_page == 'notifications.php' ? 'text-navy-light' : '' ?> whitespace-nowrap">Notifications</a>
    </div>

    <main class="max-w-7xl mx-auto px-4 md:px-8 py-6">

    <!-- Mobile App Navigation (Fixed Bottom) -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 glass border-t border-white/5 px-6 py-4 flex justify-between items-end z-50 rounded-t-3xl">
        <a href="dashboard.php" class="flex flex-col items-center gap-1 <?= $current_page == 'dashboard.php' ? 'text-navy-light' : 'text-gray-500' ?>">
            <i class="fa-solid fa-house-user text-xl"></i>
            <span class="text-[9px] font-black uppercase tracking-tighter">Home</span>
        </a>
        <a href="wallet.php" class="flex flex-col items-center gap-1 <?= $current_page == 'wallet.php' ? 'text-navy-light' : 'text-gray-500' ?>">
            <i class="fa-solid fa-wallet text-xl"></i>
            <span class="text-[9px] font-black uppercase tracking-tighter">Wallet</span>
        </a>
        
        <!-- Center Action Button -->
        <div class="relative -top-8">
            <a href="deposit.php" class="w-16 h-16 bg-navy-base text-white rounded-2xl flex items-center justify-center shadow-2xl shadow-navy-base/40 border-4 border-deep-dark rotate-45 hover:rotate-90 transition-all duration-500">
                <i class="fa-solid fa-plus text-2xl -rotate-45"></i>
            </a>
        </div>

        <a href="notifications.php" class="flex flex-col items-center gap-1 <?= $current_page == 'notifications.php' ? 'text-navy-light' : 'text-gray-500' ?>">
            <i class="fa-solid fa-comment-dots text-xl"></i>
            <span class="text-[9px] font-black uppercase tracking-tighter">Inbox</span>
        </a>
        <a href="logout.php" class="flex flex-col items-center gap-1 text-red-500/70 hover:text-red-500">
            <i class="fa-solid fa-power-off text-xl"></i>
            <span class="text-[9px] font-black uppercase tracking-tighter">Exit</span>
        </a>
    </div>