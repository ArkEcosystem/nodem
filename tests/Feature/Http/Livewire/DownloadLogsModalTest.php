<?php

declare(strict_types=1);

use App\Enums\LogLevel;
use App\Http\Livewire\DownloadLogsModal;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Livewire\Testing\TestableLivewire;
use function Tests\createServerWithFixture;

function downloadLogsModalComponent() : TestableLivewire
{
    $server = createServerWithFixture('log/search');

    return Livewire::test(DownloadLogsModal::class, [
        'server' => $server,
    ]);
};

// Basic...

it('contains a modal', function () {
    downloadLogsModalComponent()
        ->assertSet('modalShown', false)
        ->call('openModal')
        ->assertSet('modalShown', true)
        ->call('closeModal')
        ->assertSet('modalShown', false)
        ->assertEmitted('modalClosed');
});

it('can determine if certain level is contained within the selected levels', function () {
    $instance = downloadLogsModalComponent()
                ->set('state.levels', [
                    'info' => true,
                ])
                ->instance();

    expect($instance->isLevelSelected('info'))->toBeTrue();

    $instance = downloadLogsModalComponent()
                ->set('state.levels', [
                    'info'  => false,
                    'debug' => true,
                ])
                ->instance();

    expect($instance->isLevelSelected('info'))->toBeFalse();
});

// Lifecycle events...

it('resets a component state when modal is closed', function () {
    downloadLogsModalComponent()
        ->set('state.dateFrom', '10.10.2020')
        ->set('state.timeFrom', '20:10')
        ->set('state.process', 'forger')
        ->set('passedValidation', true)
        ->emit('modalClosed')
        ->assertSet('selectedAll', false)
        ->assertSet('passedValidation', false)
        ->assertSet('state.process', 'forger')
        ->assertSet('state.dateFrom', null)
        ->assertSet('state.dateTo', null)
        ->assertSet('state.timeFrom', null)
        ->assertSet('state.timeTo', null)
        ->assertSet('state.levels', fn ($levels) => collect($levels)->keys()->every(fn ($level) => in_array($level, [
            LogLevel::FATAL, LogLevel::ERROR, LogLevel::WARNING, LogLevel::INFO, LogLevel::DEBUG,
        ], true)));
});

it('opens a modal when event is emitted', function () {
    downloadLogsModalComponent()
        ->emit('showDownloadLogsModal', 'relay')
        ->assertSet('selectedAll', false)
        ->assertSet('state.process', 'relay')
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
    downloadLogsModalComponent()
        ->set('state.timeFrom', '12:00')
        ->assertSet('state.timeFrom', '12:00:00')
        ->set('state.timeTo', '12:12')
        ->assertSet('state.timeTo', '12:12:00');
});

it('ignores seconds to time if already passed', function () {
    downloadLogsModalComponent()
        ->set('state.timeFrom', '12:00:10')
        ->assertSet('state.timeFrom', '12:00:10')
        ->set('state.timeTo', '12:12:30')
        ->assertSet('state.timeTo', '12:12:30');
});

it('de-selects the "Select all" checkbox if not all checkboxes are selected', function () {
    downloadLogsModalComponent()
        ->set('selectedAll', true)
        ->set('state.levels', [
            'info' => true,
        ])
        ->assertSet('selectedAll', false);
});

it('toggles all levels when "Select all" is checked and formats the array for checkbox', function () {
    $logLevels = LogLevel::all();

    downloadLogsModalComponent()
        ->set('state.levels', [])
        ->set('selectedAll', true)
        ->assertSet('state.levels', function ($levels) use ($logLevels) {
            return collect($levels)->values()->every(fn ($value) => is_bool($value)) // assert value is true / false
                && collect($levels)->keys()->every(fn ($key)     => in_array($key, $logLevels, true)); // assert key is a string
        }) // assert format
        ->assertSet('state.levels', fn ($levels) => collect($levels)->keys()->diff($logLevels)->isEmpty()) // assert all have been checked
        ->set('selectedAll', false)
        ->assertSet('state.levels', fn ($levels) => count($levels) === 0);
});

// Validation tests...

it('requires start date', function () {
    downloadLogsModalComponent()
        ->set('state.dateFrom', '')
        ->call('download')
        ->assertHasErrors([
            'state.dateFrom' => 'required',
        ]);
});

test('that start date must be a valid date format', function () {
    downloadLogsModalComponent()
        ->set('state.dateFrom', '2020-01-01')
        ->call('download')
        ->assertHasErrors([
            'state.dateFrom' => 'date_format',
        ]);
});

test('that start date cannot be a future date', function () {
    downloadLogsModalComponent()
        ->set('state.dateFrom', now()->addDay()->format('d.m.Y'))
        ->call('download')
        ->assertHasErrors('state.dateFrom');
});

it('requires end date', function () {
    downloadLogsModalComponent()
        ->set('state.dateTo', '')
        ->call('download')
        ->assertHasErrors([
            'state.dateTo' => 'required',
        ]);
});

test('that end date must be a valid date format', function () {
    downloadLogsModalComponent()
        ->set('state.dateTo', '2020-01-01')
        ->call('download')
        ->assertHasErrors([
            'state.dateTo' => 'date_format',
        ]);
});

test('that end date cannot be date earlier than start date', function () {
    downloadLogsModalComponent()
        ->set('state.dateFrom', now()->subDay()->format('d.m.Y'))
        ->set('state.dateTo', now()->subDays(3)->format('d.m.Y'))
        ->call('download')
        ->assertHasErrors('state.dateTo');
});

it('requires start time', function () {
    downloadLogsModalComponent()
        ->set('state.timeFrom', '')
        ->call('download')
        ->assertHasErrors([
            'state.timeFrom' => 'required',
        ]);
});

it('requires end time', function () {
    downloadLogsModalComponent()
        ->set('state.timeFrom', '')
        ->call('download')
        ->assertHasErrors([
            'state.timeFrom' => 'required',
        ]);
});

it('requires a least one level selected', function () {
    downloadLogsModalComponent()
        ->set('state.levels', [
            'info' => false,
        ])
        ->call('download')
        ->assertHasErrors('state.levels');
});

// Happy path...

it('handles server outages', function () {
    Http::fake(fn ($request) => Http::response('Something went wrong.', 500));

    $state = [
        'dateFrom' => '01.01.2020',
        'dateTo'   => '30.01.2020',
        'timeFrom' => '00:00',
        'timeTo'   => '06:00',
        'levels'   => [
            'debug' => true,
        ],
        'process' => 'relay',
    ];

    downloadLogsModalComponent()
        ->set('state', $state)
        ->set('passedValidation', true)
        ->call('download')
        ->assertHasErrors('server');
});

it('dispatches an event to initiate the download', function () {
    $state = [
        'dateFrom' => '01.01.2020',
        'dateTo'   => '30.01.2020',
        'timeFrom' => '00:00',
        'timeTo'   => '06:00',
        'levels'   => [
            'debug' => true,
        ],
        'process' => 'relay',
    ];

    downloadLogsModalComponent()
        ->set('state', $state)
        ->call('download')
        ->assertSet('passedValidation', true)
        ->assertDispatchedBrowserEvent('perform-logs-download');
});

it('generates the archive and downloads the file', function () {
    $server = Server::factory()->create(['host' => 'https://mynode.com']);

    Http::fake(function ($request) {
        if (($request['method'] ?? false) === 'log.download') {
            return Http::response(json_decode(file_get_contents(base_path('tests/fixtures/log/download.json')), true));
        }

        if (($request['method'] ?? false) === 'log.archived') {
            return Http::response(json_decode(file_get_contents(base_path('tests/fixtures/log/archived.json')), true));
        }

        return Http::response('Dummy');
    });

    $response = Livewire::test(DownloadLogsModal::class, [
        'server' => $server,
    ])->set('state', [
        'dateFrom' => '01.01.2020',
        'dateTo'   => '30.01.2020',
        'timeFrom' => '00:00',
        'timeTo'   => '06:00',
        'levels'   => [
            'debug' => true,
        ],
        'process' => 'relay',
    ])->set('passedValidation', true)->call('download')->lastResponse->original;

    // First request...
    Http::assertSent(
        fn ($request) => ($request['method'] ?? false) === 'log.download' &&
            $request['params']->processes === ['relay'] &&
            $request['params']->dateFrom === Carbon::parse('01.01.2020 00:00')->unix() &&
            $request['params']->dateTo === Carbon::parse('30.01.2020 06:00')->unix() &&
            $request['params']->levels === ['debug']
    );

    // Second request...
    Http::assertSent(fn ($request) => ($request['method'] ?? false) === 'log.archived');

    // Downloading archive...
    Http::assertSent(
        fn ($request) => $request->hasHeader('Authorization') &&
            $request->url() === 'log/archived/2020-12-14_17-38-00.log.gz' &&
            $request->method() === 'GET'
    );

    expect($response['effects']['download']['name'])->toBe('2020-12-14_17-38-00.log.gz');
});
