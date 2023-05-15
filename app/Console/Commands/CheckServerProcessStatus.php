<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CheckServerProcessStatus as CheckServerProcessStatusJob;
use App\Models\Server;
use Illuminate\Console\Command;

final class CheckServerProcessStatus extends Command
{
    protected $signature = 'servers:check-process-status';

    protected $description = 'Check if server node height is behind';

    public function handle(): void
    {
        Server::all()->each(function ($server): void {
            CheckServerProcessStatusJob::dispatch($server);
        });
    }
}
