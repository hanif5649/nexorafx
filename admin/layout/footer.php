</div> <!-- End of p-4 -->
</div> <!-- End of main-content -->

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar Toggle for Mobile
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    if(sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }

    // Modern 2026 Theme Switcher Logic
    const themeSwitcher = document.getElementById('themeSwitcher');
    const themeIcon = document.getElementById('themeIcon');
    const htmlEl = document.documentElement;

    themeSwitcher.addEventListener('click', () => {
        if (htmlEl.getAttribute('data-bs-theme') === 'dark') {
            htmlEl.setAttribute('data-bs-theme', 'light');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            htmlEl.setAttribute('data-bs-theme', 'dark');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        }
    });
</script>
</body>
</html>