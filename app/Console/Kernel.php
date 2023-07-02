<?php

namespace App\Console;

use App\Console\Commands\GeneratePlanning;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command(GeneratePlanning::class)->weeklyOn(0, '23:00');
        $schedule->call('App\Http\Controllers\TeamScheduleController@checkHoraire')->weeklyOn(1, '1:00');

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
