<?php

declare(strict_types=1);

use App\Models\Server;
use App\Support\DatePeriod;
use Carbon\Carbon;
use Illuminate\Support\Collection;

// Friday, Aug 20th
beforeEach(fn () => $this->travelTo(Carbon::parse('2021-08-20 12:17')));

it('generates an array of datetime instances for the last 24 hours', function () {
    $periods = DatePeriod::day();

    expect($periods)->toBeInstanceOf(Collection::class);
    expect($periods)->toHaveCount(25); // last 24 hours + now
    expect($periods->every(fn ($period) => $period instanceof Carbon))->toBeTrue();

    expect($periods->first()->toDateTimeString())->toBe('2021-08-19 12:00:00');

    $first = $periods->first()->toImmutable();

    foreach ($periods as $index => $period) {
        expect(
            $period->eq($first->addHour($index)) // span by an hour...
        )->toBeTrue();
    }

    expect($periods->last()->toDateTimeString())->toBe('2021-08-20 12:00:00');
});

it('generates an array of datetime instances for the last week', function () {
    $periods = DatePeriod::week();

    expect($periods)->toBeInstanceOf(Collection::class);
    expect($periods)->toHaveCount(8); // last 7 days + now
    expect($periods->every(fn ($period) => $period instanceof Carbon))->toBeTrue();

    // Today is Friday, so this is last Friday...
    $first = $periods->first()->toImmutable();
    expect($first->toDateTimeString())->toBe('2021-08-13 00:00:00');
    expect($first->isFriday())->toBeTrue();

    foreach ($periods as $index => $period) {
        expect(
            $period->eq($first->addDay($index)) // span by days...
        )->toBeTrue();
    }

    expect($periods->last()->toDateTimeString())->toBe('2021-08-20 00:00:00');
    expect($periods->last()->isFriday())->toBeTrue();
});

it('generates an array of datetime instances for the last month', function () {
    $periods = DatePeriod::month();

    expect($periods)->toBeInstanceOf(Collection::class);
    expect($periods)->toHaveCount(32); // last 31 days + now
    expect($periods->every(fn ($period) => $period instanceof Carbon))->toBeTrue();

    // Today is Friday, a month ago was Tuesday...
    $first = $periods->first()->toImmutable();
    expect($first->toDateTimeString())->toBe('2021-07-20 00:00:00');
    expect($first->isTuesday())->toBeTrue();

    foreach ($periods as $index => $period) {
        expect(
            $period->eq($first->addDay($index)) // span by days...
        )->toBeTrue();
    }

    expect($periods->last()->toDateTimeString())->toBe('2021-08-20 00:00:00');
    expect($periods->last()->isFriday())->toBeTrue();
});

it('generates an array of datetime instances for the last year', function () {
    $periods = DatePeriod::year();

    expect($periods)->toBeInstanceOf(Collection::class);
    expect($periods)->toHaveCount(13); // last 12 months + now
    expect($periods->every(fn ($period) => $period instanceof Carbon))->toBeTrue();

    // Today is Friday, a year ago today was Saturday...
    $first = $periods->first()->toImmutable();
    expect($first->toDateTimeString())->toBe('2020-08-01 00:00:00');
    expect($first->isSaturday())->toBeTrue();

    foreach ($periods as $index => $period) {
        expect(
            $period->eq($first->addMonth($index)) // span by months...
        )->toBeTrue();
    }

    expect($periods->last()->toDateTimeString())->toBe('2021-08-01 00:00:00');
    expect($periods->last()->isSunday())->toBeTrue();
});

it('generates an array of datetime instances for the server\'s creation period', function () {
    $server = Server::factory()->create([
        'created_at' => Carbon::parse('2020-10-10'),
    ]);

    $periods = DatePeriod::allTime($server);

    expect($periods)->toBeInstanceOf(Collection::class);
    expect($periods)->toHaveCount(2); // 2020 and 2021
    expect($periods->every(fn ($period) => $period instanceof Carbon))->toBeTrue();

    expect($periods->first()->toDateTimeString())->toBe('2020-01-01 00:00:00');
    expect($periods->last()->toDateTimeString())->toBe('2021-01-01 00:00:00');
});
