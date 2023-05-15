@props(['type'])

@php
    $colors = Arr::get([
        'error'     => 'bg-theme-danger-50 text-theme-danger-400',
        'warning'   => 'bg-theme-warning-50 text-theme-warning-400',
        'debug'     => 'bg-theme-hint-50 text-theme-hint-600',
        'info'      => 'bg-theme-info-100 text-theme-info-600',
        'trace'     => 'bg-theme-primary-100 text-theme-primary-600',
        'fatal'     => 'bg-theme-danger-200 text-theme-danger-700',
        'skeleton'  => 'bg-theme-secondary-100',
        'notice'    => 'bg-theme-warning-50 text-theme-warning-400',
    ], $type);

    $icon = null;

    if ($type !== 'skeleton') {
        $icon = Arr::get([
            'error'     => 'circle.cross',
            'warning'   => 'circle.exclamation-mark',
            'debug'     => 'clock',
            'info'      => 'circle.info',
            'trace'     => 'circle.search',
            'fatal'     => 'circle.lock',
            'notice'    => 'circle.exclamation-mark',
        ], $type, 'warning');
    }
@endphp

<td {{ $attributes->merge(['class' => 'hoverable-cell']) }}>
    <div class="table-cell-bg"></div>

    <div class="h-full table-cell-content" style="padding: 0px;">
        <div class="h-full w-full mx-3 p-3 {{ $colors }}">
            <div class="flex items-center space-x-2 font-semibold rounded-sm">
                @if($type !== 'skeleton')
                    <x-ark-icon :name="$icon" />

                    <div>@lang('status.'.$type)</div>
                @else
                    <x-loading.text />
                @endif
            </div>
        </div>
    </div>
</td>
