@props(['tableView'])

<x-page-header :title="trans('pages.home.title')" has-slot>
    <div class="flex items-stretch mt-6 w-full lg:mt-0 lg:w-auto">
        <livewire:toggle-view-option :table-view="$tableView" />

        <div class="flex flex-row-reverse justify-between w-full md:flex-row md:justify-start md:ml-6">
            <div class="flex relative items-center pl-4 border-l md:pr-4 border-theme-secondary-300">
                <livewire:refresh-button />

                <livewire:table-column-filter
                    :columns="[
                        'provider',
                        'usage',
                        'ping',
                        'height',
                    ]"
                />
            </div>

            <div class="flex flex-row-reverse">
                @can(TeamMemberPermission::SERVER_ADD)
                    <div>
                        <div class="flex justify-start md:pl-6 md:space-x-3 md:border-l border-theme-secondary-300">
                            @can (TeamMemberPermission::SERVER_CONFIGURATION_IMPORT)
                                <a href="{{ route('servers.import') }}" class="hidden md:inline">
                                    <div
                                        class="flex items-center space-x-2 w-full button-secondary"
                                        wire:key="import-modal-button"
                                    >
                                        <x-ark-icon name="arrows.arrow-turn-down-bracket" size="sm" />

                                        <span class="hidden md:block">
                                            @lang('actions.import')
                                        </span>
                                    </div>
                                </a>
                            @endcan

                            @can (TeamMemberPermission::SERVER_CONFIGURATION_EXPORT)
                                <livewire:export-modal />
                            @endcan

                            <button
                                class="w-full whitespace-nowrap button-primary"
                                wire:click="$emit('showAddServerModal')"
                            >
                                @lang('actions.add_server')
                            </button>
                        </div>
                    </div>
                @endcan

                <x-dropdown
                    button-class="flex mr-3 w-11 h-11 md:hidden button-icon"
                    dropdown-property="serverActionsOpen"
                    :close-on-click="true"
                    on-closed="(dropdown) => {
                        dropdown.querySelectorAll('.accordion-open .accordion-trigger').forEach(e => e.click());
                    }"
                    @dropdown-update.window="update"
                    @dropdown-close.window="close"
                >
                    @slot('button')
                        <div class="flex relative justify-center">
                            <x-ark-icon name="ellipsis-vertical" class="m-2" />
                        </div>
                    @endslot

                    <div class="block justify-center items-center">
                        @can (TeamMemberPermission::SERVER_CONFIGURATION_IMPORT)
                            <a href="{{ route('servers.import') }}" class="inline md:hidden">
                                <div
                                    class="flex items-center py-4 px-10 space-x-2 w-full font-semibold cursor-pointer md:hidden text-theme-secondary-900 hover:bg-theme-secondary-100 hover:text-theme-primary-500"
                                    wire:key="import-modal-button"
                                >
                                    <x-ark-icon name="arrows.arrow-turn-down-bracket" size="sm" />

                                    <span>@lang('actions.import')</span>
                                </div>
                            </a>
                        @endcan

                        @can (TeamMemberPermission::SERVER_CONFIGURATION_EXPORT)
                            <button
                                class="flex items-center py-4 px-10 space-x-2 w-full font-semibold cursor-pointer md:hidden text-theme-secondary-900 hover:bg-theme-secondary-100 hover:text-theme-primary-500"
                                type="button"
                                wire:click="$emit('triggerExportModal')"
                            >
                                <x-ark-icon
                                    name="arrows/arrow-up-turn-bracket"
                                    size="sm"
                                />

                                <span>@lang('pages.export-modal.button')</span>
                            </button>
                        @endcan
                    </div>
                </x-dropdown>
            </div>
        </div>
    </div>
</x-page-header>
