<?php

namespace App\Console;

use App\Console\Commands\GeneratePlanning;
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
//        $schedule->command(GeneratePlanning::class)->weeklyOn(1, '2:00');
//        $schedule->call('App\Http\Controllers\TeamScheduleController@checkHoraire')->weeklyOn(1, '3:00');
        $schedule->command(GeneratePlanning::class)->everyMinute();
        $this->scheduleRunsHourly($schedule);
        $schedule->call('App\Http\Controllers\TeamScheduleController@checkHoraire')->everyMinute();
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
