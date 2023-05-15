<?php

declare(strict_types=1);

use App\Http\Livewire\ServerLogs;
use App\Models\Process;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use function Tests\createServerWithFixture;

afterEach(fn () => Cache::clearResolvedInstances());

it('mounts by initializing relay and forger', function () {
    $server = Server::factory()->create();

    $server->processes()->save(Process::factory()->make([
        'type' => 'relay',
    ]));

    $server->processes()->save(Process::factory()->make([
        'type' => 'forger',
    ]));

    Livewire::actingAs($server->user)->test(ServerLogs::class, [
        'server' => $server,
    ])->assertSet(
        'processes',
        fn ($processes) => count($processes) === 2 &&
            // Assert processes
            array_key_exists('relay', $processes) &&
            array_key_exists('forger', $processes) &&
            // Assert structure
            count(array_intersect_key($processes['relay'], ['loaded', 'failed', 'logs', 'filters', 'search'])) === 0 &&
            count(array_intersect_key($processes['forger'], ['loaded', 'failed', 'logs', 'filters', 'search'])) === 0
    );
});

it('mounts by initializing core if user prefers combined', function () {
    $server = Server::factory()->prefersCombined()->create();

    $server->processes()->save(Process::factory()->core()->make());

    Livewire::actingAs($server->user)->test(ServerLogs::class, [
        'server' => $server,
    ])->assertSet(
        'processes',
        fn ($processes) => count($processes) === 1 &&
            // Assert processes
            array_key_exists('core', $processes) &&
            // Assert structure
            count(array_intersect_key($processes['core'], ['loaded', 'failed', 'logs', 'filters', 'search'])) === 0
    );
});

it('does not initialize a relay if relay is missing from server', function () {
    $server = Server::factory()->create();

    $server->processes()->save(Process::factory()->make([
        'type' => 'forger',
    ]));

    Livewire::actingAs($server->user)->test(ServerLogs::class, [
        'server' => $server,
    ])->assertSet('processes', fn ($processes) => count($processes) === 1 && array_key_exists('forger', $processes));
});

it('does not initialize a forger if forger is missing from server', function () {
    $server = Server::factory()->create();

    $server->processes()->save(Process::factory()->make([
        'type' => 'relay',
    ]));

    Livewire::actingAs($server->user)->test(ServerLogs::class, [
        'server' => $server,
    ])->assertSet('processes', fn ($processes) => count($processes) === 1 && array_key_exists('relay', $processes));
});

it('handles server failures', function (): void {
    $server = Server::factory()->create(['host' => 'https://mynode.com']);

    $server->processes()->save(Process::factory()->make([
        'type' => 'relay',
    ]));

    Http::fake([
        '*' => Http::response('', 500),
    ]);

    Livewire::actingAs($server->user)->test(ServerLogs::class, [
        'server' => $server,
    ])->call('loadLogs')->assertSet('processes.relay.loaded', false)->assertSet('processes.relay.failed', true);
});

it('loads logs', function (): void {
    $server = createServerWithFixture('log/search-additional');

    $server->processes()->save(Process::factory()->make([
        'type' => 'relay',
    ]));

    Livewire::actingAs($server->user)->test(ServerLogs::class, [
        'server' => $server,
    ])->call('loadLogs')
        ->assertSet('processes.relay.loaded', true)
        ->assertSet('processes.relay.failed', false)
        ->assertSet('processes.relay.logs', fn ($logs) => count($logs) === 5);
});

// Searching...

it('loads the log when search event is emitted', function () {
    $server = createServerWithFixture('log/search');

    $server->processes()->save(Process::factory()->make([
        'type' => 'relay',
    ]));

    Livewire::actingAs($server->user)->test(ServerLogs::class, [
        'server' => $server,
    ])->emit('changeSearchTerm', [
        'term'    => 'hello',
        'process' => 'relay',
    ])->assertSet('processes.relay.search', 'hello');

    Http::assertSent(fn ($request) => $request['params']->searchTerm === 'hello');
});

it('ignores search event when process is not valid', function () {
    $server = createServerWithFixture('log/search');

    $server->processes()->save(Process::factory()->make([
        'type' => 'relay',
    ]));

    Livewire::actingAs($server->user)->test(ServerLogs::class, [
        'server' => $server,
    ])->emit('changeSearchTerm', [
        'term'    => 'hello',
        'process' => 'something-else',
    ])->assertSet('processes.relay.search', null);

    Http::assertNothingSent();
});

// Filters...

it('ignores filter events if process is not valid', function () {
    $server = createServerWithFixture('log/search');

    $server->processes()->save(Process::factory()->make([
        'type' => 'relay',
    ]));

    Livewire::actingAs($server->user)->test(ServerLogs::class, [
        'server' => $server,
    ])->emit('applyFilters', [
        'identifier' => 'my-identifier',
        'startDate'  => Carbon::parse('01.01.2020 00:00')->timestamp,
        'endDate'    => Carbon::parse('30.01.2020 06:00')->timestamp,
        'levels'     => ['info' => true],
        'process'    => 'forger',
    ])->assertSet('processes.relay.filters', [
        'from'   => null,
        'to'     => null,
        'levels' => [],
    ]);

    Http::assertNothingSent();
});

it('updates search filters when filter event is received', function () {
    $server = createServerWithFixture('log/search');

    $server->processes()->save(Process::factory()->make([
        'type' => 'relay',
    ]));

    Livewire::actingAs($server->user)->test(ServerLogs::class, [
        'server' => $server,
    ])->emit('applyFilters', [
        'identifier' => 'my-identifier',
        'startDate'  => Carbon::parse('01.01.2020 00:00')->timestamp,
        'endDate'    => Carbon::parse('30.01.2020 06:00')->timestamp,
        'levels'     => ['info' => true],
        'process'    => 'relay',
    ])->assertSet('processes.relay.filters', [
        'from'   => Carbon::parse('01.01.2020 00:00')->timestamp,
        'to'     => Carbon::parse('30.01.2020 06:00')->timestamp,
        'levels' => ['info' => true],
    ]);

    Http::assertSent(
        fn ($request) => $request['params']->processes === ['relay'] &&
            $request['params']->dateFrom === 1577836800 &&
            $request['params']->dateTo === 1580364000 &&
            $request['params']->levels === ['info']
    );
});

it('emits a filtersApplied event after filtering', function () {
    $server = createServerWithFixture('log/search');

    $server->processes()->save(Process::factory()->make([
        'type' => 'relay',
    ]));

    Livewire::actingAs($server->user)->test(ServerLogs::class, [
        'server' => $server,
    ])->emit('applyFilters', [
        'identifier' => 'my-identifier',
        'process'    => 'relay',
    ])->assertEmitted('filtersApplied:my-identifier');
});

// Resetting...

it('resets when reset event is emitted', function () {
    $server = createServerWithFixture('log/search');

    Livewire::actingAs($server->user)->test(ServerLogs::class, [
        'server'  => $server,
    ])->set('state', [
        'from'   => Carbon::parse('12.01.2020 00:00')->timestamp,
        'to'     => null,
        'term'   => 'hello',
        'levels' => ['trace' => true],
    ])->emit('resetAll')->assertSet('state', [
        'from'   => null,
        'to'     => null,
        'term'   => null,
        'levels' => [],
    ])->assertSet('hasLoaded', true);

    expect(Cache::tags([
        'server-filters:'.$server->id,
        'user-filters:'.$server->user->id,
    ])->get('filters:'.$server->id.':'.$server->user->id.':relay'))->toBeNull();

    Cache::tags([
        'server-filters:'.$server->id,
        'user-filters:'.$server->user->id,
    ])->forget('filters:'.$server->id.':'.$server->user->id.':relay');
})->skip();
