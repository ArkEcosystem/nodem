<div class="md:hidden">
    <div class="flex flex-col mt-8 space-y-8 divide-y divide-theme-secondary-300">
        @foreach ($logs as $log)
            <div class="flex flex-col pt-8 space-y-4 first:pt-0" wire:key="mobile-log-{{ $log->id() }}">
                <x-ark-details-box-mobile :title="trans('general.date')" title-class="w-40">
                    {{ $log->date() }}
                </x-ark-details-box-mobile>

                <x-ark-details-box-mobile :title="trans('general.time')" title-class="w-40">
                    {{ $log->time() }}
                </x-ark-details-box-mobile>

                <x-ark-details-box-mobile :title="trans('general.user')" title-class="w-40">
                    {{ $log->userName() }}
                </x-ark-details-box-mobile>

                <x-ark-details-box-mobile :title="trans('pages.server.logs.message_action')" title-class="w-40">
                    {{ $log->description() }}
                </x-ark-details-box-mobile>
            </div>
        @endforeach
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
