<?php

declare(strict_types=1);

use App\Jobs\PingServer;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('should store `true` on the cache when server is online', function (): void {
    Http::fake();

    $host = 'http:://1.2.3.4';

    (new PingServer($host))->handle();

    Http::assertSent(function ($request) use ($host): bool {
        return $request->url() === $host;
    });

    expect(Cache::get('ping-'.$host))->toBe(true);
});

it('should store `false` on the cache when connection exception', function (): void {
    Http::fake(function () {
        throw new ConnectionException();
    });

    $host = 'http:://1.2.3.4';

    (new PingServer($host))->handle();

    expect(Cache::get('ping-'.$host))->toBe(false);
});

it('should store `false` on the cache when connection failed', function (): void {
    Http::fake(function () {
        return Http::response('Forbidden', 403);
    });

    $host = 'http:://1.2.3.4';

    (new PingServer($host))->handle();

    expect(Cache::get('ping-'.$host))->toBe(false);
});

it('should clear the cache when job is created', function (): void {
    $host = 'http:://1.2.3.4';

    Cache::set('ping-'.$host, true);

    new PingServer($host);

    expect(Cache::get('ping-'.$host))->toBe(null);
});
