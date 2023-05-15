@props([
    'type',
    'tooltip'   => null,
    'large'     => false,
    'iconColor' => null,
    'largeSize' => '2xl',
])

@php
    if ($large) {
        $icon = Arr::get([
            'undefined' => 'circle.question-mark-big',
            'online' => 'circle.check-mark-big',
            'stopped' => 'circle.pause-big',
            'stopping' => 'circle.hand-big',
            'waiting restart' => 'circle.clock-big',
            'launching' => 'circle.play-big',
            'errored' => 'circle.cross-big',
            'one-launch-status' => 'circle.forward-big',
            'not-inline' => 'circle.exclamation-mark-big',
            'height-mismatch' => 'circle.exclamation-mark-big',
            'unable-to-fetch-height' => 'circle.exclamation-mark-big',
        ], $type, 'undefined');
    } else {
        $icon = Arr::get([
            'undefined' => 'circle.question-mark',
            'online' => 'circle.check-mark',
            'stopped' => 'circle.pause',
            'stopping' => 'circle.hand',
            'waiting restart' => 'clock',
            'launching' => 'circle.play',
            'errored' => 'circle.cross',
            'one-launch-status' => 'circle.forward',
            'not-inline' => 'circle.exclamation-mark',
            'height-mismatch' => 'circle.exclamation-mark',
            'unable-to-fetch-height' => 'circle.exclamation-mark',
        ], $type, 'undefined');
    }
    $iconColor = $iconColor ?? Arr::get([
        'undefined' => 'text-theme-secondary-700',
        'online' => 'text-theme-success-600',
        'stopped' => 'text-theme-warning-500',
        'stopping' => 'text-theme-warning-500',
        'waiting restart' => 'text-theme-hint-400',
        'launching' => 'text-theme-primary-500',
        'errored' => 'text-theme-danger-400',
        'one-launch-status' => 'text-theme-info-500',
        'not-inline' => 'text-theme-warning-500',
        'height-mismatch' => 'text-theme-warning-500',
        'unable-to-fetch-height' => 'text-theme-warning-500',
    ], $type, 'text-theme-secondary-700');
@endphp

<div
    class="flex flex-shrink-0 justify-center items-center"
    @if ($tooltip)
        data-tippy-content="{{ $tooltip }}"
    @endif
>
    <x-ark-icon
        :name="$icon"
        :size="$large ? $largeSize : 'base'"
        :class="$iconColor"
    />
</div>
