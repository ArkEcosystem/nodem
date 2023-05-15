<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\UpdateServerResources as Job;
use App\Models\Server;
use Illuminate\Console\Command;

final class UpdateServerResources extends Command
{
    protected $signature = 'servers:update-resources';

    protected $description = 'Update the disk space of each server.';

    public function handle(): void
    {
        foreach (Server::cursor() as $server) {
            Job::dispatch($server);
        }
    }
}
