/**
 * Search Modal Toggle Logic
 */
document.addEventListener('DOMContentLoaded', () => {
    const trigger = document.getElementById('smh-search-trigger');
    const modal = document.getElementById('search-modal');

    if (!trigger || !modal) return;

    const closeBtn = modal.querySelector('.ct-toggle-close');
    const searchInput = modal.querySelector('input[type="search"]');

    const openModal = () => {
        modal.classList.add('is-active');
        modal.removeAttribute('inert');
        document.body.classList.add('ct-modal-open');
        
        // Focus the input after a short delay for the transition
        setTimeout(() => {
            if (searchInput) searchInput.focus();
        }, 300); // Increased delay for CSS transition
    };

    const closeModal = () => {
        modal.classList.remove('is-active');
        modal.setAttribute('inert', '');
        document.body.classList.remove('ct-modal-open');
    };

    trigger.addEventListener('click', (e) => {
        e.preventDefault();
        openModal();
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal();
        });
    }

    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });

    // Close on click outside (overlay)
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });
});
