@props(['server'])


@if(($server->isLoadingProcesses() || $server->isLoadingManagerState()) && !$server->isSilentLoading())
    <x-loader-icon />
@elseif($server->isUpdating() && !$server->isSilentLoading())
    <x-server.updating-core :server="$server"/>
@else
    <span
        {{ $attributes->class('relative select-none cursor-default') }}
        @if($server->hasNewCoreVersion())
            data-tippy-content="{{ __('server.actions.update_core_version', ['version' => $server->coreLatestVersion() ])}}"
        @endif
    >
        @if($server->hasNewCoreVersion())
            <x-ark-notification-dot class="top-0 right-0 p-0.5 -mt-1.5 -mr-2 bg-white"/>
        @endif

        @if($server->isManagerRunning() || $server->coreCurrentVersion() !== '')
            v{{ $server->coreCurrentVersion() }}
        @else
            <span class="w-full text-center text-theme-secondary-500">@lang('server.not_available')</span>
        @endif
    </span>
@endif
