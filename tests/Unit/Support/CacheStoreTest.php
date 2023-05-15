<?php

declare(strict_types=1);

use App\Facades\CacheStore;
use Illuminate\Support\Facades\Cache as CacheFacade;

it('should cache the given callback', function (string $method): void {
    expect(CacheStore::$method('cache-store-callback', fn () => 'Hello World'))
        ->toBeString()
        ->toBe('Hello World');
})->with([
    'minute',
    'fiveMinutes',
    'tenMinutes',
    'fifteenMinutes',
    'thirtyMinutes',
    'hour',
    'twoHours',
    'sixHours',
    'day',
]);

it('should cache the given callback forever', function (): void {
    expect(CacheStore::rememberForever('cache-store-callback', fn () => 'Hello World'))
        ->toBeString()
        ->toBe('Hello World');
});

it('should cache the given callback forever with tags', function (): void {
    expect(CacheStore::rememberForever('cache-store-callback', fn () => 'Hello World', ['cache-tag']))
        ->toBeString()
        ->toBe('Hello World');
});

it('should forget the given cache key', function (): void {
    expect(CacheStore::rememberForever('cache-store-callback', fn () => 'Hello World'))
        ->toBeString()
        ->toBe('Hello World');

    expect(CacheFacade::get('cache-store-callback'))
        ->toBeString()
        ->toBe('Hello World');

    CacheStore::forget('cache-store-callback');

    expect(CacheFacade::get('cache-store-callback'))
        ->toBeNull();
});
