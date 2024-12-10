<x-mail::message>
Bonjour,

<h3 class="text-2xl">{{ $content['subject'] }}</h3>

<x-mail::panel>
<div class="flex items-center justify-center min-h-screen p-5 bg-gray-600 min-w-screen">
<div class="max-w-xl p-8 text-center text-gray-800 bg-white shadow-xl lg:max-w-3xl rounded-3xl lg:p-12">
<div class="mt-4">
<p class="mt-4 text-sm">
@foreach( $content['text'] as $line)
{{ $line }}
<br>
@endforeach
</p>
</div>
</div>
<br>
<br>
<div class="max-w-xl p-8 text-center text-gray-800 bg-white shadow-xl lg:max-w-3xl rounded-3xl lg:p-12">
<h3 class="text-2xl">Information de l'auteur du message</h3>
<div class="mt-4">
<p class="mt-4 text-sm">
Message envoyé par {{ $content['author']['name'] }}
<br>
Email : {{ $content['author']['email'] }}
<br>
Identifiant {{ $content['author']['identifiant'] }}
</p>
</div>
</div>
</div>
</x-mail::panel>


Merci
{{ config('app.name') }}
</x-mail::message>
