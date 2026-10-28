const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const sidebar = document.querySelector('#app-sidebar');

function setSidebarOpen(isOpen) {
    document.body.classList.toggle('sidebar-open', isOpen);
    sidebarToggle?.setAttribute('aria-expanded', String(isOpen));
}

sidebarToggle?.addEventListener('click', () => {
    setSidebarOpen(!document.body.classList.contains('sidebar-open'));
});

document.addEventListener('click', (event) => {
    if (
        document.body.classList.contains('sidebar-open')
        && event.target instanceof Node
        && !sidebar?.contains(event.target)
        && !sidebarToggle?.contains(event.target)
    ) {
        setSidebarOpen(false);
    }
});

document.querySelectorAll('[data-dismiss-flash]').forEach((button) => {
    button.addEventListener('click', () => button.closest('.flash-message')?.remove());
});

const confirmDialog = document.querySelector('[data-confirm-dialog]');
const confirmTitle = confirmDialog?.querySelector('[data-confirm-title]');
const confirmMessage = confirmDialog?.querySelector('[data-confirm-message]');
const confirmAccept = confirmDialog?.querySelector('[data-confirm-accept]');
let pendingForm = null;

document.querySelectorAll('[data-confirm-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (form.dataset.confirmed === 'true') {
            delete form.dataset.confirmed;
            return;
        }

        event.preventDefault();
        pendingForm = form;

        if (confirmTitle) {
            confirmTitle.textContent = form.dataset.confirmTitle || 'Deactivate this member?';
        }
        if (confirmMessage) {
            confirmMessage.textContent = form.dataset.confirmMessage || 'This member will no longer be active. Their profile and history will be kept.';
        }

        confirmDialog?.showModal();
    });
});

confirmDialog?.querySelector('[data-confirm-cancel]')?.addEventListener('click', () => {
    confirmDialog.close();
    pendingForm = null;
});

confirmAccept?.addEventListener('click', () => {
    if (!pendingForm) {
        return;
    }

    pendingForm.dataset.confirmed = 'true';
    confirmDialog?.close();
    pendingForm.requestSubmit();
    pendingForm = null;
});

confirmDialog?.addEventListener('close', () => {
    if (pendingForm) {
        pendingForm = null;
    }
});
