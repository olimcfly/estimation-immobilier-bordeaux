</main>
</div>
<footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto max-w-[1600px] px-6 py-4 text-xs text-slate-500">
        © <?= date('Y') ?> <?= htmlspecialchars((string) SITE_NAME, ENT_QUOTES, 'UTF-8') ?>
    </div>
</footer>
<script>
    (function () {
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggle = document.getElementById('sidebar-toggle');
        const closeBtn = document.getElementById('sidebar-close');

        if (!sidebar || !overlay || !toggle) {
            return;
        }

        const openSidebar = function () {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            toggle.setAttribute('aria-expanded', 'true');
        };

        const closeSidebar = function () {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', function () {
            if (sidebar.classList.contains('-translate-x-full')) {
                openSidebar();
                return;
            }

            closeSidebar();
        });

        overlay.addEventListener('click', closeSidebar);
        if (closeBtn) {
            closeBtn.addEventListener('click', closeSidebar);
        }

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) {
                overlay.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
                sidebar.classList.remove('-translate-x-full');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });
    })();
</script>
</body>
</html>
