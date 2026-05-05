const hamburger = document.getElementById('hamburgerBtn');
const sideMenu = document.getElementById('sideMenu');
const closeBtn = document.querySelector('.closeMenu');


hamburger.addEventListener('click', () => {
    if (sideMenu.classList.contains('is-open')) {

        sideMenu.classList.remove('is-open');
        sideMenu.classList.add('not-open');
    } else {

        sideMenu.classList.remove('not-open');
        sideMenu.classList.add('is-open');
    }
});

document.addEventListener('click', (event) => {
    const isClickOnHamburger = hamburger.contains(event.target);
    const isClickInsideMenu = sideMenu.contains(event.target);

    if (!isClickOnHamburger && !isClickInsideMenu) {
        if (sideMenu.classList.contains('is-open')) {
            sideMenu.classList.remove('is-open');
            sideMenu.classList.add('not-open');
        }
    }
});
const mobileNav = document.querySelector('.mobile-nav');

if (mobileNav) {
    const observer = new ResizeObserver(entries => {
        for (let entry of entries) {
            const height = entry.contentRect.height;
            document.documentElement.style.setProperty('--nav-height', `${height}px`);
        }
    });

    observer.observe(mobileNav);
}


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