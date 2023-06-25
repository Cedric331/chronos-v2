@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Chronos')
<img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" class="logo" alt="{{ $slot }}">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
