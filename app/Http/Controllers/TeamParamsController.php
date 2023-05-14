<?php

namespace App\Http\Controllers;

use App\Models\TeamParams;
use Illuminate\Http\Request;

class TeamParamsController extends Controller
{

    /**
     * @param Request $request
     * @param TeamParams $teamParams
     * @return \Illuminate\Http\JsonResponse
     */
    public function update (Request $request, TeamParams $teamParams): \Illuminate\Http\JsonResponse
    {
        $teamParams->update([
            'type_day' => json_encode($request->type_day),
            'update_planning' => $request->update_planning,
        ]);
        $teamParams->save();

        return response()->json($teamParams);
    }
}
