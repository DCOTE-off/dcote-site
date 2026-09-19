<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Тесты идут в одном процессе, а фоновые задачи планировщика могут
        // держать лок в кэше. Без сброса второй тест мог бы пропустить работу.
        Cache::flush();
    }
}
