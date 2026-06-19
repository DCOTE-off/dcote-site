<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // completed — источник истины для доступности серии; синхронизируем его с appear_in каждую минуту.
        $schedule->command('anime:release-due-episodes')
                ->everyMinute()
                ->withoutOverlapping();

        $schedule->command('sitemap:generate')
                ->dailyAt('03:00')
                ->sendOutputTo(storage_path('logs/sitemap.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
