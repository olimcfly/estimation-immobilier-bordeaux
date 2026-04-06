</main>
</div>
<footer class="border-t bg-[var(--admin-header-bg)]" style="border-color: var(--admin-border)">
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
        const submenuToggles = document.querySelectorAll('[data-submenu-toggle]');
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;

        if (sidebar && overlay && toggle) {
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
        }

        submenuToggles.forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = button.getAttribute('data-submenu-toggle');
                const target = targetId ? document.getElementById(targetId) : null;
                if (!target) {
                    return;
                }

                const expanded = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                target.classList.toggle('hidden', expanded);
            });
        });

        if (themeToggle && body) {
            const storedTheme = window.localStorage.getItem('admin-theme');
            if (storedTheme === 'dark' || storedTheme === 'light') {
                body.setAttribute('data-theme', storedTheme);
                themeToggle.setAttribute('aria-pressed', storedTheme === 'dark' ? 'true' : 'false');
            }

            themeToggle.addEventListener('click', function () {
                const currentTheme = body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
                const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';

                body.setAttribute('data-theme', nextTheme);
                window.localStorage.setItem('admin-theme', nextTheme);
                themeToggle.setAttribute('aria-pressed', nextTheme === 'dark' ? 'true' : 'false');
            });
        }
    })();
</script>
</body>
</html>
