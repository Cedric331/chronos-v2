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
        AlertSchedule::truncate();

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

                for ($time = $startOfDay; $time->lessThan($endOfDay); $time->addHour()) {
                    $timeSlot = $time->format('H:i:s').' - '.$time->copy()->addHour()->format('H:i:s');
                    $day = ucfirst($date->locale('fr')->isoFormat('dddd'));

                    $requiredSchedule = $requiredSchedules->where('day', $day)
                        ->where('time', $timeSlot)->first();

                    if ($requiredSchedule !== null && $requiredSchedule->value > 0) {
                        $realCount = Planning::whereHas('calendar', function ($query) use ($date) {
                            $query->where('date', $date->format('Y-m-d'));
                        })
                            ->where(DB::raw('TIME(debut_journee)'), '<=', $time->format('H:i:s'))
                            ->where(DB::raw('TIME(fin_journee)'), '>=', $time->copy()->addHour()->format('H:i:s'))
                            ->count();

                        if ($realCount < $requiredSchedule->value) {
                            $timeSlot = $this->formatTime($timeSlot);
                            if ($realCount == 0) {
                                $required = $requiredSchedule->value > 1 ? "{$requiredSchedule->value} sont nécessaires" : "{$requiredSchedule->value} est nécessaire";
                                $message = "Aucun conseiller n'est prévu pour le créneau $timeSlot le ".$date->isoFormat('dddd D MMMM YYYY').", alors que $required.\n";
                            } elseif ($realCount == 1) {
                                $required = $requiredSchedule->value > 1 ? "{$requiredSchedule->value} sont nécessaires" : "{$requiredSchedule->value} est nécessaire";
                                $message = "Seulement 1 conseiller est prévu pour le créneau $timeSlot le ".$date->isoFormat('dddd D MMMM YYYY').", alors que $required.\n";
                            } else {
                                $required = $requiredSchedule->value > 1 ? "{$requiredSchedule->value} sont nécessaires" : "{$requiredSchedule->value} est nécessaire";
                                $message = "Seulement $realCount conseillers sont prévus pour le créneau $timeSlot le ".$date->isoFormat('dddd D MMMM YYYY').", alors que $required.\n";
                            }

                            AlertSchedule::create([
                                'team_id' => $team->id,
                                'time' => $timeSlot,
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
