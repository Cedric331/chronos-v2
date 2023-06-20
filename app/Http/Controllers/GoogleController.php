<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    protected string $provider = "google";


    /**
     * Redirige l'utilisateur vers l'authentification Google.
     *
     */
    public function redirectToGoogle()
    {
        return Socialite::driver($this->provider)->scopes(['https://www.googleapis.com/auth/calendar', 'https://www.googleapis.com/auth/calendar.events'])->redirect();
    }

    /**
     * Obtient l'utilisateur de Google et le connecte à l'application.
     *
     */
    public function handleGoogleCallback(): \Inertia\Response
    {
        $user = Socialite::driver($this->provider)->user();

        $this->createGoogleCalendarEvent($user->token);

        return Inertia::render('Agenda/CongratulationAgenda');
    }

    public function createGoogleCalendarEvent($accessToken)
    {
        $client = new \Google_Client();
        $client->setApplicationName('Google Calendar API PHP Quickstart');
        $client->setScopes([
            'https://www.googleapis.com/auth/calendar',
            'https://www.googleapis.com/auth/calendar.events',
        ]);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));

        $client->setAccessToken($accessToken);

        $service = new \Google_Service_Calendar($client);

        $calendarName = 'Chronos';
        $calendarId = null;

        $calendarList = $service->calendarList->listCalendarList();

        foreach ($calendarList->getItems() as $calendarListItem) {
            if ($calendarListItem->getSummary() == $calendarName) {
                $calendarId = $calendarListItem->getId();
                break;
            }
        }

        if ($calendarId) {
            $service->calendars->delete($calendarId);
        }

        $newCalendar = new Google_Service_Calendar_Calendar();
        $newCalendar->setSummary($calendarName);

        $createdCalendar = $service->calendars->insert($newCalendar);
        $calendarId = $createdCalendar->getId();

        $plannings = $this->getPlanning();

        foreach ($plannings as $calendar) {
            foreach ($calendar->plannings as $planning) {
                $startTime = null;
                $endTime = null;
                // Convertir les temps de début et de fin en format de date et heure ISO
                if ($planning->debut_journee && $planning->fin_journee) {
                    $startTime = $calendar->date . 'T' . str_replace('h', ':', $planning->debut_journee) . ':00+02:00';
                    $endTime = $calendar->date . 'T' . str_replace('h', ':', $planning->fin_journee) . ':00+02:00';
                }


                if ($startTime && $endTime) {
                    $this->insertEventWork($calendarId, $service, $startTime, $endTime, $planning);
                } else {
                    $this->insertEventRest($calendarId, $service, $calendar->date, $planning);

                }
            }
        }
    }

    private function insertEventRest($calendarId, $service, $date, $planning)
    {

            $event = new \Google_Service_Calendar_Event([
                'summary' => $planning->type_day,
                'description' => 'Import de planning depuis Chronos',
                'start' => [
                    'date' => $date,
                    'timeZone' => 'Europe/Paris',
                ],
                'end' => [
                    'date' => $date,
                    'timeZone' => 'Europe/Paris',
                ],
            ]);

            $event = $service->events->insert($calendarId, $event);

    }


    private function insertEventWork ($calendarId, $service, $startTime, $endTime, $planning)
    {

            // Créer un nouvel événement
            $event = new \Google_Service_Calendar_Event([
                'summary' => $planning->type_day,
                'location' => $planning->telework ? 'Télétravail' : 'Hub',
                'description' => 'Import de planning depuis Chronos',
                'start' => [
                    'dateTime' => $startTime,
                    'timeZone' => 'Europe/Paris',
                ],
                'end' => [
                    'dateTime' => $endTime,
                    'timeZone' => 'Europe/Paris',
                ],
            ]);

            // Ajouter l'événement au calendrier principal de l'utilisateur
            $event = $service->events->insert($calendarId, $event);

    }

    private function getPlanning ()
    {
        $user = User::find(Auth::id());

        $monday = Carbon::now()->startOfWeek();

        $calendar = Calendar::whereHas('plannings', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['plannings' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->where('date', '>=', $monday)
            ->limit(63)
            ->get();

        return $calendar;
    }



}
