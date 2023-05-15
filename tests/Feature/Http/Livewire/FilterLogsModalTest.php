<?php

declare(strict_types=1);

use App\Enums\LogLevel;
use App\Http\Livewire\FilterLogsModal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Livewire\Testing\TestableLivewire;
use function Tests\createServerWithFixture;

function filterLogsModalComponent(string $process = 'relay'): TestableLivewire
{
    $server = createServerWithFixture('log/search');

    return Livewire::actingAs($server->user)
        ->test(FilterLogsModal::class, [
            'server'  => $server,
            'process' => $process,
        ]);
};

afterEach(fn () => Cache::clearResolvedInstances());

it('contains a modal', function () {
    filterLogsModalComponent()
        ->assertSet('modalShown', false)
        ->call('openModal')
        ->assertSet('modalShown', true)
        ->call('closeModal')
        ->assertSet('modalShown', false)
        ->assertEmitted('modalClosed');
});

it('can determine if certain level is contained within the selected levels', function () {
    $instance = filterLogsModalComponent()
                ->set('state.levels', [
                    'info' => true,
                ])
                ->instance();

    expect($instance->isLevelSelected('info'))->toBeTrue();

    $instance = filterLogsModalComponent()
                ->set('state.levels', [
                    'info'  => false,
                    'debug' => true,
                ])
                ->instance();

    expect($instance->isLevelSelected('info'))->toBeFalse();
});

it('opens a modal when event is emitted', function () {
    filterLogsModalComponent()
        ->emit('showFilterLogsModal:relay')
        ->assertSet('selectedAll', false)
        ->assertSet('process', 'relay')
        ->assertSet('state.dateFrom', null)
        ->assertSet('state.dateTo', null)
        ->assertSet('state.timeFrom', null)
        ->assertSet('state.timeTo', null)
        ->assertSet('state.levels', fn ($levels) => collect($levels)->keys()->every(fn ($level) => in_array($level, [
            LogLevel::FATAL, LogLevel::ERROR, LogLevel::WARNING, LogLevel::INFO, LogLevel::DEBUG,
        ], true)))
        ->assertSet('passedValidation', false)
        ->assertSet('modalShown', true);
});

it('adds seconds to time if ommited', function () {
    filterLogsModalComponent()
        ->set('state.timeFrom', '12:00')
        ->assertSet('state.timeFrom', '12:00:00')
        ->set('state.timeTo', '12:12')
        ->assertSet('state.timeTo', '12:12:00');
});

it('ignores seconds to time if already passed', function () {
    filterLogsModalComponent()
        ->set('state.timeFrom', '12:00:10')
        ->assertSet('state.timeFrom', '12:00:10')
        ->set('state.timeTo', '12:12:30')
        ->assertSet('state.timeTo', '12:12:30');
});

test('that start date must be a valid date format', function () {
    filterLogsModalComponent()
        ->set('state.dateFrom', '2020-01-01')
        ->call('submit')
        ->assertHasErrors([
            'state.dateFrom' => 'date_format',
        ]);
});

test('that start date cannot be a future date', function () {
    filterLogsModalComponent()
        ->set('state.dateFrom', now()->addDay()->format('d.m.Y'))
        ->call('submit')
        ->assertHasErrors('state.dateFrom');
});

test('that end date must be a valid date format', function () {
    filterLogsModalComponent()
        ->set('state.dateTo', '2020-01-01')
        ->call('submit')
        ->assertHasErrors([
            'state.dateTo' => 'date_format',
        ]);
});

test('that end date cannot be date earlier than start date', function () {
    filterLogsModalComponent()
        ->set('state.dateFrom', now()->subDay()->format('d.m.Y'))
        ->set('state.dateTo', now()->subDays(3)->format('d.m.Y'))
        ->call('submit')
        ->assertHasErrors('state.dateTo');
});

it('requires start time', function () {
    filterLogsModalComponent()
        ->set('state.dateFrom', '01.01.2020')
        ->set('state.timeFrom', '')
        ->call('submit')
        ->assertHasErrors([
            'state.timeFrom' => 'required_with',
        ]);
});

it('requires end time', function () {
    filterLogsModalComponent()
        ->set('state.dateTo', '01.01.2020')
        ->set('state.timeTo', '')
        ->call('submit')
        ->assertHasErrors([
            'state.timeTo' => 'required_with',
        ]);
});

it('emits event when form is submitted', function () {
    filterLogsModalComponent()
        ->emit('showFilterLogsModal:relay')
        ->set('state.dateTo', '13.05.2020')
        ->set('state.timeTo', '12:00')
        ->call('submit')
        ->assertSet('busy', true)
        ->assertSet('closeAfterFilterApplied', true)
        ->assertEmitted('applyFilters', [
            'identifier' => 'relay',
            'process'    => 'relay',
            'startDate'  => null,
            'endDate'    => Carbon::parse('2020-05-13 12:00')->unix(),
            'levels'     => [],
        ]);
});

it('closes modal when filter is applied', function () {
    $component = filterLogsModalComponent()
        ->emit('showFilterLogsModal:relay')
        ->set('state.dateTo', '13.05.2020')
        ->set('state.timeTo', '12:00')
        ->call('submit')
        ->emit('filtersApplied:relay')
        ->assertSet('busy', false)
        ->assertSet('closeAfterFilterApplied', false)
        ->assertSet('modalShown', false);
});

it('does nothing when filters applied emitted but not busy', function () {
    $component = filterLogsModalComponent()
        ->call('openModal')
        ->emit('filtersApplied:relay')
        ->assertSet('busy', false)
        ->assertSet('closeAfterFilterApplied', false)
        ->assertSet('modalShown', true);
});

it('should reset filters', function () {
    $component = filterLogsModalComponent()
        ->emit('showFilterLogsModal:relay')
        ->set('state.dateTo', '13.05.2020')
        ->set('state.timeTo', '')
        ->call('submit')
        ->assertHasErrors(['state.timeTo'])
        ->set('state.dateFrom', '11.05.2020')
        ->set('state.timeFrom', '11:00')
        ->call('resetFilters')
        ->assertHasNoErrors(['state.timeTo'])
        ->assertSet('state.dateTo', null)
        ->assertSet('state.timeTo', null)
        ->assertSet('state.dateFrom', null)
        ->assertSet('state.timeFrom', null)
        ->assertSet('busy', true)
        ->assertSet('closeAfterFilterApplied', true)
        ->assertEmitted('applyFilters', [
            'identifier' => 'relay',
            'process'    => 'relay',
        ]);
});

it('should load state from cache', function () {
    $from   = Carbon::parse('2020-01-01 10:00');
    $to     = Carbon::parse('2020-01-12 12:00');
    $server = createServerWithFixture('log/search');

    Cache::shouldReceive('tags')
        ->once()
        ->with([
            'server-filters:'.$server->id,
            'user-filters:'.$server->user->id,
        ])
        ->andReturnSelf();

    Cache::shouldReceive('get')
        ->once()
        ->with('filters:'.$server->id.':'.$server->user->id.':relay', [])
        ->andReturn([
            'from'   => $from->timestamp,
            'to'     => $to->timestamp,
            'levels' => ['info'],
        ]);

    Livewire::actingAs($server->user)
        ->test(FilterLogsModal::class, [
            'server'  => $server,
            'process' => 'relay',
        ])
        ->assertSet('state', [
            'dateFrom' => '01.01.2020',
            'timeFrom' => '10:00:00',
            'dateTo'   => '12.01.2020',
            'timeTo'   => '12:00:00',
            'levels'   => ['info'],
        ]);
});
