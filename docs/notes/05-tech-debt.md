# Технический долг

Работает, но требует переделки. Это не баги (они в `03-findings.md`), а
осознанно отложенные решения. Правило то же: закрыл пункт — удали его.

---

## Магические ID ролей

Единого источника правды нет, `role_id` захардкожены в шести местах:

- `app/Providers/AppServiceProvider.php:35` — гейт `access-admin`: `[3, 4]`
- `app/Models/User.php:66` — `canAccessPanel`: `[3, 4]` (дубль гейта)
- `app/Http/Controllers/CommentsController.php:30` — `TEAM_ROLE_IDS`: `[2, 3, 4]`
- `app/Http/Controllers/CommentsController.php:410` — `canModerate()`: `[2, 3, 4]`
- `app/Policies/CommentPolicy.php` — `MODERATION_ROLE_IDS`: `[2, 3, 4]` (дубль `canModerate`)
- `app/Policies/UserPolicy.php:15,23,31` — `role_id === 4`

Последствия: смена прав роли — правка в пяти-шести файлах. Рассинхрон уже
есть: `[2, 3, 4]` объявлено в трёх местах (модерация и «команда»), доступ в
админку `[3, 4]` — в двух.

Что делать: вынести роли/права в один источник — enum или таблицу
`permissions` + `role_has_permissions` (`spatie/laravel-permission`), а проверки
свести к способностям (`$user->can('moderate-comments')`).

---

## Размытое разграничение прав

- Одно и то же «админ» выражено двумя независимыми реализациями: гейт
  `access-admin` (`AppServiceProvider.php:34`) и `User::canAccessPanel`
  (`User.php:63`).
- Модерация комментариев продублирована: `CommentPolicy` (для `authorize`) и
  `CommentsController::canModerate()` (для поля `can.delete` в ответе API).
  Логика обязана совпадать, но живёт в двух местах.
- `canAccessPanel` бросает `HttpResponseException` с редиректом — смешение
  авторизации и HTTP-ответа; Filament ожидает просто `bool`.
- На фронт уходит плоский флаг `auth.can_access_admin`
  (`HandleInertiaRequests.php:22`) вместо набора способностей. При росте прав
  UI придётся расширять проп вручную.

Что делать: способности в одном месте, политики — единственный источник
логики, `canAccessPanel` → чистый `bool`, на фронт — список abilities.

---

## Захардкоженные значения

- ID ролей (см. выше).
- Инлайн-цвета в компонентах: акцентный `rgba(146, 21, 69, 1)` в
  `Comments.vue`/`SiteHeader.vue`, `#c6750c`, `rgba(98, 59, 146, 1)` — вместо
  CSS-переменных темы. Уже частично отмечено в `04-roadmap.md` (инлайн-стили
  в `SiteHeader.vue`).
- `config('app.key')` как HMAC-секрет в сниппете метрик
  (`resources/views/app.blade.php`) — нарушение key separation: ротация
  `APP_KEY` меняет все псевдонимы пользователей.

Что делать: цвета — в переменные темы; секрет метрик — в отдельный ключ
(`services.dcote.metrics_hmac_key`).

---

## Метрики: `page` при SPA-переходах

`window.DCOTE_SITE_METRICS.page` вычисляется на полной загрузке
(`resources/views/app.blade.php`) и при клиентских Inertia-переходах не
обновляется. Presence-трекер приписывает активность первой открытой странице.
Принято как допустимое (см. сессию про перенос сниппета).

Смежное в том же сниппете:

- нет guard по окружению: `metrics_base_url` по умолчанию указывает на прод,
  поэтому трекер грузится и в local, и в тестах;
- `page` неканоничный — `route()->getName()` / `route()->uri()` (шаблон с
  `{...}`) / `path()` дают разные форматы для одного и того же потребителя.

Что делать: обновлять `page` на `router.on('navigate')` (имя/путь роута шарить
через `HandleInertiaRequests::share()`), либо вовсе отдать выбор страницы
самому трекеру (`location.pathname`); обернуть вывод в `@production` и
`@if($metricsBaseUrl)`. Правки поля `page` согласовать с репозиторием метрик —
контракт требует обратной совместимости (`docs/metrics-contract.md`).
