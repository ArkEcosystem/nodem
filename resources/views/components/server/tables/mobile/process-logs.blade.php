@props ([
    'process',
    'logs',
    'server',
    'source',
])

<div class="my-8 space-y-8 lg:hidden">
    <div class="flex">
        <div class="flex items-center">
            <button
                type="button"
                class="inline-block cursor-pointer text-theme-primary-500 hover:text-theme-primary-400"
                data-tippy-hover="@lang('pages.server.logs.download_logs')"
                @click="window.livewire.emit('showDownloadLogsModal', '{{ $process }}')"
                x-data
            >
                <x-ark-icon name="arrows.arrow-down-bracket" />
            </button>
        </div>

        <div class="flex items-center pl-3 ml-3 border-l border-theme-secondary-300">
            <livewire:logs-search
                key="{{ $source }}-{{ $process }}-search-mobile"
                :process="$process"
                left
            />
        </div>

        <div class="flex items-center pl-3 ml-3 border-l border-theme-secondary-300">
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
    </div>

    @if(count($logs) > 0)
        <div class="flex flex-col space-y-6 divide-y divide-theme-secondary-300">
            @foreach ($logs as $log)
                <div class="flex flex-col pt-6 space-y-4 first:pt-0" wire:key="mobile-{{ $process }}-log-{{ $log->id() }}">
                    <div class="flex text-sm font-semibold text-theme-secondary-500">
                        <span class="flex items-center">{{ $log->date() }}</span>
                        <span class="flex items-center pl-3 ml-3 border-l border-theme-secondary-300">{{ $log->time() }}</span>
                        <span class="flex items-center pl-3 ml-3 border-l border-theme-secondary-300">
                            <x-server.tables.mobile.status :type="$log->level()" />
                        </span>
                    </div>

                    <div class="break-words">
                        {{ $log->message() }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
