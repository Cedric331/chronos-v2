<?php

namespace App\Http\Controllers;

use App\Models\TeamParams;
use Illuminate\Http\Request;

class TeamParamsController extends Controller
{
    public function __construct()
    {
        $this->middleware('isCoordinateur');
    }

    public function update(Request $request, TeamParams $teamParams): \Illuminate\Http\JsonResponse
    {
        //        $unique_type = collect($request->type_day);

        $teamParams->update([
            //            'type_day' => json_encode($unique_type->unique()->all()),
            'share_link_planning' => $request->share_link_planning,
            'share_link' => $request->share_link,
            'update_planning' => $request->update_planning,
            'module_alert' => $request->module_alert,
        ]);
        $teamParams->save();

        return response()->json($teamParams);
    }
}
