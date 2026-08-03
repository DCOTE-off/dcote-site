document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.dropdown-select-btn');

    function closeAll(exceptId) {
        document.querySelectorAll('.dropdown-select-btn.is-open').forEach(button => {
            const targetId = button.getAttribute('data-target');
            if (targetId === exceptId) return;
            const content = document.getElementById(targetId);
            if (content) {
                content.classList.remove('is-open');
                button.classList.remove('is-open');
                button.setAttribute('aria-expanded', 'false');
            }
        });
    }

    buttons.forEach(button => {
        const targetId = button.getAttribute('data-target');
        const wrapper = document.getElementById(targetId);

        if (!wrapper) return;

        button.addEventListener('click', (event) => {
            event.stopPropagation();
            closeAll(targetId);
            const isOpen = wrapper.classList.contains('is-open');
            wrapper.classList.toggle('is-open');
            button.setAttribute('aria-expanded', !isOpen);
            button.classList.toggle('is-open');
        });
    });

    document.addEventListener('click', (event) => {
        const wrapper = event.target.closest('.dropdown-select-wrapper');
        const content = event.target.closest('.dropdown-content');
        if (wrapper || content) return;

        closeAll();
    });
});
