@props(['server'])

<div class="flex space-x-5 divide-x-2 divide-theme-secondary-200">
    @if(($server->isLoadingProcesses() || $server->isLoadingManagerState()) && !$server->isSilentLoading())
        <x-ark-details-box
            :title="trans('server.process')"
            icon-wrapper-class="bg-none"
            reverse
            no-border
        >
            <x-slot name="iconRaw">
                <x-loader-icon size="w-6 h-6" />
            </x-slot>

            <div class="text-lg">
                <div class="text-theme-secondary-500">
                    @lang('general.loading')
                </div>
            </div>
        </x-ark-details-box>
    @elseif($server->isManagerRunning())
        @if($server->hasCore())
            <x-ark-details-box
                :title="trans('server.process')"
                icon-wrapper-class="bg-none"
                reverse
                no-border
            >
                <x-slot name="iconRaw">
                    <x-server.status-icon
                        :type="$server->core()->statusIcon()"
                        :tooltip="$server->core()->statusTooltip()"
                        large
                        large-size="xl"
                    />
                </x-slot>

                <div class="text-lg">
                    @lang('server.types.core')
                </div>
            </x-ark-details-box>
        @else
            @if($server->hasForger())
                <x-ark-details-box
                    :title="trans('server.process')"
                    icon-wrapper-class="bg-none"
                    reverse
                    no-border
                >
                    <x-slot name="iconRaw">
                        <x-server.status-icon
                            :type="$server->forger()->statusIcon()"
                            :tooltip="$server->forger()->statusTooltip()"
                            large
                            large-size="xl"
                        />
                    </x-slot>

                    <div class="text-lg">
                        @lang('server.types.forger')
                    </div>
                </x-ark-details-box>
            @endif

            @if($server->hasRelay())
                <x-ark-details-box
                    :title="trans('server.process')"
                    icon-wrapper-class="bg-none"
                    class="pl-5"
                    reverse
                    no-border
                >
                    <x-slot name="iconRaw">
                        <x-server.status-icon
                            :type="$server->relay()->statusIcon()"
                            :tooltip="$server->relay()->statusTooltip()"
                            large
                            large-size="xl"
                        />
                    </x-slot>

                    <div class="text-lg">
                        @lang('server.types.relay')
                    </div>
                </x-ark-details-box>
            @endif
        @endif
    @else
        <x-ark-details-box
            :title="trans('server.process')"
            icon-wrapper-class="bg-none"
            reverse
            no-border
        >
            <x-slot name="iconRaw">
                <x-server.status-icon large large-size="xl" :type="$server->statusIcon()" />
            </x-slot>

            <div class="text-lg xl:text-2xl">
                <div class="text-lg xl:text-2xl text-theme-secondary-500">
                    @lang('server.actions.manager')
                </div>
            </div>
        </x-ark-details-box>
    @endif
</div>
