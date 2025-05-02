<x-mail::message>
# Bonjour Coordinateur,

Une demande d'échange de planning vient d'être acceptée sur {{ config('app.name') }}.

<x-mail::panel>
<table>
<tr>
<td>Demande effectuée par</td>
<td>{{ $requester->name }}</td>
</tr>
<tr>
<td>Demande acceptée par</td>
<td>{{ $requested->name }}</td>
</tr>
<tr>
<td>Date du planning échangé</td>
<td>{{ $requesterPlanning->calendar->date_fr_full }}</td>
</tr>
<tr>
<td>Horaires du planning échangé</td>
<td>{{ $requesterPlanning->debut_journee }} - {{ $requesterPlanning->fin_journee }}</td>
</tr>
<tr>
<td>Date du planning obtenu</td>
<td>{{ $requestedPlanning->calendar->date_fr_full }}</td>
</tr>
<tr>
<td>Horaires du planning obtenu</td>
<td>{{ $requestedPlanning->debut_journee }} - {{ $requestedPlanning->fin_journee }}</td>
</tr>
@if($exchangeRequest->requester_comment)
<tr>
<td>Commentaire du demandeur</td>
<td>{{ $exchangeRequest->requester_comment }}</td>
</tr>
@endif
@if($exchangeRequest->requested_comment)
<tr>
<td>Commentaire de l'accepteur</td>
<td>{{ $exchangeRequest->requested_comment }}</td>
</tr>
@endif
</table>
</x-mail::panel>

<x-mail::button :url="$url">
Voir les détails de l'échange
</x-mail::button>

<p>Merci,</p>
{{ config('app.name') }}
</x-mail::message>
