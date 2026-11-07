<div id="admin-confirm-modal"
     class="admin-confirm-backdrop"
     hidden
     role="dialog"
     aria-modal="true"
     aria-labelledby="admin-confirm-title"
     aria-describedby="admin-confirm-message">
    <div class="admin-confirm-card">
        <div class="admin-confirm-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"></path>
            </svg>
        </div>
        <div class="admin-confirm-copy">
            <h2 id="admin-confirm-title">Xác nhận thao tác</h2>
            <p id="admin-confirm-message"></p>
        </div>
        <div class="admin-confirm-actions">
            <button type="button" class="admin-confirm-cancel">Hủy</button>
            <button type="button" class="admin-confirm-submit">Xác nhận</button>
        </div>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('admin-confirm-modal');
        if (!modal) {
            return;
        }

        const message = modal.querySelector('#admin-confirm-message');
        const cancelButton = modal.querySelector('.admin-confirm-cancel');
        const submitButton = modal.querySelector('.admin-confirm-submit');
        let pendingForm = null;

        const closeModal = () => {
            modal.hidden = true;
            pendingForm = null;
        };

        document.querySelectorAll('form[data-confirm-message]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.confirmed === 'true') {
                    delete form.dataset.confirmed;
                    return;
                }

                event.preventDefault();
                pendingForm = form;
                message.textContent = form.dataset.confirmMessage;
                modal.hidden = false;
                cancelButton.focus();
            });
        });

        cancelButton.addEventListener('click', closeModal);
        submitButton.addEventListener('click', () => {
            if (!pendingForm) {
                return;
            }

            pendingForm.dataset.confirmed = 'true';
            pendingForm.requestSubmit();
            closeModal();
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });
    })();
</script>
