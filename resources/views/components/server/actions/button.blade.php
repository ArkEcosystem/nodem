@props([
    'icon',
    'class'    => 'button-secondary',
    'text'     => null,
    'onClick'  => false,
    'disabled' => false,
    'tooltip'  => false,
])

<div @if($tooltip) data-tippy-content="{{ $tooltip }}" @endif>
    <button
        type="button"
        class="flex items-center w-full h-11 sm:w-auto md:flex-auto md:py-3 lg:py-2 focus-visible:ring-offset-2 {{ $class }}"
        @if($disabled) disabled @elseif($onClick) x-on:click="{{ $onClick }}" @endif
    >
        <div @if($text) class="hidden mr-2 sm:block md:mr-0 lg:mr-2" @endif>
            <x-ark-icon :name="$icon" size="sm" />
        </div>

        @if($text)
            <div class="md:hidden lg:block">{{ $text }}</div>
        @endif
    </button>
</div>
