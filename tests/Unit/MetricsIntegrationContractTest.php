<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MetricsIntegrationContractTest extends TestCase
{
    public function test_site_uses_dedicated_video_and_metrics_origins(): void
    {
        $root = dirname(__DIR__, 2);
        $layout = file_get_contents($root.'/resources/views/app.blade.php');
        $services = file_get_contents($root.'/config/services.php');

        $this->assertStringContainsString('src="{{ $metricsBaseUrl }}/site-presence-tracker.js"', $layout);
        $this->assertStringContainsString('contractVersion: 1', $layout);
        $this->assertStringContainsString("env('DCOTE_VIDEO_BASE_URL', 'https://video.dcote.net')", $services);
        $this->assertStringContainsString("env('DCOTE_METRICS_BASE_URL', 'https://metrics-api.dcote.net')", $services);
        $this->assertFileDoesNotExist($root.'/public/js/site-presence-tracker.js');
    }
}
