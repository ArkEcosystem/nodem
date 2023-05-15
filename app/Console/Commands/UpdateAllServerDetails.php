<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\UpdateAllServerDetails as Job;
use App\Models\Server;
use Illuminate\Console\Command;

final class UpdateAllServerDetails extends Command
{
    protected $signature = 'servers:update-all';

    protected $description = 'Update all the server details';

    public function handle(): void
    {
        Server::all()->each(function ($server): void {
            Job::dispatch($server);
        });
    }
}
