document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.sort-toggle')?.addEventListener('click', function() {
        const cont = document.querySelector('.grid-area');
        const sortAsc = this.querySelector('.sort-ascending');
        const sortDesc = this.querySelector('.sort-descending');
        const isReversed = cont.style.flexDirection === 'column-reverse';
        cont.style.flexDirection = isReversed ? 'column' : 'column-reverse';

        if (sortAsc && sortDesc) {
            sortAsc.style.display = isReversed ? 'block' : 'none';
            sortDesc.style.display = isReversed ? 'none' : 'block';
        }

        this.setAttribute('aria-label', isReversed ? 'Сортировка: по возрастанию' : 'Сортировка: по убыванию');
    });
});
