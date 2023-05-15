@props([
    'alpine' => false,
    'checked' => false,
    'labelClass' => 'ml-3 font-semibold whitespace-nowrap text-theme-secondary-500',
    'withLabel' => false,
    'right' => false,
])

<div
    @class([
        'flex items-center h-5',
        'flex-row-reverse' => $right,
    ])
    {{ $attributes }}
>
    <input
        type="checkbox"
        @class([
            'focus-visible:ring-2 form-checkbox input-checkbox focus-visible:ring-theme-primary-500',
            'bg-theme-success-600'=> $checked,
            'border-2 border-theme-secondary-300' => ! $checked,
        ])
        wire:loading.delay.longest.class="opacity-50 cursor-not-allowed"
        wire:loading.delay.longest.class.remove="hover:bg-theme-secondary-100 cursor-pointer"
        wire:loading.delay.longest.attr="disabled"
        @if($alpine) @click="{{ $alpine }}" @endif
        @if($checked) checked @endif
    />

    @if($withLabel)
        <span class="{{ $labelClass }}">
            {{ $slot }}
        </span>
    @endif
</div>
