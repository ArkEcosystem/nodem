@props ([
    'wire' => null,
    'target' => null,
    'icon'
])

{{--
    If using `type="submit"` for a button, there is no need to specify the `wire` attribute.
    `target` attribute specifies which `wire:target` the loader is going to show for.
    Because modals (for example) have another Livewire call (`closeModal`),
    you'd need to specify which target the loader will show for.

    For a regular button, `<x-loading-button wire="runAction">Run action</x-loading-button>` would do the trick, because we can infer the target.
    For a submit button, `<x-loading-button type="submit" target="runAction">Run action</x-loading-button>` would be better, to have explicit Livewire target.
--}}

@php ($target = $target ?? $wire)

<button
    {{ $attributes->class('flex justify-center items-center button-primary focus-visible:ring-offset-2')->merge(['type' => 'button']) }}
    @if ($wire)
        wire:click="{{ $wire }}"
    @endif
    @if ($target)
        wire:target="{{ $target }}"
    @endif
    wire:loading.attr="disabled"
>
    <span
        class="hidden mr-2"
        wire:loading.class.remove="hidden"
        @if ($target)
        wire:target="{{ $target }}"
        @endif
    >
        <x-ark-loader-icon class="w-4 h-4" />
    </span>

    <span
        class="mr-2"
        wire:loading.class="hidden"
        @if ($target)
        wire:target="{{ $target }}"
        @endif
    >
        <x-ark-icon :name="$icon" size="sm" />
    </span>

    <span>{{ $slot }}</span>
</button>
