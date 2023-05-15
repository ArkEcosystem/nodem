<div class="hidden justify-center items-center w-full h-32 rounded-2xl border-2 cursor-pointer lg:flex" :class="{{ $isSelected }}">
    <x-server.progress-arc
        identifier="{{ $type }}-{{ Str::random(8) }}-desktop"
        radius="55"
        stroke="12"
        progress="{{ $currentPercentage ?? '0' }}"
        progress-color="{{ $progressColor }}"
    >
        <div class="flex flex-col items-center">
            <div class="mt-10 font-semibold">
                <span class="text-2xl">{{ ceil($currentPercentage) ?? '0' }}</span><span class="text-sm">%</span>
            </div>

            <span class="mt-4 text-sm font-semibold">@lang("server.{$type}")</span>
        </div>
    </x-server.progress-arc>
</div>

<div class="flex flex-shrink-0 justify-center items-center w-full h-36 rounded-2xl border-2 cursor-pointer lg:hidden" :class="{{ $isSelected }}">
    <x-server.progress-arc
        identifier="{{ $type }}-{{ Str::random(8) }}-mobile"
        radius="55"
        stroke="12"
        progress="{{ $currentPercentage ?? '0' }}"
        progress-color="{{ $progressColor }}"
    >
        <div class="flex flex-col items-center">
            <div class="mt-12 font-semibold">
                <span class="text-2xl">{{ ceil($currentPercentage) ?? '0' }}</span><span class="text-sm">%</span>
            </div>

            <span class="mt-4 text-sm font-semibold">@lang("server.{$type}")</span>
        </div>
    </x-server.progress-arc>
</div>
