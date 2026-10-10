<?php 
require_once('../config/config.php'); 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Core | <?= SITE_NAME ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root { --sidebar-width: 260px; --accent-color: #0d6efd; }
        body { font-family: 'Inter', sans-serif; font-size: 0.875rem; background-color: var(--bs-body-bg); }
        
        #sidebar { 
            width: var(--sidebar-width); height: 100vh; position: fixed; left: 0; top: 0; z-index: 1000; 
            border-right: 1px solid var(--bs-border-color); transition: all 0.3s;
            display: flex; flex-direction: column; background: var(--bs-body-tertiary);
        }
        
        #main-content { margin-left: var(--sidebar-width); min-height: 100vh; transition: all 0.3s; }

        .nav-link { 
            font-size: 0.825rem; font-weight: 500; padding: 0.7rem 1.2rem; 
            color: var(--bs-secondary-color); display: flex; align-items: center; transition: all 0.2s;
        }
        .nav-link i { width: 25px; font-size: 1rem; opacity: 0.8; }
        
        .nav-link.active { 
            color: var(--accent-color) !important; background: rgba(13, 110, 253, 0.08); 
            border-right: 3px solid var(--accent-color);
        }

        .section-label { 
            font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.2px; 
            color: var(--bs-secondary-emphasis); margin: 1.5rem 1.2rem 0.5rem; font-weight: 800; opacity: 0.6;
        }

        .nav-scrollable-area { flex-grow: 1; overflow-y: auto; overflow-x: hidden; padding-bottom: 20px; }
        .nav-scrollable-area::-webkit-scrollbar { width: 3px; }
        .nav-scrollable-area::-webkit-scrollbar-thumb { background: rgba(13, 110, 253, 0.1); }

        @media (max-width: 991.98px) {
            #sidebar { 
                margin-left: calc(-1 * var(--sidebar-width)); 
                /* Ensures a completely solid background matching the theme when sliding out on mobile */
                background-color: var(--bs-body-bg) !important; 
                box-shadow: 0 0 15px rgba(0,0,0,0.5);
            }
            #sidebar.active { margin-left: 0; }
            #main-content { margin-left: 0; }
            .mobile-nav-scroller {
                display: flex; overflow-x: auto; white-space: nowrap;
                background: var(--bs-body-tertiary); border-bottom: 1px solid var(--bs-border-color);
                padding: 5px 10px; scrollbar-width: none;
            }
            .mobile-nav-scroller::-webkit-scrollbar { display: none; }
            .mobile-nav-item { padding: 8px 15px; font-size: 0.75rem; font-weight: 600; color: var(--bs-secondary-color); text-decoration: none; }
            .mobile-nav-item.active { color: var(--accent-color); border-bottom: 2px solid var(--accent-color); }
        }
    </style>
</head>
<body>

<nav id="sidebar">
    <div class="p-4 d-flex align-items-center">
        <div class="bg-primary rounded-3 p-1 me-2 d-flex align-items-center justify-content-center" style="width:30px; height:30px;">
            <i class="fa-solid fa-terminal text-white small"></i>
        </div>
        <span class="fw-bold fs-5 text-primary">ADMIN<span class="text-body-emphasis">CORE</span></span>
    </div>

    <div class="nav-scrollable-area">
        <div class="nav flex-column">
            <a href="dashboard.php" class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
            
            <div class="section-label">User Management</div>
            <a href="users_list.php" class="nav-link <?= ($current_page == 'users_list.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> All Users
            </a>
            <a href="kyc_manage.php" class="nav-link <?= ($current_page == 'kyc_manage.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-id-card"></i> KYC Applications
            </a>
            <a href="user_messages.php" class="nav-link <?= ($current_page == 'user_messages.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-comment-dots"></i> Support Tickets
            </a>

            <div class="section-label">Investment Engine</div>
            <a href="plans.php" class="nav-link <?= ($current_page == 'plans.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-layer-group"></i> Investment Plans
            </a>
            <a href="active_investments.php" class="nav-link <?= ($current_page == 'active_investments.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line"></i> Active Stakes
            </a>
            <a href="tradable_assets.php" class="nav-link <?= ($current_page == 'tradable_assets.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-coins"></i> Tradable Assets
            </a>

            <div class="section-label">Finance & Ledger</div>
            <a href="deposits.php" class="nav-link <?= ($current_page == 'deposits.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-wallet"></i> Deposit Requests
            </a>
            <a href="withdrawals.php" class="nav-link <?= ($current_page == 'withdrawals.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-money-bill-transfer"></i> Withdrawals
            </a>
            <a href="transactions.php" class="nav-link <?= ($current_page == 'transactions.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-list-ul"></i> Master Ledger
            </a>

            <div class="section-label">System Control</div>
            <a href="settings.php" class="nav-link <?= ($current_page == 'settings.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-gears"></i> Site Settings
            </a>
            <a href="payment_methods.php" class="nav-link <?= ($current_page == 'payment_methods.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-credit-card"></i> Payment Gateways
            </a>
            <a href="profile.php" class="nav-link <?= ($current_page == 'profile.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-shield"></i> Admin Profile
            </a>
            
            <a href="logout.php" class="nav-link text-danger mt-4">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div id="main-content">
    <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top border-bottom">
        <div class="container-fluid">
            <button class="btn btn-sm btn-light d-lg-none me-2" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
            
            <div class="mobile-nav-scroller d-lg-none flex-grow-1">
                <a href="dashboard.php" class="mobile-nav-item <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">Dashboard</a>
                <a href="users_list.php" class="mobile-nav-item <?= ($current_page == 'users_list.php') ? 'active' : '' ?>">Users</a>
                <a href="deposits.php" class="mobile-nav-item <?= ($current_page == 'deposits.php') ? 'active' : '' ?>">Deposits</a>
                <a href="withdrawals.php" class="mobile-nav-item <?= ($current_page == 'withdrawals.php') ? 'active' : '' ?>">Withdrawals</a>
                <a href="transactions.php" class="mobile-nav-item <?= ($current_page == 'transactions.php') ? 'active' : '' ?>">Ledger</a>
            </div>

            <div class="ms-auto d-flex align-items-center">
                <button class="btn btn-link nav-link p-2 me-2" id="themeSwitcher"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-body" data-bs-toggle="dropdown">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-user text-white" style="font-size: 0.8rem;"></i>
                        </div>
                        <span class="d-none d-md-inline small fw-bold">Admin</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                        <li><a class="dropdown-item py-2" href="profile.php">Profile Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <div class="p-3 p-md-4">