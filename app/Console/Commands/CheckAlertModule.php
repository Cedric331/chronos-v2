<?php

namespace App\Console\Commands;

use App\Models\AlertSchedule;
use App\Models\Planning;
use App\Models\Team;
use App\Models\TeamSchedule;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckAlertModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-alert-module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les alertes des horaires programmés';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $teams = Team::whereHas('params', function ($query) {
            $query->where('module_alert', true);
        })->get();

        $date = Carbon::parse(now());

        $startOfWeek = $date->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $date->copy()->endOfWeek(Carbon::SUNDAY)->addWeek();

        foreach ($teams as $team) {
            $requiredSchedules = TeamSchedule::where('team_id', $team->id)->get();

            for ($date = $startOfWeek; $date->lessThanOrEqualTo($endOfWeek); $date->addDay()) {
                $startOfDay = Carbon::createFromTime(8, 0); // Assuming your day starts at 8 AM
                $endOfDay = Carbon::createFromTime(21, 0); // And ends at 9 PM

                for ($time = $startOfDay; $time->lessThan($endOfDay); $time->addMinutes(30)) {

                    $timeSlot = $time->format('H:i:s').' - '.$time->copy()->addMinutes(30)->format('H:i:s');

                    $timeSlot1 = $time->format('H:i:s');
                    $timeSlot2 = $time->copy()->addMinutes(30)->format('H:i:s');

                    $day = ucfirst($date->locale('fr_FR')->isoFormat('dddd'));

                    $requiredSchedule = $requiredSchedules->where('day', $day)->first();

                    //                    $requiredSchedule = $requiredSchedules->where('day', $day)
//                        ->filter(function ($schedule) use ($timeSlot1, $timeSlot2) {
//                            $scheduleTime = $schedule['time'];
//                            return Str::contains($scheduleTime, $timeSlot1) || Str::contains($scheduleTime, $timeSlot2);
//                        })
//                        ->first();
                    Log::info('Day : '.$day);

                    if ($requiredSchedule !== null) {
                        Log::info('$requiredSchedule : '.$timeSlot1 . ' - ' . $timeSlot2 );
                    }

                    if ($requiredSchedule !== null && $requiredSchedule->value > 0) {
                        Log::info("Checking $requiredSchedule->value on ".$date->isoFormat('dddd D MMMM YYYY'));
                        $realCount = Planning::whereHas('calendar', function ($query) use ($date) {
                            $query->where('date', $date->format('Y-m-d'));
                        })
                            ->where(function ($query) use ($time) {
                                $query->where(function ($query) use ($time) {
                                    $query->where(DB::raw('TIME(debut_journee)'), '<=', $time->format('H:i:s'))
                                        ->where(DB::raw('TIME(fin_journee)'), '>=', $time->copy()->addMinutes(30)->format('H:i:s'));
                                })
                                    ->orWhere(function ($query) use ($time) {
                                        $query->where(DB::raw('TIME(debut_pause)'), '<=', $time->format('H:i:s'))
                                            ->where(DB::raw('TIME(fin_pause)'), '>=', $time->copy()->addMinutes(30)->format('H:i:s'));
                                    });
                            })
                            ->where('team_id', $team->id)
                            ->count();

                        Log::info("Found $realCount on ".$date->isoFormat('dddd D MMMM YYYY'));
                        if ($realCount < $requiredSchedule->value) {

                                $required = $requiredSchedule->value > 1 ? "{$requiredSchedule->value} sont nécessaires" : "{$requiredSchedule->value} est nécessaire";
                                $message = "Le créneau $requiredSchedule->time du ".$date->isoFormat('dddd D MMMM YYYY')." n'est pas couvert, alors que $required.\n";

                            AlertSchedule::firstOrCreate([
                                'team_id' => $team->id,
                                'time' => $requiredSchedule->time,
                                'date' => $date->format('Y-m-d'),
                                'message' => $message,
                                'is_read' => false,
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function formatTime($timeString): string
    {
        $times = explode(' - ', $timeString);

        $start = DateTime::createFromFormat('H:i:s', $times[0]);
        $end = DateTime::createFromFormat('H:i:s', $times[1]);

        return $start->format('G\hi').' - '.$end->format('G\hi');
    }
}
