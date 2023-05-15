<?php

declare(strict_types=1);

use App\Enums\ServerUpdatingTasksEnum;
use App\Jobs\RestartCoreManager;
use App\Jobs\UpdateCoreManager;
use App\Models\Server;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use function Tests\createServerWithFixture;

it('should send a request to update the core manager of the server to the latest version', function (): void {
    Queue::fake();

    $server = createServerWithFixture('info/coreVersion');

    (new UpdateCoreManager($server, $server->user))->handle();

    Queue::assertPushed(RestartCoreManager::class, 1);

    Http::assertSent(function ($request) use ($server): bool {
        return $request->url() === $server->host &&
            $request['method'] === 'plugin.update';
    });
});

it('should send a request to restart core manager after update', function (): void {
    $server = createServerWithFixture('info/coreVersion');

    (new UpdateCoreManager($server, $server->user))->handle();

    Http::assertSentInOrder([
        function ($request) use ($server): bool {
            return $request->url() === $server->host &&
                $request['method'] === 'plugin.update';
        },
        function ($request) use ($server): bool {
            return $request->url() === $server->host &&
                $request['method'] === 'process.restart';
        },
        function ($request) use ($server): bool {
            return $request->url() === $server->host &&
                $request['method'] === 'info.coreVersion';
        },
    ]);
});

it('should handle no response when restarting core manager', function (): void {
    $server = Server::factory()->create(['host' => 'https://mynode.com']);

    $sequence = Http::fakeSequence('mynode.com');
    $sequence->push(json_decode(file_get_contents(base_path('tests/fixtures/plugin/update.json')), true));
    $sequence->push(function () {
        throw new ConnectionException('Empty reply from server');
    });
    $sequence->push(json_decode(file_get_contents(base_path('tests/fixtures/info/coreVersion.json')), true));

    (new UpdateCoreManager($server, $server->user))->handle();

    Http::assertSentInOrder([
        function ($request) use ($server): bool {
            return $request->url() === $server->host &&
                $request['method'] === 'plugin.update';
        },
        function ($request) use ($server): bool {
            return $request->url() === $server->host &&
                $request['method'] === 'process.restart';
        },
        function ($request) use ($server): bool {
            return $request->url() === $server->host &&
                $request['method'] === 'info.coreVersion';
        },
    ]);
});

it('should throw if different exception is thrown when restarting core manager', function (): void {
    $server = Server::factory()->create(['host' => 'https://mynode.com']);

    $sequence = Http::fakeSequence('mynode.com');
    $sequence->push(json_decode(file_get_contents(base_path('tests/fixtures/plugin/update.json')), true));
    $sequence->push(function () {
        throw new ConnectionException('Different exception thrown');
    });
    $sequence->push(json_decode(file_get_contents(base_path('tests/fixtures/info/coreVersion.json')), true));

    expect(fn () => (new UpdateCoreManager($server, $server->user))->handle())->toThrow(ConnectionException::class);

    Http::assertSentInOrder([
        function ($request) use ($server): bool {
            return $request->url() === $server->host &&
                $request['method'] === 'plugin.update';
        },
        function ($request) use ($server): bool {
            return $request->url() === $server->host &&
                $request['method'] === 'process.restart';
        },
    ]);
});

it('should add a loading state', function () {
    $server = createServerWithFixture('info/coreVersion');

    new UpdateCoreManager($server, $server->user);

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_CORE_MANAGER))->toBeTrue();
    expect($server->isLoading())->toBeTrue();
});
