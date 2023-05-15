<?php

declare(strict_types=1);

use App\Jobs\CheckServerProcessStatus;
use App\Jobs\UpdateAllServerDetails;
use App\Jobs\UpdateProcesses;
use App\Jobs\UpdateServerCoreVersion;
use App\Jobs\UpdateServerHeight;
use App\Jobs\UpdateServerPing;
use App\Jobs\UpdateServerResources;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

it('dispatches all server details-related jobs', function (): void {
    $jobs = [
        CheckServerProcessStatus::class,
        UpdateServerCoreVersion::class,
        UpdateServerHeight::class,
        UpdateServerPing::class,
        UpdateProcesses::class,
        UpdateServerResources::class,
    ];

    Bus::fake($jobs);

    $server = Server::factory()->create();
    $user   = User::factory()->create();

    UpdateAllServerDetails::dispatch($server, $user);

    array_shift($jobs);

    Bus::assertBatched(fn () => [$jobs]);

    Bus::assertBatchCount(1);
});
