<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\UpdateProcesses as Job;
use App\Models\Server;
use Illuminate\Console\Command;

final class UpdateProcesses extends Command
{
    protected $signature = 'servers:update-processes';

    protected $description = 'Update the processes of each server.';

    public function handle(): void
    {
        foreach (Server::cursor() as $server) {
            Job::dispatch($server);
        }
    }
}
