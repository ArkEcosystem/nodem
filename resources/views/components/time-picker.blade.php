@props ([
    'wire',
    'id',
    'required' => false,
    'target',
])

<div {{ $attributes->class('relative') }}>
    <input
        type="text"
        x-data
        {{ $required ? 'required' : '' }}
        wire:loading.attr="disabled"
        x-on:focus="
            if (! $root.disabled && ! $root.readOnly) {
                {{-- To not have line height issues... --}}
                const currentHeight = $root.offsetHeight
                $root.type = 'time'
                $root.style.height = currentHeight + 'px'
            }
        "
        x-on:blur="$root.type = $root.value.length === 0 ? 'text' : 'time'"
        @class([
            'input-text appearance-none w-full',
            'input-text--error input-wrapper-with-suffix' => $errors->has($wire),
        ])
        wire:model.defer="{{ $wire }}"
        id="{{ $id }}"
        step="1"
        placeholder="HH:mm:ss"
    />

    <span
        class="flex absolute inset-y-0 right-0 items-center pr-3 my-px mr-1 bg-white"
        wire:loading.class.remove="bg-white"
        wire:loading.class="bg-theme-secondary-100"
        wire:target="{{ $target }}"
    >
        @error ($wire)
            <span data-tippy-content="{{ $message }}">
                <x-ark-icon name="circle.exclamation-mark" class="text-theme-danger-500" />
            </span>
        @else
            <x-ark-icon name="clock" class="text-theme-primary-400" />
        @enderror
    </span>
</div>
