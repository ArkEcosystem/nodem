<?php

declare(strict_types=1);

use App\DTO\Alert;
use App\Enums\AlertType;
use App\Enums\ServerUpdatingTasksEnum;
use App\Jobs\UpdateServerPing;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('should update the ping of the server', function (): void {
    $server = Server::factory()->create(['host' => 'https://127.0.0.1:4040']);

    (new UpdateServerPing($server))->handle();

    expect($server->ping)->not()->toBeNull();
});

it('should add a loading state', function () {
    $server = Server::factory()->create(['host' => 'https://127.0.0.1:4040']);

    new UpdateServerPing($server);

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_PING))->toBeTrue();
    expect($server->isLoading())->toBeTrue();
});

it('removes the loading state after job finish', function (): void {
    $server = Server::factory()->create(['host' => 'https://127.0.0.1:4040']);

    (new UpdateServerPing($server))->handle();

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_PING))->toBeNull();
    expect($server->isLoading())->toBeFalse();
});

it('removes the loading state after job fails', function (): void {
    Http::fake([
        'https://mynode.com' => function () {
            throw new Exception('Something went wrong');
        },
    ]);

    $server = Server::factory()->create(['host' => 'https://mynode.com']);
    $job    = new UpdateServerPing($server);

    try {
        $job->handle();
    } catch (Exception $e) {
        $job->failed($e);
    }

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_PING))->toBeNull();
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

    $job = new UpdateServerPing($server, $user);

    Cache::shouldReceive('get')
        ->once()
        ->with('alerts-'.$user->id, [])
        ->andReturn([])
        ->shouldReceive('put')
        ->once()
        ->with(
            'alerts-'.$user->id,
            [
                new Alert(AlertType::UPDATING_SERVER_PING, 'warning', $server->name),
            ],
            Carbon::class,
        );

    $job->failed($exception);
});
