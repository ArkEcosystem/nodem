<div class="hidden justify-between items-center lg:flex">
    <div class="flex items-center space-x-4">
        <x-ark-icon :name="$server->providerIcon()" class="text-theme-secondary-900" size="xl" />

        <div class="flex pr-8 space-x-8">
            <x-ark-details-box :title="trans('server.provider')" shallow>
                <div class="text-lg">
                    @lang('server.providers.'.$server->provider())
                </div>
            </x-ark-details-box>

            <x-ark-details-box :title="trans('server.ip_address')" shallow>
                <div class="text-lg">
                    {{ $server->hostShort() }}
                </div>
            </x-ark-details-box>

            <x-ark-details-box :title="trans('server.ping')" shallow>
                <div class="text-lg">
                    @if($server->isManagerRunning())
                        {{ $server->ping() }}ms
                    @else
                        <span class="text-theme-secondary-500">@lang('server.not_available')</span>
                    @endif
                </div>
            </x-ark-details-box>

            <x-ark-details-box :title="trans('server.height')" shallow>
                <div class="text-lg">
                    @if($server->isManagerRunning())
                        <x-number>{{ $server->height() }}</x-number>
                    @else
                        <span class="text-theme-secondary-500">@lang('server.not_available')</span>
                    @endif
                </div>
            </x-ark-details-box>

            <x-ark-details-box :title="trans('server.manager_version')" shallow>
                <div class="text-lg">
                    @if($server->coreManagerCurrentVersion() !== '')
                        {{ $server->coreManagerCurrentVersion() }}
                    @else
                        <span class="text-theme-secondary-500">@lang('server.not_available')</span>
                    @endif
                </div>
            </x-ark-details-box>
        </div>
    </div>

    <x-server.header.desktop-processes :server="$server" />
</div>
