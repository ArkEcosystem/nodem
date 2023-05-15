<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\UpdateServerPing as Job;
use App\Models\Server;
use Illuminate\Console\Command;

final class UpdateServerPings extends Command
{
    protected $signature = 'servers:update-ping';

    protected $description = 'Update the ping of each server.';

    public function handle(): void
    {
        foreach (Server::cursor() as $server) {
            Job::dispatch($server);
        }
    }
}
