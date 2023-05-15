<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class PerformanceCharts
{
    public static function dailyIndicators(Server $server) : Collection
    {
        return static::periodBasedQuery(
            $server,
            'hour',
            Carbon::now()->startOfHour()->subDay(),
            DatePeriod::day()
        );
    }

    public static function weeklyIndicators(Server $server) : Collection
    {
        return static::periodBasedQuery(
            $server,
            'day',
            Carbon::now()->subWeek(),
            DatePeriod::week()
        );
    }

    public static function monthlyIndicators(Server $server) : Collection
    {
        return static::periodBasedQuery(
            $server,
            'day',
            Carbon::now()->subMonth(),
            DatePeriod::month()
        );
    }

    public static function yearlyIndicators(Server $server) : Collection
    {
        return static::periodBasedQuery(
            $server,
            'month',
            Carbon::now()->subYear(),
            DatePeriod::year()
        );
    }

    public static function allTimeIndicators(Server $server) : Collection
    {
        /** @var \Carbon\Carbon */
        $date = $server->created_at;

        return static::periodBasedQuery(
            $server,
            'year',
            $date,
            DatePeriod::allTime($server)
        );
    }

    private static function periodBasedQuery(Server $server, string $groupBy, Carbon $olderThan, Collection $period) : Collection
    {
        $indicators = DB::table('resource_indicators')
                        ->selectRaw("
                            date_trunc('".$groupBy."', created_at) as date,
                            round(avg(cpu), 2) as cpu,
                            round(avg(ram), 0) as ram,
                            round(avg(disk), 0) as disk
                        ")
                        ->whereNotNull('server_id')
                        ->where('server_id', $server->id)
                        ->where('created_at', '<=', Carbon::now())
                        ->where('created_at', '>=', $olderThan)
                        ->groupBy('date')
                        ->get()
                        ->map(fn ($indicator) : array => [
                            'date' => Carbon::parse($indicator->date),
                            'cpu'  => round(($indicator->cpu * 100) / (int) $server->cpu_total, 2),
                            'ram'  => round(($indicator->ram * 100) / (int) $server->ram_total, 2),
                            'disk' => round(($indicator->disk * 100) / (int) $server->disk_total, 2),
                        ]);

        return $period->map(function ($date) use ($indicators) : array {
            return $indicators->first(fn ($indicator) : bool => $indicator['date']->eq($date)) ?? [
                'date' => $date,
                'cpu'  => null,
                'ram'  => null,
                'disk' => null,
            ];
        });
    }
}
