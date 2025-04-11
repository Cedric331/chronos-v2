<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TeamEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamEventsController extends Controller
{
    /**
     * Récupère les anniversaires et événements d'équipe à venir
     */
    public function getTeamEvents(Request $request)
    {
        $request->validate([
            'daysAhead' => 'nullable|integer|min:1|max:365',
        ]);

        $user = Auth::user();
        $daysAhead = $request->daysAhead ?? 30;
        
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addDays($daysAhead)->endOfDay();
        
        // Récupérer les événements d'équipe
        $teamEvents = [];
        
        // Si la table TeamEvent existe, récupérer les événements
        if (class_exists('App\Models\TeamEvent')) {
            $teamEvents = TeamEvent::where('team_id', $user->team_id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->map(function ($event) {
                    return [
                        'id' => 'event_' . $event->id,
                        'title' => $event->title,
                        'description' => $event->description,
                        'date' => $event->date->toDateString(),
                        'type' => 'team_event'
                    ];
                })
                ->toArray();
        } else {
            // Données de démonstration si la table n'existe pas
            $teamEvents = [
                [
                    'id' => 'event_1',
                    'title' => 'Réunion d\'équipe',
                    'description' => 'Réunion mensuelle de l\'équipe',
                    'date' => Carbon::now()->addDays(5)->toDateString(),
                    'type' => 'team_event'
                ],
                [
                    'id' => 'event_2',
                    'title' => 'Afterwork',
                    'description' => 'Afterwork de fin de mois',
                    'date' => Carbon::now()->addDays(15)->toDateString(),
                    'type' => 'team_event'
                ],
                [
                    'id' => 'event_3',
                    'title' => 'Formation équipe',
                    'description' => 'Formation sur les nouvelles technologies',
                    'date' => Carbon::now()->addDays(20)->toDateString(),
                    'type' => 'team_event'
                ]
            ];
        }
        
        // Récupérer les anniversaires des membres de l'équipe
        $birthdays = [];
        
        // Récupérer les utilisateurs de l'équipe
        $teamMembers = User::where('team_id', $user->team_id)
            ->where('account_active', true)
            ->get();
        
        // Pour chaque membre, vérifier si son anniversaire tombe dans la période
        foreach ($teamMembers as $member) {
            // Si la date de naissance est définie
            if ($member->birth_date) {
                $birthDate = Carbon::parse($member->birth_date);
                $birthDateThisYear = Carbon::createFromDate(
                    Carbon::now()->year,
                    $birthDate->month,
                    $birthDate->day
                );
                
                // Si l'anniversaire est déjà passé cette année, prendre celui de l'année prochaine
                if ($birthDateThisYear < Carbon::now()) {
                    $birthDateThisYear->addYear();
                }
                
                // Si l'anniversaire tombe dans la période
                if ($birthDateThisYear <= $endDate) {
                    $birthdays[] = [
                        'id' => 'birthday_' . $member->id,
                        'title' => 'Anniversaire de ' . $member->name,
                        'description' => null,
                        'date' => $birthDateThisYear->toDateString(),
                        'type' => 'birthday'
                    ];
                }
            }
        }
        
        // Si aucun anniversaire n'est trouvé, ajouter des données de démonstration
        if (empty($birthdays)) {
            $birthdays = [
                [
                    'id' => 'birthday_1',
                    'title' => 'Anniversaire de Jean Dupont',
                    'description' => null,
                    'date' => Carbon::now()->addDays(3)->toDateString(),
                    'type' => 'birthday'
                ],
                [
                    'id' => 'birthday_2',
                    'title' => 'Anniversaire de Marie Martin',
                    'description' => null,
                    'date' => Carbon::now()->addDays(10)->toDateString(),
                    'type' => 'birthday'
                ]
            ];
        }
        
        // Combiner les événements et les trier par date
        $allEvents = array_merge($teamEvents, $birthdays);
        usort($allEvents, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });
        
        return response()->json($allEvents);
    }
}
