<div align="center">

# DCOTE

**Фан-сайт ранобэ «Добро пожаловать в класс превосходства» (You-Zitsu / Classroom of the Elite)**

[![Laravel](https://img.shields.io/badge/Laravel-10-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![Docker](https://img.shields.io/badge/Docker-2496ED?logo=docker&logoColor=white)](https://docker.com)
[![Filament](https://img.shields.io/badge/Filament-3-FFA500)](https://filamentphp.com)
[![Sanctum](https://img.shields.io/badge/Sanctum-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/docs/sanctum)

</div>

## О проекте

[dcote.net](https://dcote.net/) — фан-сайт по серии ранобэ и аниме **«Добро пожаловать в класс превосходства» (You-Zitsu / Classroom of the Elite)**. Реализован на **Laravel 10** с рейтингами, Filament Admin и двумя Docker-сценариями.
Были сделаны:

- Библиотека переводов ранобэ с разбивкой по годам, томам и главам
- Страницы сезонов и серий аниме со встроенными плеерами
- Систему пользовательских рейтингов (1–10) с API upsert
- Галереи иллюстраций с автопоиском файлов
- Сессионная авторизация (Laravel web guard); Sanctum подключает сессию к API-маршрутам `/api/*` (рейтинги)
- Filament-админка для управления контентом

## Стек

- **Backend:** Laravel 10, PHP 8.4, MySQL 8
- **Frontend:** Blade, vanilla JS, Vite 8; Vue 3 и Inertia 2 подготовлены для поэтапного внедрения
- **Admin:** Filament 3
- **Auth:** Laravel web guard (сессии) + Sanctum для аутентификации API через сессию
- **Storage:** Cloudflare R2 (S3-совместимое объектное хранилище)
- **Infra:** Docker, docker-compose (dev + prod), Nginx, Nginx Proxy Manager
- **Прочее:** Cloudflare Turnstile, Embla Carousel, Floating UI

---

<br>

## Docker Dev

Dev-сборка использует облегчённый Sail-like PHP 8.4 контейнер с Composer и MySQL client, монтирует проект внутрь контейнера и запускает Laravel через `php artisan serve`.
Для запуска использовать WSL.

Перед первым запуском (копируем переменные окружения):

```bash
cp .env.example .env
```

Первичная установка зависимостей (Composer):

```bash
docker compose -f docker-compose.dev.yml run --rm --no-deps app composer install
```

Запуск:

```bash
docker compose -f docker-compose.dev.yml up -d --build
```

Установка и запуск frontend-зависимостей:

```bash
npm install
npm run dev
```

`npm run dev` нужно держать запущенным в отдельном терминале. Vite отслеживает изменения в `resources/css` и `resources/js` и автоматически обновляет страницу через HMR. Сайт открывается по адресу `http://localhost:8080`; порт `5173` используется только Vite.

Исходники публичного frontend-кода находятся в `resources/css` и `resources/js`. Новые CSS/JS-файлы не следует добавлять в `public/css` или `public/js`; `public/images`, `public/fonts` и `public/svgs` остаются для статических ресурсов. Ресурсы Filament не относятся к публичной Vite-сборке.

Проверка production-сборки:

```bash
npm run build
```

Создание app key

```bash
docker compose -f docker-compose.dev.yml exec app php artisan key:generate
```

Миграции:

```bash
docker compose -f docker-compose.dev.yml exec app php artisan migrate
```

Тестовые аккаунты:
В дев-окружении (`APP_ENV=local`) при старте приложения автоматически создаются 4 тестовых аккаунта для удобного входа и проверки ролей. Вручную их можно создать через seed:

```bash
docker compose -f docker-compose.dev.yml exec app php artisan db:seed --class=TestAccountsSeeder
```

| Логин | Пароль   | Роль         | access-admin |
| ----- | -------- | ------------ | ------------ |
| dev01 | 12345678 | Пользователь | нет          |
| dev02 | 12345678 | Модератор    | нет          |
| dev03 | 12345678 | Редактор     | да           |
| dev04 | 12345678 | Разработчик  | да           |

Создание идемпотентно: при повторном запуске уже существующие аккаунты не дублируются. В продакшен-окружении (`APP_ENV=production`) аккаунты не создаются.

Данные с дампа бд:

```bash
docker compose -f docker-compose.dev.yml exec -T mysql mysql -u sail -p"password" dcote < dump.sql
```

Для работы со storage создать симлинк:

```bash
docker compose -f docker-compose.dev.yml exec app php artisan storage:link
```

## Работа с WSL

Для открытия vs code в линуксовом окружении:

```PowerShell
code .
```

Для открытия wsl консоли в PowerShell (пример на ubuntu 24.04):

```bash
wsl -d Ubuntu-24.04
```

Посмотреть доступные дистрибутивы:

```bash
wsl --list --online
```

Установить конкретную версию:

```bash
wsl --install -d Ubuntu-24.04
```

Порт Laravel можно изменить через `.env`:

```env
APP_PORT=8081
```

Для dev Compose база доступна приложению как `mysql`. Эти значения уже прокидываются в контейнер:

```env
DB_HOST=mysql
DB_DATABASE=dcote
DB_USERNAME=sail
DB_PASSWORD=password
```

## Docker Production

Production-сборка использует схему:

```text
Nginx Proxy Manager -> nginx -> php-fpm -> Laravel
```

`nginx` отдаёт статику из `public` и прокидывает PHP-запросы в `php-fpm`. `app` содержит Laravel-приложение и Composer-зависимости.

Создайте `.env.production` на сервере:

```env
APP_NAME=DCOTE
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.example
DCOTE_VIDEO_BASE_URL=https://video.dcote.net
DCOTE_METRICS_BASE_URL=https://metrics-api.dcote.net

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_DATABASE=dcote
DB_USERNAME=dcote
DB_PASSWORD=change-me
DB_ROOT_PASSWORD=change-root-password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

TURNSTILE_SECRET_KEY=
TURNSTILE_SITE_KEY=
```

Сгенерировать ключ можно так:

```bash
docker compose -f docker-compose.prod.yml run --rm app php artisan key:generate --show
```

Вставьте полученное значение в `APP_KEY` внутри `.env`.

Запуск production:

```bash
npm ci
npm run build
docker compose -f docker-compose.prod.yml up -d --build
```

Команды после деплоя:

```bash
docker compose -f docker-compose.prod.yml --env-file .env.production exec -T app php artisan migrate --force
docker compose -f docker-compose.prod.yml --env-file .env.production exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml --env-file .env.production exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml --env-file .env.production exec -T app php artisan view:cache
```

## Nginx Proxy Manager

Production Compose публикует внутренний nginx наружу через `APP_PORT`, по умолчанию `8080`.

В Nginx Proxy Manager укажите:

```text
Forward Hostname / IP: IP сервера с Docker
Forward Port: 8080
Scheme: http
```

Если Nginx Proxy Manager подключён к Docker-сети `dcote-prod`, можно проксировать напрямую на:

```text
Forward Hostname / IP: nginx
Forward Port: 80
Scheme: http
```

## Полезные Команды

Остановить dev:

```bash
docker compose -f docker-compose.dev.yml down
```

Остановить production:

```bash
docker compose -f docker-compose.prod.yml down
```

Посмотреть логи:

```bash
docker compose -f docker-compose.prod.yml logs -f nginx app
```
