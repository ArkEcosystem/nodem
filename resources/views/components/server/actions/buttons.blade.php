<div
    x-data="{ dropdownOpen: false }"
    class="grid flex-shrink-0 grid-cols-2 gap-3 sm:flex sm:gap-0 sm:items-center sm:space-x-3"
>
    @can(TeamMemberPermission::CORE_UPDATE)
        <x-server.actions.button-entry-update
            :disabled="!$server->canUpdate() || $server->isNotAvailable() || ! $server->processTypeIsInline() || $server->isUpdating()"
            :server="$server"
        />
    @endcan

    @canany([
        TeamMemberPermission::SERVER_PROCESSES_START,
        TeamMemberPermission::SERVER_PROCESSES_STOP,
        TeamMemberPermission::SERVER_PROCESSES_RESTART,
    ])
        @can(TeamMemberPermission::SERVER_PROCESSES_START)
            <x-server.actions.button-entry
                :disabled="$server->isNotAvailable() || ! $server->processTypeIsInline() || $server->isUpdating()"
                :server="$server"
                action="start"
            />
        @endcan

        @can(TeamMemberPermission::SERVER_PROCESSES_STOP)
            <x-server.actions.button-entry
                :disabled="$server->isNotAvailable() || ! $server->processTypeIsInline() || $server->isUpdating()"
                :server="$server"
                action="stop"
            />
        @endcan

        @can(TeamMemberPermission::SERVER_PROCESSES_RESTART)
            <x-server.actions.button-entry
                :disabled="$server->isNotAvailable() || ! $server->processTypeIsInline() || $server->isUpdating()"
                :server="$server"
                action="restart"
            />
        @endcan
    @endcanany
</div>
