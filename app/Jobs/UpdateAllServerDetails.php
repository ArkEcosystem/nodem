<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\JobQueuesEnum;
use App\Models\Server;
use App\Models\User;
use App\Services\ServerDetailsUpdaterBatch;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class UpdateAllServerDetails
{
    use Dispatchable;
    use SerializesModels;

    public Server $server;

    public ?User $initiator;

    public function __construct(Server $server, ?User $initiator = null)
    {
        $this->server      = $server;
        $this->initiator   = $initiator;
    }

    public function handle(): void
    {
        (new ServerDetailsUpdaterBatch($this->server, $this->initiator))
            ->onQueue(JobQueuesEnum::BACKGROUND_UPDATES_QUEUE)
            ->dispatchSilently();
    }
}
