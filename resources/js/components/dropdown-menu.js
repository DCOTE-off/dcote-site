document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.dropdown-menu-btn');

    buttons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();

            const targetId = button.getAttribute('data-target');
            const wrapper = document.getElementById(targetId);

            if (!wrapper) return;

            const isOpen = wrapper.classList.toggle('is-open');
            button.setAttribute('aria-expanded', isOpen);
            button.classList.toggle('is-open')
        });
    });
})
