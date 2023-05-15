<div
    x-data="{ tableView: @entangle('tableView') }"
    x-init="$watch('tableView', value => $dispatch('updated-table-view', value))"
    class="flex items-center"
>
    <x-ark-tables.view-options
        :disabled="$disabled"
        selected-classes="text-theme-primary-600 disabled:text-theme-secondary-300 disabled:pointer-events-none"
        unselected-classes="text-theme-primary-400 hover:text-theme-primary-500 disabled:text-theme-secondary-300 disabled:pointer-events-none"
    />
</div>
