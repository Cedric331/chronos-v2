<?php

namespace App\Http\Controllers;

use App\Models\LinkTeam;

class LinkTeamController extends Controller
{
    public function destroy(LinkTeam $link)
    {
        if ($link->user_id == auth()->user()->id || auth()->user()->isCoordinateur()) {
            $link->delete();
        } else {
            return response()->json([
                'message' => 'Vous n\'avez pas les droits pour supprimer ce lien'
            ], 403);
        }

        return response()->json([
            'message' => 'Le lien a bien été supprimé'
        ], 200);
    }
}
