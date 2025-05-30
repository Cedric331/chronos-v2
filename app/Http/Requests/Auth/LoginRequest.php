<?php

namespace App\Http\Requests\Auth;

use App\Mail\ActivationAccount;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
//    public function authenticate(): void
//    {
//        $this->ensureIsNotRateLimited();
//
//        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
//            RateLimiter::hit($this->throttleKey());
//
//            throw ValidationException::withMessages([
//                'email' => trans('auth.failed'),
//            ]);
//        }
//
//        $user = Auth::user();
//
//        if (! $user->isActivated()) {
//            $this->sendActivationMail($user);
//        }
//
//        RateLimiter::clear($this->throttleKey());
//    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');

        // On vérifie si l'on est en environnement local
        if (app()->environment('local')) {
            $user = \App\Models\User::where('email', $credentials['email'])->first();

            if ($user) {
                Auth::login($user, $this->boolean('remember'));

                if (! $user->isActivated()) {
                    $this->sendActivationMail($user);
                }

                RateLimiter::clear($this->throttleKey());

                return;
            }
        }

        // Comportement normal pour les autres environnements
        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $user = Auth::user();

        if (! $user->isActivated()) {
            $this->sendActivationMail($user);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }

    public function sendActivationMail($user)
    {

        $activationLink = URL::temporarySignedRoute('activation', now()->addHour(48), ['email' => $user->email, 'name' => $user->name]);
        $user->last_invitation = now();
        $user->save();

        $mailData = [
            'link' => $activationLink,
            'email' => $user->email,
            'name' => $user->name,
        ];

        Mail::to($user->email)->queue(new ActivationAccount($mailData));

        Auth::guard('web')->logout();

        throw ValidationException::withMessages([
            'activation' => 'Votre compte n\'est pas activé. Vous devez activer votre compte via le mail que vous avez reçu. Un nouvel email vous a été envoyé',
        ]);
    }
}
