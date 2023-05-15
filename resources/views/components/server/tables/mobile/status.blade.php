@props(['type'])

@php
    $colors = Arr::get([
        'error'     => 'text-theme-danger-400',
        'warning'   => 'text-theme-warning-400',
        'debug'     => 'text-theme-hint-600',
        'info'      => 'text-theme-info-600',
        'trace'     => 'text-theme-primary-600',
        'fatal'     => 'text-theme-danger-700',
        'notice'    => 'text-theme-warning-400',
    ], $type);

    $icon = Arr::get([
        'error'     => 'circle.cross',
        'warning'   => 'circle.exclamation-mark',
        'debug'     => 'clock',
        'info'      => 'circle.info',
        'trace'     => 'circle.search',
        'fatal'     => 'circle.lock',
        'notice'    => 'circle.exclamation-mark',
    ], $type, 'warning');
@endphp

<span class="{{ $colors }} space-x-2 inline-flex items-center">
    <x-ark-icon :name="$icon" size="sm" />

    <span>@lang('status.'.$type)</span>
</span>
