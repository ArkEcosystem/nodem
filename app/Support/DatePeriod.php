<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Server;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

final class DatePeriod
{
    public static function day() : Collection
    {
        return collect(CarbonPeriod::create(
            Carbon::now()->startOfHour()->subHours(24),
            '1 hour',
            Carbon::now()->startOfHour()
        ));
    }

    public static function week() : Collection
    {
        return collect(CarbonPeriod::create(
            Carbon::now()->startOfDay()->subWeek(),
            '1 day',
            Carbon::now()->startOfDay()
        ));
    }

    public static function month() : Collection
    {
        return collect(CarbonPeriod::create(
            Carbon::now()->startOfDay()->subMonth(),
            '1 day',
            Carbon::now()->startOfDay()
        ));
    }

    public static function year() : Collection
    {
        return collect(CarbonPeriod::create(
            Carbon::now()->startOfMonth()->subYear(),
            '1 month',
            Carbon::now()->startOfMonth()
        ));
    }

    /**
     * Generate the period of Carbon instances for the period that the server was created.
     *
     * @param \App\Models\Server $server
     *
     * @return \Illuminate\Support\Collection
     */
    public static function allTime(Server $server) : Collection
    {
        /** @var \Carbon\Carbon */
        $date = $server->created_at;

        return collect(CarbonPeriod::create(
            $date->startOfYear(),
            '1 year',
            Carbon::now()->startOfYear()
        ));
    }
}
