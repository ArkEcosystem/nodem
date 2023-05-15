@props([
    'radius' => '25',
    'stroke' => '2',
    'circleColor' => 'secondary-900',
    'strokeColor' => 'secondary-200',
    'progress' => '25',
    'disabled' => false,
])

@php
$normalizedRadius = $radius - $stroke * 2;
$circumference = $normalizedRadius * 2 * pi();
$strokeDashoffset = $disabled ? $circumference - 5  : $circumference - $progress / 100 * $circumference;
@endphp

<span
    class="flex relative justify-center items-center transition rotate-minus-90"
    style="height: {{ $radius * 2 }}px; width: {{ $radius * 2 }}px; margin: -{{ $stroke }}px"
>

    <svg
        height="{{ $radius * 2}}"
        width="{{ $radius * 2}}"
        class="absolute"
    >
        <circle
            stroke="var(--theme-color-{{ $disabled ? 'secondary-200' : $strokeColor }})"
            fill="transparent"
            stroke-dasharray="{{ $circumference }} {{ $circumference }}"
            stroke-width="{{ $stroke }}"
            r="{{ $normalizedRadius }}"
            cx="{{ $radius }}"
            cy="{{ $radius }}"
            class="absolute"
        ></circle>
        <circle
            stroke="var(--theme-color-{{ $disabled ? 'secondary-500' : $circleColor }})"
            fill="transparent"
            stroke-dasharray="{{ $circumference }} {{ $circumference }}"
            style="stroke-dashoffset: {{ $strokeDashoffset }};"
            stroke-width="{{ $stroke }}"
            r="{{ $normalizedRadius }}"
            cx="{{ $radius }}"
            cy="{{ $radius }}"
        ></circle>
    </svg>

    {{ $slot }}
</span>
