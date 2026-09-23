# Plain-text описания сезонов и томов — что ещё нужно сделать

Статус: локальная БД уже нормализована, код вывода переведён на plain-text.
Ниже — что осталось сделать на проде/инфраструктуре, чтобы не сломать вывод.

## Контекст

- `anime_seasons.season_description` (`text`, nullable)
- `ranobe_volumes.volume_description` (`text`, not null)
- Раньше это был HTML (реально использовался только `<br><br>` между абзацами),
  выводился через `SafeDescriptionHelper` + `v-html`.
- Теперь целевой формат — **plain-text**:
    - `\n\n` — разделитель абзацев (пустая строка),
    - одиночный `\n` — мягкий перенос строки (реплики диалогов),
    - HTML-тегов нет.
- Рендер — экранированный текст (`{{ }}`) + `white-space: pre-line`, без `v-html`
  и без санитайзера. Для сезона и тома используется компонент `ClampedText`.
- Сентинел пустого описания: в админке нельзя оставить пусто, поэтому иногда
  пишут литеральную строку `null`. Vue подставляет fallback ("Описание сезона" /
  "Описание N тома Y года обучения"). Значение в БД не трогаем.

## Что уже сделано (локально)

- Данные в локальной БД приведены к plain-text (теги `<`/`>` отсутствуют).
- Бэкап до правки: `/tmp/opencode/dbdesc/backup_before_norm.sql`
  и таблицы `_bak_anime_seasons_desc`, `_bak_ranobe_volumes_desc` (MySQL `dcote`).
- `SafeDescriptionHelper` больше не используется, мёртвые Blade-шаблоны
  `pages/anime/season.blade.php` и `pages/ranobe/volume.blade.php` удалены.

---

## TODO 1 (критично) — миграция данных на проде

Локальную правку делали прямым SQL. Для прода нужна **Laravel data-миграция**,
чтобы она применилась автоматически шагом `php artisan migrate --force` в CI
(`.github/workflows/deploy.yml`).

Требования к миграции:

- самодостаточная (не зависит от классов приложения, которые потом могут измениться);
- идемпотентная (повторный прогон на уже нормализованных данных — no-op);
- чанками (`chunkById`), чтобы не держать таблицу.

Логика нормализации (проверена на локальных данных):

```php
$text = str_replace(["\r\n", "\r"], "\n", $value);   // CRLF -> LF
$text = str_replace('<br>', "\n", $text);              // <br> -> перенос
$text = preg_replace('/[ \t]+\n/', "\n", $text);       // хвостовые пробелы
$text = preg_replace('/\n{2,}/', "\n\n", $text);       // схлопнуть 2+ переносов в абзац
$text = trim($text);
```

Эквивалентный SQL (использовался локально):

```sql
UPDATE anime_seasons
SET season_description = TRIM(
  REGEXP_REPLACE(
    REGEXP_REPLACE(
      REGEXP_REPLACE(
        REPLACE(REPLACE(season_description, '\r\n', '\n'), '\r', '\n'),
        '<br>', '\n'
      ),
      '[ \t]+\n', '\n'
    ),
    '\n{2,}', '\n\n'
  )
)
WHERE season_description IS NOT NULL;

UPDATE ranobe_volumes
SET volume_description = TRIM(
  REGEXP_REPLACE(
    REGEXP_REPLACE(
      REGEXP_REPLACE(
        REPLACE(REPLACE(volume_description, '\r\n', '\n'), '\r', '\n'),
        '<br>', '\n'
      ),
      '[ \t]+\n', '\n'
    ),
    '\n{2,}', '\n\n'
  )
);
```

Перед прогоном на проде — бэкап:

```bash
docker compose -f docker-compose.prod.yml exec -T mysql sh -c \
  'mysqldump -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" anime_seasons ranobe_volumes' \
  > backup-desc-$(date +%F).sql
```

Проверка после миграции (оба числа должны быть 0):

```sql
SELECT
  (SELECT COUNT(*) FROM anime_seasons WHERE season_description LIKE '%<%') AS s,
  (SELECT COUNT(*) FROM ranobe_volumes WHERE volume_description LIKE '%<%') AS v;
```

Порядок в деплое сейчас: `up -d` (новый код) → `migrate --force`. Поэтому в коде
на время оставлен **толерантный нормализатор** (`DescriptionTextHelper::normalize`)
— он переваривает и старый HTML, и новый plain-text. Благодаря этому окно между
новым кодом и миграцией, а также падение/забытая миграция, не ломают вывод.

---

## TODO 2 (критично) — ассеты фронта не собираются в прод

Симптом: изменения Vue/CSS могут не доезжать до прода.

Факты:

- В `docker/prod/Dockerfile` раньше была node-стадия `assets`
  (`npm ci && npm run build` + `COPY --from=assets .../public/build`), удалена
  коммитом `cd94887 fix: remove vite connect from dockerfile`.
- `public/build` в `.gitignore` **и** `.dockerignore`.
- В `.github/workflows/deploy.yml` нет `npm ci && npm run build`.
- `docker/prod/nginx.conf` отдаёт `/build/` из `/var/www/html/public/build`.

Варианты:

- **A (рекомендуется):** вернуть node-стадию `assets` в `docker/prod/Dockerfile`
  (по сути реверт `cd94887`). Сборка самодостаточна, `.dockerignore` не мешает,
  т.к. файлы копируются из стадии.
- **B:** добавить `npm ci && npm run build` в воркфлоу до `docker build` и убрать
  `public/build` из `.dockerignore` (нужен Node на сервере).

---

## TODO 3 (не срочно) — фаза cleanup

После того как на проде подтверждена миграция данных:

1. Убрать толерантный нормализатор из контроллеров
   (`DescriptionTextHelper::normalize`) и удалить сам хелпер, если он больше нигде
   не нужен.
2. Проверить, что `public/build` и мёртвые Blade-шаблоны действительно не нужны.

## Справка: где что лежит

- Контроллеры: `app/Http/Controllers/AnimeController.php` (showSeason),
  `app/Http/Controllers/RanobeController.php` (showVolume).
- Компонент обрезки: `resources/js/Components/ClampedText.vue`
  (`lines`, `text`, `expandable`, `alwaysExpanded`, слот `#button`).
- Страницы: `resources/js/Pages/Anime/Season.vue`, `resources/js/Pages/Ranobe/Volume.vue`.
