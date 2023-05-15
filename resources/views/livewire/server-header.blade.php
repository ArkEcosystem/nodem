<div
    @if($server->isOnPendingState())
        wire:poll
    @else
        wire:poll.60s
    @endif
>
    <div class="flex pb-6 space-x-4 w-full lg:mb-6 lg:border-b lg:border-theme-secondary-300">
        <div class="flex flex-col flex-1 space-y-2 min-w-0 font-semibold items-between">
            <div class="text-sm text-theme-secondary-500">@lang('general.server_name')</div>

            <div class="flex space-x-2 text-lg sm:text-2xl">
                <div class="truncate text-theme-secondary-900">
                    {{ $server->name() }}
                </div>

                <div class="hidden flex-shrink-0 sm:block text-theme-secondary-500">
                    @lang('server.core_with_version', [$server->coreCurrentVersion()])
                </div>

                @if ($server->isUpdating() && !$server->isSilentLoading())
                    <x-server.updating-core class="pl-2 mt-0.5 sm:mt-1" :server="$server"/>
                @endif
            </div>
        </div>

        <div class="flex items-end sm:space-x-3">
            <div class="flex items-center mr-3 h-11 sm:mr-0">
                <livewire:refresh-button :server="$server->model()" />
            </div>

            <div class="w-14" wire:key="server-dropdown-{{ $server->model()->updated_at->timestamp }}">
                <x-server.actions.consolidated.dropdown
                    :server="$server"
                    button-width="w-14"
                    notification-background="bg-theme-secondary-100"
                >
                    @can(TeamMemberPermission::SERVER_DELETE)
                        <div
                            class="flex items-center py-4 px-10 space-x-2 font-semibold sm:hidden text-theme-secondary-900"
                            @click="window.livewire.emit('triggerServerDelete', {{ $server->id() }})"
                        >
                            <x-ark-icon name="trash" />

                            <span>@lang('actions.remove')</span>
                        </div>
                    @endcan
                </x-server.actions.consolidated.dropdown>
            </div>

            @can(TeamMemberPermission::SERVER_DELETE)
                <div x-data="{}">
                    <x-server.actions.button
                        icon="trash"
                        class="hidden sm:block button-cancel"
                        on-click="window.livewire.emit('triggerServerDelete', {{ $server->id() }})"
                    />
                </div>
            @endcan
        </div>
    </div>

    <div>
        <x-server.header.information-desktop :server="$server" />
        <x-server.header.information-mobile :server="$server" />
    </div>
</div>
