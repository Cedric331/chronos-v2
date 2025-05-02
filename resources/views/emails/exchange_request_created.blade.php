<x-mail::message>
# Bonjour Coordinateur,

Une nouvelle demande d'échange de planning vient d'être effectuée sur {{ config('app.name') }}.

<x-mail::panel>
<table>
<tr>
<td>Demande effectuée par</td>
<td>{{ $requester->name }}</td>
</tr>
<tr>
<td>Demande adressée à</td>
<td>{{ $requested->name }}</td>
</tr>
<tr>
<td>Date du planning à échanger</td>
<td>{{ $requesterPlanning->calendar->date_fr_full }}</td>
</tr>
<tr>
<td>Horaires du planning à échanger</td>
<td>{{ $requesterPlanning->debut_journee }} - {{ $requesterPlanning->fin_journee }}</td>
</tr>
<tr>
<td>Date du planning souhaité</td>
<td>{{ $requestedPlanning->calendar->date_fr_full }}</td>
</tr>
<tr>
<td>Horaires du planning souhaité</td>
<td>{{ $requestedPlanning->debut_journee }} - {{ $requestedPlanning->fin_journee }}</td>
</tr>
@if($exchangeRequest->requester_comment)
<tr>
<td>Commentaire</td>
<td>{{ $exchangeRequest->requester_comment }}</td>
</tr>
@endif
</table>
</x-mail::panel>

<x-mail::button :url="$url">
Voir la demande d'échange
</x-mail::button>

<p>Merci,</p>
{{ config('app.name') }}
</x-mail::message>
