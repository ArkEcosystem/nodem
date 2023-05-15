<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Server;
use App\Support\DatePeriod;
use App\Support\PerformanceCharts;
use Closure;
use DateTime;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

final class ServerPerformance extends Component
{
    public Server $server;

    public string $device;

    public string $selectedConfiguration = 'ram';

    public function render() : View
    {
        return view('livewire.server-performance', [
            'configurations' => $this->configurationOptions(),
            'charts'         => $this->metrics(),
            'periods'        => trans('pages.server.performance.chart.periods'),
        ]);
    }

    /**
     * Generate the chart X axis ticks for the time period.
     *
     * @param string $period
     *
     * @return array
     */
    public function ticks(string $period) : array
    {
        // Last 24 hours... 23:00, 00:00, 01:00, etc
        if ($period === 'day') {
            return DatePeriod::day()
                        ->map(fn ($date, $index) => $index === 24 ? 'Now' : $date->format('H:i'))
                        ->toArray();
        }

        // Last 7 days... 29.11, 30.11, 01.12, etc
        if ($period === 'week') {
            return DatePeriod::week()
                        ->map(fn ($date) => $date->isToday() ? 'Today' : $date->format('l'))
                        ->toArray();
        }

        // Last month... 29.11, 30.11, 01.12, etc
        if ($period === 'month') {
            return DatePeriod::month()
                        ->map(fn ($date) => $date->isToday() ? 'Today' : $date->format('d.m'))
                        ->toArray();
        }

        // Last 365 days... Feb, Mar, Apr
        if ($period === 'year') {
            return DatePeriod::year()
                        ->map
                        ->format('M')
                        ->toArray();
        }

        // Since server was created...
        if ($period === 'all') {
            return DatePeriod::allTime($this->server)
                        ->map
                        ->format('Y')
                        ->toArray();
        }

        return [];
    }

    /**
     * Generate available configuration options for the performance chart.
     *
     * @return array
     */
    private function configurationOptions() : array
    {
        $viewModel = $this->server->toViewModel();

        return [
            [
                'type'              => 'ram',
                'circleColor'       => 'warning-500',
                'currentPercentage' => $viewModel->ramPercentage(),
                'progressColor'     => 'theme-color-warning-500',
            ],
            [
                'type'              => 'cpu',
                'circleColor'       => 'hint-600',
                'currentPercentage' => $viewModel->cpuPercentage(),
                'progressColor'     => 'theme-color-hint-600',
            ],
            [
                'type'              => 'disk',
                'circleColor'       => 'info-600',
                'currentPercentage' => $viewModel->diskPercentage(),
                'progressColor'     => 'theme-color-info-600',
            ],
        ];
    }

    /**
     * Generate the chart payload.
     *
     * @param string                         $resource
     * @param string                         $period
     * @param \Illuminate\Support\Collection $data
     *
     * @return array
     */
    private function chart(string $resource, string $period, Collection $data) : array
    {
        return [
            'labels'   => $this->ticks($period),
            'datasets' => $data->pluck($resource),
        ];
    }

    /**
     * Generate all of the metrics for the server.
     *
     * @return array
     */
    private function metrics() : array
    {
        [$daily, $weekly, $monthly, $yearly, $allTime] = [
            $this->cache('day', now()->addMinutes(5), fn () => PerformanceCharts::dailyIndicators($this->server)),
            $this->cache('week', now()->addMinutes(15), fn () => PerformanceCharts::weeklyIndicators($this->server)),
            $this->cache('month', now()->addMinutes(30), fn () => PerformanceCharts::monthlyIndicators($this->server)),
            $this->cache('year', now()->addHour(), fn () => PerformanceCharts::yearlyIndicators($this->server)),
            $this->cache('all-time', now()->addHours(2), fn () => PerformanceCharts::allTimeIndicators($this->server)),
        ];

        return collect(['ram', 'cpu', 'disk'])->mapWithKeys(fn (string $metric) : array => [
            $metric => [
                'day'   => $this->chart($metric, 'day', $daily),
                'week'  => $this->chart($metric, 'week', $weekly),
                'month' => $this->chart($metric, 'month', $monthly),
                'year'  => $this->chart($metric, 'year', $yearly),
                'all'   => $this->chart($metric, 'all', $allTime),
            ],
        ])->toArray();
    }

    /**
     * Cache the chart results for the server.
     *
     * @param string    $key
     * @param \DateTime $ttl
     * @param \Closure  $callback
     *
     * @return \Illuminate\Support\Collection
     */
    private function cache(string $key, DateTime $ttl, Closure $callback) : Collection
    {
        return cache()->remember(
            sprintf('charts:%s:%s', $this->server->id, $key),
            $ttl,
            $callback
        );
    }
}
