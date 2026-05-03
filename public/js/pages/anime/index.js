document.addEventListener('DOMContentLoaded', () => {

    const globalDropdown = document.querySelector('.dropdown-list');
    let currentDdButton = null;

    if (globalDropdown) {
        document.querySelectorAll('.dropdown-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();

                if (currentDdButton && currentDdButton !== button) {
                    globalDropdown.classList.add('hidden');
                    currentDdButton.classList.remove('active');
                    currentDdButton.setAttribute('aria-expanded', 'false');
                }
                const rect = button.getBoundingClientRect();
                globalDropdown.style.top = rect.bottom + window.scrollY + 'px';
                globalDropdown.style.left = rect.left + window.scrollX + 'px';
                globalDropdown.style.width = `${button.offsetWidth}px`;

                const isHidden = globalDropdown.classList.toggle('hidden');
                button.classList.toggle('active');
                button.setAttribute('aria-expanded', !isHidden);
                currentDdButton = isHidden ? null : button;
            });
        });

        document.addEventListener('click', (e) => {
            const clickedBtn = e.target.closest('.dropdown-btn');

            if (!clickedBtn && !globalDropdown.contains(e.target) && !globalDropdown.classList.contains('hidden')) {
                globalDropdown.classList.add('hidden');

                document.querySelectorAll('.dropdown-btn').forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-expanded', 'false');
                });

                currentDdButton = null;
            }
        });
    }
})