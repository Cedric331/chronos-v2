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
        // Log les données reçues
        \Log::info('TeamParamsController::update - Request data', [
            'team_params_id' => $teamParams->id,
            'request_data' => $request->all()
        ]);

        // Valider les données reçues
        $validated = $request->validate([
            'send_coordinateur' => 'required|boolean',
            'share_link_planning' => 'required|boolean',
            'share_link' => 'required|boolean',
            'paid_leave' => 'required|boolean',
            'update_planning' => 'required|boolean',
            'module_alert' => 'required|boolean',
            'exchange_module' => 'required|boolean',
        ]);

        // Log les données validées
        \Log::info('TeamParamsController::update - Validated data', [
            'team_params_id' => $teamParams->id,
            'validated_data' => $validated
        ]);


        try {
            // Mettre à jour les paramètres
            $teamParams->update($validated);
            $teamParams->save();

            // Log les paramètres après la mise à jour
            \Log::info('TeamParamsController::update - After update', [
                'team_params_id' => $teamParams->id,
                'updated_data' => $teamParams->toArray()
            ]);

            // Enregistrer l'activité
            activity($teamParams->team->name)
                ->event('Mise à jour')
                ->performedOn($teamParams->team)
                ->withProperties($validated)
                ->log('Les paramètres de l\'équipe ont été modifiés');
            // Recharger les paramètres pour s'assurer d'avoir les dernières valeurs
            $teamParams->refresh();

            return response()->json($teamParams);
        } catch (\Exception $e) {
            // Log l'erreur
            \Log::error('Error updating team params', [
                'team_params_id' => $teamParams->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Une erreur est survenue lors de la mise à jour des paramètres.'], 500);
        }
    }
}
