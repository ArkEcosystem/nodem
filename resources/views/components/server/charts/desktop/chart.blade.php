@push('scripts')
<script>
/**
 * Create title & body DOM elements that are added in the root DOM element of the tooltip (wrapper).
 *
 * @param {Array} titleText
 * @param {Array} bodyText
 * @param {Object} alpineComponent
 * @return {Object}
 */
createTooltipText = (titleText, bodyText, alpineComponent) => {
    const title = document.createElement('div')
    titleText.forEach(line => {
        const span = document.createElement('span')
        span.appendChild(document.createTextNode(line))

        title.appendChild(span)
    })

    const body = document.createElement('div')
    bodyText.forEach((line, i) => {
        const span = document.createElement('span')
        span.appendChild(document.createTextNode(`${alpineComponent.metrics[alpineComponent.metric]}: ${line}%`))

        body.appendChild(span)
    })

    return { title, body }
}


/**
 * Create the root tooltip DOM node (wrapper).
 *
 * @param {Object} chart
 * @return {Object}
 */
createTooltip = chart => {
    let element = chart.canvas.parentNode.querySelector('#chart-tooltip')

    if (! element) {
        element = document.createElement('div')
        element.id = 'chart-tooltip'
        element.classList.add(
            'bg-white', 'text-xs', 'text-theme-secondary-700', 'font-semibold', 'rounded-lg',
            'text-black', 'shadow-lg', 'pointer-events-none', 'absolute', 'opacity-100'
        )

        const innerElement = document.createElement('div')
        innerElement.classList.add('space-y-2', 'p-2')

        element.appendChild(innerElement)
        chart.canvas.parentNode.appendChild(element)
    }

    return element
}


/**
 * Generate the external tooltip DOM structure.
 *
 * @param {Object} options
 * @return {Object}
 */
tooltipStructure = ({ chart, tooltip }) => {
    const element = createTooltip(chart)

    // Should hide the tooltip and annotations...
    if (tooltip.opacity === 0) {
        element.style.opacity = 0

        return
    }

    // Tooltip has text...
    if (tooltip.body) {
        const { title, body } = createTooltipText(
            tooltip.title || [],
            tooltip.body.map(b => b.lines),
            chart.options.alpine
        )

        const rootElement = element.querySelector('div')

        // Remove any existing texts in tooltip...
        while (rootElement.firstChild) {
            rootElement.firstChild.remove()
        }

        // Push new text to tooltip element...
        rootElement.appendChild(title)
        rootElement.appendChild(body)
    }

    const { offsetLeft: positionX, offsetTop: positionY } = chart.canvas

    // Reposition tooltip element...
    element.style.opacity = 1
    element.style.left = positionX + tooltip.caretX + 'px'
    element.style.top = positionY + tooltip.caretY + 'px'
    element.style.font = tooltip.options.bodyFont.string
    element.style.padding = tooltip.options.padding + 'px ' + tooltip.options.padding + 'px'
}


hex2rgb = (hex, opacity = 1) => {
    if (hex.startsWith('#')) {
        hex = hex.substring(1)
    }

    const bigint = parseInt(hex, 16)
    const r = (bigint >> 16) & 255
    const g = (bigint >> 8) & 255
    const b = bigint & 255

    return `rgba(${r}, ${g}, ${b}, ${opacity})`
}


lastTruthyElement = array => {
    let index = 0
    const cloned = array.slice().reverse()

    for (let item of cloned) {
        if (item !== null) {
            break
        }

        index++
    }

    return cloned.length - index - 1
}


make{{ $device }}Chart = (metric, colorScheme) => {
    return {
        isDarkTheme: false,
        chart: null,
        period: 'day',
        periods: @json($periods),
        metrics: {
            ram: 'RAM',
            cpu: 'CPU',
            disk: 'Disk',
        },
        metric,
        data: @json($data),
        colorScheme,
        themeColors: {
            light: {
                gridLines: '#DBDEE5',
                ticks: '#B0B0B8',
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 1)',
                    color: '#637282',
                }
            },

            dark: {
                gridLines: "#3C4249",
                ticks: "#7E8A9C",
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 1)',
                    color: '#637282',
                }
            },
        },


        isActivePeriod(period) {
            return period === this.period
        },


        /**
         * Get the 2D context of the chart DOM element.
         *
         * @return {Object}
         */
        context: () => document.querySelector('#performanceChart-{{ $device }}').getContext('2d'),


        /**
         * Retrieve the chart data (labels and dataset) for the current metric and the current period.
         *
         * @return {Array}
         */
        getData() {
            return this.data[this.metric][this.period]
        },


        /**
         * Generate the linear gradient for the background color of the chart.
         *
         * @return {Object}
         */
        gradient() {
            const gradient = this.context().createLinearGradient(0, 0, 0, 400)

            gradient.addColorStop(0, hex2rgb(this.colorScheme, 0.1))
            gradient.addColorStop(1, hex2rgb(this.colorScheme, 0))

            return gradient
        },


        /**
         * Get the current theme (color scheme).
         *
         * @return {Object}
         */
        theme() {
            return this.isDarkTheme
                    ? this.themeColors.dark
                    : this.themeColors.light
        },


        setPeriod(period) {
            if (this.chart) {
                this.period = period

                this.renderChart()
            }
        },


        setType(metric) {
            if (this.chart) {
                this.metric = metric

                const colors = {
                    ram: '#FFAE10',
                    cpu: '#5452CE',
                    disk: '#007DFF',
                }

                this.colorScheme = colors[this.metric]
                this.chart.data.datasets[0].borderColor = hex2rgb(this.colorScheme)
                this.chart.data.datasets[0].backgroundColor = this.gradient()

                this.renderChart()
            }
        },


        renderChart() {
            const datasets = this.getData().datasets;
            const index = lastTruthyElement(datasets);

            const options = {
                alpine: this,
                responsive: true,
                maintainAspectRatio: false,
                elements: {
                    line: { cubicInterpolationMode: 'default' },
                },
                scales: {
                    y: this.yAxisConfiguration(),
                    x: this.xAxisConfiguration(),
                },
                plugins: {
                    tooltip: {
                        enabled: false,
                        position: 'nearest',
                        external: tooltipStructure
                    },
                    legend: { display: false },
                    annotation: {
                        annotations: {
                            currentMark: {
                                type: 'line',
                                display: index >= 0,
                                yMin: datasets[index],
                                yMax: datasets[index],
                                borderColor: this.colorScheme,
                                borderWidth: 2,
                                borderDash: [5],
                                borderDashOffset: 3,
                            },
                        }
                    }
                }
            };

            if (this.chart) {
                options.animation = false;
            }

            this.rerenderCanvas();

            this.chart = new Chart(this.context(), {
                type: "line",
                data: {
                    labels: this.getData().labels,
                    datasets: [{
                        type: "line",
                        data: datasets,
                        fill: true,
                        hidden: false,
                        showLine: true,
                        backgroundColor: this.gradient(),
                        borderColor: this.colorScheme,
                        borderWidth: 3,
                        pointRadius: [...Array(datasets.length)].map((x, i) => i === index ? 4 : 0),
                        pointBorderWidth: 3,
                        pointHoverRadius: 10,
                        pointHoverBorderWidth: 3,
                        pointHoverBackgroundColor: "rgba(255, 255, 255, 1)",
                        pointHitRadius: 12,
                        pointBackgroundColor: "#FFFFFF",
                    }],
                },
                options,
            })
        },


        rerenderCanvas() {
            const wrapperElement = document.querySelector('#chart-wrapper-{{ $device }}')

            if (wrapperElement) {
                wrapperElement.remove()
            }

            let wrapper = document.createElement('div')
            wrapper.setAttribute('id', 'chart-wrapper-{{ $device }}')
            wrapper.style.height = '400px'
            wrapper.style.width = '100%'

            let canvas = document.createElement('canvas')
            canvas.setAttribute('id', 'performanceChart-{{ $device }}')

            wrapper.appendChild(canvas)
            document.querySelector('#chart-container-{{ $device }}').appendChild(wrapper)
        },


        /**
         * Generate the configuration for Y axis.
         *
         * @return {Object}
         */
        yAxisConfiguration() {
            const theme = this.theme()

            return {
                type: 'linear',
                position: 'right',
                stacked: true,
                grid: {
                    color: theme.gridLines,
                    display: true,
                    drawBorder: false,
                },
                suggestedMin: 0,
                suggestedMax: 100,
                max: 100,
                min: 0,
                ticks: {
                    padding: 15,
                    stepSize: 10,
                    color: theme.ticks,
                    font: {
                        size: 14,
                        weight: 600,
                    },
                    align: 'center',
                    callback: value => {
                        const label = `${value}%`

                        // Don't ask... hack to right-align ticks...
                        if (value === 0) {
                            return `    ${label}`
                        }

                        return value < 100 ? `  ${label}` : `${label}`
                    },
                }
            }
        },


        /**
         * Generate the configuration for X axis.
         *
         * @return {Object}
         */
        xAxisConfiguration() {
            const theme = this.theme()

            return {
                grid: {
                    color: theme.gridLines,
                    drawBorder: false,
                    display: true,
                },
                ticks: {
                    padding: 10,
                    color: theme.ticks,
                    font: {
                        size: 14,
                        weight: 600,
                    },
                    callback: (value, index, values) => {
                        value = this.getData().labels[index]

                        // Very small screens...
                        if (this.$root.clientWidth <= 600 && (this.period === 'day' || this.period === 'month')) {
                            return index % 4 ? null : value
                        }

                        if (this.$root.clientWidth <= 600 && this.period === 'year') {
                            return index % 3 ? null : value
                        }

                        if (this.$root.clientWidth <= 700 && this.period === 'week') {
                            return index % 2 ? null : value
                        }

                        // Slightly larger screens...
                        if (this.$root.clientWidth <= 700 && (this.period === 'day' || this.period === 'year')) {
                            return index % 3 ? null : value
                        }

                        if (this.$root.clientWidth <= 700 && this.period === 'month') {
                            return index % 4 ? null : value
                        }

                        // Rest...
                        if (this.period === 'day' || this.period === 'month') {
                            return index % 2 ? null : value
                        }

                        return value
                    },
                },
            }
        }
    }
}
</script>
@endpush

<div
    x-data="make{{ $device }}Chart('{{ $identifier }}', '{{ $colorScheme }}')"
    x-on:chart-period-selected.window="$nextTick(() => setPeriod($event.detail))"
    x-on:chart-type-selected.window="$nextTick(() => setType($event.detail))"
    x-on:show-performance-chart-{{ $device }}.window="renderChart('{{ $identifier }}')"
    id="chart-container-{{ $device }}"
    {{ $attributes->whereStartsWith('class')->class('w-full bg-white dark:border-black border-theme-secondary-100 dark:bg-theme-secondary-900') }}
>
    <div id="chart-wrapper-{{ $device }}" class="w-full" style="height: 400px;">
        <canvas id="performanceChart-{{ $device }}"></canvas>
    </div>
</div>
