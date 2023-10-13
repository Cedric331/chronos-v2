<?php

namespace App\Console;

use App\Console\Commands\GeneratePlanning;
use App\Console\Commands\CheckAlertModule;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function scheduleRunsHourly(Schedule $schedule)
    {
        foreach ($schedule->events() as $event) {
            $event->expression = substr_replace($event->expression, '*', 0, 1);
        }
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(GeneratePlanning::class)->everyMinute();
        $this->scheduleRunsHourly($schedule);
        $schedule->command(CheckAlertModule::class)->everyMinute();
        $this->scheduleRunsHourly($schedule);
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
