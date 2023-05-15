<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\UpdateServerHeight as Job;
use App\Models\Server;
use Illuminate\Console\Command;

final class UpdateServerHeights extends Command
{
    protected $signature = 'servers:update-height';

    protected $description = 'Update the height of each server.';

    public function handle(): void
    {
        foreach (Server::cursor() as $server) {
            Job::dispatch($server);
        }
    }
}
