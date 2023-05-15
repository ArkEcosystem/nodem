<?php

declare(strict_types=1);

use App\Models\Server;
use App\Services\Client\Client;
use App\Services\Client\Resources\Log;
use Illuminate\Support\Facades\Http;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\createServerWithFixture;

it('should call [log.search] and pass', function (): void {
    $server = createServerWithFixture('log/search');

    $result = (new Log(Client::fromServer($server)))->search([]);

    assertMatchesSnapshot($result);
});

it('should call [log.archived] and pass', function (): void {
    $server = createServerWithFixture('log/archived');

    $result = (new Log(Client::fromServer($server)))->archived();

    assertMatchesSnapshot($result);
});

it('should call [log.download] and pass', function (): void {
    $server = Server::factory()->create(['host' => 'https://mynode.com']);

    Http::fakeSequence()
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/log/download.json')), true))
        ->push(json_decode(file_get_contents(base_path('tests/fixtures/log/archived.json')), true));

    $result = (new Log(Client::fromServer($server)))->download([]);

    assertMatchesSnapshot($result);
});
