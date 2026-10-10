<?php include('config/config.php'); // Your PDO connection file

// Fetch Site Settings
try {
    $stmt = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
    $settings = $stmt->fetch();
} catch (PDOException $e) {
    // Fallback if DB fails
    $settings = [
        'site_name' => 'City Capital',
        'logo_url' => 'assets/img/logo.png'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $settings['site_name'] ?> | Premier US Asset Management</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/theme.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'golf-green': '#2ecc71',
                        'navy-dark': '#0f172a',
                        'slate-light': '#f8fafc',
                    }
                }
            }
        }
    </script>
    <!-- Smartsupp Live Chat script -->
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = 'f09f0ffcd0f7bdc91c2e84a466851f83d8560143';
window.smartsupp||(function(d) {
  var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
  s=d.getElementsByTagName('script')[0];c=d.createElement('script');
  c.type='text/javascript';c.charset='utf-8';c.async=true;
  c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
</script>
<noscript>Powered by <a href="https://www.smartsupp.com" target="_blank">Smartsupp</a></noscript>

    <style>
        .nav-link { position: relative; transition: all 0.3s; }
        .nav-link::after { content: ''; position: absolute; bottom: -4px; left: 0; width: 0; height: 2px; background: #2ecc71; transition: width 0.3s; }
        .nav-link:hover::after { width: 100%; }
        .glass-nav { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-slate-light text-slate-900 font-['Plus_Jakarta_Sans']">

    <!-- Top Utility Bar -->
    <div class="bg-navy-dark text-white py-2 px-6 hidden md:block">
        <div class="max-w-7xl mx-auto flex justify-between items-center text-[10px] font-bold uppercase tracking-[0.2em]">
            <div class="flex gap-6">
                <span><i class="fa-solid fa-envelope text-golf-green mr-2"></i> <?= $settings['email'] ?></span>
                <span><i class="fa-solid fa-phone text-golf-green mr-2"></i> <?= $settings['phone'] ?></span>
            </div>
            <div>NEXORA Investment Platform</div>
        </div>
    </div>

    <!-- Main Navigation -->
    <header class="glass-nav sticky top-0 z-[100] py-5">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            
            <!-- Dynamic Logo -->
            <a href="index.php" class="flex items-center gap-3">
                <?php if($settings['logo_url']): ?>
                    <img src="<?= $settings['logo_url'] ?>" alt="<?= $settings['site_name'] ?>" class="h-10">
                <?php else: ?>
                    <span class="font-['Syne'] text-2xl font-extrabold tracking-tighter text-navy-dark uppercase">
                        <?= substr($settings['site_name'], 0, -4) ?><span class="text-golf-green"><?= substr($settings['site_name'], -4) ?></span>
                    </span>
                <?php endif; ?>
            </a>

            <!-- Menu -->
            <nav class="hidden lg:flex items-center gap-10 text-[12px] font-extrabold uppercase tracking-widest text-slate-600">
                <a href="index.php" class="nav-link hover:text-navy-dark">Wealth Solutions</a>
                <a href="services.php" class="nav-link hover:text-navy-dark">Our Portfolio</a>
                <a href="contact.php" class="nav-link hover:text-navy-dark">Contact Us</a>
            </nav>

            <!-- CTA -->
            <div class="flex items-center gap-6">
                <a href="user/login.php" class="text-[12px] font-black uppercase tracking-widest text-navy-dark hover:text-golf-green transition">Login</a>
                <a href="user/register.php" class="bg-golf-green text-white px-7 py-3 rounded-lg text-[11px] font-black uppercase tracking-widest shadow-xl shadow-golf-green/20 hover:-translate-y-1 transition-all">
                    Open Account
                </a>
            </div>
        </div>
    </header>

    <main>