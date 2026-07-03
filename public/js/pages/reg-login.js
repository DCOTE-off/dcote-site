let captchaToken = null;

function onCaptchaSuccess(token) {
    captchaToken = token;
    const submitButton = document.querySelector('.submit-btn');

    if (!submitButton) {
        return;
    }

    submitButton.disabled = false;
    submitButton.classList.remove('disabled');
}

document.querySelectorAll('.password-toggle').forEach(toggle => {
    toggle.addEventListener('click', () => {
        const input = toggle.closest('.input-with-icon')?.querySelector('input');

        if (!input) {
            return;
        }

        const showPassword = input.type === 'password';
        input.type = showPassword ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', String(showPassword));
        toggle.setAttribute('aria-label', showPassword ? 'Скрыть пароль' : 'Показать пароль');
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registration-form');
    if (!form) return;

    const turnstileSlot = form.querySelector('.turnstile-slot');
    if (turnstileSlot) {
        const MOBILE_BREAKPOINT = 768;
        const DESKTOP_REFERENCE_WIDTH = 1540;
        const MOBILE_SCALE_BOOST = 1.2;

        const syncTurnstileSize = () => {
            // Turnstile is fixed at 300x65, so its wrapper follows the form's responsive scale.
            const isMobile = window.innerWidth <= MOBILE_BREAKPOINT;
            const referenceWidth = isMobile ? MOBILE_BREAKPOINT : DESKTOP_REFERENCE_WIDTH;
            const mobileScale = isMobile ? MOBILE_SCALE_BOOST : 1;
            const scale = Math.min(window.innerWidth / referenceWidth, 1) * mobileScale;
            turnstileSlot.style.setProperty('--turnstile-scale', scale);
        };

        window.addEventListener('resize', syncTurnstileSize);
        syncTurnstileSize();
    }

    document.querySelectorAll('.error-bubble').forEach(bubble => {
        if (bubble.textContent.trim() !== '') {
            const input = bubble.closest('.input-group').querySelector('input');
            if (input) input.classList.add('input-error');
        }
    });
    const getErrorBubble = (input) => {
        return input.closest('.input-group').querySelector('.error-bubble');
    };

    const showError = (input, customMessage = null) => {
        const bubble = getErrorBubble(input);
        if (!bubble) return;
        
        let message = customMessage || 
            (input.validity.valueMissing 
                ? `Поле «${input.closest('.input-group').querySelector('h3')?.textContent || 'Это поле'}» обязательно` 
                : (input.validity.patternMismatch ? input.title : input.validationMessage));

        bubble.textContent = message;
        bubble.style.display = 'flex';
        input.classList.add('input-error');
    };

    const hideError = (input) => {
        const bubble = getErrorBubble(input);
        if (bubble) {
            bubble.style.display = 'none';
            input.classList.remove('input-error');
        }
    };


    document.addEventListener('click', (e) => {
        if (!e.target.closest('input') && !e.target.closest('.error-bubble')) {
            document.querySelectorAll('.error-bubble').forEach(bubble => {
                bubble.style.display = 'none';
            });
            document.querySelectorAll('input').forEach(input => {
                input.classList.remove('input-error');
            });
        }
    });

    form.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', () => hideError(input));
    });

    form.addEventListener('submit', (e) => {
        let isFormValid = true;
        const inputs = form.querySelectorAll('input[required]');

        inputs.forEach(input => {
            hideError(input);

            if (input.validity.valueMissing) {
                showError(input);
                isFormValid = false;
            } 
            else if (!input.checkValidity()) {
                showError(input);
                isFormValid = false;
            }
            else if (input.id === 'password_confirmation') {
                const pass = document.getElementById('password').value;
                if (input.value !== pass) {
                    showError(input, 'Пароли не совпадают');
                    isFormValid = false;
                }
            }
        });

        if (!isFormValid) {
            e.preventDefault();
            const firstError = form.querySelector('.input-error');
            if (firstError) firstError.focus();
        }
    });
});
