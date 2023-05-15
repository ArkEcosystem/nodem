<x-ark-dropdown
    wrapper-class="inline-block sm:relative filter-button"
    dropdown-classes="left-0 z-10 mx-8 sm:mx-auto sm:w-137 lg:left-auto lg:right-0"
    dropdown-origin-class="origin-top-left lg:origin-top-right"
    dropdown-property="filterOpen"
    :close-on-click="false"
    button-class="inline cursor-pointer focus-visible:rounded focus-visible:ring-offset-2 text-theme-primary-500 hover:text-theme-primary-400"
    button-class-expanded="text-theme-primary-400"
>
    @slot('button')
        <div data-tippy-hover="@lang('pages.server.logs.search_logs')">
            <x-ark-icon name="magnifying-glass" />
        </div>
    @endslot

    <div class="flex items-center p-10 py-6 px-8">
        <input
            type="text"
            placeholder="@lang('pages.server.logs.search.placeholder')"
            class="hidden w-full sm:block"
            wire:model.debounce.750ms="state.term"
        />

        <input
            type="text"
            placeholder="@lang('pages.server.logs.search.placeholder_mobile')"
            class="w-full sm:hidden"
            wire:model.debounce.750ms="state.term"
        />

        <button
            type="button"
            class="cursor-pointer text-theme-secondary-600 hover:text-theme-secondary-500"
            wire:click="performSearch"
        >
            <x-ark-icon name="magnifying-glass" />
        </button>
    </div>
</x-ark-dropdown>
