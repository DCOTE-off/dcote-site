<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MetricsIntegrationContractTest extends TestCase
{
    public function test_site_owns_the_tracker_and_keeps_the_metrics_url_optional(): void
    {
        $root = dirname(__DIR__, 2);
        $layout = file_get_contents($root.'/resources/views/layouts/app.blade.php');
        $services = file_get_contents($root.'/config/services.php');
        $tracker = file_get_contents($root.'/public/js/site-presence-tracker.js');

        $this->assertStringContainsString("asset('js/site-presence-tracker.js')", $layout);
        $this->assertStringNotContainsString('metrics-api/site-presence-tracker.js', $layout);
        $this->assertStringContainsString("'base_url' => env('METRICS_BASE_URL'", $services);
        $this->assertStringContainsString('/metrics/site/ws', $tracker);

        foreach (['page', 'userId', 'sessionId', 'tabId'] as $field) {
            $this->assertStringContainsString($field, $tracker);
        }
    }

    public function test_descriptions_are_escaped_before_line_breaks_are_added(): void
    {
        $root = dirname(__DIR__, 2);
        $season = file_get_contents($root.'/resources/views/pages/anime/season.blade.php');
        $volume = file_get_contents($root.'/resources/views/pages/ranobe/volume.blade.php');

        $this->assertStringContainsString('SafeDescriptionHelper::render($about_season->season_description', $season);
        $this->assertStringContainsString('SafeDescriptionHelper::render(', $volume);
    }
}
