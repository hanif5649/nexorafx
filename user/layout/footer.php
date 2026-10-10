</main>

    <!-- Mobile App Navigation (Fixed Bottom) -->
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

    <a href="withdraw.php" class="flex flex-col items-center gap-1 <?= $current_page == 'withdraw.php' ? 'text-navy-light' : 'text-gray-500' ?>">
        <i class="fa-solid fa-money-bill-transfer text-xl"></i>
        <span class="text-[9px] font-black uppercase tracking-tighter">Payout</span>
    </a>
    
    <!-- Logout replaced Security -->
    <a href="logout.php" class="flex flex-col items-center gap-1 text-red-500 hover:text-red-400 transition-colors">
        <i class="fa-solid fa-power-off text-xl"></i>
        <span class="text-[9px] font-black uppercase tracking-tighter">Logout</span>
    </a>
</div>

    <!-- Global Desktop Footer -->
    <footer class="hidden md:block mt-20 bg-surface/50 border-t border-white/5 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-8 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-1">
                <h3 class="font-['Syne'] text-2xl font-bold text-navy-light mb-4 italic uppercase tracking-tighter">
                    <?= $settings['site_name'] ?>
                </h3>
                <p class="text-sm text-gray-500 leading-relaxed mb-4">
                    <?= $settings['address'] ?>
                </p>
                <div class="flex gap-4 text-gray-400">
                    <i class="fa-brands fa-whatsapp hover:text-navy-light"></i>
                    <i class="fa-brands fa-telegram hover:text-navy-light"></i>
                </div>
            </div>
            
            <div>
                <h4 class="text-xs font-black uppercase tracking-widest text-white mb-6">Investments</h4>
                <ul class="text-sm space-y-3 text-gray-500">
                    <li><a href="plans.php" class="hover:text-navy-light">Asset Portfolios</a></li>
                    <li><a href="running_investments.php" class="hover:text-navy-light">Running Stakes</a></li>
                    <li><a href="running_trading.php" class="hover:text-navy-light">Live Market Trading</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-black uppercase tracking-widest text-white mb-6">Account</h4>
                <ul class="text-sm space-y-3 text-gray-500">
                    <li><a href="kyc.php" class="hover:text-navy-light">Identity Verification</a></li>
                    <li><a href="transactions.php" class="hover:text-navy-light">Transaction Ledger</a></li>
                    <li><a href="profile.php" class="hover:text-navy-light">Personal Settings</a></li>
                </ul>
            </div>

            <div class="bg-navy-dark/30 p-6 rounded-2xl border border-navy-mid/20">
                <h4 class="text-xs font-black uppercase tracking-widest text-navy-light mb-4">Official Support</h4>
                <p class="text-[11px] text-gray-400 mb-4 italic">Direct line to our wealth management team.</p>
                <a href="tel:<?= $settings['phone'] ?>" class="text-sm font-bold text-white block mb-2"><?= $settings['phone'] ?></a>
                <a href="mailto:<?= $settings['email'] ?>" class="text-xs text-navy-light/60 underline hover:text-navy-light"><?= $settings['email'] ?></a>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-8 mt-16 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center text-[10px] text-gray-600 font-bold uppercase tracking-widest">
            <p>&copy; 2026 <?= $settings['site_name'] ?>. All Digital Assets Secured.</p>
            <div class="flex gap-6 mt-4 md:mt-0">
                <a href="#" class="hover:text-navy-light">Terms</a>
                <a href="#" class="hover:text-navy-light">Privacy</a>
                <a href="logout.php" class="text-red-500 hover:text-red-400">Secure Logout</a>
            </div>
        </div>
    </footer>
<script>
    window.addEventListener('load', function() {
        const preloader = document.getElementById('preloader');
        
        // Add a slight delay for that premium "processing" feel
        setTimeout(() => {
            preloader.style.opacity = '0';
            document.body.classList.remove('loading');
            
            // Remove from DOM after transition
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 700);
        }, 1000);
    });

    // Mark body as loading initially
    document.body.classList.add('loading');
</script>


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>