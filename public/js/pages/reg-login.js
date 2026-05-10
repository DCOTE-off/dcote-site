let captchaToken = null;

function onCaptchaSuccess(token) {
    captchaToken = token;
    const btn = document.querySelector('.submit-btn');
    btn.disabled = false;
    btn.classList.remove('disabled');
}

// Универсальный переключатель видимости пароля (для всех кнопок в форме)
document.querySelectorAll('.password-toggle').forEach(button => {
    button.addEventListener('click', function() {
        const input = this.closest('.input-with-icon').querySelector('input');
        const eyeOn = this.querySelector('.eye-icon');
        const eyeOff = this.querySelector('.eye-off-icon');

        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        
        eyeOn.style.display = isPassword ? 'none' : 'flex';
        eyeOff.style.display = isPassword ? 'flex' : 'none';
        this.setAttribute('aria-label', isPassword ? 'Скрыть пароль' : 'Показать пароль');
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registration-form');
    if (!form) return;

    document.querySelectorAll('.error-bubble').forEach(bubble => {
        if (bubble.textContent.trim() !== '') {
            // Если в баббле есть текст, находим соответствующий инпут и красим его
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