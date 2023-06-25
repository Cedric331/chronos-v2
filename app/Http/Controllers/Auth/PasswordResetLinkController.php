<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ActivationAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->input('email'))->first();
        if ($user) {
            if (!$user->isActivated()) {
                $activationLink = URL::temporarySignedRoute('activation', now()->addHour(24), ['email' => $user->email, 'name' => $user->name]);

                $mailData = [
                    'link' => $activationLink,
                    'email' => $user->email,
                    'name' => $user->name,
                    'title' => 'Bienvenue sur Chronos'
                ];

                Mail::to($user->email)->send(new ActivationAccount($mailData));
                throw ValidationException::withMessages([
                    'email' => ['Votre compte n\'est pas activé. Veuillez cliquer sur le lien d\'activation dans le mail que vous avez reçu. Un nouvel email vous a été envoyé.'],
                ]);
            }
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
