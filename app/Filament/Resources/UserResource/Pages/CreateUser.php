<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\ActivationAccount;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = bcrypt(Str::random(30));
        $data['company_id'] = auth()->user()->company_id;
        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;
        $activationLink = URL::temporarySignedRoute(
            'activation', now()->addHours(48), ['email' => $user->email, 'name' => $user->name]
        );

        $mailData = [
            'link' => $activationLink,
            'email' => $user->email,
            'name' => $user->name,
            'title' => 'Bienvenue sur Chronos',
        ];

        Mail::to($user->email)->queue(new ActivationAccount($mailData));
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Utilisateur créé')
            ->body('Une invitation a été envoyée à l\'utilisateur.');
    }
}
