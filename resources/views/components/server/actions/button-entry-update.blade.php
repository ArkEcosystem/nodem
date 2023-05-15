@props([
    'server',
    'disabled' => false,
])

<x-dropdown
    button-class="flex justify-between items-center space-x-2 w-full sm:w-auto focus:outline-none transition-default button-secondary"
    dropdown-property="actionOpen"
    :close-on-click="false"
    placement="bottom"
    @dropdown-update.window="update"
    @dropdown-close.window="close"
    :disabled="$disabled"
    :transition-ease="false"
>
    @slot('button')
        <div>@lang('server.actions.update')</div>

        <div
            :class="{ 'rotate-180 text-theme-primary-600': actionOpen }"
            class="block transition-default"
        >
            @if ($server->isUpdating())
                <div>
                    <x-loader-icon />
                </div>
            @else
                <x-ark-icon
                    name="arrows.chevron-down-small"
                    size="xs"
                />
            @endif
        </div>
    @endslot

    <div class="block justify-center items-center py-3">
        @foreach([ServerTypeEnum::CORE, ServerTypeEnum::CORE_MANAGER] as $type)
            <x-server.actions.consolidated.update-button
                :type="$type"
                :server="$server"
                disabled-style="text-theme-secondary-700"
                class="flex items-center py-4 px-10 space-x-2 w-full font-semibold"
            />
        @endforeach
    </div>
</x-ark-dropdown>

