<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CalendarService;
use App\Services\HolidayService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function __construct(
        private CalendarService $calendarService,
        private HolidayService $holidayService
    ) {}

    public function getPlanning(Request $request): \Inertia\Response
    {
        $user = User::with('team')->find(Auth::id());
        $team = $user->team;

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

        $selectedUser = $user;
        if (count($users) > 0 && ! $users->contains('id', $user->id)) {
            $selectedUser = $users->first();
        }

        $planningData = $this->calendarService->getPlanningForUser($user, $selectedUser);

        // Get user's planning widget preferences
        $planningWidgetsPrefs = [];
        if ($selectedUser->preference && $selectedUser->preference->planning_widgets) {
            $planningWidgetsPrefs = $selectedUser->preference->planning_widgets;
        }

        return Inertia::render('Planning', [
            'user' => $selectedUser,
            'users' => $users,
            'isToday' => ucwords(Carbon::now()->isoFormat('dddd D MMMM')),
            'calendar' => $planningData['calendar'],
            'weeklyHours' => $planningData['weeklyHours'],
            'planningWidgetsPrefs' => $planningWidgetsPrefs,
        ]);
    }

    public function getPlanningCustom(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = User::findOrFail($request->user['id']);
        $getAllPlanning = $request->getAllPlanning ?? false;

        $planningData = $this->calendarService->getPlanningCustom($user, $getAllPlanning);

        return response()->json([
            'calendar' => $planningData['calendar'],
            'weeklyHours' => $planningData['weeklyHours'],
        ]);
    }

    public static function generateDaysWithHolidays($dateDebut = null, $dateFin = null): array
    {
        $holidayService = app(HolidayService::class);

        $dateDebut = $dateDebut ? CarbonImmutable::parse($dateDebut) : null;
        $dateFin = $dateFin ? CarbonImmutable::parse($dateFin) : null;

        return $holidayService->generateDaysWithHolidays($dateDebut, $dateFin);
    }
}
