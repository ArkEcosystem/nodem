<div
    @if($this->hasPendingServers)
        wire:poll.500ms="updatePingState"
    @endif
>
    <div>
        <h2 class="mb-4">@lang('pages.import-servers.manage-import.title')</h2>
        <span class="leading-7 text-theme-scondary-700">@lang('pages.import-servers.manage-import.description')</span>
    </div>

    <div>
        @if($this->hasServersWithError)
            <x-ark-alert type="warning" class="mt-4">
                @lang('pages.import-servers.manage-import.messages.problem_when_trying_to_import')
            </x-ark-alert>
        @endif
    </div>

    <div class="hidden mb-12 md:block" >
        <div class="flex flex-col mt-8 space-y-8 table-container">
            <table>
                <thead>
                    <tr>
                        <x-ark-tables.header class="text-left" name="server.name" />
                        <x-ark-tables.header name="server.provider" />
                        <x-ark-tables.header class="text-right" name="server.ip_address" />
                        <x-ark-tables.header class="justify-center">
                            <span>
                                <x-checkbox
                                    :checked="$this->hasAllServersSelected()"
                                    wire:click="toggleAllServers"
                                />
                            </span>
                        </x-ark-tables.header>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->temporaryServers as $index => $server)
                        @php
                            $exists = $this->servers[$index]['exists'];
                            $isSelected = $this->isSelected($index);
                        @endphp

                        <x-ark-tables.row class="{{ $isSelected ? 'selected-row' : '' }}">
                            <x-ark-tables.cell
                                class="max-w-0 text-lg font-semibold min-w-60 text-theme-primary-600"
                            >
                                <div class="truncate">
                                    <span class="text-lg text-theme-primary-600">
                                        {{ $server->name() }}
                                    </span>
                                </div>
                            </x-ark-tables.cell>

                            <x-ark-tables.cell>
                                <x-ark-icon class="mx-auto w-9 h-9" name="{{ $server->providerIcon() }}" />
                            </x-ark-tables.cell>

                            <x-ark-tables.cell class="text-right">
                                {{ $server->hostShort() }}
                            </x-ark-tables.cell>

                            <x-ark-tables.cell>
                                <x-server.import.manage-state
                                    :index="$index"
                                    :server="$server"
                                    :exists="$exists"
                                    :is-selected="$isSelected"
                                />
                            </x-ark-tables.cell>
                        </x-ark-tables.row>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="block mt-12 md:hidden">
        <div class="flex flex-col w-full">
            <div class="flex justify-end w-auto">
                <span>
                    <x-checkbox
                        :checked="$this->hasAllServersSelected()"
                        wire:click="toggleAllServers"
                        label-class="pr-2 font-semibold text-theme-secondary-500"
                        with-label
                        right
                    >@lang('actions.select_all')</x-checkbox>
                </span>
            </div>

            <hr class="mt-3 mb-3 border border-theme-secondary-300">

            @foreach($this->temporaryServers as $index => $server)
                @php
                    $exists = $this->servers[$index]['exists'];
                    $isSelected = $this->isSelected($index);
                @endphp

                <div class="flex flex-col py-4 px-3 -mx-3 space-y-4 {{ $isSelected ? 'bg-theme-success-50' : '' }}">
                    <div class="flex justify-end w-full" >
                        <x-server.import.manage-state
                            :index="$index"
                            :server="$server"
                            :exists="$exists"
                            :is-selected="$isSelected"
                        />
                    </div>

                    <x-ark-details-box-mobile
                        :title="trans('server.name')"
                        title-class="font-semibold leading-7 whitespace-nowrap text-theme-secondary-500"
                        wrapper-class="min-w-0"
                        slot-class="ml-3 truncate"
                    >
                        {{ $server->name() }}
                    </x-ark-details-box-mobile>

                    <x-ark-details-box-mobile
                        :title="trans('server.provider')"
                        title-class="font-semibold leading-7 text-theme-secondary-500"
                    >
                        <x-ark-icon class="mx-auto" name="{{ $server->providerIcon() }}" size="md" />
                    </x-ark-details-box-mobile>

                    <x-ark-details-box-mobile
                        :title="trans('server.ip')"
                        title-class="font-semibold leading-7 text-theme-secondary-500"
                    >
                        {{ $server->hostShort() }}
                    </x-ark-details-box-mobile>
                </div>

                @if(! $loop->last)
                    <hr class="my-0.5 border-dashed border-theme-secondary-300">
                @endif
            @endforeach
        </div>
    </div>

    <div class="flex flex-col-reverse mt-11 w-full sm:flex-row sm:justify-between">
        <div class="flex">
            <button
                class="flex justify-center w-full sm:w-auto button-secondary"
                type="button"
                wire:loading.attr="disabled"
                wire:click="retry"
                @if (! $this->hasServersWithError) disabled @endif
            >
                <x-ark-icon class="inline my-auto mr-2 flip-horizontally" name="arrows.arrow-rotate-left" />
                @lang('actions.retry')
            </button>
        </div>
        <div class="flex mb-3 space-x-3 sm:mb-0">
            <button
                type="button"
                class="w-full sm:w-auto button-secondary"
                wire:click="resetWizard"
            >
                @lang('actions.back')
            </button>

            {{--TODO: Disable unless everything is properly imported and without errors --}}
            <button
                type="button"
                class="w-full sm:w-auto button-primary"
                wire:click="goToNextStep"
                {{ count($this->selectedServers) > 0 ? '' : 'disabled' }}
            >
                @lang('actions.continue')
            </button>
        </div>
    </div>
</div>
