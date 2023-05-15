<?php

declare(strict_types=1);

use App\Actions\SearchLogs;
use App\Models\Server;
use Illuminate\Support\Facades\Http;
use function Tests\createServerWithFixture;

it('should send an HTTP request and receive an array as result', function (): void {
    $result = SearchLogs::new(createServerWithFixture('log/search'))
        ->process('relay')
        ->time(now()->startOfDay(), now()->endOfDay())
        ->level('debug')
        ->page(5)
        ->term('missed')
        ->search();

    expect($result)->toBeArray();
});

it('should send an HTTP request and receive an array as result with core process', function (): void {
    $result = SearchLogs::new(createServerWithFixture('log/search'))
        ->process('core')
        ->time(now()->startOfDay(), now()->endOfDay())
        ->level('debug')
        ->page(5)
        ->term('missed')
        ->search();

    expect($result)->toBeArray();
});

it('should send an HTTP request and receive a string as result', function (): void {
    $server = Server::factory()->create(['host' => 'https://mynode.com']);

    Http::fakeSequence()
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/log/download.json')), true))
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/log/archived.json')), true));

    $result = SearchLogs::new($server)
        ->process('relay')
        ->timeFrom(now()->startOfDay())
        ->level('debug')
        ->page(5)
        ->term('missed')
        ->download();

    expect($result)->toBeArray();
    expect($result['filename'])->toBe('2020-12-14_17-38-00.log.gz');
    expect($result['url'])->toBe('/log/archived/2020-12-14_17-38-00.log.gz');
});
