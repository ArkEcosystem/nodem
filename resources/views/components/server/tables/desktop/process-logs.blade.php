@props ([
    'process',
    'logs',
    'server',
    'source',
])

<x-ark-tables.table class="hidden lg:block">
    <thead>
        <x-ark-tables.row>
            <x-ark-tables.header name="general.date" class="w-32 text-left" />
            <x-ark-tables.header name="general.time" class="w-32 text-left" />
            <x-ark-tables.header name="general.type" class="w-32 text-left" />
            <x-ark-tables.header name="general.message" class="text-left" last-on="full" />
            <x-ark-tables.header class="w-12">
                <div class="flex items-center">
                    <button
                        type="button"
                        class="inline-block cursor-pointer focus-visible:rounded focus-visible:ring-offset-2 text-theme-primary-500 hover:text-theme-primary-400"
                        data-tippy-hover="@lang('pages.server.logs.download_logs')"
                        @click="window.livewire.emit('showDownloadLogsModal', '{{ $process }}')"
                        x-data
                    >
                        <x-ark-icon name="arrows.arrow-down-bracket" />
                    </button>
                </div>
            </x-ark-tables.header>

            <x-ark-tables.header class="w-12">
                <div class="flex justify-center items-center h-full">
                    <livewire:logs-search
                        key="{{ $source }}-{{ $process }}-search-desktop"
                        :process="$process"
                    />
                </div>
            </x-ark-tables.header>

            <x-ark-tables.header class="w-12">
                <div class="flex justify-center items-center h-full">
                    <button
                        type="button"
                        class="inline-block cursor-pointer focus-visible:rounded focus-visible:ring-offset-2 text-theme-primary-500 hover:text-theme-primary-400"
                        data-tippy-hover="@lang('general.filter')"
                        @click="window.livewire.emit('showFilterLogsModal:{{ $process }}')"
                        x-data
                    >
                        <x-ark-icon name="sliders-vertical" />
                    </button>
                </div>
            </x-ark-tables.header>
        </x-ark-tables.row>
    </thead>
    <tbody>
        @foreach($logs as $log)
            <x-ark-tables.row>
                <x-ark-tables.cell class="table-cell-height-full">
                    <div class="h-full whitespace-nowrap">
                        {{ $log->date() }}
                    </div>
                </x-ark-tables.cell>

                <x-ark-tables.cell class="table-cell-height-full">
                    <div class="h-full whitespace-nowrap">
                        {{ $log->time() }}
                    </div>
                </x-ark-tables.cell>

                <x-server.tables.desktop.status :type="$log->level()" />

                <x-ark-tables.cell class="text-left" colspan="4">
                    <div class="break-words">{{ $log->message() }}</div>
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
