<?php

namespace App\Console;

use App\Console\Commands\GeneratePlanning;
use App\Console\Commands\CheckAlertModule;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run GeneratePlanning once per day at midnight
        $schedule->command(GeneratePlanning::class)->dailyAt('00:00');

        // Run CheckAlertModule once per hour
        $schedule->command(CheckAlertModule::class)->hourly();
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
