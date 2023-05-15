@props([
    'server',
    'tooltip'  => null,
])

@if($server->canUpdate())
    <x-ark-dropdown-accordion>
        <x-slot name="title">
            <span class="relative">
                @if($server->hasNewVersion())
                    <x-ark-notification-dot class="top-0 right-0 p-0.5 -mt-1.5 -mr-2 bg-white" />
                @endif
                @lang('server.actions.update')
            </span>
        </x-slot>

        <div class="font-semibold text-theme-secondary-500">
            @foreach([ServerTypeEnum::CORE, ServerTypeEnum::CORE_MANAGER] as $type)
                <x-server.actions.consolidated.update-button
                    :type="$type"
                    :server="$server"
                />
            @endforeach
        </div>
    </x-ark-dropdown-accordion>
@else
    <div
        class="flex justify-between items-center py-4 px-10 font-semibold cursor-not-allowed"
        @if($tooltip) data-tippy-content="{{ $tooltip }}" @endif
    >
        <span class="relative">
            @if($server->hasNewVersion())
                <x-ark-notification-dot class="top-0 right-0 p-0.5 -mt-1.5 -mr-2 bg-white" />
            @endif

            @lang('server.actions.update')
        </span>

        @if ($server->isUpdating())
            <div>
                <x-loader-icon />
            </div>
        @else
            <x-ark-icon
                name="arrows.chevron-down-small"
                size="xs"
            />
        @endif
    </div>
@endif
