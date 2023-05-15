<div
    @if ($server->isOnPendingState())
    class="px-8 md:hidden"
    wire:poll
    @endif
>
    <x-ark-accordion
        class="rounded-lg border md:hidden border-theme-info-100"
        button-class="py-4 px-5"
        content-class="px-5"
        title-class="flex-1 w-full min-w-0"
        :border="false"
    >
        <x-slot name="title">
            <div class="flex justify-between items-center pr-4 mr-4 space-x-4 min-w-0 border-r border-theme-secondary-300">
                <a href="{{ route('server', $server->id()) }}" class="font-semibold truncate text-theme-primary-600" @click.stop>
                    {{ $server->name() }}
                </a>

                @if(($server->isLoadingProcesses() || $server->isLoadingManagerState()) && !$server->isSilentLoading())
                    <div x-show="! openPanel">
                        <x-loader-icon />
                    </div>
                @elseif($server->isManagerRunning())
                    @if($server->hasCore())
                        <div x-show="! openPanel">
                            <x-server.status-icon :type="$server->core()->statusIcon()" />
                        </div>
                    @else
                        @if($server->hasForger() || $server->hasRelay())
                            <div class="flex space-x-2" x-show="! openPanel">
                                @if($server->hasForger())
                                    <x-server.status-icon :type="$server->forger()->statusIcon()" />
                                @endif

                                @if($server->hasRelay())
                                    <x-server.status-icon :type="$server->relay()->statusIcon()" />
                                @endif
                            </div>
                        @endif
                    @endif
                @else
                    <div x-show="! openPanel">
                        <x-server.status-icon :type="$server->statusIcon()" />
                    </div>
                @endif
            </div>
        </x-slot>

        <div class="flex flex-col pb-4 space-y-4">
            <x-ark-details-box-mobile :title="trans('server.process')" shallow>
                <div class="flex flex-col space-y-2">
                    @if(($server->isLoadingProcesses() || $server->isLoadingManagerState()) && !$server->isSilentLoading())
                        <div class="flex justify-end space-x-2">
                            <span class="text-theme-secondary-500">
                                @lang('general.loading')
                            </span>

                            <x-loader-icon />
                        </div>
                    @elseif($server->isNotAvailable())
                        <div class="flex justify-end space-x-2">
                            <span class="text-theme-secondary-500">
                                @lang('server.actions.manager')
                            </span>

                            <x-server.status-icon
                                :type="$server->statusIcon()"
                                :icon-color="$server->isOffline() ? 'text-theme-secondary-900' : null"
                                :tooltip="$server->statusTooltip()"
                            />
                        </div>
                    @else
                        @if($server->hasCore())
                            <div class="flex justify-end space-x-2">
                                <span>@lang('server.types.core')</span>

                                <x-server.status-icon :type="$server->core()->statusIcon()" :tooltip="$server->core()->statusTooltip()" />
                            </div>
                        @else
                            @if($server->hasForger())
                                <div class="flex justify-end space-x-2">
                                    <span>@lang('server.types.forger')</span>

                                    <x-server.status-icon :type="$server->forger()->statusIcon()" :tooltip="$server->forger()->statusTooltip()" />
                                </div>
                            @endif

                            @if($server->hasRelay())
                                <div class="flex justify-end space-x-2">
                                    <span>@lang('server.types.relay')</span>

                                    <x-server.status-icon :type="$server->relay()->statusIcon()" :tooltip="$server->relay()->statusTooltip()" />
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile :title="trans('server.core_version')">
                <x-server.version :server="$server" />
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile :icon="$server->providerIcon()" icon-class="text-theme-secondary-900" :title="trans('server.provider')">
                @lang('server.providers.'.$server->provider())
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile :title="trans('server.ip')">
                {{ $server->hostShort() }}
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile icon="app-server.disk" icon-class="text-theme-info-600" :title="trans('server.disk')">
                @if($server->isManagerRunning())
                    <x-percentage>{{ $server->diskPercentage() }}</x-percentage>
                @else
                    <span class="text-theme-secondary-500">@lang('server.not_available')</span>
                @endif
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile icon="app-server.ram" icon-class="text-theme-success-600" :title="trans('server.ram')">
                @if($server->isManagerRunning())
                    <x-percentage>{{ $server->ramPercentage() }}</x-percentage>
                @else
                    <span class="text-theme-secondary-500">@lang('server.not_available')</span>
                @endif
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile icon="app-server.cpu" icon-class="text-theme-hint-600" :title="trans('server.cpu')">
                @if($server->isManagerRunning())
                    <x-percentage>{{ $server->cpuPercentage() }}</x-percentage>
                @else
                    <span class="text-theme-secondary-500">@lang('server.not_available')</span>
                @endif
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile :title="trans('server.ping')">
                @if($server->isManagerRunning())
                    {{ $server->ping() }}ms
                @else
                    <span class="text-theme-secondary-500">@lang('server.not_available')</span>
                @endif
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile :title="trans('server.type')" shallow>
                @if($server->hasRelay())
                    @lang('server.types.relay')
                @endif

                @if($server->hasForger())
                    @lang('server.types.forger')
                @endif
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile :title="trans('server.height')">
                @if($server->isManagerRunning())
                    <x-number>{{ $server->height() }}</x-number>
                @else
                    <span class="text-theme-secondary-500">@lang('server.not_available')</span>
                @endif
            </x-ark-details-box-mobile>

            <x-server.actions.buttons :server="$server" />
        </div>
    </x-ark-accordion>
</div>
