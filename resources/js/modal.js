function openOverlay(overlay) {
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeOverlay(overlay) {
    overlay.classList.remove('open');
    const stillOpen = document.querySelector('.modal-overlay.open, .drawer-overlay.open');
    if (!stillOpen) {
        document.body.style.overflow = '';
    }
}

function closeAllDropdowns(except = null) {
    document.querySelectorAll('.dropdown.open').forEach((dd) => {
        if (dd !== except) dd.classList.remove('open');
    });
}

function initModals() {
    document.addEventListener('click', (e) => {
        const openTrigger = e.target.closest('[data-modal-open]');
        if (openTrigger) {
            const target = document.getElementById(openTrigger.dataset.modalOpen);
            if (target) openOverlay(target);
        }

        const openDrawerTrigger = e.target.closest('[data-drawer-open]');
        if (openDrawerTrigger) {
            const target = document.getElementById(openDrawerTrigger.dataset.drawerOpen);
            if (target) openOverlay(target);
        }

        const closeTrigger = e.target.closest('[data-modal-close], [data-drawer-close]');
        if (closeTrigger) {
            const overlay = closeTrigger.closest('.modal-overlay, .drawer-overlay');
            if (overlay) closeOverlay(overlay);
        }

        if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('drawer-overlay')) {
            closeOverlay(e.target);
        }

        const dropdownToggle = e.target.closest('[data-dropdown-toggle]');
        if (dropdownToggle) {
            const dropdown = dropdownToggle.closest('.dropdown');
            const isOpen = dropdown.classList.contains('open');
            closeAllDropdowns();
            dropdown.classList.toggle('open', !isOpen);
        } else if (!e.target.closest('.dropdown-menu')) {
            closeAllDropdowns();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open, .drawer-overlay.open').forEach(closeOverlay);
            closeAllDropdowns();
        }
    });
}

function confirmAction({ title = 'Konfirmasi', message = 'Apakah Anda yakin ingin melanjutkan?', confirmText = 'Ya, Lanjutkan', cancelText = 'Batal', danger = true } = {}) {
    return new Promise((resolve) => {
        let overlay = document.getElementById('km-confirm-modal');
        if (overlay) overlay.remove();

        overlay = document.createElement('div');
        overlay.id = 'km-confirm-modal';
        overlay.className = 'modal-overlay open';
        overlay.innerHTML = `
            <div class="modal modal-sm">
                <div class="modal-header">
                    <h3>${title}</h3>
                    <button type="button" class="modal-close" data-cancel><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-cancel>${cancelText}</button>
                    <button type="button" class="btn ${danger ? 'btn-danger' : 'btn-primary'}" data-confirm>${confirmText}</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';

        const cleanup = (result) => {
            overlay.remove();
            document.body.style.overflow = '';
            resolve(result);
        };

        overlay.querySelectorAll('[data-cancel]').forEach((el) => el.addEventListener('click', () => cleanup(false)));
        overlay.querySelector('[data-confirm]').addEventListener('click', () => cleanup(true));
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) cleanup(false);
        });
    });
}

function initConfirmForms() {
    document.addEventListener('submit', async (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (!form.hasAttribute('data-confirm-submit')) return;
        if (form.dataset.confirmed === 'true') return;

        e.preventDefault();
        const ok = await confirmAction({
            title: form.dataset.confirmTitle || 'Hapus Data',
            message: form.dataset.confirmMessage || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
            confirmText: form.dataset.confirmText || 'Ya, Hapus',
            danger: form.dataset.confirmDanger !== 'false',
        });

        if (ok) {
            form.dataset.confirmed = 'true';
            form.submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initModals();
    initConfirmForms();
});

window.KM = window.KM || {};
window.KM.confirmAction = confirmAction;
window.KM.openOverlay = openOverlay;
window.KM.closeOverlay = closeOverlay;
