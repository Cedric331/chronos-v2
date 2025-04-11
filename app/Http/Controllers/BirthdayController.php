<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BirthdayController extends Controller
{
    /**
     * Récupère les anniversaires à venir
     */
    public function getBirthdays(Request $request)
    {
        $request->validate([
            'daysAhead' => 'nullable|integer|min:1|max:365',
            'teamOnly' => 'nullable|boolean',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $user = Auth::user();
        $daysAhead = $request->daysAhead ?? 365; // Par défaut, toute l'année
        $teamOnly = $request->teamOnly ?? false; // Par défaut, tous les utilisateurs
        $limit = $request->limit ?? 10; // Par défaut, limiter à 10 utilisateurs

        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addDays($daysAhead)->endOfDay();

        // Récupérer les anniversaires
        $birthdays = [];

        // Récupérer les utilisateurs (de l'équipe ou tous)
        $usersQuery = User::where('account_active', true);

        if ($teamOnly) {
            $usersQuery->where('team_id', $user->team_id);
        }

        // Inclure la relation avec l'équipe pour récupérer le nom du HUB
        $usersQuery->with('team');

        $users = $usersQuery->get();

        // Pour chaque utilisateur, vérifier si son anniversaire tombe dans la période
        foreach ($users as $member) {
            // Si la date de naissance est définie
            if ($member->birthday) {
                $birthDate = Carbon::parse($member->birthday);
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
                    // Calculer l'âge
                    $age = $birthDate->diffInYears(Carbon::now());
                    if ($birthDateThisYear->year > Carbon::now()->year) {
                        $age++; // Ajouter 1 an si l'anniversaire est l'année prochaine
                    }

                    $birthdays[] = [
                        'id' => 'birthday_' . $member->id,
                        'name' => $member->name,
                        'date' => $birthDateThisYear->toDateString(),
                        'age' => $age,
                        'team_id' => $member->team_id,
                        'team_name' => $member->team ? $member->team->name : 'Sans HUB',
                        'is_team_member' => $member->team_id == $user->team_id
                    ];
                }
            }
        }

        // Pas de données de démonstration, uniquement les vraies données

        // Trier les anniversaires par date
        usort($birthdays, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        // Ajouter une information indiquant si l'utilisateur est dans la même équipe
        foreach ($birthdays as &$birthday) {
            if (!isset($birthday['is_team_member'])) {
                // Vérifier si team_id existe avant de l'utiliser
                if (isset($birthday['team_id'])) {
                    $birthday['is_team_member'] = $birthday['team_id'] == $user->team_id;
                } else {
                    // Si team_id n'existe pas, définir is_team_member à false par défaut
                    $birthday['is_team_member'] = false;
                    $birthday['team_id'] = null; // Ajouter team_id pour éviter d'autres erreurs
                }
            }
        }

        // Limiter le nombre d'anniversaires retournés
        $birthdays = array_slice($birthdays, 0, $limit);

        return response()->json($birthdays);
    }
}
