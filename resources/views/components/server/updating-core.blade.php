@props(['server'])

<span {{ $attributes }} data-tippy-content="{{ __('server.status.updating_core', ['version' => $server->coreLatestVersion() ]) }}">
    <x-loader-icon />
</span>
