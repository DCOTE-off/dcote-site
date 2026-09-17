# Незнакомый код: что это и как работает

Файлы, которые появились в проекте не от тебя. Здесь разбор каждого — что
делает, зачем нужен, что с ним не так и что бы стоило изменить.

Правило: пока не понимаешь файл, не трогай и не удаляй. Но и не бойся его —
ничего магического внутри нет.

---

## 1. Морф-связи (полиморфные отношения)

### Идея

Обычная связь: `ratings.anime_episode_id` → `anime_episodes.id`. Один тип цели.

Проблема: оценивать надо и серии аниме, и тома ранобэ. Варианты решения:

- Две таблицы `anime_episode_ratings` и `ranobe_volume_ratings` — дублирование
  логики, но простые FK и индексы.
- Одна таблица с двумя nullable FK — растёт с каждым новым типом.
- Морф: две колонки `rateable_type` + `rateable_id`, где `type` говорит, в какой
  таблице искать `id`.

Морф выбран третий. Цена: **нельзя поставить foreign key**. База не может
сослаться на «одну из таблиц в зависимости от значения колонки». Значит
целостность обеспечивает приложение, а не БД.

### Где это в проекте

Миграция `2026_06_15_000001_create_ratings_table.php`:

```php
$table->morphs('rateable');   // создаёт rateable_type (string) + rateable_id (bigint) + индекс по паре
$table->unique(['user_id', 'rateable_type', 'rateable_id']);
```

`morphs()` — это шорткат. Он создаёт две колонки и составной индекс.

Модель `app/Models/Rating.php:27`:

```php
public function rateable(): MorphTo
{
    return $this->morphTo();   // читает $this->rateable_type, находит класс, грузит по rateable_id
}
```

Обратная сторона, `app/Models/AnimeEpisode.php:63`:

```php
public function ratings(): MorphMany
{
    return $this->morphMany(Rating::class, 'rateable');
}
```

`morphMany` = «у меня много Rating, где `rateable_type` = мой тип и
`rateable_id` = мой id».

### enforceMorphMap — зачем

По умолчанию Laravel пишет в `rateable_type` полное имя класса:
`App\Models\AnimeEpisode`. Это плохо: переименуешь класс или папку — все
существующие строки в БД станут мусором.

`app/Providers/AppServiceProvider.php:38` это исправляет:

```php
Relation::enforceMorphMap([
    Popular::TYPE_ANIME => AnimeSeason::class,     // 'anime'
    Popular::TYPE_RANOBE => RanobeVolume::class,   // 'ranobe'
    'anime_episode' => AnimeEpisode::class,
    'ranobe_volume' => RanobeVolume::class,
]);
```

Теперь в БД лежит `anime_episode`, а не FQCN. `enforce` (в отличие от
`morphMap`) ещё и бросает исключение, если встретился морф-класс не из карты —
защита от опечаток.

### НАЙДЕННЫЙ БАГ: RanobeVolume в карте дважды

`RanobeVolume::class` присутствует в карте под двумя алиасами: `ranobe`
(строка 40) и `ranobe_volume` (строка 42).

Что происходит при **записи**. Laravel определяет алиас в
`HasRelationships::getMorphClass()`
(`vendor/laravel/framework/src/Illuminate/Database/Eloquent/Concerns/HasRelationships.php:766`):

```php
if (! empty($morphMap) && in_array(static::class, $morphMap)) {
    return array_search(static::class, $morphMap, true);
}
```

`array_search` возвращает **первый** найденный ключ. Для `RanobeVolume` это
`'ranobe'`, потому что он раньше в массиве.

Последствие: если оценку тома сохранить через связь
(`$volume->ratings()->create([...])`), в `rateable_type` попадёт `ranobe`, а не
`ranobe_volume`. А все запросы на чтение ищут строку `ranobe_volume` —
захардкожено в `RanobeController.php:43,49,55` и в
`RatingController::RATEABLE_MODELS`. Такая оценка станет невидимой.

Почему баг ещё не проявился: запись идёт через `RatingController::store`,
который подставляет тип из валидированного запроса вручную
(`'rateable_type' => $data['rateable_type']`), а не через морф-связь. То есть
связи `RanobeVolume::ratings()` и `AnimeEpisode::ratings()` объявлены, но для
записи не используются. Мина заложена, взрыватель не подключён.

Это сработает в момент, когда ты напишешь `$volume->ratings()->create(...)` —
что абсолютно естественно сделать при рефакторинге.

Как починить правильно: развести сущности по разным морф-именам. `Popular`
таргетится на `AnimeSeason`/`RanobeVolume`, `Rating` — на
`AnimeEpisode`/`RanobeVolume`. Один класс не может иметь два алиаса, значит
алиас должен быть один на класс:

```php
Relation::enforceMorphMap([
    'anime_season' => AnimeSeason::class,
    'ranobe_volume' => RanobeVolume::class,
    'anime_episode' => AnimeEpisode::class,
]);
```

Но `populars` уже содержит строки `anime` и `ranobe`, а `ratings` — строки
`ranobe_volume`. Значит нужна миграция данных:

```php
DB::table('populars')->where('target_type', 'anime')->update(['target_type' => 'anime_season']);
DB::table('populars')->where('target_type', 'ranobe')->update(['target_type' => 'ranobe_volume']);
```

Плюс поправить `Popular::TYPE_ANIME`/`TYPE_RANOBE` и `getTypeLabelAttribute()`.

Это хорошая первая самостоятельная работа с морфами: ты понимаешь идею, а здесь
надо применить её на реальном баге с миграцией данных. И это готовый ответ на
интервью на вопрос «морф-таблицу делал не ты — объясни, как она работает».

### Что ещё стоит знать про морфы

Индексы. `morphs()` создаёт индекс по `(rateable_type, rateable_id)` — этого
достаточно для «найти оценки объекта». Но запросы вида
`where('rateable_type', ...)->where('user_id', ...)` из
`RanobeController.php:51-56` этим индексом покрываются плохо. Нужен
`(user_id, rateable_type, rateable_id)` — он, кстати, уже есть как unique,
и MySQL его использует. Проверить через `EXPLAIN`.

`morphWith` / `morphWithCount` в `MainController.php:59-68` — это защита от
N+1 для морф-связей. Обычный `with('target')` сделал бы отдельный запрос на
каждый тип; `morphWith` группирует по типу и позволяет догрузить вложенные
связи для конкретного типа. Это грамотный код, разберись в нём — он
показательный.

---

## 2. PublicationStateSynchronizer

`app/Services/PublicationStateSynchronizer.php`, 33 строки.

### Что делает

Три вещи по расписанию, но без расписания:

1. `AnimeEpisode::releaseDue()` — ставит `completed = true` всем сериям,
   у которых `appear_in` уже в прошлом. То есть автопубликация по времени.
2. `AnimeSeason::syncFinishedStatuses()` — если у сезона все серии вышли,
   переводит статус «Онгоинг» → «Вышел».
3. `RanobeVolume::syncFinishedStatuses()` — то же для томов по главам.

### Как устроено

```php
if (!Cache::add(self::CACHE_KEY, true, now()->addSeconds(60))) {
    return;
}
```

`Cache::add` — атомарная операция «записать, только если ключа нет». Возвращает
`false`, если ключ уже был. Здесь это используется как **лок с TTL**: первый
запрос за минуту проходит и делает работу, остальные сразу выходят.

Без этого лока три `UPDATE` выполнялись бы на каждый хит любой страницы.
При твоих 100k показов это десятки тысяч лишних UPDATE в месяц.

```php
} catch (Throwable $error) {
    Cache::forget(self::CACHE_KEY);
    report($error);
}
```

Если работа упала — лок снимается, чтобы следующий запрос попробовал снова,
а не ждал минуту. `report()` пишет в лог, но не роняет страницу. Это
аккуратно сделано.

Вызывается из `MainController:25`, `AnimeController:18,49,80`,
`RanobeController` — то есть с публичных страниц.

### Что с этим не так

Архитектурно это **cron, замаскированный под HTTP-запрос**. Проблемы:

**Нет трафика — нет обновлений.** Серия должна выйти в 03:00, но если до утра
никто не зашёл, она висит невышедшей. Для сайта с трафиком терпимо, но это
неявная зависимость логики от посещаемости.

**`CACHE_DRIVER=file` в проде** (проверено в README и `.env`). Файловый драйвер
даёт атомарность через блокировку файла, но при нескольких php-fpm воркерах
гонка возможна. Три `UPDATE` идемпотентны, поэтому двойное выполнение
безвредно — но это везение, а не дизайн.

**Плата на каждом запросе.** Даже когда лок закрыт, это обращение к кэшу на
каждый хит. Мелочь, но она в горячем пути.

**Смешение ответственности.** Контроллер страницы аниме занимается фоновой
синхронизацией всего сайта. `syncReleaseState()` в
`AnimeController.php:136` — приватный метод-обёртка, который просто вызывает
сервис; лишний слой.

### Как надо

Один cron на хосте:

```
* * * * * cd /var/www/dcote.net && docker compose -f docker-compose.prod.yml exec -T app php artisan schedule:run >> /dev/null 2>&1
```

И команда в `app/Console/Kernel.php` рядом с уже существующим
`sitemap:generate`:

```php
$schedule->command('publication:sync')->everyMinute();
```

`Console/Kernel.php` уже настроен и работает (там `sitemap:generate` daily at
03:00) — значит `schedule:run` на сервере **уже крутится**. То есть
инфраструктура для этого есть, надо только добавить строку и создать команду.

Проверить: есть ли реально cron на сервере, или `sitemap:generate` тоже никогда
не выполняется? Если сайтмап обновляется — cron есть, и весь синхронизатор
можно заменить на 5 строк.

Историю вопроса помнишь: у напарника это был отдельный docker-сервис, ты его
убрал и был прав — сложность не оправдывала пользу. Но правильный вывод из
этого — cron-строка, а не синхронизация на каждом хите.

Артефакт для портфолио: `docs/adr/0001-publication-sync.md` с описанием трёх
рассмотренных вариантов (отдельный сервис / request-driven / scheduler) и
почему выбран третий. Это язык, на котором говорят senior-разработчики.

---

## 3. GuardsNaturalKeyUniqueness

`app/Models/Concerns/GuardsNaturalKeyUniqueness.php`, трейт на 30 строк.

Проверяет, что комбинация колонок уникальна, до сохранения, и бросает
`ValidationException` с человеческим сообщением на русском.

Используется в `AnimeEpisode` (`season_id` + `episode_number`),
`AnimeSeason` (`season_number`), `RanobeVolume` (`ranobe_year_id` +
`volume_number`).

Вызывается из `booted()` через хук `saving`:

```php
static::saving(fn (AnimeEpisode $episode) => $episode->ensureUniqueNaturalKey(...));
```

`$this->exists` + `whereKeyNot($this->getKey())` — при обновлении исключает саму
запись из проверки, иначе запись всегда конфликтовала бы с собой.

Зачем нужно, если можно unique-индекс в БД: индекс даёт `SQLSTATE[23000]`
и白 screen для контент-редактора в Filament. Трейт даёт понятное сообщение
в форме.

Недочёт: **в БД unique-индексов при этом нет**. То есть защита только на уровне
приложения. При двух одновременных сохранениях оба пройдут проверку и оба
запишутся. Правильно — иметь и то и другое: индекс как гарантия, трейт как UX.

Это тоже задача на миграцию: добавить
`unique(['season_id','episode_number'])`, `unique(['season_number'])`,
`unique(['ranobe_year_id','volume_number'])`. Перед этим проверить, нет ли уже
дублей в проде.

---

## 4. SafeDescriptionHelper

`app/Helpers/SafeDescriptionHelper.php`.

Задача: описания сезонов и томов редактируются через админку и содержат
разметку (переносы, жирный, списки). Выводить их через `{!! !!}` в Blade
означает XSS, если в админку попадёт `<script>`.

Решение: `strip_tags` с белым списком тегов, затем нормализация оставшихся
тегов через `preg_replace_callback` (сбрасывает все атрибуты, приводит имя тега
к нижнему регистру).

Ключевая деталь: атрибуты вырезаются полностью. Это важно, потому что
`<b onclick="...">` прошёл бы `strip_tags` — тот проверяет только имя тега.
Именно поэтому регулярка нужна, а не только `strip_tags`.

На это есть тест: `tests/Unit/SafeDescriptionHelperTest.php`.

Есть и контрактный тест, который проверяет, что в Blade-шаблонах helper
действительно используется: `MetricsIntegrationContractTest.php`, метод
`test_descriptions_are_escaped_before_line_breaks_are_added`.

Замечание на будущее: при переезде на Vue этот helper перестанет работать
как задумано, потому что во Vue вывод пойдёт через `v-html`. Логика должна
остаться на сервере — то есть контроллер должен отдавать уже очищенную строку
в пропсах. Не забыть при миграции страниц `anime/season` и `ranobe/volume`.

---

## 5. Turnstile

`app/Rules/Turnstile.php`. Кастомное правило валидации: отправляет токен
капчи на `challenges.cloudflare.com/turnstile/v0/siteverify` и проверяет
`success`.

Хорошо сделано: `connectTimeout(2)` + `timeout(5)`, а `ConnectionException`
ловится отдельно и даёт понятную ошибку вместо 500. То есть если Cloudflare
недоступен, регистрация не падает белым экраном.

Отключается через `config('services.cloudflare.enabled')` — поэтому тесты
проходят без сети.

Тест есть: `tests/Unit/TurnstileRuleTest.php`.

---

## 6. FilesCollectionHelper

`app/Helpers/FilesCollectionHelper.php`. Сканирует диск (Cloudflare R2),
фильтрует `.webp` по подстроке в имени, возвращает коллекцию с `url` и
`download_url` (ищет одноимённый `.png` рядом).

Это «галереи с автопоиском файлов» из README — контент добавляется загрузкой
файла, без записи в БД.

Недочёт: на каждый вызов идёт `$storage->files()` плюс `$storage->exists()`
на каждый файл. Для R2 это сетевые запросы. При десятках изображений
страница иллюстраций будет заметно медленной.

Что делать: закэшировать результат (`Cache::remember` на час) или, если
галереи станут важной частью, вести файлы в БД.

---

## 7. DateHelpers

`app/Helpers/DateHelpers.php`, подключён через `composer.json` → `autoload.files`.
Это глобальная функция `russian_date()`, используется в
`MainController.php:152-153`.

Не самая чистая практика (глобальная функция вместо класса или Blade-директивы),
но рабочая. При переезде на Vue форматирование даты уедет на клиент
(`Intl.DateTimeFormat`, как уже сделано в `Home.vue:19`) — тогда решить, где
источник истины, и не иметь два разных формата в двух местах.

---

## Порядок изучения

Если разбирать по одному файлу за вечер, то в таком порядке — от простого
к сложному, каждый следующий опирается на предыдущий:

1. `SafeDescriptionHelper` + его тест (проще всего, сразу видно пользу)
2. `Turnstile` + его тест (внешний вызов, таймауты)
3. `GuardsNaturalKeyUniqueness` (хуки модели, `booted`, `saving`)
4. `FilesCollectionHelper` (коллекции, Storage)
5. Морфы: `Rating` → `Popular` → `enforceMorphMap` → починить баг с дублем
6. `PublicationStateSynchronizer` → заменить на scheduler
