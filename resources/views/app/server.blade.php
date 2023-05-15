@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        @livewire('trigger-server-action')

        <x-server.header.header :server="$server" />

        <div class="flex flex-col py-8 content-container">
            @if ($server->isNotAvailable() || ! $server->processTypeIsInline() || $server->hasHeightMismatch())
                <div class="space-y-4">
                    @if ($server->isNotAvailable())
                        <x-ark-alert
                            :type="$server->isOffline() ? 'error' : 'warning'"
                            :message="$server->statusTooltip()"
                        />
                    @elseif (! $server->processTypeIsInline())
                        <x-ark-alert
                            type="warning"
                            :message="trans('pages.add-server-modal.server_process_type_error')"
                        />
                    @elseif($server->hasHeightMismatch())
                        <x-ark-alert
                            type="warning"
                            :message="trans('server.tooltips.server_height_mismatch')"
                        />
                    @endif
                </div>
            @endif

            <livewire:server-logs :server="$server->model()" />
        </div>

        <livewire:delete-server-modal />
        <livewire:edit-server-modal />
        <livewire:bip38-password-modal />
        <livewire:download-logs-modal :server="$server->model()" />

        @foreach ($server->model()->processes as $process)
            <livewire:filter-logs-modal
                :server="$server->model()"
                :process="$process->type"
            />
        @endforeach
    @endsection

@endcomponent
