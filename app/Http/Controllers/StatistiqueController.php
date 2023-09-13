<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Calendar;
use Illuminate\Http\Request;

class StatistiqueController extends Controller
{
    public function getStatistics(Request $request)
    {
        $userId = $request->input('user_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $calendars = Calendar::whereBetween('date', [$startDate, $endDate])->get();
        
        $calendarIds = $calendars->pluck('id')->toArray();

        $plannings = Planning::where('user_id', $userId)
        ->whereIn('calendar_id', $calendarIds)
        ->get();

        $types = [
            'Planifié',
            'Récup JF',
            'Congés Payés',
            'Jour Férié',
            'Maladie',
            'Repos',
            'Formation',
        ];

        $statistics = [];
        foreach ($types as $type) {
            $statistics[$type] = $plannings->where('type_day', $type)->count();
        }

        return response()->json($statistics);
    }
}
