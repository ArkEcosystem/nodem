<?php

declare(strict_types=1);

namespace App\Jobs\Concerns;

use App\Actions\AlertFailedTask;
use Throwable;

trait ManagesFailedTask
{
    public function failed(Throwable $e): void
    {
        AlertFailedTask::dispatch($this->server, $this->getAlertName(), $e, $this->initiator);

        $this->server->markTaskAsFailed($this->getTaskName());
    }
}
