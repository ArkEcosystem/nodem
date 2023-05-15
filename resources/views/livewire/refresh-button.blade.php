<button
    type="button"
    wire:click="update"
    @if($busy)
        disabled
        wire:poll.5000ms="refresh"
    @endif
    @class([
        'inline px-3 py-2 text-theme-primary-400',
        'animate-reverse-spin opacity-50 cursor-default' => $busy,
        'hover:text-theme-primary-500' => !$busy,
    ])
>
    <x-ark-icon name="arrows.arrow-rotate-left" />
</button>
