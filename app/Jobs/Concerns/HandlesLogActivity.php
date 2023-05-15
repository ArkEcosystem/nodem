<?php

declare(strict_types=1);

namespace App\Jobs\Concerns;

use App\Models\Server;
use App\Models\User;

trait HandlesLogActivity
{
    private function logActivity(Server $server, User $initiator): void
    {
        activity()
            ->performedOn($server)
            ->causedBy($initiator)
            ->withProperties(['username' => $initiator->username])
            ->log($this->activityDescription());
    }
}
