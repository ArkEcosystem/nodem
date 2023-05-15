{{-- Variation of the UI dropdown component - incorporates popperjs --}}
@props([
    'button'                 => false,
    'dropdownProperty'       => 'dropdownOpen',
    'dropdownContentClasses' => 'bg-white rounded-xl shadow-lg dark:bg-theme-secondary-800 dark:text-theme-secondary-200',
    'buttonClassExpanded'    => 'text-theme-primary-500',
    'buttonClass'            => 'text-theme-secondary-400 hover:text-theme-primary-500',
    'dropdownClasses'        => 'w-52 z-50',
    'closeOnClick'           => true,
    'closeOnBlur'            => true,
    'disabled'               => false,
    'onClosed'               => null,
    'placement'              => null,
    'transitionEase'         => true,
])

<div
    x-data="Dropdown.setup('{{ $dropdownProperty }}', {
        @if($onClosed)
            onClosed: ({{ $onClosed }}),
        @endif
        @if($placement)
            placement: '{{ $placement }}',
        @endif
    })"
    class="dropdown-container"
    @if($closeOnBlur)
    @keydown.escape="close"
    @click.outside="close"
    @endif
    {{ $attributes }}
>
    <button
        type="button"
        :class="{ '{{ $buttonClassExpanded }}' : {{ $dropdownProperty }} }"
        class="dropdown-button flex items-center focus:outline-none transition-default {{ $buttonClass }}"
        @if($disabled) disabled @else @click="toggle" @endif
    >
        @if($button)
            {{ $button }}
        @else
            <x-ark-icon name="ellipsis-vertical" />
        @endif
    </button>

    <div
        x-cloak
        x-show="{{ $dropdownProperty }}"
        @if ($transitionEase)
            x-transition:enter="transition ease-out duration-100"
        @endif
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        @if ($transitionEase)
            x-transition:leave="transition ease-in duration-75"
        @endif
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="dropdown {{ $dropdownClasses }}"
        {{-- It prevents that the style attribute added by Popper from being removed.  --}}
        wire:ignore.self
    >
        <div class="{{ $dropdownContentClasses }}" x-cloak>
            <div
                class="py-6"
                @if($closeOnClick)
                    @click="{{ $dropdownProperty }} = !{{ $dropdownProperty }}"
                    role="button"
                @endif
            >
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
