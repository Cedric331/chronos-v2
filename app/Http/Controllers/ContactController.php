<?php

namespace App\Http\Controllers;

use App\Mail\ContactAdministrateur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'subject' => 'required|string',
            'text' => 'required|string'
        ]);

        $data = [
            'subject' => $request->subject,
            'text' => explode("\n", $request->text),
            'author' => [
                'identifiant' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email
            ]
        ];

        $admin = User::role('Administrateur')->first();

        try {
            Mail::to($admin->email)->queue(new ContactAdministrateur($data));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du mail: ' . $e->getMessage());

            return response()->json(['error' => 'Erreur lors de l\'envoi du mail.'], 404);
        }

        return response()->json(true);
    }

}
