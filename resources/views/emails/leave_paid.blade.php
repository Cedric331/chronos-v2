<x-mail::message>
# Bonjour {{ $coordinateur_name }},

Un demande de {{ $leave_type  }} vient d'être effectuée sur {{ config('app.name') }}.

<x-mail::panel>
<table>
<tr>
<td>Demande effectuée par</td>
<td>{{ $name }}</td>
</tr>
<tr>
<td>Type de congés</td>
<td>{{ $leave_type }}</td>
</tr>
<tr>
<td>Dates</td>
<td>
<ul>
@foreach($dates as $date)
<li>{{ $date }}</li>
@endforeach
</ul>
</td>
</tr>
<tr>
<td>Commentaire</td>
<td>{{ $comment }}</td>
</tr>
</table>
</x-mail::panel>

<x-mail::button :url="$url">
Voir la demande sur {{ config('app.name') }}
</x-mail::button>

<p>Merci,</p>
{{ config('app.name') }}
</x-mail::message>
