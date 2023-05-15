<?php

declare(strict_types=1);

use App\Models\ResourceIndicator;
use App\Models\Server;
use App\Support\PerformanceCharts;
use Carbon\Carbon;

beforeEach(fn () => $this->travelTo(Carbon::parse('2021-08-20 12:17')));

// Daily...

it("can get server's resource indicators in the last 24 hours", function () {
    $server = Server::factory()->create([
        'ram_total'  => toKilobyte(2),
        'disk_total' => toKilobyte(20),
        'cpu_total'  => 100,
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-20 12:00'),
        'cpu'        => 36,
        'disk'       => toKilobyte(7),
        'ram'        => toKilobyte(1.6),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-20 11:00'),
        'cpu'        => 42,
        'disk'       => toKilobyte(10),
        'ram'        => toKilobyte(1.5),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-20 11:30'),
        'cpu'        => 75,
        'disk'       => toKilobyte(13),
        'ram'        => toKilobyte(1.8),
    ]);

    $data = PerformanceCharts::dailyIndicators($server);

    // Assert structure...
    $data->every(function ($record) {
        expect($record)->toBeArray();

        expect($record)->tohaveKeys([
            'date', 'ram', 'cpu', 'disk',
        ]);

        expect($record['date'])->toBeInstanceOf(Carbon::class);
    });

    // Assert dates...
    expect($data)->toHaveCount(25); // 24 hours + now
    $periods = $data->pluck('date');
    expect($periods->first()->toDateTimeString())->toBe('2021-08-19 12:00:00');

    foreach ($periods as $index => $period) {
        expect(
            $period->eq($periods->first()->toImmutable()->addHour($index)) // span by an hour...
        )->toBeTrue();
    }

    expect($periods->last()->toDateTimeString())->toBe('2021-08-20 12:00:00');

    // Assert value...
    $last = $data->last();
    expect($last['cpu'])->toBe(36.0);
    expect($last['ram'])->toBe(round(1.6 * 100 / 2, 2));
    expect($last['disk'])->toBe(round(7 * 100 / 20, 2));

    $oneBeforeLast = $data[23];
    expect($oneBeforeLast['cpu'])->toBe(58.5);
    expect($oneBeforeLast['ram'])->toBe(round(1.65 * 100 / 2, 2));
    expect($oneBeforeLast['disk'])->toBe(round(11.5 * 100 / 20, 2));

    $data->splice(2)->every(fn ($record) => $record['cpu'] === null && $record['disk'] === null && $record['ram'] === null);
});

// Weekly...

it("can get server's resource indicators in the last week", function () {
    $server = Server::factory()->create([
        'ram_total'  => toKilobyte(2),
        'disk_total' => toKilobyte(20),
        'cpu_total'  => 100,
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-20 12:00'),
        'cpu'        => 36,
        'disk'       => toKilobyte(7),
        'ram'        => toKilobyte(1.6),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-19 11:00'),
        'cpu'        => 42,
        'disk'       => toKilobyte(10),
        'ram'        => toKilobyte(1.5),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-19 11:30'),
        'cpu'        => 75,
        'disk'       => toKilobyte(13),
        'ram'        => toKilobyte(1.8),
    ]);

    $data = PerformanceCharts::weeklyIndicators($server);

    // Assert structure...
    $data->every(function ($record) {
        expect($record)->toBeArray();

        expect($record)->tohaveKeys([
            'date', 'ram', 'cpu', 'disk',
        ]);

        expect($record['date'])->toBeInstanceOf(Carbon::class);
    });

    // Assert dates...
    expect($data)->toHaveCount(8); // 7 days + now
    $periods = $data->pluck('date');
    expect($periods->first()->toDateTimeString())->toBe('2021-08-13 00:00:00');

    foreach ($periods as $index => $period) {
        expect(
            $period->eq($periods->first()->toImmutable()->addDay($index)) // span by day...
        )->toBeTrue();
    }

    expect($periods->last()->toDateTimeString())->toBe('2021-08-20 00:00:00');

    // Assert value...
    $last = $data->last();
    expect($last['cpu'])->toBe(36.0);
    expect($last['ram'])->toBe(round(1.6 * 100 / 2, 2));
    expect($last['disk'])->toBe(round(7 * 100 / 20, 2));

    $oneBeforeLast = $data[6];
    expect($oneBeforeLast['cpu'])->toBe(58.5);
    expect($oneBeforeLast['ram'])->toBe(round(1.65 * 100 / 2, 2));
    expect($oneBeforeLast['disk'])->toBe(round(11.5 * 100 / 20, 2));

    $data->splice(2)->every(fn ($record) => $record['cpu'] === null && $record['disk'] === null && $record['ram'] === null);
});

// Monthly...

it("can get server's resource indicators in the last month", function () {
    $server = Server::factory()->create([
        'ram_total'  => toKilobyte(2),
        'disk_total' => toKilobyte(20),
        'cpu_total'  => 100,
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-20 12:00'),
        'cpu'        => 36,
        'disk'       => toKilobyte(7),
        'ram'        => toKilobyte(1.6),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-19 11:00'),
        'cpu'        => 42,
        'disk'       => toKilobyte(10),
        'ram'        => toKilobyte(1.5),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-19 11:30'),
        'cpu'        => 75,
        'disk'       => toKilobyte(13),
        'ram'        => toKilobyte(1.8),
    ]);

    $data = PerformanceCharts::monthlyIndicators($server);

    // Assert structure...
    $data->every(function ($record) {
        expect($record)->toBeArray();

        expect($record)->tohaveKeys([
            'date', 'ram', 'cpu', 'disk',
        ]);

        expect($record['date'])->toBeInstanceOf(Carbon::class);
    });

    // Assert dates...
    expect($data)->toHaveCount(32); // 31 days + now
    $periods = $data->pluck('date');
    expect($periods->first()->toDateTimeString())->toBe('2021-07-20 00:00:00');

    foreach ($periods as $index => $period) {
        expect(
            $period->eq($periods->first()->toImmutable()->addDay($index)) // span by day...
        )->toBeTrue();
    }

    expect($periods->last()->toDateTimeString())->toBe('2021-08-20 00:00:00');

    // Assert value...
    $last = $data->last();
    expect($last['cpu'])->toBe(36.0);
    expect($last['ram'])->toBe(round(1.6 * 100 / 2, 2));
    expect($last['disk'])->toBe(round(7 * 100 / 20, 2));

    $oneBeforeLast = $data[30];
    expect($oneBeforeLast['cpu'])->toBe(58.5);
    expect($oneBeforeLast['ram'])->toBe(round(1.65 * 100 / 2, 2));
    expect($oneBeforeLast['disk'])->toBe(round(11.5 * 100 / 20, 2));

    $data->splice(2)->every(fn ($record) => $record['cpu'] === null && $record['disk'] === null && $record['ram'] === null);
});

// Yearly...

it("can get server's resource indicators in the last year", function () {
    $server = Server::factory()->create([
        'ram_total'  => toKilobyte(2),
        'disk_total' => toKilobyte(20),
        'cpu_total'  => 100,
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-08-20 12:00'),
        'cpu'        => 36,
        'disk'       => toKilobyte(7),
        'ram'        => toKilobyte(1.6),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-07-15 11:00'),
        'cpu'        => 42,
        'disk'       => toKilobyte(10),
        'ram'        => toKilobyte(1.5),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-07-19 11:30'),
        'cpu'        => 75,
        'disk'       => toKilobyte(13),
        'ram'        => toKilobyte(1.8),
    ]);

    $data = PerformanceCharts::yearlyIndicators($server);

    // Assert structure...
    $data->every(function ($record) {
        expect($record)->toBeArray();

        expect($record)->tohaveKeys([
            'date', 'ram', 'cpu', 'disk',
        ]);

        expect($record['date'])->toBeInstanceOf(Carbon::class);
    });

    // Assert dates...
    expect($data)->toHaveCount(13); // 12 months + now
    $periods = $data->pluck('date');
    expect($periods->first()->toDateTimeString())->toBe('2020-08-01 00:00:00');

    foreach ($periods as $index => $period) {
        expect(
            $period->eq($periods->first()->toImmutable()->addMonth($index)) // span by month...
        )->toBeTrue();
    }

    expect($periods->last()->toDateTimeString())->toBe('2021-08-01 00:00:00');

    // Assert value...
    $last = $data->last();
    expect($last['cpu'])->toBe(36.0);
    expect($last['ram'])->toBe(round(1.6 * 100 / 2, 2));
    expect($last['disk'])->toBe(round(7 * 100 / 20, 2));

    $oneBeforeLast = $data[11];
    expect($oneBeforeLast['cpu'])->toBe(58.5);
    expect($oneBeforeLast['ram'])->toBe(round(1.65 * 100 / 2, 2));
    expect($oneBeforeLast['disk'])->toBe(round(11.5 * 100 / 20, 2));

    $data->splice(2)->every(fn ($record) => $record['cpu'] === null && $record['disk'] === null && $record['ram'] === null);
});

// All time...

it("can get server's resource indicators in the all time", function () {
    $server = Server::factory()->create([
        'ram_total'  => toKilobyte(2),
        'disk_total' => toKilobyte(20),
        'cpu_total'  => 100,
        'created_at' => Carbon::parse('2019-12-10'),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2020-05-07 12:00'),
        'cpu'        => 36,
        'disk'       => toKilobyte(7),
        'ram'        => toKilobyte(1.6),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-07-15 11:00'),
        'cpu'        => 42,
        'disk'       => toKilobyte(10),
        'ram'        => toKilobyte(1.5),
    ]);

    ResourceIndicator::forceCreate([
        'server_id'  => $server->id,
        'created_at' => Carbon::parse('2021-07-19 11:30'),
        'cpu'        => 75,
        'disk'       => toKilobyte(13),
        'ram'        => toKilobyte(1.8),
    ]);

    $data = PerformanceCharts::allTimeIndicators($server);

    // Assert structure...
    $data->every(function ($record) {
        expect($record)->toBeArray();

        expect($record)->tohaveKeys([
            'date', 'ram', 'cpu', 'disk',
        ]);

        expect($record['date'])->toBeInstanceOf(Carbon::class);
    });

    // Assert dates...
    expect($data)->toHaveCount(3); // 2019, 2020, 2021
    $periods = $data->pluck('date');

    // Assert value...
    expect($data[0]['date']->toDateTimeString())->toBe('2019-01-01 00:00:00');
    expect($data[0]['cpu'])->toBeNull();
    expect($data[0]['ram'])->toBeNull();
    expect($data[0]['disk'])->toBeNull();

    expect($data[1]['date']->toDateTimeString())->toBe('2020-01-01 00:00:00');
    expect($data[1]['cpu'])->toBe(36.0);
    expect($data[1]['ram'])->toBe(round(1.6 * 100 / 2, 2));
    expect($data[1]['disk'])->toBe(round(7 * 100 / 20, 2));

    expect($data[2]['date']->toDateTimeString())->toBe('2021-01-01 00:00:00');
    expect($data[2]['cpu'])->toBe(58.5);
    expect($data[2]['ram'])->toBe(round(1.65 * 100 / 2, 2));
    expect($data[2]['disk'])->toBe(round(11.5 * 100 / 20, 2));
});
