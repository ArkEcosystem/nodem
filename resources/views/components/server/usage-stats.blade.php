<div class="flex flex-col space-y-1 text-xs">
    <div class="flex space-x-2">
        <div class="overflow-hidden w-16 whitespace-nowrap">
            @lang('server.cpu')
            @if($server->isManagerRunning())
                <x-short-percentage>{{ $server->cpuPercentage() }}</x-short-percentage>
            @else
                <span class="text-theme-secondary-500">@lang('server.not_available')</span>
            @endif
        </div>

        <div class="w-24">
            <x-server.progress-linear
                :disabled="$server->isNotAvailable()"
                :percentage="$server->cpuPercentage()"
                color="bg-theme-hint-600"
            />
        </div>
    </div>

    <div class="flex space-x-2">
        <div class="overflow-hidden w-16 whitespace-nowrap">
            @lang('server.ram')
            @if($server->isManagerRunning())
                <x-short-percentage>{{ $server->ramPercentage() }}</x-short-percentage>
            @else
                <span class="text-theme-secondary-500">@lang('server.not_available')</span>
            @endif
        </div>

        <div class="w-24">
            <x-server.progress-linear
                :disabled="$server->isNotAvailable()"
                :percentage="$server->ramPercentage()"
                color="bg-theme-success-600"
            />
        </div>
    </div>

    <div class="flex space-x-2">
        <div class="overflow-hidden w-16 whitespace-nowrap">
            @lang('server.disk')
            @if($server->isManagerRunning())
                <x-short-percentage>{{ $server->diskPercentage() }}</x-short-percentage>
            @else
                <span class="text-theme-secondary-500">@lang('server.not_available')</span>
            @endif
        </div>

        <div class="overflow-hidden w-24">
            <x-server.progress-linear
                :disabled="$server->isNotAvailable()"
                :percentage="$server->diskPercentage()"
                color="bg-theme-info-600"
            />
        </div>
    </div>
</div>
