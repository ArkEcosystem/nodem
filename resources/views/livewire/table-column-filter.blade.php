<div>
    <x-ark-dropdown
        wrapper-class="filter-button"
        button-class="hidden md:inline focus-visible:rounded focus-visible:ring-offset-2 disabled:pointer-events-none text-theme-primary-400 hover:text-theme-primary-500 focus-visible:ring-offset-theme-secondary-100 disabled:text-theme-secondary-300"
        dropdown-classes="left-6 w-full z-10 sm:w-56"
        dropdown-property="filterOpen"
        :close-on-click="false"
        :height="(37 * (count($columns) - 1)) + 21 + 88"
        :disabled="$disabled"
    >
        @slot('button')
            <div data-tippy-hover="@lang('general.column_filter')" class="py-2 px-3">
                <x-ark-icon name="sliders-vertical" />
            </div>
        @endslot

        <div class="p-10 space-y-8">
            <div class="space-y-4">
                @foreach ($columns as $column => $visible)
                    <x-checkbox
                        :checked="$this->isColumnVisible($column)"
                        alpine="$wire.toggleColumn('{{ $column }}')"
                        id="toggle-column-{{ $column }}"
                        label-class="items-center pl-2 text-sm text-base leading-5 cursor-pointer text-theme-secondary-700"
                        with-label
                    >
                        @lang('column.' . $column)
                    </x-checkbox>
                @endforeach
            </div>
        </div>
    </x-ark-dropdown>
</div>
