<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Cache\ServerStore;
use App\Contracts\ViewModel;
use App\Enums\ServerProcessTypeEnum;
use App\Enums\ServerProviderTypeEnum;
use App\Enums\ServerTypeEnum;
use App\Enums\TeamMemberPermission;
use App\Models\Server;
use App\ViewModels\Concerns\CanBeCached;
use App\ViewModels\Concerns\CanPerformProcessActions;
use Composer\Semver\Comparator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Mattiasgeniar\Percentage\Percentage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class ServerViewModel implements ViewModel
{
    use CanBeCached;
    use CanPerformProcessActions;

    private Server $model;

    private string $cacheTag;

    public function __construct(Server $server)
    {
        $this->model    = $server;
        $this->cacheTag = ServerStore::getViewCacheTag($server);
    }

    public function id(): int
    {
        return $this->model->id;
    }

    public function model(): Server
    {
        return $this->model;
    }

    public function provider(): string
    {
        return $this->model->provider;
    }

    public function providerIcon(): string
    {
        return ServerProviderTypeEnum::iconName($this->model->provider);
    }

    public function name(): string
    {
        return $this->model->name;
    }

    public function host(): string
    {
        return $this->model->host;
    }

    public function hostShort(): string
    {
        return (string) parse_url($this->host(), PHP_URL_HOST);
    }

    public function ping(): int|null
    {
        return (int) $this->model->ping;
    }

    public function pingSucceed(): bool
    {
        return Cache::get('ping-'.$this->host()) === true;
    }

    public function pingFailed(): bool
    {
        return Cache::get('ping-'.$this->host()) === false;
    }

    public function pingIsPending(): bool
    {
        return Cache::get('ping-'.$this->host()) === null;
    }

    public function height(): int|null
    {
        return (int) $this->model->height;
    }

    public function coreCurrentVersion(): string
    {
        return $this->model->core_version_current ?? '';
    }

    public function coreLatestVersion(): string
    {
        return $this->model->core_version_latest ?? '';
    }

    public function coreManagerCurrentVersion(): string
    {
        return $this->model->coreManagerCurrentVersion();
    }

    public function coreManagerLatestVersion(): string
    {
        return $this->model->coreManagerLatestVersion();
    }

    public function hasNewCoreVersion(): bool
    {
        $currentVersion = $this->coreCurrentVersion();

        if ($currentVersion === '') {
            return false;
        }

        $latestVersion = $this->coreLatestVersion();

        if ($latestVersion === '') {
            return false;
        }

        return Comparator::greaterThan($latestVersion, $currentVersion);
    }

    public function hasNewCoreManagerVersion(): bool
    {
        $currentVersion = $this->coreManagerCurrentVersion();

        if ($currentVersion === '') {
            return false;
        }

        $latestVersion = $this->coreManagerLatestVersion();

        if ($latestVersion === '') {
            return false;
        }

        return Comparator::greaterThan($latestVersion, $currentVersion);
    }

    public function hasNewVersion(): bool
    {
        return $this->hasNewCoreVersion() || $this->hasNewCoreManagerVersion();
    }

    public function hasNewVersionFor(string $type): bool
    {
        if (! collect([
            ServerTypeEnum::CORE,
            ServerTypeEnum::CORE_MANAGER,
        ])->containsStrict($type)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid argument.', $type));
        }

        $method = vsprintf('hasNew%sVersion', [
            $type === ServerTypeEnum::CORE ? 'Core' : 'CoreManager',
        ]);

        return call_user_func_safe([$this, $method]);
    }

    public function cpu(): int|null
    {
        return $this->model->cpu_total ?? 0;
    }

    public function ram(): int|null
    {
        return $this->model->ram_total ?? 0;
    }

    public function diskTotal(): int
    {
        return $this->model->disk_total ?? 0;
    }

    public function diskFree(): int|null
    {
        return $this->model->disk_available ?? 0;
    }

    public function cpuPercentage(): float
    {
        return round($this->model->cpu_used ?? 0, 2);
    }

    public function ramPercentage(): float
    {
        if (is_null($this->model->ram_used)) {
            return 0;
        }

        return round(Percentage::calculate($this->model->ram_used, (float) $this->model->ram_total), 2);
    }

    public function diskPercentage(): float
    {
        if (is_null($this->model->disk_used)) {
            return 0;
        }

        return round(Percentage::calculate($this->model->disk_used, (float) $this->model->disk_total), 2);
    }

    public function hasCore(): bool
    {
        return $this->model->processes->where('type', 'core')->count() > 0;
    }

    public function hasRelay(): bool
    {
        return $this->model->processes->where('type', 'relay')->count() > 0;
    }

    public function hasForger(): bool
    {
        return $this->model->processes->where('type', 'forger')->count() > 0;
    }

    public function core(): ProcessViewModel
    {
        return $this->storeWithCache(fn (): ProcessViewModel => $this->getProcess('core'), [$this->cacheTag]);
    }

    public function relay(): ProcessViewModel
    {
        return $this->storeWithCache(fn (): ProcessViewModel => $this->getProcess('relay'), [$this->cacheTag]);
    }

    public function forger(): ProcessViewModel
    {
        return $this->storeWithCache(fn (): ProcessViewModel => $this->getProcess('forger'), [$this->cacheTag]);
    }

    public function prefersCombined(): bool
    {
        return $this->model->process_type === ServerProcessTypeEnum::COMBINED;
    }

    public function prefersSeparated(): bool
    {
        return $this->model->process_type === ServerProcessTypeEnum::SEPARATE;
    }

    public function isAWS(): bool
    {
        return ServerProviderTypeEnum::isAWS($this->model->provider);
    }

    public function isDigitalOcean(): bool
    {
        return ServerProviderTypeEnum::isDigitalOcean($this->model->provider);
    }

    public function isHetzner(): bool
    {
        return ServerProviderTypeEnum::isHetzner($this->model->provider);
    }

    public function isLinode(): bool
    {
        return ServerProviderTypeEnum::isLinode($this->model->provider);
    }

    public function isVultr(): bool
    {
        return ServerProviderTypeEnum::isVultr($this->model->provider);
    }

    public function isCustom(): bool
    {
        return ServerProviderTypeEnum::isCustom($this->model->provider);
    }

    public function hasWarningState(): bool
    {
        if ($this->hasCore()) {
            return $this->core()->isWarningStatus();
        }

        if ($this->hasForger() && $this->forger()->isWarningStatus()) {
            return true;
        }

        if ($this->hasRelay() && $this->relay()->isWarningStatus()) {
            return true;
        }

        return false;
    }

    public function hasErrorState(): bool
    {
        if ($this->isOffline()) {
            return true;
        }

        if ($this->hasCore() && $this->core()->isErrored()) {
            return true;
        }

        if ($this->hasForger() && $this->forger()->isErrored()) {
            return true;
        }

        if ($this->hasRelay() && $this->relay()->isErrored()) {
            return true;
        }

        return false;
    }

    public function processTypeIsInline(): bool
    {
        if ($this->prefersCombined() && $this->hasForger() && ! $this->forger()->isStopped()) {
            return false;
        }

        if ($this->prefersCombined() && $this->hasRelay() && ! $this->relay()->isStopped()) {
            return false;
        }

        if ($this->prefersSeparated() && $this->hasCore() && ! $this->core()->isStopped()) {
            return false;
        }

        return true;
    }

    public function usesBip38Encryption(): bool
    {
        return $this->model->uses_bip38_encryption;
    }

    public function actionRequiresPassword(string $action, string $type): bool
    {
        if (! $this->usesBip38Encryption() || $action !== 'start') {
            return  false;
        }

        if ($this->prefersCombined()) {
            return $this->canStartCore();
        }

        return $this->canStartForger()
            && in_array($type, [ServerTypeEnum::FORGER, 'all'], true);
    }

    public function isManagerRunning(): bool
    {
        return $this->model->isManagerRunning();
    }

    public function isUpdating(): bool
    {
        return $this->model->isUpdating();
    }

    public function isLoadingManagerState(): bool
    {
        return $this->model->isLoadingManagerState();
    }

    public function isOnPendingState(): bool
    {
        if ($this->hasPendingProcesses()) {
            return true;
        }

        if ($this->isLoading()) {
            return true;
        }

        return false;
    }

    public function isOffline(): bool
    {
        return $this->model->isOffline();
    }

    public function isNotAvailable(): bool
    {
        return $this->isManagerNotRunning() || $this->isOffline();
    }

    public function actionTooltip(?string $action = null): ?string
    {
        $statusTooltip = $this->statusTooltip();

        if ($statusTooltip !== null) {
            return $statusTooltip;
        }

        if ((bool) Auth::user()?->cannot(TeamMemberPermission::SERVER_PROCESSES_DELETE) && $action === 'delete') {
            return trans('server.tooltips.no_permission', ['action' => $action]);
        }

        return null;
    }

    public function statusIcon(): string
    {
        if ($this->hasHeightMismatch()) {
            return 'height-mismatch';
        }

        if ($this->isManagerRunning()) {
            return 'online';
        }

        if ($this->isOffline()) {
            return 'undefined';
        }

        return 'stopped';
    }

    public function statusTooltip(): string | null
    {
        if ($this->isOffline()) {
            return trans('server.tooltips.connection_failed', ['server_name' => $this->name()]);
        }

        if ($this->isManagerNotRunning()) {
            return trans('server.tooltips.process_not_running');
        }

        return null;
    }

    public function canGetHeight(): bool
    {
        if ($this->isManagerNotRunning()) {
            return false;
        }

        if ($this->hasCore()) {
            return $this->core()->isOnline();
        }

        if ($this->hasRelay()) {
            return $this->relay()->isOnline();
        }

        return false;
    }

    public function isLoading(): bool
    {
        return $this->model->isLoading();
    }

    public function isSilentLoading(): bool
    {
        return $this->model->getMetaAttribute('silent_update') === true;
    }

    public function isLoadingProcesses(): bool
    {
        return $this->model->isLoadingProcesses();
    }

    public function isManagerNotRunning(): bool
    {
        return $this->model->isManagerNotRunning();
    }

    public function hasPendingProcesses(): bool
    {
        return $this->storeWithCache(function (): bool {
            $processesViewModels = [];

            if ($this->hasCore()) {
                $processesViewModels[] = $this->core();
            }
            if ($this->hasRelay()) {
                $processesViewModels[] = $this->relay();
            }
            if ($this->hasForger()) {
                $processesViewModels[] = $this->forger();
            }

            return collect($processesViewModels)->some(function (ProcessViewModel $process): bool {
                return $process->isLaunching()
                    || $process->isStopping()
                    || $process->isWaitingRestart();
            });
        }, [$this->cacheTag]);
    }

    public function logo(): Media|null
    {
        return null;
    }

    public function hasHeightMismatch(): bool
    {
        return $this->model->hasHeightMismatch();
    }

    private function getProcess(string $type): ProcessViewModel
    {
        $process = $this->model->processes->where('type', $type)->first();

        abort_if($process === null, 404);

        /** @var ProcessViewModel $viewModel */
        $viewModel = ViewModelFactory::make($process);

        return $viewModel;
    }
}
