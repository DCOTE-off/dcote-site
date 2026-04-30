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
    <symbol id="info-circle" viewBox="0 0 24 24">
        <path fill="currentColor" d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm0 9h-1l-.117 .007a1 1 0 0 0 0 1.986l.117 .007v3l.007 .117a1 1 0 0 0 .876 .876l.117 .007h1l.117 -.007a1 1 0 0 0 .876 -.876l.007 -.117l-.007 -.117a1 1 0 0 0 -.764 -.857l-.112 -.02l-.117 -.006v-3l-.007 -.117a1 1 0 0 0 -.876 -.876l-.117 -.007zm.01 -3l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007z" />
    </symbol>
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
    <a href="/login"><span>АВТОРИЗАЦИЯ</span></a>
    <p>/</p>
    <a class="current-page" href="/reg"><span>РЕГИСТРАЦИЯ</span></a>
</div>
<main class="page-reg">
    <div class="auth-container">
        <div class="auth-image scale-in">
            <img src="images/auth/auth_4.webp" alt="base_dcote_image">
        </div>
        <div class="auth-form scale-in slide-in-left">
            <form method="POST" id="registration-form" novalidate>
                <h1>РЕГИСТРАЦИЯ</h1>
                <div class="input-group">
                    <label for="email">
                        <h3>Электронная почта</h3>
                    </label>
                    <input type="email" id="email" value="<?= e($old['email'] ?? '') ?>" name="email" data-required>
                </div>
                <div class="input-group">
                    <label for="tag">
                        <h3>Имя пользователя</h3>
                    </label>
                    <input id="tag" value="<?= e($old['tag'] ?? '') ?>" name="tag" data-required>
                </div>
                <div class="input-group">
                    <label for="nickname">
                        <h3>Отображаемое имя</h3>
                    </label>
                    <input id="nickname" value="<?= e($old['nickname'] ?? '') ?>" name="nickname" data-required>
                </div>
                <div class="input-group">
                    <div class="password-title"><label for="password">
                            <h3>Пароль</h3>
                        </label>
                        <div class="password-info"><svg class="info-icon" width="24" height="24">
                                <use href="#info-circle"></use>
                            </svg>
                            <div class="password-tooltip">
                                <div class="tooltip-text">
                                    <h3>Требования к паролю</h3>
                                    <p>Не менее 6 символов<br>
                                        Не менее 1 буквы<br>
                                        Не менее 1 цифры</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-with-icon"><input type="password" id="password" name="password" pattern="^(?=.*[a-z])(?=.*\d).{6,}$" data-required>
                        <button type="button " class="password-toggle button-without-styles" aria-label="Показать пароль">
                            <svg class="eye-icon" width="20" height="20">
                                <use href="#eye"></use>
                            </svg>
                            <svg class="eye-off-icon" width="20" height="20" style="display: none;">
                                <use href="#eye-off"></use>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="check">
                    <p>Регистрируясь, вы принимаете <a href="/rules">правила сайта</a> и обязуетесь их соблюдать.</p>
                </div>
                <div class="cf-turnstile" style="width: 300px;height: 69px;"
                    data-sitekey="0x4AAAAAACpYY5Y5Jlu5driC"
                    data-callback="onCaptchaSuccess">
                </div>
                <div class="input-group">
                    <button type="submit" class="submit-btn" disabled>ЗАРЕГИСТРИРОВАТЬСЯ</button>
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


        const email = form.querySelector('input[name="email"]');
        if (email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            showFieldError(email, 'Введите корректный email');
            hasErrors = true;
        } else if (email.value.length > 50) {
            showFieldError(email, 'email не должен быть длиннее 50 символов');
            hasErrors = true;
        }

        const username = form.querySelector('input[name="tag"]');
        if (username.value.length > 50) {
            showFieldError(tag, 'Имя пользователя не должно быть длиннее 50 символов');
            hasErrors = true;
        }

        const nickname = form.querySelector('input[name="nickname"]');
        if (nickname.value.length > 50) {
            showFieldError(nickname, 'Отображаемое имя не должно быть длиннее 50 символов');
            hasErrors = true;
        }


        const password = form.querySelector('input[name="password"]');
        if (password.value) {
            if (password.value.length < 6) {
                showFieldError(password, 'Пароль должен быть не менее 6 символов');
                hasErrors = true;
            } else if (password.value.length > 50) {
                showFieldError(password, 'Пароль не должен быть длиннее 50 символов');
                hasErrors = true;
            } else if (!/[a-zA-Zа-яА-ЯёЁ]/.test(password.value)) {
                showFieldError(password, 'Пароль должен содержать хотя бы одну букву');
                hasErrors = true;
            } else if (!/\d/.test(password.value)) {
                showFieldError(password, 'Пароль должен содержать хотя бы одну цифру');
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

    });
</script>