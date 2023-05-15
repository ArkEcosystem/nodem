<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\AlertType;
use App\Enums\ProcessStatusEnum;
use Illuminate\Support\Str;

final class StopProcess extends ManipulatesProcesses
{
    protected function execute(): array
    {
        return $this->process->stop();
    }

    protected function activityDescription(): string
    {
        return trans('logs.process_stopped', ['type' => Str::title($this->process->type)]);
    }

    protected function setPendingStatus(): void
    {
        $this->process->markAs(ProcessStatusEnum::STOPPING);
    }

    protected function getAlertName(): string
    {
        return AlertType::STOP_SERVER;
    }
}
