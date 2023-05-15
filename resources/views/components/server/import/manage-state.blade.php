@props([
    'index',
    'server',
    'exists',
    'isSelected',
])

<span
    @if($exists)
        data-tippy-content="@lang('pages.import-servers.manage-import.messages.duplicated_server')"
    @elseif($server->pingFailed())
        data-tippy-content="@lang('pages.import-servers.manage-import.messages.cannot_connect_to_server')"
    @endif
>
    @if($exists)
        <x-ark-icon class="text-theme-warning-600" name="circle.exclamation-mark" />
    @elseif($server->pingIsPending())
        <x-loader-icon />
    @elseif($server->pingFailed())
        <x-ark-icon class="text-theme-danger-400" name="circle.cross" />
    @else
        <x-checkbox
            :checked="$isSelected"
            wire:click="selectServer({{ $index }})"
        />
    @endif
</span>
