document.addEventListener('DOMContentLoaded', () => {
    const player = document.getElementById('episode-iframe-player');
    const buttons = document.querySelectorAll('.subs-and-dubs button');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            if (button.hasAttribute('disabled') || button.classList.contains('active')) {
                return;
            }
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            const newSrc = button.getAttribute('data-src');
            
            if (newSrc) {
                player.src = newSrc;
            }
        });
    });
});