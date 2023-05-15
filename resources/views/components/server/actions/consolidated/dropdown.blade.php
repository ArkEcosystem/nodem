@props([
    'server',
    'slot'                   => null,
    'buttonWidth'            => 'w-11',
    'notificationBackground' => 'bg-white',
    'button'                 => null,
    'singleAction'           => null,
])

@canany([
    TeamMemberPermission::SERVER_PROCESSES_START,
    TeamMemberPermission::SERVER_PROCESSES_RESTART,
    TeamMemberPermission::SERVER_PROCESSES_STOP,
    TeamMemberPermission::SERVER_PROCESSES_DELETE,
])
    @php
        $actions = [
            'start',
            'restart',
            'stop',
            'delete',
        ];
        if ($singleAction) {
            $actions = [$singleAction];
        }

        $disabledActions = [];
        if (in_array('start', $actions) && Auth::user()->cannot(TeamMemberPermission::SERVER_PROCESSES_START) || ! $server->processTypeIsInline() || $server->isUpdating() || $server->isManagerNotRunning()) {
            $disabledActions[] = 'start';
        }

        if (in_array('restart', $actions) && Auth::user()->cannot(TeamMemberPermission::SERVER_PROCESSES_RESTART) || ! $server->processTypeIsInline() || $server->isUpdating() || $server->isManagerNotRunning()) {
            $disabledActions[] = 'restart';
        }

        if (in_array('stop', $actions) && Auth::user()->cannot(TeamMemberPermission::SERVER_PROCESSES_STOP) || ! $server->processTypeIsInline() || $server->isUpdating() || $server->isManagerNotRunning()) {
            $disabledActions[] = 'stop';
        }

        if (in_array('delete', $actions) && Auth::user()->cannot(TeamMemberPermission::SERVER_PROCESSES_DELETE) || ! $server->processTypeIsInline() || $server->isUpdating() || $server->isManagerNotRunning()) {
            $disabledActions[] = 'delete';
        }

        $disableDropdown = true;

        if (
            // The disabled actions doesnt match all the actions
            (count($disabledActions) !== count($actions))
            // The user has permission to edit the server
            || Auth::user()->can(TeamMemberPermission::SERVER_EDIT)
            // The server can be updated
            || $server->canUpdate()
        ) {
            $disableDropdown = false;
        }
    @endphp

    <x-dropdown
        :button-class="Arr::toCssClasses([
            $button ? $button->attributes->get('class', 'h-11 button-icon') : 'h-11 button-icon',
        ])"
        dropdown-property="serverActionsOpen"
        :close-on-click="false"
        :placement="$singleAction ? 'bottom' : 'left-start'"
        on-closed="(dropdown) => {
            dropdown.querySelectorAll('.accordion-open .accordion-trigger').forEach(e => e.click());
        }"
        @dropdown-update.window="update"
        @dropdown-close.window="close"
        :disabled="$disableDropdown"
        :data-tippy-content="$disableDropdown ? $server->statusTooltip() : null"
        :transition-ease="false"
    >
        @slot('button')
            @if ($button && strlen($button) > 0)
                {{ $button }}
            @else
                <div class="flex relative justify-center {{ $buttonWidth }}">
                    <x-ark-icon name="ellipsis-vertical" class="m-2" />

                    @can(TeamMemberPermission::CORE_UPDATE)
                        @if($server->hasNewVersion())
                            <x-ark-notification-dot class="top-0 right-0 p-0.5 -mt-2 -mr-1 {{ $notificationBackground }}" />
                        @endif
                    @endcan
                </div>
            @endif
        @endslot

        <div class="block justify-center items-center">
            @if ($singleAction)
                @if ($server->prefersCombined())
                    <x-server.actions.action-processes
                        :action="$singleAction"
                        :server="$server"
                        :types="['core']"
                        class="flex items-center py-4 px-10 space-x-2 w-full font-semibold"
                    />
                @else
                    <x-server.actions.action-processes
                        :action="$singleAction"
                        :server="$server"
                        class="flex items-center py-4 px-10 space-x-2 w-full font-semibold"
                    />
                @endif
            @else
                @foreach($actions as $action)
                    @if ($server->prefersCombined())
                        <x-server.actions.consolidated.entry-core
                            :action="$action"
                            :server="$server"
                            :disabled="in_array($action, $disabledActions)"
                            :tooltip="$server->actionTooltip($action)"
                        />
                    @else
                        <x-server.actions.consolidated.entry
                            :action="$action"
                            :server="$server"
                            :disabled="in_array($action, $disabledActions)"
                            :tooltip="$server->actionTooltip($action)"
                        />
                    @endif
                @endforeach

                <div class="my-4 mx-10 border-t border-theme-secondary-300"></div>

                @can(TeamMemberPermission::SERVER_EDIT)
                    <button
                        type="button"
                        class="w-full focus-visible:rounded text-theme-secondary-900"
                        @click="
                            window.livewire.emit('triggerServerEdit', {{ $server->id() }});
                            close();
                        "
                    >
                        <div class="py-4 px-10 font-semibold text-left cursor-pointer hover:bg-theme-secondary-100">
                            <span>@lang('actions.edit')</span>
                        </div>
                    </button>
                @endcan

                <x-server.actions.consolidated.update-entry
                    :server="$server"
                    :tooltip="$server->actionTooltip('update')"
                />
            @endif
        </div>
    </x-dropdown>
@endcanany
