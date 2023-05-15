@props([
    'title',
    'hasSlot' => false,
])

<x-ark-container
    class="bg-theme-secondary-100"
    container-class="flex flex-col justify-between items-center lg:flex-row"
>
    <h1
        @class([
            'mb-0 w-full',
            'text-left' => $hasSlot,
            'text-center' => ! $hasSlot,
        ])
    >
        {{ $title }}
    </h1>

    @if($hasSlot)
        {{ $slot }}
    @endif
</x-ark-container>
