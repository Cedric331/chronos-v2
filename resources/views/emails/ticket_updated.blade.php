@component('mail::message')
# {{ $subject }}

{{ $introLine }}

@if ($action === 'comment' && $comment)
**Commentaire ajouté par {{ $performedBy->name }}:**

> {{ str_replace("\n", "\n> ", $comment->content) }}
@elseif ($action === 'status')
**Le statut du ticket a été changé en:** {{ $ticket->status->name }}
@endif

**Détails du ticket:**
- **Titre:** {{ $ticket->title }}
- **Catégorie:** {{ $ticket->category->name }}
- **Priorité:** {{ $ticket->priority->name }}
- **Créé par:** {{ $ticket->creator->name }}

@component('mail::button', ['url' => $url])
Voir le ticket
@endcomponent

Vous recevez cet email car vous êtes abonné à ce ticket. Vous pouvez vous désabonner en cliquant sur le bouton 'Se désabonner' sur la page du ticket.

Merci,<br>
{{ config('app.name') }}
@endcomponent
