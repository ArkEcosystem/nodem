<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\AlertFailedTask;
use App\Enums\ServerUpdatingTasksEnum;
use App\Services\Client\RPC;
use Throwable;

final class UpdateServerHeight extends HandlesLoadingState
{
    public function failed(Throwable $exception): void
    {
        if ($exception->getCode() === -32601 ||
            $exception->getCode() === -32603 ||
            ($exception->getCode() >= -32099 && $exception->getCode() <= -32000)) {
            $this->server->setMetaAttribute('unable_to_fetch_height', true);
        }

        AlertFailedTask::dispatch($this->server, $this->getAlertName(), $exception, $this->initiator);

        $this->server->markTaskAsFailed($this->getTaskName());

        if ($exception->getMessage() === 'ERR_NO_RELAY') {
            $this->server->markTaskAsSucceed(ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING);
        } else {
            $this->server->markTaskAsFailed(ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING);
        }
    }

    protected function execute(): void
    {
        $response = RPC::fromServer($this->server)->info()->blockchainHeight();

        $this->server->refresh()->update([
            'height' => $response['height'],
        ]);

        $this->server->setMetaAttribute('unable_to_fetch_height', false);
        $this->server->markTaskAsSucceed(ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING);
    }

    protected function getTaskName(): string
    {
        return ServerUpdatingTasksEnum::UPDATING_SERVER_HEIGHT;
    }
}
