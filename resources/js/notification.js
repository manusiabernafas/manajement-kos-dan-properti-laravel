const ICONS = {
    success: 'fa-solid fa-circle-check',
    error: 'fa-solid fa-circle-xmark',
    warning: 'fa-solid fa-triangle-exclamation',
    info: 'fa-solid fa-circle-info',
};

function ensureContainer() {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    return container;
}

function toast({ type = 'info', title = '', message = '', duration = 5000 } = {}) {
    const container = ensureContainer();
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `
        <div class="toast-icon"><i class="${ICONS[type] || ICONS.info}"></i></div>
        <div class="toast-body">
            ${title ? `<div class="toast-title">${title}</div>` : ''}
            ${message ? `<div class="toast-message">${message}</div>` : ''}
        </div>
        <button type="button" class="toast-close"><i class="fa-solid fa-xmark"></i></button>
    `;

    container.appendChild(el);

    const remove = () => {
        el.style.transition = 'opacity 180ms ease';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 180);
    };

    el.querySelector('.toast-close').addEventListener('click', remove);

    if (duration > 0) {
        setTimeout(remove, duration);
    }
}

function initFlashToasts() {
    document.querySelectorAll('[data-flash]').forEach((el) => {
        toast({
            type: el.dataset.flash,
            title: el.dataset.flashTitle || '',
            message: el.dataset.flashMessage || el.textContent.trim(),
        });
        el.remove();
    });
}

document.addEventListener('DOMContentLoaded', initFlashToasts);

window.KM = window.KM || {};
window.KM.toast = toast;
