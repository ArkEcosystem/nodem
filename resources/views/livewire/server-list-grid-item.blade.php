@php
    $tooltip = false;

    if ($server->isOffline()) {
        $tooltip = trans('server.tooltips.connection_failed', ['server_name' => $server->name()]);
    }

    if ($server->isManagerNotRunning()) {
        $tooltip = trans('server.tooltips.process_not_running');
    }
@endphp

<div
    class="py-16 border-t first:pt-8 first:mt-0 first:border-t-0 border-theme-secondary-300"
    @if($server->isOnPendingState())
        wire:poll
    @endif
    x-data="{ hiddenColumns: {{ Js::from(Auth::user()->getHiddenColums()) }} }"
    x-init="() => {
        window.livewire.on('columnRefresh', (columns) => hiddenColumns = columns)
    }"
>
    <div class="flex flex-col space-y-8 content-container">
        <div class="flex items-center pb-6 border-b border-dotted lg:space-x-4 border-theme-secondary-300">
            <div class="flex flex-col flex-1 min-w-0 font-semibold">
                <div class="mb-3 text-sm text-theme-secondary-500">@lang('server.name')</div>

                <p>
                    <a href="{{ route('server', $server->id()) }}"
                       class="text-lg leading-none focus-visible:rounded truncate text-theme-primary-600">
                        {{ $server->name() }}
                    </a>
                </p>
            </div>

            <x-server.actions.consolidated.dropdown :tooltip="$tooltip" :server="$server"/>
        </div>

        <div class="server-grid-table">
            {{-- Provider --}}
            <template x-if="! hiddenColumns.provider">
                <x-ark-details-box
                    :icon="$server->providerIcon()"
                    icon-class="text-theme-secondary-900"
                    :title="trans('server.provider')"
                    shallow
                >
                    @lang('server.providers.'.$server->provider())
                </x-ark-details-box>
            </template>

            {{-- IP --}}
            <template x-if="! hiddenColumns.ip">
                <x-ark-details-box icon="app-server.ip" :title="trans('server.ip')" shallow>
                    {{ $server->hostShort() }}
                </x-ark-details-box>
            </template>

            {{-- Core version --}}
            <template x-if="! hiddenColumns.core_ver">
                <x-ark-details-box icon="app-server.core_version" :title="trans('server.core_version')" shallow>
                    <x-server.version :server="$server"/>
                </x-ark-details-box>
            </template>

            {{-- Ping --}}
            <template x-if="! hiddenColumns.ping">
                <x-ark-details-box icon="app-server.ping" :title="trans('server.ping')" shallow>
                    @if($server->isManagerRunning())
                        {{ $server->ping() }}ms
                    @else
                        <span class="w-full text-center text-theme-secondary-500">@lang('server.not_available')</span>
                    @endif
                </x-ark-details-box>
            </template>

            {{-- Height --}}
            <template x-if="! hiddenColumns.height">
                <x-ark-details-box icon="app-server.height" :title="trans('server.height')" shallow>
                    @if($server->isManagerRunning())
                        <x-number>{{ $server->height() }}</x-number>
                    @else
                        <span class="w-full text-center text-theme-secondary-500">@lang('server.not_available')</span>
                    @endif
                </x-ark-details-box>
            </template>

            {{-- Usage --}}
            <template x-if="! hiddenColumns.usage">
                <x-ark-details-box :title="trans('server.disk')" shallow>
                    <x-slot name="iconWrapper">
                        <div class="flex flex-shrink-0 justify-center items-center p-2 mr-5 w-11 h-11">
                            <x-server.progress-circle
                                :disabled="$server->isNotAvailable()"
                                circle-color="info-600"
                                stroke-color="info-200"
                                :progress="$server->diskPercentage()"
                            >
                                <x-ark-icon
                                    class="{{ $server->isNotAvailable() ? 'text-theme-secondary-500 border-theme-secondary-500' : 'text-theme-info-600 border-theme-info-600' }}"
                                    name="app-server.disk"/>
                            </x-server.progress-circle>
                        </div>
                    </x-slot>

                    @if($server->isManagerRunning())
                        <x-percentage>{{ $server->diskPercentage() }}</x-percentage>
                    @else
                        <span class="w-full text-center text-theme-secondary-500">@lang('server.not_available')</span>
                    @endif
                </x-ark-details-box>
            </template>

            <template x-if="! hiddenColumns.usage">
                <x-ark-details-box :title="trans('server.ram')" shallow>
                    <x-slot name="iconWrapper">
                        <div class="flex flex-shrink-0 justify-center items-center p-2 mr-5 w-11 h-11">
                            <x-server.progress-circle
                                :disabled="$server->isNotAvailable()"
                                circle-color="success-600"
                                stroke-color="success-200"
                                :progress="$server->ramPercentage()"
                            >
                                <x-ark-icon
                                    class="{{ $server->isNotAvailable() ? 'text-theme-secondary-500 border-theme-secondary-500' : 'text-theme-success-600 border-theme-success-600' }}"
                                    name="app-server.ram"/>
                            </x-server.progress-circle>
                        </div>
                    </x-slot>

                    @if($server->isManagerRunning())
                        <x-percentage>{{ $server->ramPercentage() }}</x-percentage>
                    @else
                        <span class="w-full text-center text-theme-secondary-500">@lang('server.not_available')</span>
                    @endif
                </x-ark-details-box>
            </template>

            <template x-if="! hiddenColumns.usage">
                <x-ark-details-box :title="trans('server.cpu')" shallow>
                    <x-slot name="iconWrapper">
                        <div class="flex flex-shrink-0 justify-center items-center p-2 mr-5 w-11 h-11">
                            <x-server.progress-circle
                                :disabled="$server->isNotAvailable()"
                                circle-color="hint-600"
                                stroke-color="hint-200"
                                :progress="$server->cpuPercentage()"
                            >
                                <x-ark-icon
                                    class="{{ $server->isNotAvailable() ? 'text-theme-secondary-500 border-theme-secondary-500' : 'text-theme-hint-600 border-theme-hint-600' }}"
                                    name="app-server.cpu"/>
                            </x-server.progress-circle>
                        </div>
                    </x-slot>

                    @if($server->isManagerRunning())
                        <x-percentage>{{ $server->cpuPercentage() }}</x-percentage>
                    @else
                        <span class="w-full text-center text-theme-secondary-500">@lang('server.not_available')</span>
                    @endif
                </x-ark-details-box>
            </template>

            {{-- Processes --}}
            @if(($server->isLoadingProcesses() || $server->isLoadingManagerState()) && !$server->isSilentLoading())
                <template x-if="! hiddenColumns.process">
                    <x-ark-details-box :title="trans('server.process')" shallow>
                        <x-slot name="iconRaw">
                            <x-loader-icon />
                        </x-slot>

                        <p class="flex items-center space-x-2 text-theme-secondary-500">
                            <span class="font-normal">@lang('general.loading')</span>
                        </p>
                    </x-ark-details-box>
                </template>
            @elseif($server->isNotAvailable())
                <template x-if="! hiddenColumns.process">
                    <x-ark-details-box :title="trans('server.process')" shallow>
                        <x-slot name="iconRaw">
                            <x-server.status-icon
                                :type="$server->statusIcon()"
                                :icon-color="$server->isOffline() ? 'text-theme-secondary-900' : null"
                                :tooltip="$server->statusTooltip()"
                            />
                        </x-slot>

                        <p class="text-theme-secondary-500">
                            @lang('server.actions.manager')
                        </p>
                    </x-ark-details-box>
                </template>
            @else
                @if($server->hasCore())
                    <template x-if="! hiddenColumns.process">
                        <x-ark-details-box :title="trans('server.process')" shallow>
                            <x-slot name="iconRaw">
                                <x-server.status-icon
                                    :type="$server->core()->statusIcon()"
                                    :tooltip="$server->core()->statusTooltip()"
                                />
                            </x-slot>

                            @lang('server.types.core')
                        </x-ark-details-box>
                    </template>
                @else
                    @if($server->hasForger())
                        <template x-if="! hiddenColumns.process">
                            <x-ark-details-box :title="trans('server.process')" shallow>
                                <x-slot name="iconRaw">
                                    <x-server.status-icon
                                        :type="$server->forger()->statusIcon()"
                                        :tooltip="$server->forger()->statusTooltip()"
                                    />
                                </x-slot>

                                @lang('server.types.forger')
                            </x-ark-details-box>
                        </template>
                    @endif

                    @if($server->hasRelay())
                        <template x-if="! hiddenColumns.process">
                            <x-ark-details-box :title="trans('server.process')" shallow>
                                <x-slot name="iconRaw">
                                    <x-server.status-icon :type="$server->relay()->statusIcon()"
                                                        :tooltip="$server->relay()->statusTooltip()"/>
                                </x-slot>

                                @lang('server.types.relay')
                            </x-ark-details-box>
                        </template>
                    @endif
                @endif
            @endif
        </div>
    </div>
</div>
