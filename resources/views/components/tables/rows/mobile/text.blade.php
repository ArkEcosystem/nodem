@props([
    'title' => null,
])

<div class="space-x-4">
    @if($title)
        <span class="whitespace-nowrap">@lang($title)</span>
    @endif

    <span {{ $attributes->only('class') }}>{{ $slot }}</span>
</div>
