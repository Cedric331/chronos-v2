<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use ICal\ICal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Yasumi\Yasumi;
use Illuminate\Support\Facades\Http;

class CalendarController extends Controller
{
    public function getPlanning(): \Inertia\Response
    {
        $user = User::find(Auth::id());
        $users = User::where('team_id', $user->team_id)->get();

        $monday = Carbon::now()->startOfWeek();

        $calendar = Calendar::whereHas('plannings', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('team_id', $user->team_id);
        })
            ->with(['plannings' => function ($query) use ($user) {
                $query->with('eventPlannings')->where('user_id', $user->id);
            }])
            ->where('date', '>=', $monday)
            ->get();

        foreach ($calendar as $day) {
            $day->number_week = Carbon::parse($day->date)->isoFormat('W');
        }

        return Inertia::render('Planning', [
            'user' => $user,
            'users' => $users,
            'isToday' => ucwords(Carbon::now()->isoFormat('dddd D MMMM')),
            'calendar' => $calendar,
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

        foreach ($calendar as $day) {
            $day->number_week = Carbon::parse($day->date)->isoFormat('W');
        }

        return response()->json($calendar);
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


    private static function getSchoolHolidays($year)
    {
        $url = 'http://fr.ftp.opendatasoft.com/openscol/fr-en-calendrier-scolaire/Zone-A-B-C-Corse.ics';

        $response = Http::get($url);
        dd($response->collect());
        // Vérifier si la requête a réussi
        if ($response->failed()) {
            // Gérer l'erreur, peut-être lever une exception ou retourner une valeur par défaut
            throw new \Exception('Failed to fetch the calendar.');
        }

        $icsContent = $response->body();

        $ical = new ICal($icsContent, [
            'defaultTimeZone' => 'Europe/Paris',
        ]);

        $holidaysByZone = [
            'A' => [],
            'B' => [],
            'C' => [],
            // 'Corse' => [],
        ];

        // (Le reste de votre code reste inchangé ...)

        return $holidaysByZone;
    }

}
