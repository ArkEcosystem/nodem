<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\AlertType;
use App\Enums\ProcessStatusEnum;
use Illuminate\Support\Str;

final class RestartProcess extends ManipulatesProcesses
{
    protected function execute(): array
    {
        return $this->process->restart();
    }

    protected function activityDescription(): string
    {
        return trans('logs.process_restarted', ['type' => Str::title($this->process->type)]);
    }

    protected function setPendingStatus(): void
    {
        $this->process->markAs(ProcessStatusEnum::WAITING_RESTART);
    }

    protected function getAlertName(): string
    {
        return AlertType::RESTART_SERVER;
    }
}
