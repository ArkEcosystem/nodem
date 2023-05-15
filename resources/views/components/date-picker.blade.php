@props ([
    'wire',
    'id',
    'required' => false,
])

<div {{ $attributes->class('relative') }}>
    <x-ark-date-picker
        :id="$id"
        placeholder="DD.MM.YYYY"
        :required="$required"
        autocomplete="off"
        class="input-text pr-10 w-full bg-transparent {{ $errors->has($wire) ? 'input-text--error' : '' }}"
        wire:model.defer="{{ $wire }}"
        wire:loading.attr="disabled"
    />

    @error ($wire)
        @include ('ark::inputs.includes.input-error-tooltip', [
            'error' => $message,
            'id' => $id,
        ])
    @else
        <span class="flex absolute inset-y-0 right-0 items-center pr-4 text-theme-primary-400">
            <x-ark-icon name="calendar" />
        </span>
    @enderror
</div>
