<?php if (isset($_SESSION['notification'])): ?>
    <?php
    $notif = $_SESSION['notification'];
    unset($_SESSION['notification']);
    $old = $_SESSION['old_input'] ?? [];
    unset($_SESSION['old_input']);
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showNotification(
                '<?= addslashes($notif['message']) ?>',
                '<?= $notif['type'] ?>',
                5000
            );
        });
    </script>
<?php endif; ?>
<svg style="display: none;">
    <symbol id="eye" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
        <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
    </symbol>
    <symbol id="eye-off" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" d="M10.585 10.587a2 2 0 0 0 2.829 2.828" />
        <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" />
        <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" d="M3 3l18 18" />
    </symbol>
</svg>
<div class="navigation-links">
    <a href="/"><span>ГЛАВНАЯ</span></a>
    <p>/</p>
    <a class="current-page" href="/login"><span>АВТОРИЗАЦИЯ</span></a>
</div>
<main class="page-login">
    <div class="auth-container">
        <div class="auth-image scale-in">
            <img src="images/auth/auth_4.webp" alt="base_dcote_image">
        </div>
        <div class="auth-form scale-in slide-in-left">
            <form method="post" novalidate>
                <h1>АВТОРИЗАЦИЯ</h1>
                <div class="input-group">
                    <label for="login">
                        <h3>Имя пользователя или почта</h3>
                    </label>
                    <input id="login" name="login" value="<?= e($old['login'] ?? '') ?>" data-required>
                    <p>Забыл(а) имя пользователя или почту? <a href="https://dcote">Восстановить</a></p>
                </div>
                <div class="input-group">
                    <label for="password">
                        <h3>Пароль</h3>
                    </label>
                    <div class="input-with-icon"><input type="password" id="password" name="password" data-required>
                        <button type="button" class="password-toggle button-without-styles" aria-label="Показать пароль">
                            <svg class="eye-icon" width="20" height="20">
                                <use href="#eye"></use>
                            </svg>
                            <svg class="eye-off-icon" width="20" height="20" style="display: none;">
                                <use href="#eye-off"></use>
                            </svg>
                        </button>
                    </div>
                    <p>Забыл(а) пароль? <a href="https://dcote">Восстановить</a></p>
                </div>
                <div class="cf-turnstile" style="width: 300px;height: 69px;"
                    data-sitekey="0x4AAAAAACpYY5Y5Jlu5driC"
                    data-callback="onCaptchaSuccess">
                </div>
                <div class="input-group">
                    <button type="submit" class="submit-btn" disabled>АВТОРИЗОВАТЬСЯ</button>
                    <p>Нет аккаунта? <a href="/reg"> Зарегистрируйся</a></p>
                </div>
            </form>
        </div>
    </div>
</main>
</body>

</html>
<script>
    let captchaToken = null;

    function onCaptchaSuccess(token) {
        captchaToken = token;
        const btn = document.querySelector('.submit-btn');
        btn.disabled = false;
        btn.classList.remove('disabled');
    }

    document.querySelector('.password-toggle')?.addEventListener('click', function() {
        const input = document.getElementById('password');
        const eyeOn = this.querySelector('.eye-icon');
        const eyeOff = this.querySelector('.eye-off-icon');

        if (input.type === 'password') {
            input.type = 'text';
            eyeOn.style.display = 'none';
            eyeOff.style.display = 'block';
            this.setAttribute('aria-label', 'Скрыть пароль');
        } else {
            input.type = 'password';
            eyeOn.style.display = 'block';
            eyeOff.style.display = 'none';
            this.setAttribute('aria-label', 'Показать пароль');
        }
    });

    function showFieldError(input, message) {
        input.classList.add('error');
        showNotification(message, 'error', 5000);
        input.focus();
    }

    function clearFieldError(input) {
        input.classList.remove('error');
        const error = input.parentElement.querySelector('.field-error-msg');
        if (error) error.remove();
    }

    function clearFormErrors(form) {
        form.querySelectorAll('.error').forEach(input => {
            clearFieldError(input);
        });
    }
    document.getElementById('registration-form')?.addEventListener('submit', function(e) {

        const form = this;
        clearFormErrors(form);

        let hasErrors = false;

        form.querySelectorAll('[data-required]').forEach(input => {
            if (!input.value.trim()) {
                showFieldError(input, 'Это поле обязательно для заполнения');
                hasErrors = true;
                e.preventDefault();
                return;
            }
        });

        const password = form.querySelector('input[name="password"]');
        if (password.value) {
            if (password.value.length < 6) {
                showFieldError(password, 'Пароль должен быть не менее 6 символов');
                hasErrors = true;
            } else if (password.value.length > 50) {
                showFieldError(password, 'Пароль не должен быть длиннее 50 символов');
                hasErrors = true;
            }
        }

        const login = form.querySelector('input[name="login"]');
        if (login.value) {
            if (login.value.length > 50) {
                showFieldError(password, 'Имя пользователя или почта не должны быть длиннее 50 символов');
                hasErrors = true;
            }
        }

        if (typeof captchaToken === 'undefined' || !captchaToken) {
            showNotification('Пройдите проверку безопасности', 'error', 5000);
            hasErrors = true;
        }


        if (hasErrors) {
            e.preventDefault();
            return;
        }

        const btn = form.querySelector('.submit-btn');
        btn.disabled = true;
        btn.textContent = 'Проверка...';

    })
</script>