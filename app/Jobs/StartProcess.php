<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\AlertType;
use App\Enums\ProcessStatusEnum;
use Illuminate\Support\Str;

final class StartProcess extends ManipulatesProcesses
{
    protected ?string $successStatus = ProcessStatusEnum::ONLINE;

    protected function execute(): array
    {
        return $this->process->start($this->options);
    }

    protected function activityDescription(): string
    {
        return trans('logs.process_started', ['type' => Str::title($this->process->type)]);
    }

    protected function setPendingStatus(): void
    {
        $this->process->markAs(ProcessStatusEnum::LAUNCHING);
    }

    protected function getAlertName(): string
    {
        return AlertType::START_SERVER;
    }
}
