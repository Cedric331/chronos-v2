<?php

namespace App\Http\Controllers;

use Carbon\Traits\Date;
use ICal\ICal;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Inertia\Inertia;
use Yasumi\Yasumi;

class CalendarController extends Controller
{

    /**
     * @param $dateDebut
     * @param $dateFin
     * @return \Inertia\Response
     */
    function generateDaysWithHolidays($dateDebut = null, $dateFin = null): \Inertia\Response
    {
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
        $holidays = $this->getFrenchHolidays($year);
        $holidaysByZone = $this->getSchoolHolidays($year);

        $currentDate = $dateDebut;
        while ($currentDate <= $dateFin) {
            $formattedDate = ucwords($currentDate->isoFormat('dddd D MMMM'));
            $currentDateString = $currentDate->toDateString();

            $isHoliday = isset($holidays[$currentDateString]);
            $holidayName = $isHoliday ? $holidays[$currentDateString] : '';

            list($isSchoolHoliday, $schoolHolidayZones) = $this->isSchoolHoliday($currentDateString, $holidaysByZone);

            $days[] = [
                'date' => $formattedDate,
                'jour_ferie' => [$isHoliday, $holidayName],
                'vacance' => [$isSchoolHoliday, $schoolHolidayZones],
            ];

            $currentDate = $currentDate->addDay();
        }


        return Inertia::render('Dashboard', [
            'days' => $days
        ]);
    }

    private function getFrenchHolidays($year): array
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

    private function isSchoolHoliday($date, $holidaysByZone)
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


    private function getSchoolHolidays($year)
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
                        'end' => date('Y-m-d', $endDate),
                    ];
                }
            }
        }

        return $holidaysByZone;
    }
}
