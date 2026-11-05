const STORAGE_KEY = 'kostmanager-theme';

function systemPrefersDark() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

function applyTheme(mode) {
    const isDark = mode === 'dark' || (mode === 'system' && systemPrefersDark());
    document.documentElement.classList.toggle('dark', isDark);
    document.querySelectorAll('[data-theme-btn]').forEach((btn) => {
        btn.classList.toggle('active', btn.dataset.themeBtn === mode);
    });
}

function setTheme(mode) {
    localStorage.setItem(STORAGE_KEY, mode);
    applyTheme(mode);

    if (window.axios) {
        window.axios.post('/profile/theme', { theme: mode }).catch(() => {});
    }
}

function initTheme() {
    const saved = localStorage.getItem(STORAGE_KEY) || document.documentElement.dataset.defaultTheme || 'system';
    applyTheme(saved);

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        const current = localStorage.getItem(STORAGE_KEY) || 'system';
        if (current === 'system') {
            applyTheme('system');
        }
    });

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-theme-btn]');
        if (btn) {
            setTheme(btn.dataset.themeBtn);
        }
    });
}

document.addEventListener('DOMContentLoaded', initTheme);

window.KM = window.KM || {};
window.KM.setTheme = setTheme;
