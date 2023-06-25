<x-mail::message>
# Bonjour {{ $name }},

Un compte sur {{ config('app.name') }} a été créé pour vous. Veuillez cliquer sur le bouton ci-dessous pour activer votre compte et définir votre mot de passe.


<x-mail::button :url="$url">
Activer mon compte
</x-mail::button>

<x-mail::panel>
    Le lien est valable pendant 24h.
</x-mail::panel>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
