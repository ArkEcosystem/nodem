<?php

declare(strict_types=1);

use App\DTO\Alert;
use App\Enums\AlertType;
use App\Enums\ProcessStatusEnum;
use App\Jobs\DeleteProcess;
use App\Jobs\UpdateServerCoreVersion;
use App\Jobs\UpdateServerPing;
use App\Models\Process;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\Activitylog\Models\Activity;
use function Tests\createServerWithFixture;

it('should send a request to delete the process', function (): void {
    $user = User::factory()->create();

    $server = createServerWithFixture('process/delete');

    $process = Process::factory()->create([
        'server_id' => $server->id,
        'name'      => 'core',
    ]);

    (new DeleteProcess($user, $process))->handle();

    Http::assertSent(function ($request) use ($process, $server): bool {
        return $request->url() === $server->host &&
            $request['method'] === 'process.delete' &&
            $request['params']->name === $process->name;
    });
});

it('logs the process', function (): void {
    $user = User::factory()->create();

    $server = createServerWithFixture('process/delete');

    $process = Process::factory()->create([
        'server_id' => $server->id,
        'name'      => 'core',
        'type'      => 'relay',
    ]);

    (new DeleteProcess($user, $process))->handle();

    $lastLoggedActivity = Activity::all()->last();

    expect($lastLoggedActivity->causer->is($user))->toBeTrue();
    expect($lastLoggedActivity->subject->is($server))->toBeTrue();
    expect($lastLoggedActivity->description)->toEqual('Deleted Relay process');
    expect($lastLoggedActivity->getExtraProperty('username'))->toEqual($user->username);
});

it('sets a pending status when job is created', function (): void {
    $user = User::factory()->create();

    $server = createServerWithFixture('process/delete');

    $process = Process::factory()->create([
        'server_id' => $server->id,
        'name'      => 'core',
        'type'      => 'relay',
    ]);

    new DeleteProcess($user, $process);

    expect($process->fresh()->status)->toEqual(ProcessStatusEnum::DELETED);
});

it('stores the error details on the failed task store', function (): void {
    Bus::fake([UpdateServerPing::class, UpdateServerCoreVersion::class]);

    $exception = new ConnectionException();
    $user      = User::factory()->create();
    $server    = createServerWithFixture('process/delete');

    // Not directly related to the test but to prevent errors
    Cache::shouldReceive('tags')
        ->andReturnSelf()
        ->shouldReceive('flush')
        ->andReturnSelf();

    $process = Process::factory()->create([
        'server_id' => $server->id,
        'name'      => 'ark-forger',
        'type'      => 'forger',
    ]);

    $job = new DeleteProcess($user, $process);

    Cache::shouldReceive('get')
        ->once()
        ->with('alerts-'.$user->id, [])
        ->andReturn([])
        ->shouldReceive('put')
        ->once()
        ->with(
            'alerts-'.$user->id,
            [
                new Alert(AlertType::DELETE_PROCESS, 'warning', $server->name),
            ],
            Carbon::class,
        );

    $job->failed($exception);
});
