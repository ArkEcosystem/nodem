<div class="hidden md:block">
    <div class="flex flex-col table-container">
        <table>
            <thead>
                <tr>
                    <x-ark-tables.header name="general.date" class="w-32 text-left" />
                    <x-ark-tables.header name="general.time" class="w-32 text-left" />
                    <x-ark-tables.header name="general.user" class="text-left"  />
                    <x-ark-tables.header name="pages.server.logs.message_action" class="text-left" />
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <x-ark-tables.row wire:key="log-{{ $log->id() }}">
                        <x-ark-tables.cell>
                            {{ $log->date() }}
                        </x-ark-tables.cell>

                        <x-ark-tables.cell>
                            {{ $log->time() }}
                        </x-ark-tables.cell>

                        <x-ark-tables.cell>
                            {{ $log->userName() }}
                        </x-ark-tables.cell>

                        <x-ark-tables.cell class="text-left">
                            {{ $log->description() }}
                        </x-ark-tables.cell>
                    </x-ark-tables.row>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        @if(count($logs) > 0)
            <x-pagination :results="$logs" />
        @else
            <x-ark-no-results
                :text="trans('pages.server.empty_activity_logs', [trans('general.activity')])"
                exclude-dark
            />
        @endif
    </div>
</div>
