<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\AlertType;
use App\Jobs\Concerns\HandlesLogActivity;
use App\Jobs\Concerns\ManagesFailedTask;
use App\Models\User;
use App\Services\Client\RPC;

final class RestartCoreManager extends HandlesLoadingState
{
    use HandlesLogActivity;
    use ManagesFailedTask;

    protected function execute(): void
    {
        try {
            RPC::fromServer($this->server)
                ->process()
                ->restart('ark-manager');
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Empty reply from server') === false) {
                throw $e;
            }
        }

        /** @var User $initiator */
        $initiator = $this->initiator;

        $this->logActivity($this->server, $initiator);
    }

    protected function getTaskName(): string
    {
        return AlertType::RESTART_CORE_MANAGER;
    }

    private function activityDescription(): string
    {
        return trans('logs.process_restarted', ['type' => 'Core Manager']);
    }
}
