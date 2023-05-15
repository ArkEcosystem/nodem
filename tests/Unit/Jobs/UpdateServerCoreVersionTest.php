<?php

declare(strict_types=1);

use App\DTO\Alert;
use App\Enums\AlertType;
use App\Enums\ServerUpdatingTasksEnum;
use App\Jobs\UpdateServerCoreVersion;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use function Tests\createServerWithFixture;

it('should update the core version of the server', function (): void {
    $server = createServerWithFixture('info/coreVersion');

    (new UpdateServerCoreVersion($server))->handle();

    expect($server->core_version_current)->toBe('3.0.0-next.8');
    expect($server->core_version_latest)->toBe('4.0.0-next.0');

    expect($server->getMetaAttribute('core_manager_current_version'))->toBe('3.0.0');
    expect($server->getMetaAttribute('core_manager_latest_version'))->toBe('3.0.2');
});

it('should add a loading state', function () {
    $server = createServerWithFixture('info/coreVersion');

    new UpdateServerCoreVersion($server);

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_CORE))->toBeTrue();
    expect($server->isLoading())->toBeTrue();
});

it('removes the loading state after job finish', function (): void {
    $server = createServerWithFixture('info/coreVersion');

    (new UpdateServerCoreVersion($server))->handle();

    $server->refresh();

    expect($server->core_version_current)->toBe('3.0.0-next.8');
    expect($server->core_version_latest)->toBe('4.0.0-next.0');

    expect($server->getMetaAttribute('core_manager_current_version'))->toBe('3.0.0');
    expect($server->getMetaAttribute('core_manager_latest_version'))->toBe('3.0.2');

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_CORE))->toBeNull();
    expect($server->isLoading())->toBeFalse();
});

it('removes the loading state after job fails', function (): void {
    Http::fake([
        'https://mynode.com' => function () {
            throw new Exception('Something went wrong');
        },
    ]);

    $server = Server::factory()->create(['host' => 'https://mynode.com']);
    $job    = new UpdateServerCoreVersion($server);

    try {
        $job->handle();
    } catch (Exception $e) {
        $job->failed($e);
    }

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_CORE))->toBeNull();
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

    $job = new UpdateServerCoreVersion($server, $user);

    Cache::shouldReceive('get')
        ->once()
        ->with('alerts-'.$user->id, [])
        ->andReturn([])
        ->shouldReceive('put')
        ->once()
        ->with(
            'alerts-'.$user->id,
            [
                new Alert(AlertType::UPDATING_SERVER_CORE, 'warning', $server->name),
            ],
            Carbon::class,
        );

    $job->failed($exception);
});
