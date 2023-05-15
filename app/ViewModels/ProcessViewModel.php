<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Enums\ProcessStatusEnum;
use App\Models\Process;

final class ProcessViewModel implements ViewModel
{
    private Process $process;

    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    public function status(): string
    {
        return $this->process->status;
    }

    public function statusIcon(): string
    {
        if ($this->process->server->isUnableToFetchHeight()) {
            return 'unable-to-fetch-height';
        }

        if ($this->process->server->hasHeightMismatch()) {
            return 'height-mismatch';
        }

        if (! $this->process->server->toViewModel()->processTypeIsInline()) {
            return 'not-inline';
        }

        return $this->status();
    }

    public function statusTooltip(): string
    {
        if ($this->process->server->isUnableToFetchHeight()) {
            $tooltip = trans('server.status.unable_to_fetch_height');
        } elseif ($this->process->server->hasHeightMismatch()) {
            $tooltip = trans('server.status.server_height_mismatch');
        } elseif ($this->isOnline()) {
            $tooltip = trans('server.status.online');
        } elseif ($this->isErrored()) {
            $tooltip = trans('server.status.errored');
        } elseif ($this->isUndefined()) {
            $tooltip = trans('server.status.undefined');
        } elseif ($this->isStopped()) {
            $tooltip = trans('server.status.stopped');
        } elseif ($this->isStopping()) {
            $tooltip = trans('server.status.stopping');
        } elseif ($this->isWaitingRestart()) {
            $tooltip = trans('server.status.waiting_restart');
        } elseif ($this->isLaunching()) {
            $tooltip = trans('server.status.launching');
        } elseif ($this->isOneLaunchStatus()) {
            $tooltip = trans('server.status.one_launch_status');
        } elseif ($this->isDeleted()) {
            $tooltip = trans('server.status.deleted');
        } else {
            $tooltip = trans('server.status.offline');
        }

        if (! $this->process->server->toViewModel()->processTypeIsInline()) {
            return sprintf('%s (%s)', $tooltip, trans('tooltips.unexpected_process'));
        }

        return $tooltip;
    }

    public function isUndefined(): bool
    {
        return ProcessStatusEnum::isUndefined($this->process->status);
    }

    public function isOnline(): bool
    {
        return ProcessStatusEnum::isOnline($this->process->status);
    }

    public function isStopped(): bool
    {
        return ProcessStatusEnum::isStopped($this->process->status);
    }

    public function isStopping(): bool
    {
        return ProcessStatusEnum::isStopping($this->process->status);
    }

    public function isWaitingRestart(): bool
    {
        return ProcessStatusEnum::isWaitingRestart($this->process->status);
    }

    public function isLaunching(): bool
    {
        return ProcessStatusEnum::isLaunching($this->process->status);
    }

    public function isErrored(): bool
    {
        return ProcessStatusEnum::isErrored($this->process->status);
    }

    public function isWarningStatus(): bool
    {
        return $this->isStopped() || $this->isStopping();
    }

    public function isOneLaunchStatus(): bool
    {
        return ProcessStatusEnum::isOneLaunchStatus($this->process->status);
    }

    public function isDeleted(): bool
    {
        return ProcessStatusEnum::isDeleted($this->process->status);
    }
}
