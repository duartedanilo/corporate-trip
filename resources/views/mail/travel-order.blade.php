<x-mail::message>
# The travel order #{{ $id }} to {{ $destination }} was {{ $status }}!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
