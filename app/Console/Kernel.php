<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sitemap:generate')
            ->dailyAt('03:00')
            ->timezone('Europe/Moscow')
            ->sendOutputTo(storage_path('logs/sitemap.log'));

        $schedule->command('publication:sync')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command('backup:run')
            ->dailyAt('02:30')
            ->timezone('Europe/Moscow')
            ->withoutOverlapping();

        $schedule->command('backup:clean')
            ->dailyAt('04:00')
            ->timezone('Europe/Moscow')
            ->withoutOverlapping();

        $schedule->command('backup:monitor')
            ->dailyAt('05:00')
            ->timezone('Europe/Moscow')
            ->withoutOverlapping();
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
