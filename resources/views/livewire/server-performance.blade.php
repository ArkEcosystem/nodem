@push('scripts')
<script src="{{ mix('js/swiper.js')}}"></script>

<script>
    window.showProgress = (identifier) => {
        return {
            renderProgress(progress) {
                if (progress > 0) {
                    const svgPath = document.getElementById(`progress-${identifier}`);

                    const path = new window.ProgressBar.Path(svgPath);

                    path.animate(progress / 100);
                }
            }
        };
    };
</script>
@endpush

<div
    x-data="{
        currentMetric: 'ram',
        periods: {},
        period: 'day',
    }"
>
    <div class="flex items-center py-4 px-5 mt-3 space-x-2 bg-white rounded-lg border md:hidden border-theme-info-100">
        <span class="font-semibold text-theme-secondary-500">{{ trans('pages.server.performance.chart.prefix') }}</span>

        <x-ark-rich-select
            class="w-full input-group"
            wrapper-class="left-0 pr-10 w-full"
            dropdown-class="right-0 mt-1 origin-top-right"
            initial-value="day"
            button-class="flex justify-between items-center w-full font-semibold text-left bg-transparent focus-visible:rounded text-theme-secondary-900"
            icon-class="-mr-10"
            :options="trans('pages.server.performance.chart.periods')"
            dispatch-event="chart-period-selected"
        />
    </div>

    <div class="flex flex-col-reverse w-full lg:flex-row lg:space-x-8">
        <x-server.charts.desktop.chart
            :identifier="$this->selectedConfiguration"
            :is-visible="true"
            color-scheme="#FFAE10"
            alpine-show="toggle-chart"
            :device="$device"
            :data="$charts"
            :periods="$periods"
            class="flex-1"
        />

        <div class="-mb-6 w-full md:mb-0 lg:w-40">
            <x-ark-slider
                id="configuration-boxes-{{ $device }}"
                hide-navigation
                delay-init
                :space-between="12"
                :breakpoints="[
                    '375' => [
                        'slidesPerGroup' => 2,
                        'slidesPerView' => 2,
                        'allowTouchMove' => true,
                    ],
                    '640' => [
                        'slidesPerGroup' => 3,
                        'slidesPerView' => 3,
                        'allowTouchMove' => false,
                    ],
                    '1024' => [
                        'slidesPerGroup' => 1,
                        'slidesPerView' => 1,
                        'slidesPerColumn' => 3,
                        'slidesPerColumnFill' => 'row',
                        'allowTouchMove' => false,
                        'grid' => [
                            'rows' => 3,
                            'fill' => 'row'
                        ],
                    ],
                ]"
            >
                @foreach ($configurations as $configuration)
                    <x-ark-slider-slide>
                        <button
                            type="button"
                            x-on:click="
                                currentMetric = '{{ $configuration['type'] }}';
                                $dispatch('chart-type-selected', currentMetric)
                            "
                            class="w-full chart-configuration-box"
                        >
                            <x-server.charts.configuration-box
                                :circle-color="$configuration['circleColor']"
                                :type="$configuration['type']"
                                :current-percentage="$configuration['currentPercentage']"
                                :progress-color="$configuration['progressColor']"
                                is-selected="currentMetric === '{{ $configuration['type'] }}' ? 'bg-theme-success-50 border-theme-success-600' : 'border-theme-primary-100'"
                            />
                        </button>
                    </x-ark-slider-slide>
                @endforeach
            </x-ark-slider>
        </div>
    </div>
</div>
