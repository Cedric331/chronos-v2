<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use ICal\ICal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Yasumi\Yasumi;

class CalendarController extends Controller
{
    public function getPlanning(Request $request): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        $user = User::find(Auth::id());
        $team = Team::find($user->team_id);

        $roleNames = ['Conseiller'];
        if ($team->params->send_coordinateur) {
            $roleNames[] = 'Coordinateur';
        }

        $users = User::where(function ($query) use ($roleNames) {
            $query->whereHas('roles', function ($roleQuery) use ($roleNames) {
                $roleQuery->whereIn('name', $roleNames);
            });
        })
        ->where('team_id', $team->id)
        ->get();

        $users = $users->reject(function ($item) use ($team) {
            return $item->isCoordinateur() && $team->user_id !== $item->id;
        });

        if (!$users->contains('id', $user->id)) {
            $user = $users->first();
        }

        $monday = Carbon::now()->startOfWeek();

        $calendar = Calendar::whereHas('plannings', function ($query) use ($user, $team) {
            $query->where('user_id', $user->id)
                ->where('team_id', $team->id);
        })
            ->with(['plannings' => function ($query) use ($user) {
                $query->with('eventPlannings')->where('user_id', $user->id);
            }])
            ->where('date', '>=', $monday)
            ->get();

        $weeklyHours = [];
        foreach ($calendar as $day) {
            $day->number_week = Carbon::parse($day->date)->isoFormat('W').Carbon::parse($day->date)->isoFormat('Y');
            $weekNumber = $day->number_week;

            if (isset($day->plannings[0]->hours) && $day->plannings[0]->hours !== null) {
                $hoursArray = explode('h', $day->plannings[0]->hours);
                $decimalHours = intval($hoursArray[0]) + intval($hoursArray[1]) / 60;

                if (array_key_exists($weekNumber, $weeklyHours)) {
                    $weeklyHours[$weekNumber] += $decimalHours;
                } else {
                    $weeklyHours[$weekNumber] = $decimalHours;
                }
            }
        }

        foreach ($weeklyHours as $weekNumber => $decimalHours) {
            $weeklyHours[$weekNumber] = sprintf('%02d', intval($decimalHours)) . 'h' . sprintf('%02d', ($decimalHours - intval($decimalHours)) * 60);
        }

        if ($request->header('User-Agent') === 'chronos-mobile') {
            return response()->json(['calendar' => $calendar, 'weeklyHours' => $weeklyHours]);
        }

        return Inertia::render('Planning', [
            'user' => $user,
            'users' => $users,
            'isToday' => ucwords(Carbon::now()->isoFormat('dddd D MMMM')),
            'calendar' => $calendar,
            'weeklyHours' => $weeklyHours,
        ]);
    }

    public function getPlanningCustom(Request $request): \Illuminate\Http\JsonResponse
    {
        $monday = Carbon::now()->startOfWeek();
        $user = User::findOrFail($request->user['id']);

        $calendar = Calendar::whereHas('plannings', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('team_id', $user->team_id);

        })
            ->with(['plannings' => function ($query) use ($user) {
                $query->with('eventPlannings')->where('user_id', $user->id);
            }])
            ->where('date', '>=', $monday)
            ->get();

        $weeklyHours = [];
        foreach ($calendar as $day) {
            $day->number_week = Carbon::parse($day->date)->isoFormat('W').Carbon::parse($day->date)->isoFormat('Y');
            $weekNumber = $day->number_week;

            if (isset($day->plannings[0]->hours) && $day->plannings[0]->hours !== null) {
                $hoursArray = explode('h', $day->plannings[0]->hours);
                $decimalHours = intval($hoursArray[0]) + intval($hoursArray[1]) / 60;

                if (array_key_exists($weekNumber, $weeklyHours)) {
                    $weeklyHours[$weekNumber] += $decimalHours;
                } else {
                    $weeklyHours[$weekNumber] = $decimalHours;
                }
            }
        }

        foreach ($weeklyHours as $weekNumber => $decimalHours) {
            $weeklyHours[$weekNumber] = sprintf('%02d', intval($decimalHours)) . 'h' . sprintf('%02d', ($decimalHours - intval($decimalHours)) * 60);
        }

        return response()->json(['calendar' => $calendar, 'weeklyHours' => $weeklyHours]);
    }


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

            [$isSchoolHoliday, $schoolHolidayZones] = self::isSchoolHoliday($currentDateString, $holidaysByZone);

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

        return [! empty($zonesInHolidays), $zonesInHolidays];
    }

    private static function getSchoolHolidays() {
        $url = 'https://fr.ftp.opendatasoft.com/openscol/fr-en-calendrier-scolaire/Zone-A-B-C-Corse.ics';

        // Utilisez la nouvelle fonction pour récupérer le contenu
        $content = self::fetchUrlContent($url);

        // Créez l'objet ICal avec le contenu récupéré
        $ical = new ICal($content, [
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

            if (! empty($zones)) {
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

    private static function fetchUrlContent($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Erreur cURL : ' . curl_error($ch));
        }
        curl_close($ch);
        return $data;
    }

}
