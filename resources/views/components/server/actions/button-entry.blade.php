@props([
    'action',
    'server',
    'disabled' => false,
])

<x-server.actions.consolidated.dropdown
    :server="$server"
    :single-action="$action"
>
    <x-slot name="button" class="flex justify-between items-center space-x-2 w-full sm:w-auto focus:outline-none transition-default button-secondary">
        <div>@lang('server.actions.'.$action)</div>

        <div
            :class="{ 'rotate-180 text-theme-primary-600': serverActionsOpen }"
            class="block transition-default"
        >
            <x-ark-icon name="arrows.chevron-down-small" size="xs" />
        </div>
    </x-slot>
</x-server.actions.consolidated.dropdown>
