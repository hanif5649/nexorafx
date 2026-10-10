</main>

    <!-- Global Footer -->
    <footer class="bg-navy-dark text-white pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-16 mb-20">
                
                <!-- Company Bio -->
                <div class="space-y-6">
                    <h3 class="font-['Syne'] text-2xl font-bold italic tracking-tighter uppercase">
                        <?= $settings['site_name'] ?>
                    </h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Registered US Investment firm specializing in digital assets, agricultural futures, and high-yield wealth management strategies for global investors.
                    </p>
                    <div class="flex gap-4 text-golf-green">
                        <a href="#" class="hover:text-white transition"><i class="fa-brands fa-linkedin"></i></a>
                        <a href="#" class="hover:text-white transition"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="hover:text-white transition"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Expertise -->
                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-golf-green mb-8">Expertise</h4>
                    <ul class="space-y-4 text-sm text-slate-400 font-medium">
                        <li><a href="#" class="hover:text-white transition">Crypto Asset Management</a></li>
                        <li><a href="#" class="hover:text-white transition">Real Estate Syndication</a></li>
                        <li><a href="#" class="hover:text-white transition">Agro-Business Funding</a></li>
                        <li><a href="#" class="hover:text-white transition">Venture Capital</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-golf-green mb-8">Client Support</h4>
                    <ul class="space-y-4 text-sm text-slate-400 font-medium">
                        <li><a href="contact.php" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="user/login.php" class="hover:text-white transition">Secure Login</a></li>
                        <li><a href="user/register.php" class="hover:text-white transition">Create Account</a></li>
                        <li><a href="#" class="hover:text-white transition">Legal Disclosures</a></li>
                    </ul>
                </div>

                <!-- Headquarters -->
                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-golf-green mb-8">Office Presence</h4>
                    <div class="text-sm text-slate-400 leading-loose">
                        <p class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot mt-1.5 text-golf-green"></i>
                            <?= nl2br($settings['address']) ?>
                        </p>
                        <p class="mt-4 flex items-center gap-3">
                            <i class="fa-solid fa-envelope text-golf-green"></i>
                            <?= $settings['email'] ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="border-t border-white/5 pt-12 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="text-center md:text-left">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">
                        &copy; <?= date('Y') ?> <?= $settings['site_name'] ?> LLC. All Rights Reserved.
                    </p>
                    <p class="text-[9px] text-slate-600 uppercase mt-2">Certified Investment Provider | SEC Regulated Framework</p>
                </div>
                
                <div class="flex gap-8 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                    <a href="#" class="hover:text-golf-green">Privacy Policy</a>
                    <a href="#" class="hover:text-golf-green">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Alpine.js for interactive elements -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>