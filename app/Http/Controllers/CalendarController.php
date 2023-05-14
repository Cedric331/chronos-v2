<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Carbon\Traits\Date;
use ICal\ICal;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Inertia\Inertia;
use Yasumi\Yasumi;

class CalendarController extends Controller
{

    public function showGeneratedDaysWithHolidays($getAllDates = false)
    {
        if ($getAllDates) {
            // Récupérez toutes les dates
            $days = Calendar::all();
        } else {
            // Récupérez le lundi de la semaine en cours
            $startOfWeek = Carbon::now()->startOfWeek();

            // Récupérez les jours à partir du lundi de la semaine en cours
            $days = Calendar::where('date', '>=', $startOfWeek)->get();
        }

        return Inertia::render('Dashboard', [
            'days' => $days
        ]);
    }

    /**
     * @param $dateDebut
     * @param $dateFin
     */
    public static function generateDaysWithHolidays($dateDebut = null, $dateFin = null): array
    {
        date_default_timezone_set('Europe/Paris');
        $year = date('Y');
        if ($dateDebut === null) {
            $dateDebut = CarbonImmutable::parse("{$year}-01-01");
        } else {
            $dateDebut = CarbonImmutable::parse($dateDebut);
        }

        if ($dateFin === null) {
            $dateFin = CarbonImmutable::parse("{$year}-12-31");
        } else {
            $dateFin = CarbonImmutable::parse($dateFin);
        }

        $days = [];
        $holidays = self::getFrenchHolidays($year);
        $holidaysByZone = self::getSchoolHolidays($year);

        $currentDate = $dateDebut;
        while ($currentDate <= $dateFin) {
            $formattedDate = $currentDate->format('Y-m-d');
            $currentDateString = $currentDate->toDateString();

            $isHoliday = isset($holidays[$currentDateString]);
            $holidayName = $isHoliday ? $holidays[$currentDateString] : '';

            list($isSchoolHoliday, $schoolHolidayZones) = self::isSchoolHoliday($currentDateString, $holidaysByZone);

            $days[] = [
                'date' => $formattedDate,
                'jour_ferie' => [$isHoliday, $holidayName],
                'vacance' => [$isSchoolHoliday, $schoolHolidayZones],
            ];

            $currentDate = $currentDate->addDay();
        }

        return $days;
    }

    private static function getFrenchHolidays($year): array
    {
        $holidays = Yasumi::create('France', $year, 'fr_FR');

        $holidayDates = [];

        foreach ($holidays->getHolidays() as $holiday) {
            $date = $holiday->format('Y-m-d');
            $name = $holiday->getName();
            $holidayDates[$date] = $name;
        }

        return $holidayDates;
    }

    private static function isSchoolHoliday($date, $holidaysByZone)
    {
        $zonesInHolidays = [];

        foreach ($holidaysByZone as $zone => $holidays) {
            foreach ($holidays as $holiday) {
                if ($date >= $holiday['start'] && $date <= $holiday['end']) {
                    $zonesInHolidays[] = $zone;
                }
            }
        }

        return [!empty($zonesInHolidays), $zonesInHolidays];
    }


    private static function getSchoolHolidays($year)
    {
        $url = 'https://fr.ftp.opendatasoft.com/openscol/fr-en-calendrier-scolaire/Zone-A-B-C-Corse.ics';

        $ical = new ICal($url, [
            'defaultTimeZone' => 'Europe/Paris',
        ]);

        $holidaysByZone = [
            'A' => [],
            'B' => [],
            'C' => [],
//            'Corse' => [],
        ];

        foreach ($ical->events() as $event) {
            $startDate = $event->dtstart_array[2];
            $endDate = $event->dtend_array[2];


            $zones = [];

            if (strpos($event->summary, 'A') !== false) {
                $zones[] = 'A';
            }
            if (strpos($event->summary, 'B') !== false) {
                $zones[] = 'B';
            }
            if (strpos($event->summary, 'C') !== false) {
                $zones[] = 'C';
            }
//            if (strpos($event->summary, 'Corse') !== false) {
//                $zones[] = 'Corse';
//            }

            if (!empty($zones)) {
                foreach ($zones as $zone) {
                    $holidaysByZone[$zone][] = [
                        'name' => $event->summary,
                        'start' => date('Y-m-d', $startDate),
                        'end' => date('Y-m-d', strtotime('-1 day', $endDate)),
                    ];
                }
            }
        }

        return $holidaysByZone;
    }
}
