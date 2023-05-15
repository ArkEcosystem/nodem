<?php

declare(strict_types=1);

use App\Jobs\UpdateProcesses;
use App\Jobs\UpdateServer;
use App\Jobs\UpdateServerCoreVersion;
use App\Jobs\UpdateServerHeight;
use App\Jobs\UpdateServerPing;
use App\Jobs\UpdateServerResources;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

it('should call each job individually', function (): void {
    $user = User::factory()->create();

    Bus::fake();

    (new UpdateServer($user, Server::factory()->create()))->handle();

    Bus::assertDispatched(UpdateProcesses::class, 1);
    Bus::assertDispatched(UpdateServerCoreVersion::class, 1);
    Bus::assertDispatched(UpdateServerHeight::class, 1);
    Bus::assertDispatched(UpdateServerPing::class, 1);
    Bus::assertDispatched(UpdateServerResources::class, 1);
});
