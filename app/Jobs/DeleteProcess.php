<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\AlertType;
use App\Enums\ProcessStatusEnum;
use Illuminate\Support\Str;

final class DeleteProcess extends ManipulatesProcesses
{
    protected function execute(): array
    {
        return $this->process->remove();
    }

    protected function activityDescription(): string
    {
        return trans('logs.process_deleted', ['type' => Str::title($this->process->type)]);
    }

    protected function setPendingStatus(): void
    {
        $this->process->markAs(ProcessStatusEnum::DELETED);
    }

    protected function getAlertName(): string
    {
        return AlertType::DELETE_PROCESS;
    }
}
