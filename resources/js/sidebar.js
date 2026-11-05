const COLLAPSE_KEY = 'kostmanager-sidebar-collapsed';

function initSidebar() {
    const shell = document.querySelector('.app-shell');
    if (!shell) return;

    if (localStorage.getItem(COLLAPSE_KEY) === '1' && window.innerWidth > 1024) {
        shell.classList.add('sidebar-collapsed');
    }

    document.querySelectorAll('[data-sidebar-collapse-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            shell.classList.toggle('sidebar-collapsed');
            localStorage.setItem(COLLAPSE_KEY, shell.classList.contains('sidebar-collapsed') ? '1' : '0');
            // Content width changes via CSS transition without a window resize event,
            // so charts/canvases relying on `resize` listeners need a manual nudge.
            setTimeout(() => window.dispatchEvent(new Event('resize')), 240);
        });
    });

    document.querySelectorAll('[data-sidebar-mobile-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            shell.classList.toggle('sidebar-mobile-open');
        });
    });

    document.querySelectorAll('.sidebar-backdrop').forEach((el) => {
        el.addEventListener('click', () => shell.classList.remove('sidebar-mobile-open'));
    });

    document.querySelectorAll('.sidebar-group > .sidebar-link').forEach((link) => {
        link.addEventListener('click', (e) => {
            if (link.dataset.hasSubmenu !== 'true') return;
            e.preventDefault();
            link.closest('.sidebar-group').classList.toggle('open');
        });
    });
}

document.addEventListener('DOMContentLoaded', initSidebar);
