<div class="hidden mb-12 md:block">
    <div class="flex flex-col mt-8 space-y-8 table-container content-container">
        <table>
            <thead>
                <tr>
                    <x-ark-tables.header class="text-left" name="server.name" x-show="! hiddenColumns.name" />
                    <x-ark-tables.header name="server.core" responsive x-show="! hiddenColumns.core_ver" />
                    <x-ark-tables.header name="server.provider" x-show="! hiddenColumns.provider" />
                    <x-ark-tables.header class="text-left" name="server.ip_address" x-show="! hiddenColumns.ip" />
                    <x-ark-tables.header class="text-left" name="server.processes" last-on="xl" x-show="! hiddenColumns.process" />
                    <x-ark-tables.header class="text-left" name="server.usage" breakpoint="xl" responsive x-show="! hiddenColumns.usage" />
                    <x-ark-tables.header name="server.ping" breakpoint="xl" responsive x-show="! hiddenColumns.ping" />
                    <x-ark-tables.header name="server.height" last-on="full" breakpoint="xl" responsive x-show="! hiddenColumns.height" />
                    <x-ark-tables.header />
                </tr>
            </thead>
            <tbody>
                @foreach($servers as $server)
                    <livewire:server-list-item :model="$server->model()" show-as="row" :wire:key="'row-'.$server->id().'-'.Str::random(10)" />
                @endforeach
            </tbody>
        </table>
    </div>

    @if($servers->count() === 0)
        <div class="mt-8">
            <x-ark-no-results :text="trans('pages.home.no_servers')" exclude-dark />
        </div>
    @endif
</div>
