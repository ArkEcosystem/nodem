<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\UpdateServerCoreVersion as Job;
use App\Models\Server;
use Illuminate\Console\Command;

final class UpdateServerCoreVersions extends Command
{
    protected $signature = 'servers:update-core-version';

    protected $description = 'Update the core version of each server.';

    public function handle(): void
    {
        foreach (Server::cursor() as $server) {
            Job::dispatch($server);
        }
    }
}
