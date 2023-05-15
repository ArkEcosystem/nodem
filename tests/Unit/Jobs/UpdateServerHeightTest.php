<?php

declare(strict_types=1);

use App\DTO\Alert;
use App\Enums\AlertType;
use App\Enums\ServerUpdatingTasksEnum;
use App\Jobs\UpdateServerHeight;
use App\Models\Server;
use App\Services\Client\Exceptions\RPCResponseException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use function Tests\createServerWithFixture;

it('should update the height of the server', function (): void {
    $server = createServerWithFixture('info/blockchainHeight');

    $result = (new UpdateServerHeight($server))->handle();

    expect($server->height)->toBe(766);
});

it('should add a loading state', function () {
    $server = createServerWithFixture('info/blockchainHeight');

    new UpdateServerHeight($server);

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_HEIGHT))->toBeTrue();
    expect($server->isLoading())->toBeTrue();
});

it('removes the loading state after job finish', function (): void {
    $server = createServerWithFixture('info/blockchainHeight');

    (new UpdateServerHeight($server))->handle();

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_HEIGHT))->toBeNull();
    expect($server->isLoading())->toBeFalse();
});

it('removes the loading state after job fails', function (): void {
    Http::fake([
        'https://mynode.com' => function () {
            throw new Exception('Something went wrong');
        },
    ]);

    $server = Server::factory()->create(['host' => 'https://mynode.com']);
    $job    = new UpdateServerHeight($server);

    try {
        $job->handle();
    } catch (Exception $e) {
        $job->failed($e);
    }

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_HEIGHT))->toBeNull();
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

    $job = new UpdateServerHeight($server, $user);

    Cache::shouldReceive('get')
        ->once()
        ->with('alerts-'.$user->id, [])
        ->andReturn([])
        ->shouldReceive('put')
        ->once()
        ->with(
            'alerts-'.$user->id,
            [
                new Alert(AlertType::UPDATING_SERVER_HEIGHT, 'warning', $server->name),
            ],
            Carbon::class,
        );

    $job->failed($exception);
});

it('should set unable_to_fetch_height attribute to true when failed to fetch', function ($fixture) {
    $server         = createServerWithFixture($fixture);
    $fixtureContent = json_decode(file_get_contents(base_path("tests/fixtures/{$fixture}.json")), true);

    $exception = new RPCResponseException(
        message: (string) data_get($fixtureContent, 'error.message', ''),
        code: (int) data_get($fixtureContent, 'error.code', 0)
    );

    expect($server->refresh()->isUnableToFetchHeight())->toBeFalse();

    (new UpdateServerHeight($server))->failed($exception);

    expect($server->refresh()->isUnableToFetchHeight())->toBeTrue();
})->with([
    'error/managerNoRelay',
    'error/relayOffline',
    'error/processCrashed',
    'error/customRPCError',
]);

it('should reset unable_to_fetch_height attribute to false when succeeded to fetch ', function () {
    $server = createServerWithFixture('info/blockchainHeight');
    $server->setMetaAttribute('unable_to_fetch_height', true);

    (new UpdateServerHeight($server))->handle();

    expect($server->refresh()->isUnableToFetchHeight())->toBeFalse();
});

it('should flag manager as running with ERR_NO_RELAY response error', function () {
    $server         = createServerWithFixture('error/managerNoRelay');
    $fixtureContent = json_decode(file_get_contents(base_path('tests/fixtures/error/managerNoRelay.json')), true);

    $exception = new RPCResponseException(
        message: (string) data_get($fixtureContent, 'error.message', ''),
        code: (int) data_get($fixtureContent, 'error.code', 0)
    );

    expect($server->refresh()->isManagerRunning())->toBeTrue();

    (new UpdateServerHeight($server))->failed($exception);

    expect($server->refresh()->isManagerRunning())->toBeTrue();
    expect($server->refresh()->isUnableToFetchHeight())->toBeTrue();
});
