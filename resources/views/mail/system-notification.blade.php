<x-mail::message>

    @if ($recipientName)
        Hello {{ $recipientName }},
    @else
        Hello,
    @endif

    # {{ $title }}

    {{ $message }}

    @if ($actionUrl && $actionLabel)
        <x-mail::button :url="$actionUrl">
            {{ $actionLabel }}
        </x-mail::button>
    @endif

    Regards,<br>
    {{ config('app.name') }}

</x-mail::message>
