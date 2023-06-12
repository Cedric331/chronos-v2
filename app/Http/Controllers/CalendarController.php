<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\User;
use Carbon\Traits\Date;
use DateTime;
use ICal\ICal;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use IntlDateFormatter;
use Yasumi\Yasumi;

class CalendarController extends Controller
{

    /**
     * @return \Inertia\Response
     */
    public function getPlanning(): \Inertia\Response
    {
        $user = User::find(Auth::id());
        $users = User::where('team_id', $user->team_id)->get();

        $monday = Carbon::now()->startOfWeek();

        $calendar = Calendar::whereHas('plannings', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['plannings' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->where('date', '>=', $monday)
            ->get();

        foreach ($calendar as $day) {
            $day->number_week = Carbon::parse($day->date)->isoFormat('W');
            $day->date = $day->dateFr;
        }

        return Inertia::render('Planning', [
            'user' => $user,
            'users' => $users,
            'isToday' => ucwords(Carbon::now()->isoFormat('dddd D MMMM')),
            'calendar' => $calendar
        ]);
    }

    public function getPlanningCustom (Request $request): \Illuminate\Http\JsonResponse
    {
        $monday = Carbon::now()->startOfWeek();

        $calendar = Calendar::whereHas('plannings', function ($query) use ($request, $monday) {
                $query->where('user_id', $request->user['id']);
            })
            ->with(['plannings' => function ($query) use ($request, $monday) {
                $query->where('user_id', $request->user['id']);
            }])
            ->where('date', '>=', $monday)
            ->get();

        foreach ($calendar as $day) {
            $day->number_week = Carbon::parse($day->date)->isoFormat('W');
            $day->date = $day->dateFr;
        }

        return response()->json($calendar);
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

    private static function isSchoolHoliday($date, $holidaysByZone): array
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
