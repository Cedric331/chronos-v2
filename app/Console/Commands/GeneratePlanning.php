<?php

namespace App\Console\Commands;

use App\Http\Controllers\CalendarController;
use App\Models\Calendar;
use App\Models\Planning;
use Carbon\Carbon; // Assurez-vous d'importer le modèle Planning
use Illuminate\Console\Command;

class GeneratePlanning extends Command
{
    protected $signature = 'planning:generate';

    protected $description = 'Génère un planning pour 1 an et 1 mois';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $dateDebut = Carbon::now()->startOfWeek();
        $dateFin = $dateDebut->copy()->addYear()->addMonth()->endOfWeek();

        $days = CalendarController::generateDaysWithHolidays($dateDebut, $dateFin);

        foreach ($days as $day) {

            $existingPlanningEntry = Calendar::where('date', $day['date'])->first();

            if (! $existingPlanningEntry) {

                $zonesString = null;
                if ($day['vacance'][0]) {
                    $zonesString = implode(',', $day['vacance'][1]);
                }

                $newPlanningEntry = new Calendar();
                $newPlanningEntry->date = $day['date'];
                $newPlanningEntry->is_holiday = $day['jour_ferie'][0];
                $newPlanningEntry->name_holiday = $day['jour_ferie'][1];
                $newPlanningEntry->zone = $zonesString;
                $newPlanningEntry->save();
            }
        }
    }
}
