<div class="bg-white rounded-lg border lg:hidden border-theme-info-100">
    <x-ark-dropdown
        wrapper-class="relative z-0 server-table-mobile"
        dropdown-classes="w-full relative shadow-none border-0 px-5"
        button-class="py-4 px-5 w-full"
        :close-on-click="false"
        :close-on-blur="false"
    >
        @slot('button')
            <div class="flex justify-between items-center py-0.5 my-px space-x-4 w-full">
                <div class="font-semibold text-theme-secondary-900">@lang('pages.server.information')</div>

                <div class="py-2 pl-4 h-full border-l border-theme-secondary-300 text-theme-secondary-900">
                    <span
                        :class="{ 'rotate-180 text-theme-primary-600': dropdownOpen }"
                        class="block"
                    >
                        <x-ark-icon name="arrows.chevron-down-small" size="sm" />
                    </span>
                </div>
            </div>
        @endslot

        <div class="flex flex-col pb-4 space-y-4">
            <x-ark-details-box-mobile :title="trans('server.process')">
                <div class="space-y-3 font-semibold">
                    @if(($server->isLoadingProcesses() || $server->isLoadingManagerState()) && !$server->isSilentLoading())
                        <div class="flex items-center space-x-2">
                            <x-loader-icon />

                            <div class="text-theme-secondary-500">@lang('general.loading')</div>
                        </div>
                    @elseif(!$server->isManagerRunning())
                        <div class="flex items-center space-x-2">
                            <x-server.status-icon :type="$server->statusIcon()" />

                            <div>@lang('server.actions.manager')</div>
                        </div>
                    @else
                        @if($server->hasCore())
                            <div class="flex items-center space-x-2">
                                <x-server.status-icon
                                    :type="$server->core()->statusIcon()"
                                    :tooltip="$server->core()->statusTooltip()"
                                />

                                <div>@lang('server.types.core')</div>
                            </div>
                        @else
                            @if($server->hasForger())
                                <div class="flex items-center space-x-2">
                                    <x-server.status-icon
                                        :type="$server->forger()->statusIcon()"
                                        :tooltip="$server->forger()->statusTooltip()"
                                    />

                                    <div>@lang('server.types.forger')</div>
                                </div>
                            @endif

                            @if($server->hasRelay())
                                <div class="flex items-center space-x-2">
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
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile
                :icon="$server->providerIcon()"
                icon-class="text-theme-secondary-900"
                :title="trans('server.provider')"
            >
                <span class="font-semibold">
                    @lang('server.providers.'.$server->provider())
                </span>
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile :title="trans('server.ip_address')">
                <span class="font-semibold">
                    {{ $server->hostShort() }}
                </span>
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile :title="trans('server.ping')">
                <span class="font-semibold">
                    {{ $server->ping() }}ms
                </span>
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile :title="trans('server.height')">
                <span class="font-semibold">
                    <x-number>{{ $server->height() }}</x-number>
                </span>
            </x-ark-details-box-mobile>

            <x-ark-details-box-mobile :title="trans('server.manager_version')">
                <span class="font-semibold">
                    @if($server->coreManagerCurrentVersion() !== '')
                        {{ $server->coreManagerCurrentVersion() }}
                    @else
                        <span class="text-theme-secondary-500">@lang('server.not_available')</span>
                    @endif
                </span>
            </x-ark-details-box-mobile>
        </div>
    </x-ark-dropdown>
</div>
