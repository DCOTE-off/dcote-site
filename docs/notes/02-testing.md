# Тесты: с нуля и своими руками

Тесты в проекте есть, но писал их не я. Цель этой заметки — уметь писать их
самостоятельно и понимать, что уже написано.

Порядок: сначала починить инфраструктуру (без этого писать больно), потом
научиться на трёх шаблонах, потом покрыть комментарии по ходу разработки.

---

## Часть 1. Инфраструктура сломана концептуально

### Проблема: тесты гоняются на реальной базе

`phpunit.xml:24-25` — sqlite закомментирован:

```xml
<!-- <env name="DB_CONNECTION" value="sqlite"/> -->
<!-- <env name="DB_DATABASE" value=":memory:"/> -->
```

Значит тесты идут на `DB_CONNECTION=mysql`, `DB_DATABASE=dcote` из `.env` —
на той же базе, в которой лежит рабочий контент.

Спасает только `DatabaseTransactions`: каждый тест открывает транзакцию и
откатывает в конце. Работает, но:

- один упавший процесс или `exit` внутри теста — и данные остаются;
- любой `DB::statement` с DDL (например `truncate`) не откатывается в MySQL;
- нельзя запускать тесты параллельно;
- нельзя запустить тесты на чистой машине без дампа базы.

Отсюда же растут костыли в существующих тестах. Вот этот приём:

```php
$baseSeasonNumber = (int) AnimeSeason::query()->max('season_number') + 100;
```

`tests/Feature/PublicationStatusTest.php:19` — номер сезона считается от
максимума в живой базе, чтобы не столкнуться с реальными данными. А в
`ReleaseDueAnimeEpisodesTest.php:25` ещё жёстче:

```php
$season = AnimeSeason::query()->firstOrFail();
```

Тест берёт первый сезон из реальной базы. Если база пустая — тест падает.
Это не тест, это тест плюс требование к содержимому продакшн-данных.

### Как починить

Шаг 1. Отдельная тестовая база (не sqlite — MySQL, чтобы совпадать с продом;
`whereRaw` в `syncFinishedStatuses` зависит от диалекта).

`phpunit.xml`:

```xml
<env name="DB_DATABASE" value="dcote_testing"/>
```

Создать её в dev-контейнере один раз:

```bash
docker compose -f docker-compose.dev.yml exec mysql \
  mysql -u root -p -e "CREATE DATABASE dcote_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Шаг 2. Заменить `DatabaseTransactions` на `RefreshDatabase`. Он прогоняет
миграции на чистой базе и откатывает после. Тогда каждый тест стартует с
пустых таблиц, и вся арифметика с `max() + 100` становится ненужной.

Шаг 3. Переписать существующие тесты под чистую базу. Это хорошее упражнение:
ты разбираешься, что тест проверяет, и заодно убираешь костыль.

Шаг 4. `phpunit` в CI (см. `00-process.md`). Без этого пункт 1-3 бессмысленны.

### Почему это первым делом

Пока тесты требуют живой базы, ты не сможешь запустить их на чистом
клоне и не сможешь поставить в CI. Всё остальное в этой заметке зависит от
этого шага.

---

## Часть 2. Фабрики — их почти нет

`database/factories/` содержит только `UserFactory.php`. Поэтому все
существующие тесты создают данные руками, по 15 строк на объект:

```php
RanobeVolume::create([
    'volume_number' => $volumeNumber,
    'general_number' => $generalNumber,
    'cover_image' => 'ranobe/test-cover.webp',
    'cover_image_mobile' => 'ranobe/test-cover-mobile.webp',
    'status' => RanobeVolume::STATUS_ONGOING,
    'release_date_book' => now(),
    // ... ещё 6 полей
]);
```

`tests/Feature/PublicationStatusTest.php:101` — и это повторяется в каждом
тесте. С фабрикой то же самое выглядит так:

```php
$volume = RanobeVolume::factory()->create(['status' => RanobeVolume::STATUS_ONGOING]);
```

### Фабрики, которые нужны

```
AnimeSeasonFactory
AnimeEpisodeFactory      + состояния: released(), upcoming()
RanobeYearFactory
RanobeVolumeFactory
RanobeChapterFactory
RatingFactory
CommentFactory           (когда дойдёшь до комментариев)
```

Пример с состояниями — то, что реально экономит время:

```php
// database/factories/AnimeEpisodeFactory.php
namespace Database\Factories;

use App\Models\AnimeSeason;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnimeEpisodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'season_id' => AnimeSeason::factory(),
            'episode_number' => $this->faker->unique()->numberBetween(1, 24),
            'episode_name' => $this->faker->sentence(3),
            'completed' => true,
            'opening_start' => -1,
        ];
    }

    public function upcoming(): static
    {
        return $this->state(fn () => [
            'completed' => false,
            'appear_in' => now()->addDay(),
        ]);
    }

    public function due(): static
    {
        return $this->state(fn () => [
            'completed' => false,
            'appear_in' => now()->subMinute(),
        ]);
    }
}
```

Не забудь `use HasFactory;` в моделях — сейчас он есть только в
`RanobeVolume.php:12`, в `AnimeSeason` и `AnimeEpisode` его нет.

Тогда тест на `releaseDue` из 40 строк сжимается до:

```php
public function test_release_due_marks_only_due_episodes(): void
{
    AnimeEpisode::factory()->due()->create();
    AnimeEpisode::factory()->upcoming()->create();

    $this->assertSame(1, AnimeEpisode::releaseDue());
}
```

---

## Часть 3. Три шаблона, которых хватит на 90% случаев

### Шаблон 1: Unit — чистая функция

Самый простой. Нет базы, нет HTTP. Класс `PHPUnit\Framework\TestCase`
напрямую, без Laravel.

Смотри готовый пример: `tests/Unit/SafeDescriptionHelperTest.php`.

Структура любого теста — три блока (Arrange, Act, Assert):

```php
public function test_strips_disallowed_tags(): void
{
    // Arrange: подготовка
    $input = '<script>alert(1)</script><b>жирный</b>';

    // Act: одно действие
    $result = SafeDescriptionHelper::render($input, 'fallback');

    // Assert: проверка
    $this->assertSame('alert(1)<b>жирный</b>', (string) $result);
}
```

Пиши так, чтобы название теста читалось как утверждение о поведении.
`test_strips_disallowed_tags` — хорошо. `test_render` — плохо.

Твоё первое упражнение: допиши к `SafeDescriptionHelper` кейс, которого там
нет — `<b onclick="...">` должен потерять атрибут. Проверь, что helper это
действительно делает (по коду должен, но убедись тестом).

### Шаблон 2: Feature — HTTP-запрос

Нужна база и приложение. Наследуешься от `Tests\TestCase`.

```php
namespace Tests\Feature;

use App\Models\AnimeSeason;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimeSeasonPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_announced_season_returns_404(): void
    {
        $season = AnimeSeason::factory()->create([
            'status' => AnimeSeason::STATUS_ANNOUNCED,
        ]);

        $this->get(route('anime.season', ['season' => $season->season_number]))
            ->assertNotFound();
    }
}
```

Полезные проверки:

```php
$response->assertOk();                       // 200
$response->assertNotFound();                 // 404
$response->assertRedirect(route('home'));
$response->assertSee('текст');               // есть в HTML
$response->assertDontSee('черновик');
$response->viewData('seasons_list');         // что контроллер отдал в Blade
$this->assertDatabaseHas('ratings', [...]);  // проверка состояния БД
$this->assertDatabaseMissing('ratings', [...]);
```

Для авторизации:

```php
$user = User::factory()->create();
$this->actingAs($user)->get(route('account'))->assertOk();
```

Для Inertia-страниц `viewData` не работает — там свой набор:

```php
$response->assertInertia(fn (Assert $page) => $page
    ->component('Home')
    ->has('popularCards', 3)
);
```

Пакет для этого (`inertiajs/inertia-laravel` включает `Inertia\Testing`) уже
стоит. Это понадобится, когда начнёшь переносить страницы.

### Шаблон 3: тест на время

`Carbon::setTestNow()` замораживает время. Обязательно сбрасывай в `tearDown`,
иначе поедут следующие тесты.

Готовый пример — `tests/Feature/ReleaseDueAnimeEpisodesTest.php:15-20`.

```php
protected function tearDown(): void
{
    Carbon::setTestNow();
    parent::tearDown();
}
```

Это единственная неочевидная вещь в тестах на время. Всё остальное — обычный
Feature-тест.

---

## Часть 4. Что покрыть в первую очередь

Не гонись за процентом покрытия. Покрывай то, что дорого сломать.

### Приоритет 1 — деньги и безопасность

| Что | Почему |
|---|---|
| Морф-баг с `ranobe_volume` (см. `01-unknown-code.md`) | Тест должен падать до фикса |
| Мёртвые роуты `/account`, `/favorite` | Живые 500 у юзеров |
| `RatingController` — нельзя ставить рейтинг чужому/несуществующему | Валидация данных |
| `Turnstile` при недоступном Cloudflare | Регистрация не должна ломаться |

Тест на морф-баг — напиши его первым, до фикса. Это лучший способ понять
проблему:

```php
public function test_ranobe_volume_morph_alias_matches_stored_rating_type(): void
{
    $volume = RanobeVolume::factory()->create();

    Rating::create([
        'user_id' => User::factory()->create()->id,
        'rateable_type' => 'ranobe_volume',   // так пишут Blade и фронт
        'rateable_id' => $volume->id,
        'rating' => 8,
    ]);

    // А так это читает Eloquent через связь
    $this->assertCount(1, $volume->ratings);
}
```

Если тест красный — баг подтверждён. Зелёный после фикса — баг закрыт.
Это и есть смысл теста: он живёт дольше, чем твоя память о проблеме.

### Приоритет 2 — то, что уже ломалось

Правило: каждый пойманный в проде баг получает тест. Не «когда-нибудь»,
а в том же коммите, что и фикс. Тогда тесты растут сами и покрывают именно
хрупкие места, а не случайные.

### Приоритет 3 — комментарии, по ходу разработки

Здесь попробуй писать тест до кода хотя бы на одной задаче. Не ради
идеологии TDD, а чтобы почувствовать разницу: когда сначала пишешь
`test_reply_to_deleted_comment_is_rejected`, ты вынужден решить, что
вообще должно происходить. Это проектирование, а не проверка.

Минимальный набор для комментариев:

```
гость не может оставить комментарий
владелец может удалить свой
чужой не может удалить не свой
ответ на удалённый комментарий отклоняется
пустой/слишком длинный текст отклоняется
rate limit срабатывает после N запросов
дерево из 50 комментариев грузится за фиксированное число запросов (N+1!)
```

Последний — самый ценный и самый неочевидный:

```php
public function test_comment_tree_does_not_trigger_n_plus_one(): void
{
    Comment::factory()->count(50)->create(['commentable_id' => $episode->id]);

    DB::enableQueryLog();
    $this->get(route('anime.episode', [...]))->assertOk();

    $this->assertLessThan(15, count(DB::getQueryLog()));
}
```

Такой тест ловит регрессию производительности, которую глазами не увидишь,
пока комментариев не станет много.

---

## Часть 5. Инструменты, которые стоит поставить

### Model::preventLazyLoading

В `AppServiceProvider::boot()`:

```php
Model::preventLazyLoading(! app()->isProduction());
```

Приложение начнёт выбрасывать исключение при ленивой загрузке связи в dev и
тестах, а в проде промолчит. Это ловит N+1 автоматически, без чтения логов.

Учти: сразу после включения посыпятся ошибки в существующем коде —
например `AnimeController.php:119` (`$prev_episode->season->season_number`
без eager load). Это не поломка, это обнаружение.

### Laravel Pint

Уже в `composer.json` как dev-зависимость, но, судя по стилю кода, не
запускается. `AuthController.php:14-23` — скобка метода на той же строке,
что нарушает PSR-12:

```php
public function register() {
```

Запусти один раз и посмотри дифф:

```bash
./vendor/bin/pint --test    # только показать
./vendor/bin/pint           # исправить
```

Отдельным коммитом `style: apply pint`, не смешивая с логикой. Потом в CI.

### Что не ставить сейчас

Pest (переписывать существующие тесты — работа без выгоды), Dusk
(браузерные тесты дорого поддерживать), покрытие в процентах (метрика,
которая заставляет писать бесполезные тесты на геттеры).

---

## Часть 6. Порядок действий

```
1. Тестовая база + RefreshDatabase                    (вечер)
2. Фабрики для 5 моделей                              (вечер)
3. Переписать 3 существующих теста под чистую базу    (вечер, обучение)
4. Тест на морф-баг, потом фикс                       (вечер)
5. phpunit + pint в CI                                (30 минут)
6. preventLazyLoading + разгрести что вылезет         (вечер)
7. Дальше — тесты вместе с фичами, отдельно не пишем
```

После пункта 5 у тебя появляется то, чего сейчас нет: гарантия, что сломанный
код не уедет в прод. Это и есть главная ценность тестов, а не покрытие.

## Что отвечать на интервью

Вопрос «писал ли ты тесты» после этого превращается из слабого места в
сильное, если ответ звучит так: тесты в проекте были, но гонялись на живой
базе с костылями вроде `max() + 100`; я перевёл их на отдельную базу с
`RefreshDatabase`, добавил фабрики, поставил в CI и нашёл тестом реальный баг
с морф-алиасом. Это рассказ про инженерное решение, а не про знание синтаксиса
PHPUnit.
