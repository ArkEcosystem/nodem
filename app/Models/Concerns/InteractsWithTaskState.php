<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enums\ServerUpdatingTasksEnum;

trait InteractsWithTaskState
{
    /**
     * Manager is running if server is online and we were able to get the server
     * core version.
     */
    public function isManagerRunning(): bool
    {
        return boolval($this->getMetaAttribute('succeed.'.ServerUpdatingTasksEnum::UPDATING_SERVER_PING))
            && boolval($this->getMetaAttribute('succeed.'.ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING));
    }

    /**
     * Manager is not running if server is online but we were unable to get the
     * server core version.
     */
    public function isManagerNotRunning(): bool
    {
        return ! $this->isManagerRunning();
    }

    /**
     * Server is offline if host is unreachable.
     */
    public function isOffline(): bool
    {
        return boolval($this->getMetaAttribute('failed.'.ServerUpdatingTasksEnum::UPDATING_SERVER_PING));
    }

    public function isLoading(): bool
    {
        $this->refresh();

        return (bool) $this->getMetaAttribute('loading');
    }

    public function isLoadingProcesses(): bool
    {
        $this->refresh();

        return (bool) $this->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_PROCESSES);
    }

    public function isLoadingManagerState(): bool
    {
        $this->refresh();

        return (bool) $this->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_PING)
            || (bool) $this->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_CORE);
    }

    public function isUpdating(): bool
    {
        $this->refresh();

        return (bool) $this->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_CORE)
            || (bool) $this->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_CORE_MANAGER);
    }

    public function markTaskAsStarted(string $task): self
    {
        $this->refresh();

        return $this->setMetaAttribute('loading.'.$task, true);
    }

    public function markTaskAsFinished(string $task): self
    {
        $this->refresh();

        if ($this->hasMetaAttribute('loading.'.$task)) {
            $this->forgetMetaAttribute('loading.'.$task);
        }

        // If there are no longer loading task we can remove the loading object
        if (count($this->getMetaAttribute('loading', [])) === 0) {
            $this->forgetMetaAttribute('loading');
        }

        return $this;
    }

    public function markTaskAsFailed(string $task): self
    {
        $this->refresh();

        // Cannot be successful if it failed
        if ($this->hasMetaAttribute('succeed.'.$task)) {
            $this->forgetMetaAttribute('succeed.'.$task);
        }

        // If there are no longer succeed task we can remove the succeed object
        if (count($this->getMetaAttribute('succeed', [])) === 0) {
            $this->forgetMetaAttribute('succeed');
        }

        $this->setMetaAttribute('failed.'.$task, true);

        $this->markTaskAsFinished($task);

        return $this;
    }

    public function markTaskAsSucceed(string $task): self
    {
        $this->refresh();

        // Cannot be failed if it succeeds
        if ($this->hasMetaAttribute('failed.'.$task)) {
            $this->forgetMetaAttribute('failed.'.$task);
        }

        // If there are no longer failed task we can remove the failed object
        if (count($this->getMetaAttribute('failed', [])) === 0) {
            $this->forgetMetaAttribute('failed');
        }

        $this->setMetaAttribute('succeed.'.$task, true);

        $this->markTaskAsFinished($task);

        return $this;
    }

    public function resetLoadingServerDetailsStates(): self
    {
        $this->refresh();

        $this->forgetMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_PROCESSES);
        $this->forgetMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_CORE);
        $this->forgetMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_HEIGHT);
        $this->forgetMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_PING);
        $this->forgetMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_RESOURCES);

        return $this;
    }
}
