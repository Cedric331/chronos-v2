<x-mail::message>
# Bonjour,

Une demande d'échange de planning vient d'être acceptée sur {{ config('app.name') }}.

<x-mail::panel>
<table style="width: 100%;">
<tr>
<td style="padding-bottom: 15px; font-weight: bold;">Informations sur l'échange</td>
<td></td>
</tr>
<tr>
<td style="padding-bottom: 8px;">Demande effectuée par</td>
<td style="padding-bottom: 8px;">{{ $requester->name }}</td>
</tr>
<tr>
<td style="padding-bottom: 15px;">Demande acceptée par</td>
<td style="padding-bottom: 15px;">{{ $requested->name }}</td>
</tr>

<tr>
<td style="padding-top: 10px; padding-bottom: 8px; font-weight: bold;">Planning échangé</td>
<td></td>
</tr>
<tr>
<td style="padding-bottom: 8px;">Date</td>
<td style="padding-bottom: 8px;">{{ $requesterPlanning->calendar->date_fr_full }}</td>
</tr>
<tr>
<td style="padding-bottom: 15px;">Horaires</td>
<td style="padding-bottom: 15px;">{{ $requesterPlanning->debut_journee }} - {{ $requesterPlanning->fin_journee }}</td>
</tr>

<tr>
<td style="padding-top: 10px; padding-bottom: 8px; font-weight: bold;">Planning obtenu</td>
<td></td>
</tr>
<tr>
<td style="padding-bottom: 8px;">Date</td>
<td style="padding-bottom: 8px;">{{ $requestedPlanning->calendar->date_fr_full }}</td>
</tr>
<tr>
<td style="padding-bottom: 15px;">Horaires</td>
<td style="padding-bottom: 15px;">{{ $requestedPlanning->debut_journee }} - {{ $requestedPlanning->fin_journee }}</td>
</tr>

@if($exchangeRequest->requester_comment)
<tr>
<td style="padding-top: 10px; padding-bottom: 8px; font-weight: bold;">Commentaire du demandeur</td>
<td></td>
</tr>
<tr>
<td colspan="2" style="padding-bottom: 8px;">{{ $exchangeRequest->requester_comment }}</td>
</tr>
@endif

@if($exchangeRequest->requested_comment)
<tr>
<td style="padding-top: 10px; padding-bottom: 8px; font-weight: bold;">Commentaire de l'accepteur</td>
<td></td>
</tr>
<tr>
<td colspan="2" style="padding-bottom: 8px;">{{ $exchangeRequest->requested_comment }}</td>
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
