<div
    x-data="showProgress('{{ $identifier }}')"
    x-init="renderProgress('{{ $progress }}')"
    wire:key="{{ $key ?? Str::random(8) }}"
>
    <span
        class="relative flex items-center justify-center transition {{ $wrapperRotation ?? false }}"
        style="height: {{ $radius * 2 }}px; width: {{ $radius * 2 }}px;"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 141.6 128.5" class="absolute" width="100%" height="100%" style="overflow: visible;">
            <path
                d="M28.7 125.8c-30.4-23.2-36.2-66.7-13-97.1s66.7-36.2 97.1-13 36.2 66.7 13 97.1c-3.9 5.2-8.6 9.7-13.8 13.6"
                fill="transparent"
                stroke-width="3"
                style="stroke:var(--theme-color-primary-100)"
                stroke-linecap="round"
                fill-opacity="0"
            />
            @if($progress > 0)
                <path
                    id="progress-{{ $identifier }}"
                    d="M28.7 125.8c-30.4-23.2-36.2-66.7-13-97.1s66.7-36.2 97.1-13 36.2 66.7 13 97.1c-3.9 5.2-8.6 9.7-13.8 13.6"
                    fill="transparent"
                    style="stroke:var(--{{ $progressColor }})"
                    stroke-width="8"
                    stroke-linecap="round"
                    fill-opacity="0"
                />
            @endif
        </svg>
        {{ $slot }}
    </span>
</div>
