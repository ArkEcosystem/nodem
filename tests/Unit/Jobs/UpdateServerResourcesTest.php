<?php

declare(strict_types=1);

use App\DTO\Alert;
use App\Enums\AlertType;
use App\Enums\ServerUpdatingTasksEnum;
use App\Jobs\UpdateServerResources;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use function Tests\createServerWithFixture;

it('should update the disk space of the server', function (): void {
    $server = createServerWithFixture('info/resources');

    (new UpdateServerResources($server))->handle();

    expect($server->cpu_total)->toBe(100);
    expect($server->cpu_used)->toBe(12.37);
    expect($server->cpu_available)->toBe(87.63);
    expect($server->ram_total)->toBe(16777216);
    expect($server->ram_used)->toBe(16711196);
    expect($server->ram_available)->toBe(66020);
    expect($server->disk_total)->toBe(488245288);
    expect($server->disk_used)->toBe(334654912);
    expect($server->disk_available)->toBe(153590376);

    $indicator = $server->resourceIndicators()->first();
    expect($indicator->cpu)->toBeFloat();
    expect($indicator->ram)->toBeInt();
    expect($indicator->disk)->toBeInt();
});

it('should add a loading state', function () {
    $server = createServerWithFixture('info/resources');

    new UpdateServerResources($server);

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_RESOURCES))->toBeTrue();
    expect($server->isLoading())->toBeTrue();
});

it('removes the loading state after job finish', function (): void {
    $server = createServerWithFixture('info/resources');

    (new UpdateServerResources($server))->handle();

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_RESOURCES))->toBeNull();
    expect($server->isLoading())->toBeFalse();
});

it('removes the loading state after job fails', function (): void {
    Http::fake([
        'https://mynode.com' => function () {
            throw new Exception('Something went wrong');
        },
    ]);

    $server = Server::factory()->create(['host' => 'https://mynode.com']);
    $job    = new UpdateServerResources($server);

    try {
        $job->handle();
    } catch (Exception $e) {
        $job->failed($e);
    }

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_RESOURCES))->toBeNull();
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

    $job = new UpdateServerResources($server, $user);

    Cache::shouldReceive('get')
        ->once()
        ->with('alerts-'.$user->id, [])
        ->andReturn([])
        ->shouldReceive('put')
        ->once()
        ->with(
            'alerts-'.$user->id,
            [
                new Alert(AlertType::UPDATING_SERVER_RESOURCES, 'warning', $server->name),
            ],
            Carbon::class,
        );

    $job->failed($exception);
});
