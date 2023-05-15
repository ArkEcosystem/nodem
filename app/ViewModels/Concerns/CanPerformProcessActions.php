<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns;

use App\Enums\ServerTypeEnum;
use App\Enums\TeamMemberPermission;
use Illuminate\Support\Facades\Auth;

trait CanPerformProcessActions
{
    public function canStartAny(): bool
    {
        return $this->canStartRelay() || $this->canStartForger();
    }

    public function canStartAll(): bool
    {
        return $this->canStartRelay() && $this->canStartForger();
    }

    public function canRestartAny(): bool
    {
        return $this->canRestartRelay() || $this->canRestartForger();
    }

    public function canRestartAll(): bool
    {
        return $this->canRestartRelay() && $this->canRestartForger();
    }

    public function canStopAny(): bool
    {
        return $this->canStopRelay() || $this->canStopForger();
    }

    public function canStopAll(): bool
    {
        return $this->canStopRelay() && $this->canStopForger();
    }

    public function canDeleteAny(): bool
    {
        return $this->canDeleteRelay() || $this->canDeleteForger();
    }

    public function canDeleteAll(): bool
    {
        return $this->canDeleteRelay() && $this->canDeleteForger();
    }

    public function canStartRelay(): bool
    {
        if (! $this->hasRelay()) {
            return true;
        }

        if ($this->relay()->isStopped()) {
            return true;
        }

        return $this->relay()->isDeleted();
    }

    public function canStartForger(): bool
    {
        if (! $this->hasForger()) {
            return true;
        }

        if ($this->forger()->isStopped()) {
            return true;
        }

        return $this->forger()->isDeleted();
    }

    public function canRestartRelay(): bool
    {
        if (! $this->hasRelay()) {
            return false;
        }

        if ($this->relay()->isErrored()) {
            return true;
        }

        return $this->relay()->isOnline();
    }

    public function canRestartForger(): bool
    {
        if (! $this->hasForger()) {
            return false;
        }

        if ($this->forger()->isErrored()) {
            return true;
        }

        return $this->forger()->isOnline();
    }

    public function canStopRelay(): bool
    {
        if (! $this->hasRelay()) {
            return false;
        }

        return $this->relay()->isOnline();
    }

    public function canStopForger(): bool
    {
        if (! $this->hasForger()) {
            return false;
        }

        return $this->forger()->isOnline();
    }

    public function canDeleteRelay(): bool
    {
        if (! $this->hasRelay()) {
            return false;
        }

        return ! $this->relay()->isDeleted();
    }

    public function canDeleteForger(): bool
    {
        if (! $this->hasForger()) {
            return false;
        }

        return ! $this->forger()->isDeleted();
    }

    public function canStartCore(): bool
    {
        if (! $this->hasCore()) {
            return true;
        }

        if ($this->core()->isStopped()) {
            return true;
        }

        return $this->core()->isDeleted();
    }

    public function canRestartCore(): bool
    {
        if (! $this->hasCore()) {
            return false;
        }

        if ($this->core()->isErrored()) {
            return true;
        }

        return $this->core()->isOnline();
    }

    public function canStopCore(): bool
    {
        if (! $this->hasCore()) {
            return false;
        }

        return $this->core()->isOnline();
    }

    public function canDeleteCore(): bool
    {
        if (! $this->hasCore()) {
            return false;
        }

        return ! $this->core()->isDeleted();
    }

    public function canUpdate(string $type = ''): bool
    {
        if ($type === ServerTypeEnum::CORE) {
            return $this->canUpdateCore();
        }

        if ($type === ServerTypeEnum::CORE_MANAGER) {
            return $this->canUpdateCoreManager();
        }

        return $this->canUpdateCore() || $this->canUpdateCoreManager();
    }

    public function canUpdateCore(): bool
    {
        if (! (bool) Auth::user()?->can(TeamMemberPermission::CORE_UPDATE)) {
            return false;
        }

        if (! $this->hasNewCoreVersion()) {
            return false;
        }

        if ($this->isManagerNotRunning()) {
            return false;
        }

        return ! $this->isUpdating();
    }

    public function canUpdateCoreManager(): bool
    {
        if (! (bool) Auth::user()?->can(TeamMemberPermission::CORE_MANAGER_UPDATE)) {
            return false;
        }

        if (! $this->hasNewCoreManagerVersion()) {
            return false;
        }

        if ($this->isManagerNotRunning()) {
            return false;
        }

        return ! $this->isUpdating();
    }
}
