<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use Illuminate\Http\Request;

class StatistiqueController extends Controller
{
    public function getStatistics(Request $request)
    {
        $userId = $request->input('user_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $plannings = Planning::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
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
