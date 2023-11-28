<x-mail::message>
    # Bonjour {{ $name }},

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
                <td>Date de début</td>
                <td>{{ $start_date }}</td>
            </tr>
            <tr>
                <td>Date de fin</td>
                <td>{{ $end_date }}</td>
            </tr>
            <tr>
                <td>Nombre de jours</td>
                <td>{{ $days }}</td>
            </tr>
            <tr>
                <td>Commentaire</td>
                <td>{{ $comment }}</td>
            </tr>
        </table>

    <x-mail::button :url="$url">
        Voir la demande sur {{ config('app.name') }}
    </x-mail::button>

    <x-mail::button :url="$url">
        Valider la demande de congés
    </x-mail::button>

    Merci,<br>
    {{ config('app.name') }}
</x-mail::message>
