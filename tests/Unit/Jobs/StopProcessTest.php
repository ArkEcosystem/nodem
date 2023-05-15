<?php

declare(strict_types=1);

use App\DTO\Alert;
use App\Enums\AlertType;
use App\Enums\ProcessStatusEnum;
use App\Jobs\StopProcess;
use App\Jobs\UpdateServerCoreVersion;
use App\Jobs\UpdateServerPing;
use App\Models\Process;
use App\Models\Server;
use App\Models\User;
use App\Services\Client\Exceptions\RPCResponseException;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\Activitylog\Models\Activity;
use function Tests\createServerWithFixture;
use function Tests\createServerWithFixtureSequence;

it('should send a request to stop the process', function (): void {
    $user = User::factory()->create();

    $server = createServerWithFixture('process/stop');

    $process = Process::factory()->create([
        'server_id' => $server->id,
        'name'      => 'core',
    ]);

    (new StopProcess($user, $process))->handle();

    Http::assertSent(function ($request) use ($process, $server): bool {
        return $request->url() === $server->host &&
               $request['method'] === 'process.stop' &&
               $request['params']->name === $process->name;
    });
});

it('logs the process', function (): void {
    $user = User::factory()->create();

    $server = createServerWithFixture('process/stop');

    $process = Process::factory()->create([
        'server_id' => $server->id,
        'name'      => 'core',
        'type'      => 'relay',
    ]);

    (new StopProcess($user, $process))->handle();

    $lastLoggedActivity = Activity::all()->last();

    expect($lastLoggedActivity->causer->is($user))->toBeTrue();
    expect($lastLoggedActivity->subject->is($server))->toBeTrue();
    expect($lastLoggedActivity->description)->toEqual('Stopped Relay process');
    expect($lastLoggedActivity->getExtraProperty('username'))->toEqual($user->username);
});

it('sets a pending status when job is created', function (): void {
    $user = User::factory()->create();

    $server = createServerWithFixture('process/stop');

    $process = Process::factory()->create([
        'server_id' => $server->id,
        'name'      => 'core',
        'type'      => 'relay',
    ]);

    new StopProcess($user, $process);

    expect($process->fresh()->status)->toEqual(ProcessStatusEnum::STOPPING);
});

it('sets the response status when job ends', function (): void {
    $user = User::factory()->create();

    $server = createServerWithFixture('process/stop');

    $process = Process::factory()->create([
        'server_id' => $server->id,
        'name'      => 'core',
        'type'      => 'relay',
    ]);

    (new StopProcess($user, $process))->handle();

    expect($process->fresh()->status)->toEqual(ProcessStatusEnum::STOPPED);
});

it('gets the status from the server if request fails', function (): void {
    $user = User::factory()->create();

    $server = createServerWithFixtureSequence(
        'process/error',
        'process/list',
    );

    $process = Process::factory()->create([
        'server_id' => $server->id,
        'name'      => 'ark-relay',
        'type'      => 'relay',
    ]);

    $job = new StopProcess($user, $process);

    try {
        $job->handle();
    } catch (RPCResponseException $e) {
        $job->failed($e);
    }

    expect($process->fresh()->status)->toEqual(ProcessStatusEnum::ONLINE);
});

it('rollbacks the original status if cannot be fetched', function (): void {
    $user = User::factory()->create();

    $server = createServerWithFixtureSequence(
        'process/error',
        'process/error',
    );

    $process = Process::factory()->create([
        'server_id' => $server->id,
        'name'      => 'ark-forger',
        'type'      => 'forger',
        'status'    => ProcessStatusEnum::ONE_LAUNCH_STATUS,
    ]);

    $job = new StopProcess($user, $process);

    try {
        $job->handle();
    } catch (RPCResponseException $e) {
        $job->failed($e);
    }

    expect($process->fresh()->status)->toEqual(ProcessStatusEnum::ONE_LAUNCH_STATUS);
});

it('marks the manager as not running if connection exception received', function (): void {
    Bus::fake([UpdateServerPing::class, UpdateServerCoreVersion::class]);

    $user = User::factory()->create();

    $server = Server::factory()->create(['host' => 'https://mynode.com']);

    Http::fake(function () {
        throw new ConnectionException();
    });

    $process = Process::factory()->create([
        'server_id' => $server->id,
        'name'      => 'ark-forger',
        'type'      => 'forger',
        'status'    => ProcessStatusEnum::ONE_LAUNCH_STATUS,
    ]);

    expect($server->isManagerRunning())->toBeTrue();

    $job = new StopProcess($user, $process);

    try {
        $job->handle();
    } catch (ConnectionException $e) {
        $job->failed($e);
    }

    expect($process->fresh()->status)->toEqual(ProcessStatusEnum::ONE_LAUNCH_STATUS);

    Bus::assertDispatched(UpdateServerPing::class, fn ($job) => $job->server->is($server));
    Bus::assertDispatched(UpdateServerCoreVersion::class, fn ($job) => $job->server->is($server));
});

it('stores the error details on the failed task store', function (): void {
    Bus::fake([UpdateServerPing::class, UpdateServerCoreVersion::class]);

    $exception = new ConnectionException();
    $user      = User::factory()->create();
    $server    = Server::factory()->create();

    // Not directly related to the test but to prevent errors
    Cache::shouldReceive('tags')
        ->andReturnSelf()
        ->shouldReceive('flush');

    $process = Process::factory()->create([
        'server_id' => $server->id,
        'name'      => 'ark-forger',
        'type'      => 'forger',
        'status'    => ProcessStatusEnum::ONE_LAUNCH_STATUS,
    ]);

    $job = new StopProcess($user, $process);

    Cache::shouldReceive('get')
        ->once()
        ->with('alerts-'.$user->id, [])
        ->andReturn([])
        ->shouldReceive('put')
        ->once()
        ->with(
            'alerts-'.$user->id,
            [
                new Alert(AlertType::STOP_SERVER, 'warning', $server->name),
            ],
            Carbon::class,
        );

    $job->failed($exception);
});
