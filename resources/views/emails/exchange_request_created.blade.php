<x-mail::message>
# Bonjour,

Une nouvelle demande d'échange de planning vient d'être effectuée sur {{ config('app.name') }}.

<x-mail::panel>
<table style="width: 100%;">
<tr>
<td style="padding-bottom: 15px; font-weight: bold;">Informations sur la demande</td>
<td></td>
</tr>
<tr>
<td style="padding-bottom: 8px;">Demande effectuée par</td>
<td style="padding-bottom: 8px;">{{ $requester->name }}</td>
</tr>
<tr>
<td style="padding-bottom: 15px;">Demande adressée à</td>
<td style="padding-bottom: 15px;">{{ $requested->name }}</td>
</tr>

@if($comment)
<tr>
<td style="padding-top: 10px; padding-bottom: 8px; font-weight: bold;">Commentaire</td>
<td></td>
</tr>
<tr>
<td colspan="2" style="padding-bottom: 15px;">{{ $comment }}</td>
</tr>
@endif

<tr>
<td style="padding-top: 10px; padding-bottom: 15px; font-weight: bold;" colspan="2">Détails des échanges demandés ({{ count($exchangeRequests) }} jour(s))</td>
</tr>

@foreach($exchangeRequests as $index => $exchange)
<tr>
<td style="padding-top: 15px; padding-bottom: 8px; font-weight: bold;" colspan="2">Échange #{{ $index + 1 }}</td>
</tr>

<tr>
<td style="padding-bottom: 8px;">Date du planning à échanger</td>
<td style="padding-bottom: 8px;">{{ $exchange->requesterPlanning->calendar->date_fr_full }}</td>
</tr>
<tr>
<td style="padding-bottom: 15px;">Horaires</td>
<td style="padding-bottom: 15px;">{{ $exchange->requesterPlanning->debut_journee }} - {{ $exchange->requesterPlanning->fin_journee }}</td>
</tr>

<tr>
<td style="padding-bottom: 8px;">Date du planning souhaité</td>
<td style="padding-bottom: 8px;">{{ $exchange->requestedPlanning->calendar->date_fr_full }}</td>
</tr>
<tr>
<td style="padding-bottom: 15px;">Horaires</td>
<td style="padding-bottom: 15px;">{{ $exchange->requestedPlanning->debut_journee }} - {{ $exchange->requestedPlanning->fin_journee }}</td>
</tr>
@endforeach
</table>
</x-mail::panel>

<x-mail::button :url="$url">
Voir la demande d'échange
</x-mail::button>

<p>Merci,</p>
{{ config('app.name') }}
</x-mail::message>
