<?php

declare(strict_types=1);

use App\Jobs\CheckServerProcessStatus;
use App\Models\Process;
use App\Models\Server;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

it('should have a default threshold', function (): void {
    Config::set('nodem.height_mismatch.threshold.default', 5);

    $server = Server::factory()->create();

    $job = new CheckServerProcessStatus($server);

    expect($job->threshold)->toBe(5);
});

it('should have a specific threshold for server with relay only process', function (): void {
    Config::set('nodem.height_mismatch.threshold.relay', 7);

    $server = Server::factory()->create();
    Process::factory()->create(['server_id' => $server->id, 'type' => 'relay', 'name' => 'ark-relay']);

    $job = new CheckServerProcessStatus($server);

    expect($job->threshold)->toBe(7);
});

it('should check for height mismatch', function ($height, $randomHeight, $threshold, $result): void {
    Http::fake([
        'mynode.com' => Http::response([
            'result' => [
                'height'           => $height,
                'randomNodeHeight' => $randomHeight,
            ],
        ]),
    ]);

    /** @var Server $server */
    $server = Server::factory()->create(['host' => 'https://mynode.com']);

    (new CheckServerProcessStatus($server))->threshold($threshold)->handle();

    expect($server->fresh()->hasHeightMismatch())->toBeBool();
    expect($server->fresh()->hasHeightMismatch())->toBe($result);
})->with([
    [765, 760, 5, true],
    [765, 761, 5, false],
    [765, 763, 2, true],
    [765, 764, 2, false],
]);
