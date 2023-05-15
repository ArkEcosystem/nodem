<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Server;
use Illuminate\Support\Facades\Http;

function createServerWithFixture(string $fixture, int $statusCode = 200): Server
{
    $server = Server::factory()->create(['host' => 'https://mynode.com']);

    $contents = json_decode(file_get_contents(base_path("tests/fixtures/{$fixture}.json")), true);

    Http::fake([
        'mynode.com' => Http::response($contents, $statusCode),
    ]);

    return $server;
}

function createServerWithFixtureSequence(string ...$fixtures): Server
{
    $server = Server::factory()->create(['host' => 'https://mynode.com']);

    $sequence = Http::fakeSequence('mynode.com');
    foreach ($fixtures as $fixture) {
        $sequence->push(json_decode(file_get_contents(base_path("tests/fixtures/{$fixture}.json")), true));
    }

    return $server;
}

function mockWithFixture(string $fixture): void
{
    $contents = json_decode(file_get_contents(base_path("tests/fixtures/{$fixture}.json")), true);

    Http::fake([
        'mynode.com' => Http::response($contents, 200),
    ]);
}
