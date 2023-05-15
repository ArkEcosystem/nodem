@props([
    'percentage' => 0,
    'disabled' => false,
    'color' => '',
])

<div
    class="max-w-full h-full rounded-sm {{ $disabled ? 'bg-theme-secondary-500' : $color }}"
    style="width: {{ $disabled ? '2px' : $percentage.'%' }}"
></div>
