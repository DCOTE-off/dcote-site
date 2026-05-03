const hamburger = document.getElementById('hamburgerBtn');
const sideMenu = document.getElementById('sideMenu');
const closeBtn = document.querySelector('.closeMenu');
const overlay = document.getElementById('overlay');


hamburger.addEventListener('click', () => {
    sideMenu.classList.add('open');
    overlay.classList.add('active');
});

closeBtn.addEventListener('click', () => {
    sideMenu.classList.remove('open');
    overlay.classList.remove('active');
});

overlay.addEventListener('click', () => {
    sideMenu.classList.remove('open');
    overlay.classList.remove('active');
});


function showNotification(message, type = 'error', duration = 5000) {
    const container = document.getElementById('notification-container');
    
    container.classList.remove('hidden', 'hide');
    void container.offsetWidth;

    const title = container.querySelector('h1');
    const text = container.querySelector('p');
    const closeBtn = container.querySelector('.shape-close');
    
    if (type === 'error') {
        container.style.borderColor = 'rgb(229, 11, 85)';
    } else if (type === 'success') {
        title.textContent = '✅ УСПЕШНО';
        container.style.borderColor = 'rgb(46, 204, 113)';
    } else if (type === 'warning') {
        title.textContent = '⚠️ ВНИМАНИЕ';
        container.style.borderColor = 'rgb(243, 156, 18)';
    }

    text.textContent = message;
    closeBtn.onclick = () => {
        hideNotification();
    };
    
    if (duration > 0) {
        if (container.notificationTimer) {
            clearTimeout(container.notificationTimer);
        }
        
        container.notificationTimer = setTimeout(() => {
            hideNotification();
        }, duration);
    }
}

function hideNotification() {
    const container = document.getElementById('notification-container');
    
    container.classList.add('hide');
    
    container.addEventListener('animationend', (e) => {
        container.classList.add('hidden');
        container.classList.remove('hide');
    }, { once: true });
}


function updateNavHeight() {
    const nav = document.querySelector('.mobile-bottom-nav');
    if (!nav) return;

    const height = nav.offsetHeight + 
        (parseInt(getComputedStyle(nav).paddingBottom) || 0);
    
    document.documentElement.style.setProperty('--nav-height', height + 'px');
}

window.addEventListener('load', updateNavHeight);
window.addEventListener('resize', updateNavHeight);