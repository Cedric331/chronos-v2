@component('mail::message')
# Nouveau Ticket Créé

Un nouveau ticket a été créé par **{{ $creator->name }}**.

**Détails du ticket:**
- **Titre:** {{ $ticket->title }}
- **Catégorie:** {{ $ticket->category->name }}
- **Priorité:** {{ $ticket->priority->name }}
- **Créé le:** {{ $ticket->created_at->format('d/m/Y H:i') }}

**Description:**
{{ $ticket->description }}

@component('mail::button', ['url' => $url])
Voir le ticket
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent
