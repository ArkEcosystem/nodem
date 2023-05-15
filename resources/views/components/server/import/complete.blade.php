<div>
    <div>
        <h2 class="mb-4">@lang('pages.import-servers.complete-import.title')</h2>
        <span class="leading-7 text-theme-scondary-700">@lang('pages.import-servers.complete-import.description')</span>
    </div>

    <div class="hidden mb-12 md:block">
        <div class="flex flex-col mt-8 space-y-8 table-container">
            <table id="import-complete">
                <thead>
                    <tr>
                        <x-ark-tables.header class="text-left" name="server.name" />
                        <x-ark-tables.header name="server.provider" />
                        <x-ark-tables.header class="text-right" name="server.ip_address" />
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->temporarySelectedServers as $server)
                        <x-ark-tables.row wire:key="import-complete-server-list-{{ $server->name() }}-desktop">
                            <x-ark-tables.cell
                                class="max-w-0 text-lg font-semibold min-w-60 text-theme-primary-600"
                                wire:key="import-complete-server-{{ $server->name() }}-name-desktop"
                            >
                                <div class="truncate">
                                    <span class="text-lg text-theme-primary-600">
                                        {{ $server->name() }}
                                    </span>
                                </div>
                            </x-ark-tables.cell>

                            <x-ark-tables.cell wire:key="import-complete-server-{{ $server->name() }}-provider-desktop">
                                <x-ark-icon class="mx-auto w-9 h-9" name="{{ $server->providerIcon() }}" />
                            </x-ark-tables.cell>

                            <x-ark-tables.cell wire:key="import-complete-server-{{ $server->name() }}-ip-desktop" class="text-right">
                                {{ $server->hostShort() }}
                            </x-ark-tables.cell>
                        </x-ark-tables.row>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="block mt-8 md:hidden">
        <div class="flex flex-col">
            @foreach($this->temporarySelectedServers as $server)
                <div class="flex flex-col pb-4 space-y-4 @if(! $loop->last) border-b border-theme-secondary-300 border-dashed mb-4 @endif" wire:key="import-complete-box-{{ $server->name() }}-mobile">
                    <x-ark-details-box-mobile
                        :title="trans('server.name')"
                        title-class="font-semibold leading-7 whitespace-nowrap text-theme-secondary-500"
                        wire:key="import-complete-server-{{ $server->name() }}-name-mobile"
                        wrapper-class="min-w-0"
                        slot-class="ml-3 truncate"
                    >
                        {{ $server->name() }}
                    </x-ark-details-box-mobile>

                    <x-ark-details-box-mobile
                        :title="trans('server.provider')"
                        title-class="font-semibold leading-7 text-theme-secondary-500"
                        wire:key="import-complete-server-{{ $server->name() }}-provider-mobile"
                    >
                        <x-ark-icon class="mx-auto" name="{{ $server->providerIcon() }}" size="md" />
                    </x-ark-details-box-mobile>

                    <x-ark-details-box-mobile
                        :title="trans('server.ip')"
                        title-class="font-semibold leading-7 text-theme-secondary-500"
                        wire:key="import-complete-server-{{ $server->name() }}-ip-mobile"
                    >
                        {{ $server->hostShort() }}
                    </x-ark-details-box-mobile>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex justify-end mt-11 w-full">
        <button
            type="button"
            class="w-full sm:w-auto button-secondary"
            wire:click="redirectHome"
        >
            @lang('actions.back_to_dashboard')
        </button>
    </div>
</div>
