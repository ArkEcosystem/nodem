<?php

declare(strict_types=1);

use App\Jobs\RestartCoreManager;
use App\Models\Server;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

it('should handle no response when restarting core manager', function (): void {
    $server = Server::factory()->create(['host' => 'https://mynode.com']);

    $sequence = Http::fakeSequence('mynode.com');
    $sequence->push(function () {
        throw new ConnectionException('Empty reply from server');
    });

    (new RestartCoreManager($server, $server->user))->handle();

    Http::assertSent(function ($request) use ($server): bool {
        return $request->url() === $server->host &&
            $request['method'] === 'process.restart';
    });
});

it('should throw if different exception is thrown when restarting core manager', function (): void {
    $server = Server::factory()->create(['host' => 'https://mynode.com']);

    $sequence = Http::fakeSequence('mynode.com');
    $sequence->push(function () {
        throw new ConnectionException('Different exception thrown');
    });

    expect(fn () => (new RestartCoreManager($server, $server->user))->handle())->toThrow(ConnectionException::class);

    Http::assertSent(function ($request) use ($server): bool {
        return $request->url() === $server->host &&
            $request['method'] === 'process.restart';
    });
});
