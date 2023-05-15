@props([
    'action',
    'server',
    'disabled' => false,
    'tooltip'  => null,
])

@if($server->{'can'.ucfirst($action).'Any'}() && ! $disabled)
    <x-ark-dropdown-accordion>
        <x-slot name="title">
            @lang('server.actions.'.$action)
        </x-slot>

        <div class="font-semibold text-theme-secondary-500">
            <x-server.actions.action-processes
                :action="$action"
                :server="$server"
            />
        </div>
    </x-ark-dropdown-accordion>
@else
    <div
        class="flex justify-between items-center py-4 px-10 font-semibold cursor-not-allowed"
        @if($tooltip) data-tippy-content="{{ $tooltip }}" @endif
    >
        <span>@lang('server.actions.'.$action)</span>

        <x-ark-icon
            name="arrows.chevron-down-small"
            size="xs"
        />
    </div>
@endif
