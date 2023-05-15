<x-ark-tables.row
    :warning="$server->hasWarningState() || ! $server->processTypeIsInline()"
    :danger="$server->hasErrorState()"
    :wire:poll="$server->isOnPendingState() ? '' : null"
    x-data="{ hiddenColumns: {{ Js::from(Auth::user()->getHiddenColums()) }} }"
    x-init="() => {
        window.livewire.on('columnRefresh', (columns) => hiddenColumns = columns)
    }"
>
    <x-ark-tables.cell
        class="max-w-0 text-lg font-semibold min-w-50 text-theme-primary-600"
        x-show="! hiddenColumns.name"
    >
        <div class="p-1 truncate">
            <a
                href="{{ route('server', $server->id()) }}"
                class="text-lg focus-visible:border-b-2 focus-visible:ring-0 text-theme-primary-600 focus-visible:border-theme-primary-600"
            >
                {{ $server->name() }}
            </a>
        </div>
    </x-ark-tables.cell>
    <x-ark-tables.cell responsive x-show="! hiddenColumns.core_ver">
        <div class="flex justify-center w-full">
            <x-server.version class="text-center whitespace-nowrap" :server="$server" />
        </div>
    </x-ark-tables.cell>
    <x-ark-tables.cell x-show="! hiddenColumns.provider">
        <div class="flex justify-center w-full">
            <x-ark-icon :name="$server->providerIcon()" class="text-theme-secondary-900" size="lg" />
        </div>
    </x-ark-tables.cell>
    <x-ark-tables.cell x-show="! hiddenColumns.ip">
        {{ $server->hostShort() }}
    </x-ark-tables.cell>
    <x-ark-tables.cell last-on="xl" x-show="! hiddenColumns.process">
        <div class="flex space-x-3 font-semibold text-theme-secondary-900">
            @if(($server->isLoadingProcesses() || $server->isLoadingManagerState()) && !$server->isSilentLoading())
                <span class="flex items-center space-x-2 text-theme-secondary-500">
                    <x-loader-icon />
                    <span class="font-normal">@lang('general.loading')</span>
                </span>
            @elseif($server->isNotAvailable())
                <div
                    class="flex space-x-2"
                >
                    <x-server.status-icon
                        :type="$server->statusIcon()"
                        :icon-color="$server->isOffline() ? 'text-theme-secondary-900' : null"
                        :tooltip="$server->statusTooltip()"
                    />

                    <p class="text-theme-secondary-500">
                        @lang('server.actions.manager')
                    </p>
                </div>
            @else
                @if($server->hasCore())
                    @if (! $server->core()->isDeleted())
                        <div class="flex space-x-2">
                            <x-server.status-icon
                                :type="$server->core()->statusIcon()"
                                :tooltip="$server->core()->statusTooltip()"
                            />

                            <div>@lang('server.types.core')</div>
                        </div>
                    @endif
                @else
                    @if($server->hasForger() && ! $server->forger()->isDeleted())
                        <div class="flex space-x-2">
                            <x-server.status-icon
                                :type="$server->forger()->statusIcon()"
                                :tooltip="$server->forger()->statusTooltip()"
                            />

                            <div>@lang('server.types.forger')</div>
                        </div>
                    @endif

                    @if($server->hasRelay() && ! $server->relay()->isDeleted())
                        <div class="flex space-x-2">
                            <x-server.status-icon
                                :type="$server->relay()->statusIcon()"
                                :tooltip="$server->relay()->statusTooltip()"
                            />

                            <div>@lang('server.types.relay')</div>
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </x-ark-tables.cell>
    <x-ark-tables.cell breakpoint="xl" responsive x-show="! hiddenColumns.usage">
        <x-server.usage-stats :server="$server" />
    </x-ark-tables.cell>
    <x-ark-tables.cell  breakpoint="xl" responsive x-show="! hiddenColumns.ping">
        @if($server->isManagerRunning())
            <span class="w-full text-center">{{ $server->ping() }}ms</span>
        @else
            <span class="w-full text-center text-theme-secondary-500">@lang('server.not_available')</span>
        @endif
    </x-ark-tables.cell>
    <x-ark-tables.cell breakpoint="xl" responsive x-show="! hiddenColumns.height">
        <span class="w-full text-center">
            @if($server->canGetHeight())
                <x-number>{{ $server->height() }}</x-number>
            @else
                <span class="w-full text-center text-theme-secondary-500">
                    @lang('server.not_available')
                </span>
            @endif
        </span>
    </x-ark-tables.cell>
    <x-ark-tables.cell>
        <x-server.actions.consolidated.dropdown :server="$server" />
    </x-ark-tables.cell>
</x-ark-tables.row>
