<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Server;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class UpdateServer implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public User $initiator, public Server $server)
    {
        //
    }

    public function handle(): void
    {
        UpdateProcesses::dispatch($this->server, $this->initiator);
        UpdateServerPing::dispatch($this->server, $this->initiator);
        UpdateServerCoreVersion::dispatch($this->server, $this->initiator);
        UpdateServerHeight::dispatch($this->server, $this->initiator);
        UpdateServerResources::dispatch($this->server, $this->initiator);
    }
}
