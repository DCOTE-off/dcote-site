<div align="center">

# DCOTE

**Фан-сайт ранобэ «Добро пожаловать в класс превосходства» (You-Zitsu / Classroom of the Elite)**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![Docker](https://img.shields.io/badge/Docker-2496ED?logo=docker&logoColor=white)](https://docker.com)
[![Filament](https://img.shields.io/badge/Filament-3-FFA500)](https://filamentphp.com)
[![Vite](https://img.shields.io/badge/Vite-6-646CFF?logo=vite&logoColor=white)](https://vitejs.dev)
[![Sanctum](https://img.shields.io/badge/Sanctum-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/docs/sanctum)

</div>

## О проекте

[dcote.net](https://dcote.net/) — фан-сайт по серии ранобэ и аниме **«Добро пожаловать в класс превосходства» (You-Zitsu / Classroom of the Elite)**. Реализован на **Laravel 12** с рейтингами, Vite-сборкой, Filament Admin и двумя Docker-сценариями.
Были сделаны:
- Библиотека переводов ранобэ с разбивкой по годам, томам и главам
- Страницы сезонов и серий аниме со встроенными плеерами
- Систему пользовательских рейтингов (1–10) с API upsert
- Галереи иллюстраций с автопоиском файлов
- Cookie-авторизация через Laravel Sanctum
- Filament-админка для управления контентом

## Стек

- **Backend:** Laravel 12, PHP 8.4, MySQL 8
- **Frontend:** Blade, Vite 6, vanilla JS
- **Admin:** Filament 3
- **Auth:** Laravel Sanctum
- **Storage:** Cloudflare R2 (S3-совместимое объектное хранилище)
- **Infra:** Docker, docker-compose (dev + prod), Nginx, Nginx Proxy Manager
- **Прочее:** Cloudflare Turnstile, Floating UI

---

<br>

## Docker Dev

Dev-сборка использует текущий Sail-like PHP 8.4 контейнер, монтирует проект внутрь контейнера и запускает Laravel через `php artisan serve`.
Для запуска использовать WSL.

Перед первым запуском (копируем переменные окружения):

```bash
cp .env.example .env
```

Первичная установка зависимостей (Composer):

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php84-composer:latest composer install --ignore-platform-reqs
```

Запуск:

```bash
docker compose -f docker-compose.dev.yml up -d --build
```

Миграции:

```bash
docker compose -f docker-compose.dev.yml exec app php artisan migrate
```

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



По умолчанию сайт доступен на `http://localhost:8080`. Порт можно изменить через `.env`:

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

`nginx` отдаёт статику из `public` и прокидывает PHP-запросы в `php-fpm`. `app` содержит собранное Laravel-приложение, Composer-зависимости и Vite build.

Создайте `.env.production` на сервере:

```env
APP_NAME=DCOTE
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.example

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
