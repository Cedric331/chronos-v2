<?php

namespace App\Http\Controllers;

use App\Models\LinkTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LinkTeamController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lien' => 'required|url|string|max:255',
            'description' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $team = Auth::user()->team;

        if (Auth::user()->isCoordinateur() || $team->params->share_link) {
            LinkTeam::create([
                'link' => $request->lien,
                'description' => $request->description,
                'user_id' => Auth::user()->id,
                'team_id' => $team->id,
            ]);
        } else {
            return response()->json([
                'message' => 'Vous n\'avez pas les droits pour ajouter un lien',
            ], 403);
        }

        $links = LinkTeam::where('team_id', $team->id)
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->paginate(10);

        return response()->json($links, 200);
    }

    public function update(Request $request, LinkTeam $link)
    {
        $validator = Validator::make($request->all(), [
            'lien' => 'required|url|string|max:255',
            'description' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $team = Auth::user()->team;

        if (Auth::user()->isCoordinateur() || $team->params->share_link) {
            $link->update([
                'link' => $request->lien,
                'description' => $request->description,
                'updated_by' => Auth::user()->id,
            ]);
        } else {
            return response()->json([
                'message' => 'Vous n\'avez pas les droits pour ajouter un lien',
            ], 403);
        }

        $links = LinkTeam::where('team_id', $team->id)
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->paginate(10);

        return response()->json($links, 200);
    }

    public function destroy(LinkTeam $link): \Illuminate\Http\JsonResponse
    {
        if ($link->user_id == auth()->user()->id || auth()->user()->isCoordinateur()) {
            $link->delete();
        } else {
            return response()->json([
                'message' => 'Vous n\'avez pas les droits pour supprimer ce lien',
            ], 403);
        }

        return response()->json([
            'message' => 'Le lien a bien été supprimé',
        ], 200);
    }
}
