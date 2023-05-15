<?php

declare(strict_types=1);

use App\DTO\Alert;
use App\Enums\AlertType;
use App\Enums\ServerProcessTypeEnum;
use App\Enums\ServerUpdatingTasksEnum;
use App\Jobs\UpdateProcesses;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use function Tests\createServerWithFixture;

it('should update all processes that are running on the server', function (): void {
    $this->travelTo(Carbon::parse('01.01.2020 00:00:00'));

    $server = createServerWithFixture('process/list');

    expect($server->processes()->count())->toBe(0);

    $result = (new UpdateProcesses($server))->handle();

    expect($server->processes()->count())->toBe(2);
});

it('should add a loading state', function () {
    $server = createServerWithFixture('process/list');

    new UpdateProcesses($server);

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_PROCESSES))->toBeTrue();
    expect($server->isLoading())->toBeTrue();
});

it('removes the loading state after job finish', function (): void {
    $this->travelTo(Carbon::parse('01.01.2020 00:00:00'));

    $server = createServerWithFixture('process/list');

    (new UpdateProcesses($server))->handle();

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_PROCESSES))->toBeNull();
    expect($server->isLoading())->toBeFalse();
});

it('removes the loading state after job fails', function (): void {
    Http::fake([
        'https://mynode.com' => function () {
            throw new Exception('Something went wrong');
        },
    ]);

    $server = Server::factory()->create(['host' => 'https://mynode.com']);
    $job    = new UpdateProcesses($server);

    try {
        $job->handle();
    } catch (Exception $e) {
        $job->failed($e);
    }

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_PROCESSES))->toBeNull();
    expect($server->isLoading())->toBeFalse();
});

it('stores the error details after job fails', function (): void {
    // Not directly related to the test but to prevent errors
    $store = Cache::store();
    Cache::shouldReceive('tags')
        ->andReturnSelf()
        ->shouldReceive('flush')
        ->andReturnSelf()
        ->shouldReceive('store')
        ->andReturns($store);

    $exception = new Exception('Something went wrong');

    $server = Server::factory()->create();
    $user   = $server->user;

    $job = new UpdateProcesses($server, $user);

    Cache::shouldReceive('get')
        ->once()
        ->with('alerts-'.$user->id, [])
        ->andReturn([])
        ->shouldReceive('put')
        ->once()
        ->with(
            'alerts-'.$user->id,
            [
                new Alert(AlertType::UPDATING_PROCESSES, 'warning', $server->name),
            ],
            Carbon::class,
        );

    $job->failed($exception);
});

it('only stores online separated processes if all are returned', function (): void {
    $server = createServerWithFixture('process/list_all');

    (new UpdateProcesses($server))->handle();

    $server->refresh();

    expect($server->processes->firstWhere('name', 'ark-relay')->status)->toBe('online');
    expect($server->processes->firstWhere('name', 'ark-forger')->status)->toBe('online');
    expect($server->processes->firstWhere('name', 'ark-core'))->toBeNull();
});

it('only stores online combined process if all are returned', function (): void {
    $server = createServerWithFixture('process/list_all_core');

    (new UpdateProcesses($server))->handle();

    $server->refresh();

    expect($server->processes->firstWhere('name', 'ark-core')->status)->toBe('online');
    expect($server->processes->firstWhere('name', 'ark-relay'))->toBeNull();
    expect($server->processes->firstWhere('name', 'ark-forger'))->toBeNull();
});

it('stores separated processes if preferred and all are returned offline', function (): void {
    $server = tap(createServerWithFixture('process/list_all_offline'))->update([
        'process_type' => ServerProcessTypeEnum::SEPARATE,
    ]);

    (new UpdateProcesses($server->refresh()))->handle();

    $server->refresh();

    expect($server->processes->firstWhere('name', 'ark-relay')->status)->toBe('offline');
    expect($server->processes->firstWhere('name', 'ark-forger')->status)->toBe('offline');
    expect($server->processes->firstWhere('name', 'ark-core'))->toBeNull();
});

it('stores combined process if preferred and all are returned offline', function (): void {
    $server = tap(createServerWithFixture('process/list_all_offline'))->update([
        'process_type' => ServerProcessTypeEnum::COMBINED,
    ]);

    (new UpdateProcesses($server->refresh()))->handle();

    $server->refresh();

    expect($server->processes->firstWhere('name', 'ark-core')->status)->toBe('offline');
    expect($server->processes->firstWhere('name', 'ark-relay'))->toBeNull();
    expect($server->processes->firstWhere('name', 'ark-forger'))->toBeNull();
});
