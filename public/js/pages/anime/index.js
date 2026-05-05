document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.dropdown-menu-btn');

    buttons.forEach(button => {
        button.addEventListener('click', (event) => {
            // Предотвращаем стандартное поведение кнопки
            event.preventDefault();

            // Получаем ID целевого элемента из атрибута
            const targetId = button.getAttribute('data-target');
            const wrapper = document.getElementById(targetId);

            if (!wrapper) return;

            // Переключаем класс только для этого конкретного дропдауна
            const isOpen = wrapper.classList.toggle('is-open');
            button.setAttribute('aria-expanded', isOpen);
            button.classList.toggle('is-open')
        });
    });
})