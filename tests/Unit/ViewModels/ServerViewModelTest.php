<?php

declare(strict_types=1);

use App\Cache\ServerStore;
use App\Enums\ProcessStatusEnum;
use App\Enums\ServerProviderTypeEnum;
use App\Enums\ServerTypeEnum;
use App\Enums\ServerUpdatingTasksEnum;
use App\Enums\TeamMemberRole;
use App\Models\Process;
use App\Models\Server;
use App\Models\User;
use App\ViewModels\ProcessViewModel;
use App\ViewModels\ServerViewModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

function clearCache(Server $server)
{
    ServerStore::flush($server);
}

it('should get the provider', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->provider())->toBe($server->provider);
});

it('should get the name', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->name())->toBe($server->name);
});

it('should get the host', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->host())->toBe($server->host);
});

it('should get the host IP', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->hostShort())->not()->toBe($server->host);
});

it('should get the ping', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->ping())->toBe($server->ping);
});

it('should get the height', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->height())->toBe($server->height);
});

it('should get the current core version', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->coreCurrentVersion())->toBe($server->core_version_current);
});

it('should get the current core manager version', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->coreManagerCurrentVersion())->toBe($server->coreManagerCurrentVersion());
});

it('should get the latest core version', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->coreLatestVersion())->toBe($server->core_version_latest);
});

it('should get the latest core manager version', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->coreManagerLatestVersion())->toBe($server->coreManagerLatestVersion());
});

it('should determine if the server has a new core version available ', function (): void {
    $subject = new ServerViewModel(Server::factory()->create([
        'core_version_current' => null,
        'core_version_latest'  => '1.0.0',
    ]));

    expect($subject->hasNewVersion())->toBeFalse();
    expect($subject->hasNewCoreVersion())->toBeFalse();
    expect($subject->hasNewVersionFor(ServerTypeEnum::CORE))->toBeFalse();

    $subject = new ServerViewModel(Server::factory()->create([
        'core_version_current' => '1.0.0',
        'core_version_latest'  => null,
    ]));

    expect($subject->hasNewVersion())->toBeFalse();
    expect($subject->hasNewCoreVersion())->toBeFalse();
    expect($subject->hasNewVersionFor(ServerTypeEnum::CORE))->toBeFalse();

    $subject = new ServerViewModel(Server::factory()->create([
        'core_version_current' => '1.0.0',
        'core_version_latest'  => '1.0.0',
    ]));

    expect($subject->hasNewVersion())->toBeFalse();
    expect($subject->hasNewCoreVersion())->toBeFalse();
    expect($subject->hasNewVersionFor(ServerTypeEnum::CORE))->toBeFalse();

    $subject = new ServerViewModel(Server::factory()->create([
        'core_version_current' => '1.0.0',
        'core_version_latest'  => '2.0.0',
    ]));

    expect($subject->hasNewVersion())->toBeTrue();
    expect($subject->hasNewCoreVersion())->toBeTrue();
    expect($subject->hasNewVersionFor(ServerTypeEnum::CORE))->toBeTrue();
});

it('should determine if the server has a new core manager version available ', function (): void {
    $subject = new ServerViewModel(Server::factory()->create([
        'extra_attributes->core_manager_current_version' => null,
        'extra_attributes->core_manager_latest_version'  => '1.0.0',
    ]));

    expect($subject->hasNewVersion())->toBeFalse();
    expect($subject->hasNewCoreManagerVersion())->toBeFalse();
    expect($subject->hasNewVersionFor(ServerTypeEnum::CORE_MANAGER))->toBeFalse();

    $subject = new ServerViewModel(Server::factory()->create([
        'extra_attributes->core_manager_current_version' => '1.0.0',
        'extra_attributes->core_manager_latest_version'  => null,
    ]));

    expect($subject->hasNewVersion())->toBeFalse();
    expect($subject->hasNewCoreManagerVersion())->toBeFalse();
    expect($subject->hasNewVersionFor(ServerTypeEnum::CORE_MANAGER))->toBeFalse();

    $subject = new ServerViewModel(Server::factory()->create([
        'extra_attributes->core_manager_current_version' => '1.0.0',
        'extra_attributes->core_manager_latest_version'  => '1.0.0',
    ]));

    expect($subject->hasNewVersion())->toBeFalse();
    expect($subject->hasNewCoreManagerVersion())->toBeFalse();
    expect($subject->hasNewVersionFor(ServerTypeEnum::CORE_MANAGER))->toBeFalse();

    $subject = new ServerViewModel(Server::factory()->create([
        'extra_attributes->core_manager_current_version' => '1.0.0',
        'extra_attributes->core_manager_latest_version'  => '2.0.0',
    ]));

    expect($subject->hasNewVersion())->toBeTrue();
    expect($subject->hasNewCoreManagerVersion())->toBeTrue();
    expect($subject->hasNewVersionFor(ServerTypeEnum::CORE_MANAGER))->toBeTrue();
});

it('should throw an exception if pass an invalid server type when check for new version', function (): void {
    $subject = new ServerViewModel(Server::factory()->create([
        'extra_attributes->core_manager_current_version' => null,
        'extra_attributes->core_manager_latest_version'  => '1.0.0',
    ]));

    $subject->hasNewVersionFor('invalid-server-type');
})->throws(\InvalidArgumentException::class);

it('should get the cpu', function (): void {
    $subject = new ServerViewModel(Server::factory()->create(['cpu_total' => null]));

    expect($subject->cpu())->toBe(0);

    $subject = new ServerViewModel(Server::factory()->create(['cpu_total' => 4]));

    expect($subject->cpu())->toBe(4);
});

it('should get the ram', function (): void {
    $subject = new ServerViewModel(Server::factory()->create(['ram_total' => null]));

    expect($subject->ram())->toBe(0);

    $subject = new ServerViewModel(Server::factory()->create(['ram_total' => 512]));

    expect($subject->ram())->toBe(512);
});

it('should get the disk total', function (): void {
    $subject = new ServerViewModel(Server::factory()->create(['disk_total' => 1024]));

    expect($subject->diskTotal())->toBe(1024);
});

it('should get the disk free', function (): void {
    $subject = new ServerViewModel(Server::factory()->create(['disk_available' => 512]));

    expect($subject->diskFree())->toBe(512);
});

it('should get the cpu percentage', function (): void {
    $subject = new ServerViewModel(Server::factory()->create(['cpu_used' => null]));

    expect($subject->cpuPercentage())->toBe(0.0);

    $server = Server::factory()->create(['cpu_used' => 50.0]);

    $subject = new ServerViewModel($server);

    expect($subject->cpuPercentage())->toBe(50.0);
});

it('should get the ram percentage', function (): void {
    $subject = new ServerViewModel(Server::factory()->create(['ram_used' => null]));

    expect($subject->ramPercentage())->toBe(0.0);

    $server = Server::factory()->create([
        'ram_total' => 1024,
        'ram_used'  => 512,
    ]);

    $subject = new ServerViewModel($server);

    expect($subject->ramPercentage())->toBe(50.0);
});

it('should get the disk percentage', function (): void {
    $subject = new ServerViewModel(Server::factory()->create([
        'disk_total'     => null,
        'disk_used'      => null,
        'disk_available' => null,
    ]));

    expect($subject->diskPercentage())->toBe(0.0);

    $subject = new ServerViewModel(Server::factory()->create([
        'disk_total'     => 1024,
        'disk_used'      => 512,
        'disk_available' => 512,
    ]));

    expect($subject->diskPercentage())->toBe(50.0);
});

it('should determine if the value is a core instance', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    Process::factory()->create(['server_id' => $server->id, 'type' => 'core']);

    expect($subject->hasCore())->toBeTrue();
    expect($subject->hasRelay())->toBeFalse();
    expect($subject->hasForger())->toBeFalse();
});

it('should determine if the value is a relay instance', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    Process::factory()->create(['server_id' => $server->id, 'type' => 'relay']);

    expect($subject->hasCore())->toBeFalse();
    expect($subject->hasRelay())->toBeTrue();
    expect($subject->hasForger())->toBeFalse();
});

it('should determine if the server has entered a warning state because of the core', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->hasWarningState())->toBeFalse();

    $server->processes()->save(Process::factory()->create([
        'type'   => 'core',
        'status' => ProcessStatusEnum::STOPPED,
    ]));

    $server->refresh();

    expect($subject->hasWarningState())->toBeTrue();
});

it('should get a view model instance of the given process', function (string $process): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    Process::factory()->create(['server_id' => $server->id, 'type' => $process]);

    expect($subject->{$process}())->toBeInstanceOf(ProcessViewModel::class);
})->with(['core', 'forger', 'relay']);

it('should determine if the value is a forger instance', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    Process::factory()->create(['server_id' => $server->id, 'type' => 'forger']);

    expect($subject->hasCore())->toBeFalse();
    expect($subject->hasRelay())->toBeFalse();
    expect($subject->hasForger())->toBeTrue();
});

it('should determine if the value is aws', function (): void {
    $subject = new ServerViewModel(Server::factory()->create(['provider' => ServerProviderTypeEnum::AWS]));

    expect($subject->isAWS())->toBeTrue();
    expect($subject->isDigitalOcean())->toBeFalse();
    expect($subject->isHetzner())->toBeFalse();
    expect($subject->isLinode())->toBeFalse();
    expect($subject->isVultr())->toBeFalse();
    expect($subject->isCustom())->toBeFalse();
});

it('should determine if the value is digitalocean', function (): void {
    $subject = new ServerViewModel(Server::factory()->create(['provider' => ServerProviderTypeEnum::DIGITAL_OCEAN]));

    expect($subject->isAWS())->toBeFalse();
    expect($subject->isDigitalOcean())->toBeTrue();
    expect($subject->isHetzner())->toBeFalse();
    expect($subject->isLinode())->toBeFalse();
    expect($subject->isVultr())->toBeFalse();
    expect($subject->isCustom())->toBeFalse();
});

it('should determine if the value is hetzner', function (): void {
    $subject = new ServerViewModel(Server::factory()->create(['provider' => ServerProviderTypeEnum::HETZNER]));

    expect($subject->isAWS())->toBeFalse();
    expect($subject->isDigitalOcean())->toBeFalse();
    expect($subject->isHetzner())->toBeTrue();
    expect($subject->isLinode())->toBeFalse();
    expect($subject->isVultr())->toBeFalse();
    expect($subject->isCustom())->toBeFalse();
});

it('should determine if the value is linode', function (): void {
    $subject = new ServerViewModel(Server::factory()->create(['provider' => ServerProviderTypeEnum::LINODE]));

    expect($subject->isAWS())->toBeFalse();
    expect($subject->isDigitalOcean())->toBeFalse();
    expect($subject->isHetzner())->toBeFalse();
    expect($subject->isLinode())->toBeTrue();
    expect($subject->isVultr())->toBeFalse();
    expect($subject->isCustom())->toBeFalse();
});

it('should determine if the value is vultr', function (): void {
    $subject = new ServerViewModel(Server::factory()->create(['provider' => ServerProviderTypeEnum::VULTR]));

    expect($subject->isAWS())->toBeFalse();
    expect($subject->isDigitalOcean())->toBeFalse();
    expect($subject->isHetzner())->toBeFalse();
    expect($subject->isLinode())->toBeFalse();
    expect($subject->isVultr())->toBeTrue();
    expect($subject->isCustom())->toBeFalse();
});

it('should determine if the value is custom', function (): void {
    $subject = new ServerViewModel(Server::factory()->create(['provider' => 'custom']));

    expect($subject->isAWS())->toBeFalse();
    expect($subject->isDigitalOcean())->toBeFalse();
    expect($subject->isHetzner())->toBeFalse();
    expect($subject->isLinode())->toBeFalse();
    expect($subject->isVultr())->toBeFalse();
    expect($subject->isCustom())->toBeTrue();
});

it('should determine if the server has entered a warning state because of the forger', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->hasWarningState())->toBeFalse();

    $server->processes()->save(Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::STOPPED,
    ]));

    $server->refresh();

    expect($subject->hasWarningState())->toBeTrue();
});

it('should determine if the server has entered a warning state because of the relay', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->hasWarningState())->toBeFalse();

    $server->processes()->save(Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::STOPPED,
    ]));

    $server->refresh();

    expect($subject->hasWarningState())->toBeTrue();
});

it('should determine if the server has entered an error state because of the core', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->hasErrorState())->toBeFalse();

    $server->processes()->save(Process::factory()->create([
        'type'   => 'core',
        'status' => ProcessStatusEnum::ERRORED,
    ]));

    $server->refresh();

    expect($subject->hasErrorState())->toBeTrue();
});

it('should determine if the server has entered an error state because of the forger', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->hasErrorState())->toBeFalse();

    $server->processes()->save(Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::ERRORED,
    ]));

    $server->refresh();

    expect($subject->hasErrorState())->toBeTrue();
});

it('should determine if the server has entered an error state because of the relay', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->hasErrorState())->toBeFalse();

    $server->processes()->save(Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::ERRORED,
    ]));

    $server->refresh();

    expect($subject->hasErrorState())->toBeTrue();
});

it('should determine if the server has entered an error state because is offline', function (): void {
    $subject = new ServerViewModel(Server::factory()->offline()->create());

    expect($subject->hasErrorState())->toBeTrue();
});

it('should get the logo', function (): void {
    $subject = Server::factory()->create()->toViewModel();

    expect($subject->logo())->toBeNull();
});

it('should be able to start any process', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStartAny())->toBeTrue();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::STOPPED,
    ]));
    $server->refresh();

    expect($subject->canStartAny())->toBeTrue();

    $forgerProcess->status = ProcessStatusEnum::ONLINE;
    $forgerProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStartAny())->toBeTrue();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::STOPPED,
    ]));
    $server->refresh();
    clearCache($server);

    expect($subject->canStartAny())->toBeTrue();

    $relayProcess->status = ProcessStatusEnum::ONLINE;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStartAny())->toBeFalse();
});

it('should not be able to start an already started process', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStartAny())->toBeTrue();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::ONLINE,
    ]));

    $server->refresh();

    expect($subject->canStartForger())->toBeFalse();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::ONLINE,
    ]));

    $server->refresh();

    expect($subject->canStartRelay())->toBeFalse();
    expect($subject->canStartAny())->toBeFalse();
    expect($subject->canStartAll())->toBeFalse();
});

it('should be able to start a deleted process', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStartAny())->toBeTrue();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::DELETED,
    ]));

    $server->refresh();

    expect($subject->canStartForger())->toBeTrue();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::DELETED,
    ]));

    $server->refresh();

    expect($subject->canStartRelay())->toBeTrue();
    expect($subject->canStartAny())->toBeTrue();
    expect($subject->canStartAll())->toBeTrue();
});

it('should be able to start all processes', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStartAll())->toBeTrue();

    $server->processes()->save(Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::STOPPED,
    ]));
    $server->refresh();

    expect($subject->canStartAll())->toBeTrue();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::STOPPED,
    ]));
    $server->refresh();

    expect($subject->canStartAll())->toBeTrue();

    $relayProcess->status = ProcessStatusEnum::ONLINE;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStartAll())->toBeFalse();
});

it('should be able to start relay', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStartRelay())->toBeTrue();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::STOPPED,
    ]));
    $server->refresh();

    expect($subject->canStartRelay())->toBeTrue();

    $forgerProcess->status = ProcessStatusEnum::ONLINE;
    $forgerProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStartRelay())->toBeTrue();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::STOPPED,
    ]));
    $server->refresh();

    expect($subject->canStartRelay())->toBeTrue();

    $relayProcess->status = ProcessStatusEnum::ONLINE;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStartRelay())->toBeFalse();
});

it('should be able to start forger', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStartForger())->toBeTrue();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::STOPPED,
    ]));
    $server->refresh();

    expect($subject->canStartForger())->toBeTrue();

    $relayProcess->status = ProcessStatusEnum::ONLINE;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStartForger())->toBeTrue();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::STOPPED,
    ]));
    $server->refresh();

    expect($subject->canStartForger())->toBeTrue();

    $forgerProcess->status = ProcessStatusEnum::ONLINE;
    $forgerProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStartForger())->toBeFalse();
});

it('should be able to restart any process', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canRestartAny())->toBeFalse();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canRestartAny())->toBeTrue();

    $forgerProcess->status = ProcessStatusEnum::STOPPED;
    $forgerProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartAny())->toBeFalse();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartAny())->toBeTrue();

    $forgerProcess->status = ProcessStatusEnum::ERRORED;
    $forgerProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartAny())->toBeTrue();

    $forgerProcess->status = ProcessStatusEnum::STOPPED;
    $forgerProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartAny())->toBeTrue();

    $relayProcess->status = ProcessStatusEnum::STOPPED;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartAny())->toBeFalse();
});

it('should not be able to restart a deleted process', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStartAny())->toBeTrue();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::DELETED,
    ]));

    $server->refresh();

    expect($subject->canRestartForger())->toBeFalse();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::DELETED,
    ]));

    $server->refresh();

    expect($subject->canRestartRelay())->toBeFalse();
    expect($subject->canRestartAny())->toBeFalse();
    expect($subject->canRestartAll())->toBeFalse();
});

it('should be able to restart all processes', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canRestartAll())->toBeFalse();

    $server->processes()->save(Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canRestartAll())->toBeFalse();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canRestartAll())->toBeTrue();

    $relayProcess->status = ProcessStatusEnum::STOPPED;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartAll())->toBeFalse();

    $relayProcess->status = ProcessStatusEnum::ERRORED;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartAll())->toBeTrue();
});

it('should be able to restart relay', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canRestartRelay())->toBeFalse();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canRestartRelay())->toBeFalse();

    $forgerProcess->status = ProcessStatusEnum::STOPPED;
    $forgerProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartRelay())->toBeFalse();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canRestartRelay())->toBeTrue();

    $relayProcess->status = ProcessStatusEnum::STOPPED;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartRelay())->toBeFalse();

    $relayProcess->status = ProcessStatusEnum::ERRORED;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartRelay())->toBeTrue();
});

it('should be able to restart forger', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canRestartForger())->toBeFalse();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canRestartForger())->toBeFalse();

    $relayProcess->status = ProcessStatusEnum::STOPPED;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartForger())->toBeFalse();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canRestartForger())->toBeTrue();

    $forgerProcess->status = ProcessStatusEnum::STOPPED;
    $forgerProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartForger())->toBeFalse();

    $forgerProcess->status = ProcessStatusEnum::ERRORED;
    $forgerProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canRestartForger())->toBeTrue();
});

it('should be able to stop any process', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStopAny())->toBeFalse();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canStopAny())->toBeTrue();

    $forgerProcess->status = ProcessStatusEnum::STOPPED;
    $forgerProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStopAny())->toBeFalse();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();
    clearCache($server);

    expect($subject->canStopAny())->toBeTrue();

    $relayProcess->status = ProcessStatusEnum::STOPPED;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStopAny())->toBeFalse();
});

it('should not be able to stop an already stopped process', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStartAny())->toBeTrue();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::STOPPED,
    ]));

    $server->refresh();

    expect($subject->canStopForger())->toBeFalse();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::STOPPED,
    ]));

    $server->refresh();

    expect($subject->canStopRelay())->toBeFalse();
    expect($subject->canStopAny())->toBeFalse();
    expect($subject->canStopAll())->toBeFalse();
});

it('should be able to stop all processes', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStopAll())->toBeFalse();

    $server->processes()->save(Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canStopAll())->toBeFalse();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canStopAll())->toBeTrue();

    $relayProcess->status = ProcessStatusEnum::STOPPED;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStopAll())->toBeFalse();
});

it('should be able to stop relay', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStopRelay())->toBeFalse();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canStopRelay())->toBeFalse();

    $forgerProcess->status = ProcessStatusEnum::STOPPED;
    $forgerProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStopRelay())->toBeFalse();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canStopRelay())->toBeTrue();

    $relayProcess->status = ProcessStatusEnum::STOPPED;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStopRelay())->toBeFalse();
});

it('should be able to stop forger', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStopForger())->toBeFalse();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canStopForger())->toBeFalse();

    $relayProcess->status = ProcessStatusEnum::STOPPED;
    $relayProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStopForger())->toBeFalse();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::ONLINE,
    ]));
    $server->refresh();

    expect($subject->canStopForger())->toBeTrue();

    $forgerProcess->status = ProcessStatusEnum::STOPPED;
    $forgerProcess->save();
    $server->refresh();
    clearCache($server);

    expect($subject->canStopForger())->toBeFalse();
});

it('should be able to delete any process', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canDeleteAny())->toBeFalse();

    $server->processes()->save(Process::factory()->create(['type' => 'forger']));
    $server->refresh();

    expect($subject->canDeleteAny())->toBeTrue();

    $server->processes()->delete();
    $server->refresh();

    expect($subject->canDeleteAny())->toBeFalse();

    $server->processes()->save(Process::factory()->create(['type' => 'relay']));
    $server->refresh();

    expect($subject->canDeleteAny())->toBeTrue();
});

it('should not be able to delete an already deleted process', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canStartAny())->toBeTrue();

    $server->processes()->save($forgerProcess = Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::DELETED,
    ]));

    $server->refresh();

    expect($subject->canDeleteForger())->toBeFalse();

    $server->processes()->save($relayProcess = Process::factory()->create([
        'type'   => 'relay',
        'status' => ProcessStatusEnum::DELETED,
    ]));

    $server->refresh();

    expect($subject->canDeleteRelay())->toBeFalse();
    expect($subject->canDeleteAny())->toBeFalse();
    expect($subject->canDeleteAll())->toBeFalse();
});

it('should be able to delete all processes', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canDeleteAll())->toBeFalse();

    $server->processes()->save(Process::factory()->create(['type' => 'forger']));
    $server->refresh();

    expect($subject->canDeleteAll())->toBeFalse();

    $server->processes()->save(Process::factory()->create(['type' => 'relay']));
    $server->refresh();

    expect($subject->canDeleteAll())->toBeTrue();
});

it('should be able to delete relay', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canDeleteRelay())->toBeFalse();

    $server->processes()->save(Process::factory()->create(['type' => 'forger']));
    $server->refresh();

    expect($subject->canDeleteRelay())->toBeFalse();

    $server->processes()->save(Process::factory()->create(['type' => 'relay']));
    $server->refresh();

    expect($subject->canDeleteRelay())->toBeTrue();
});

it('should be able to delete forger', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    expect($subject->canDeleteForger())->toBeFalse();

    $server->processes()->save(Process::factory()->create(['type' => 'relay']));
    $server->refresh();

    expect($subject->canDeleteForger())->toBeFalse();

    $server->processes()->save(Process::factory()->create(['type' => 'forger']));
    $server->refresh();

    expect($subject->canDeleteForger())->toBeTrue();
});

it('should get the provider icon', function ($provider, $iconName): void {
    $subject = Server::factory()->create(['provider' => $provider])->toViewModel();

    expect($subject->providerIcon())->toBe($iconName);
})->with([
    [ServerProviderTypeEnum::AWS, 'app-provider.aws'],
    [ServerProviderTypeEnum::AZURE, 'app-provider.azure'],
    [ServerProviderTypeEnum::DIGITAL_OCEAN, 'app-provider.digitalocean'],
    [ServerProviderTypeEnum::GOOGLE, 'app-provider.google'],
    [ServerProviderTypeEnum::HETZNER, 'app-provider.hetzner'],
    [ServerProviderTypeEnum::LINODE, 'app-provider.linode'],
    [ServerProviderTypeEnum::NETCUP, 'app-provider.netcup'],
    [ServerProviderTypeEnum::OVH, 'app-provider.ovh'],
    [ServerProviderTypeEnum::VULTR, 'app-provider.vultr'],
    [ServerProviderTypeEnum::OTHER, 'server'],
]);

it('should determine if ping job is pending', function (): void {
    $subject = new ServerViewModel(Server::factory()->create());

    expect($subject->pingIsPending())->toBeTrue();

    Cache::set('ping-'.$subject->host(), true);

    expect($subject->pingIsPending())->toBeFalse();
});

it('should determine if ping job succeed', function (): void {
    $subject = new ServerViewModel(Server::factory()->create());

    expect($subject->pingSucceed())->toBeFalse();

    Cache::set('ping-'.$subject->host(), true);

    expect($subject->pingSucceed())->toBeTrue();

    Cache::set('ping-'.$subject->host(), false);

    expect($subject->pingSucceed())->toBeFalse();
});

it('should determine if ping job failed', function (): void {
    $subject = new ServerViewModel(Server::factory()->create());

    expect($subject->pingFailed())->toBeFalse();

    Cache::set('ping-'.$subject->host(), false);

    expect($subject->pingFailed())->toBeTrue();

    Cache::set('ping-'.$subject->host(), true);

    expect($subject->pingFailed())->toBeFalse();
});

it('determines if a server uses bip38 encryption', function () {
    $subject = new ServerViewModel(Server::factory()->create([
        'uses_bip38_encryption' => true,
    ]));

    $subject2 = new ServerViewModel(Server::factory()->create([
        'uses_bip38_encryption' => false,
    ]));

    expect($subject->usesBip38Encryption())->toBeTrue();
    expect($subject2->usesBip38Encryption())->toBeFalse();
});

it('determines that the start action requires password when use bip38 encryption and is forger', function () {
    $subject = new ServerViewModel(Server::factory()->create([
        'uses_bip38_encryption' => true,
    ]));

    expect($subject->actionRequiresPassword('start', 'forger'))->toBeTrue();
});

it('determines that the start action requires password when use bip38 encryption and prefers combined', function () {
    $server = Server::factory()->usesBip38Encryption()->prefersCombined()->create();

    expect($server->toViewModel()->actionRequiresPassword('start', 'core'))->toBeTrue();
});

it('determines that the start action requires password when use bip38 encryption and is all', function () {
    $subject = new ServerViewModel(Server::factory()->create([
        'uses_bip38_encryption' => true,
    ]));

    expect($subject->actionRequiresPassword('start', 'all'))->toBeTrue();
});

it('determines that the start action doesnt requires password when doesnt use bip38 encryption and is forger', function () {
    $subject = new ServerViewModel(Server::factory()->create([
        'uses_bip38_encryption' => false,
    ]));

    expect($subject->actionRequiresPassword('start', 'forger'))->toBeFalse();
});

it('determines that the start action doesnt requires password when use bip38 encryption and is not forger', function () {
    $subject = new ServerViewModel(Server::factory()->create([
        'uses_bip38_encryption' => true,
    ]));

    expect($subject->actionRequiresPassword('start', 'relay'))->toBeFalse();
});

it('determines that a random action doesnt requires password when use bip38 encryption and is forger', function () {
    $subject = new ServerViewModel(Server::factory()->create([
        'uses_bip38_encryption' => true,
    ]));

    expect($subject->actionRequiresPassword('stop', 'forger'))->toBeFalse();
});

it('determines that the start action doesnt requires password when use bip38 encryption, is forger and already started', function () {
    $server = Server::factory()->create([
        'uses_bip38_encryption' => true,
    ]);

    $server->processes()->save(Process::factory()->create([
        'type'   => 'forger',
        'status' => ProcessStatusEnum::ONLINE,
    ]));

    $subject = new ServerViewModel($server);

    expect($subject->actionRequiresPassword('start', 'forger'))->toBeFalse();
});

it('should get dropdown tooltip if server is offline', function (): void {
    $server = Server::factory()
        ->offline()
        ->create()
        ->toViewModel();

    expect($server->actionTooltip())
        ->toBe(trans('server.tooltips.connection_failed', ['server_name' => $server->name()]));
});

it('should get dropdown tooltip if manager not running', function (): void {
    $server = Server::factory()
        ->managerNotRunning()
        ->create()
        ->toViewModel();

    expect($server->actionTooltip())
        ->toBe(trans('server.tooltips.process_not_running'));
});

it('should get dropdown tooltip based on action', function (): void {
    Role::create(['name' => TeamMemberRole::MAINTAINER]);

    $owner  = User::factory()->create();
    $member = tap(User::factory()->create())
        ->joinAs(TeamMemberRole::MAINTAINER, $owner);

    $this->actingAs($member);

    $server = Server::factory()
        ->create()
        ->toViewModel();

    expect($server->actionTooltip('delete'))->toBe(trans('server.tooltips.no_permission', ['action' => 'delete']));
});

it('should get no dropdown tooltip based on action', function (): void {
    $this->actingAs(User::factory()->create());

    $server = Server::factory()
        ->create()
        ->toViewModel();

    expect($server->actionTooltip('start'))->toBe(null);
});

it('should get the status icon', function (): void {
    expect(
        Server::factory()
            ->create()
            ->toViewModel()
            ->statusIcon()
    )->toBe('online');

    expect(
        Server::factory()
            ->managerNotRunning()
            ->create()
            ->toViewModel()
            ->statusIcon()
    )->toBe('stopped');

    expect(
        Server::factory()
            ->offline()
            ->create()
            ->toViewModel()
            ->statusIcon()
    )->toBe('undefined');

    expect(
        Server::factory()
            ->heightMismatch()
            ->create()
            ->toViewModel()
            ->statusIcon()
    )->toBe('height-mismatch');
});

it('determines if a server doesnt have pending processes', function ($status): void {
    $server = Server::factory()->create();

    Process::factory()->create(['server_id' => $server->id, 'status' => $status]);

    expect($server->toViewModel()->hasPendingProcesses())->toBeFalse();
})->with([
    ProcessStatusEnum::ONLINE,
    ProcessStatusEnum::STOPPED,
    ProcessStatusEnum::ERRORED,
    ProcessStatusEnum::ONE_LAUNCH_STATUS,
    ProcessStatusEnum::UNDEFINED,
]);

it('determines if a server has at least one process in a pending state', function ($status): void {
    $server = Server::factory()->create();

    Process::factory()->create(['server_id' => $server->id, 'type' => 'forger', 'name' => 'ark-forger', 'status' => ProcessStatusEnum::ONLINE]);
    Process::factory()->create(['server_id' => $server->id, 'type' => 'relay', 'name' => 'ark-relay', 'status' => $status]);

    expect($server->toViewModel()->hasPendingProcesses())->toBeTrue();
})->with([
    ProcessStatusEnum::LAUNCHING,
    ProcessStatusEnum::STOPPING,
    ProcessStatusEnum::WAITING_RESTART,
]);

it('should determine can get height with running manager process', function (): void {
    $server = Server::factory()->create([
        sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::UPDATING_SERVER_PING) => true,
        sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING) => true,
    ]);
    Process::factory()->create([
        'server_id' => $server->id,
        'type'      => 'relay',
        'name'      => 'ark-relay',
        'status'    => ProcessStatusEnum::ONLINE,
    ]);

    expect($server->toViewModel()->isManagerRunning())->toBeTrue();
    expect($server->toViewModel()->canGetHeight())->toBeTrue();
});

it('should determine can get height without running manager, relay or core server', function (): void {
    $server = Server::factory()->create();
    expect($server->toViewModel()->canGetHeight())->toBeFalse();
});

it('should determine cannot get height with stopped manager process', function (): void {
    $server = Server::factory()->create([
        sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::UPDATING_SERVER_PING) => true,
        sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING) => false,
    ]);
    Process::factory()->create([
        'server_id' => $server->id,
        'type'      => 'relay',
        'name'      => 'ark-relay',
        'status'    => ProcessStatusEnum::ONLINE,
    ]);

    expect($server->toViewModel()->isManagerRunning())->toBeFalse();
    expect($server->toViewModel()->canGetHeight())->toBeFalse();
});

it('should determine can get height with online relay process', function (): void {
    $server = Server::factory()->create();
    Process::factory()->create([
        'server_id' => $server->id,
        'type'      => 'relay',
        'name'      => 'ark-relay',
        'status'    => ProcessStatusEnum::ONLINE,
    ]);

    expect($server->toViewModel()->canGetHeight())->toBeTrue();
});

it('should determine cannot get height with stopped relay process', function (): void {
    $server = Server::factory()->create();
    Process::factory()->create([
        'server_id' => $server->id,
        'type'      => 'relay',
        'name'      => 'ark-relay',
        'status'    => ProcessStatusEnum::STOPPED,
    ]);

    expect($server->toViewModel()->canGetHeight())->toBeFalse();
});

it('should determine can get height with online core process', function (): void {
    $server = Server::factory()->create();
    Process::factory()->create([
        'server_id' => $server->id,
        'type'      => 'core',
        'name'      => 'ark-core',
        'status'    => ProcessStatusEnum::ONLINE,
    ]);

    expect($server->toViewModel()->canGetHeight())->toBeTrue();
});

it('should determine cannot get height with stopped core process', function (): void {
    $server = Server::factory()->create();
    Process::factory()->create([
        'server_id' => $server->id,
        'type'      => 'core',
        'name'      => 'ark-core',
        'status'    => ProcessStatusEnum::STOPPED,
    ]);

    expect($server->toViewModel()->canGetHeight())->toBeFalse();
});

it('should determine if server is loading', function (): void {
    $server = Server::factory()->create([
        'extra_attributes->loading' => true,
    ]);

    expect($server->toViewModel()->isLoading())->toBeTrue();
});

it('should determine if server is not loading', function (): void {
    $server = Server::factory()->create();

    expect($server->toViewModel()->isLoading())->toBeFalse();
});

it('should determine if server is updating core and not manager', function (): void {
    $server = Server::factory()->create([
        'extra_attributes' => [
            'loading' => [
                'updating_server_core'         => true,
                'updating_server_core_manager' => false,
            ],
        ],
    ]);

    expect($server->toViewModel()->isUpdating())->toBeTrue();
});

it('should determine if server is updating manager and not core', function (): void {
    $server = Server::factory()->create([
        'extra_attributes' => [
            'loading' => [
                'updating_server_core'         => false,
                'updating_server_core_manager' => true,
            ],
        ],
    ]);

    expect($server->toViewModel()->isUpdating())->toBeTrue();
});

it('should determine if server is updating both core and manager', function (): void {
    $server = Server::factory()->create([
        'extra_attributes' => [
            'loading' => [
                'updating_server_core'         => true,
                'updating_server_core_manager' => true,
            ],
        ],
    ]);

    expect($server->toViewModel()->isUpdating())->toBeTrue();
});

it('should determine if server is not updating', function (): void {
    $server = Server::factory()->create();

    expect($server->toViewModel()->isUpdating())->toBeFalse();
});

it('should determine that a server is not on a pending state', function (): void {
    $server = Server::factory()->create();

    expect($server->toViewModel()->isOnPendingState())->toBeFalse();
});

it('should determine that a server is on pending state if has a pending process', function (): void {
    $server = Server::factory()->create();

    Process::factory()->create(['server_id' => $server->id, 'type' => 'relay', 'name' => 'ark-relay', 'status' => ProcessStatusEnum::LAUNCHING]);

    expect($server->fresh()->toViewModel()->isOnPendingState())->toBeTrue();
});

it('should determine that a server is on pending state if is loading', function (): void {
    $server = Server::factory()->create();

    $server->markTaskAsStarted('task');

    expect($server->fresh()->toViewModel()->isOnPendingState())->toBeTrue();
});

it('determines if user prefers combined process', function (): void {
    $server = Server::factory()->prefersCombined()->create();

    expect($server->toViewModel()->prefersCombined())->toBeTrue();

    expect($server->toViewModel()->prefersSeparated())->toBeFalse();
});

it('determines if user prefers separated process', function (): void {
    $server = Server::factory()->prefersSeparated()->create();

    expect($server->toViewModel()->prefersCombined())->toBeFalse();

    expect($server->toViewModel()->prefersSeparated())->toBeTrue();
});

it('determines process type is inline if prefers combined and has an online core process', function (): void {
    $server = Server::factory()->prefersCombined()->create();

    Process::factory()->forServer($server)->core()->online()->create();

    expect($server->toViewModel()->processTypeIsInline())->toBeTrue();
});

it('determines process type is inline if prefers combined and doesnt have any process', function (): void {
    $server = Server::factory()->prefersCombined()->create();

    expect($server->toViewModel()->processTypeIsInline())->toBeTrue();
});

it('determines process type is inline if prefers combined, have separated processes but they are stopped', function (): void {
    $server = Server::factory()->prefersCombined()->create();

    Process::factory()->forServer($server)->relay()->stopped()->create();

    Process::factory()->forServer($server)->forger()->stopped()->create();

    expect($server->toViewModel()->processTypeIsInline())->toBeTrue();
});

it('determines process type is not inline if prefers combined but has separated forger process', function (): void {
    $server = Server::factory()->prefersCombined()->create();

    Process::factory()->forServer($server)->forger()->online()->create();

    expect($server->toViewModel()->processTypeIsInline())->toBeFalse();
});

it('determines process type is not inline if prefers combined but has separated relay process', function (): void {
    $server = Server::factory()->prefersCombined()->create();

    Process::factory()->forServer($server)->relay()->online()->create();

    expect($server->toViewModel()->processTypeIsInline())->toBeFalse();
});

it('determines process type is inline if prefers separated and has an separated processes', function (): void {
    $server = Server::factory()->prefersSeparated()->create();

    Process::factory()->forServer($server)->forger()->online()->create();

    expect($server->toViewModel()->processTypeIsInline())->toBeTrue();
});

it('determines process type is inline if prefers separated and doesnt have any process', function (): void {
    $server = Server::factory()->prefersSeparated()->create();

    expect($server->toViewModel()->processTypeIsInline())->toBeTrue();
});

it('determines process type is inline if prefers separated has a core process but is stopped', function (): void {
    $server = Server::factory()->prefersSeparated()->create();

    Process::factory()->forServer($server)->core()->stopped()->create();

    expect($server->toViewModel()->processTypeIsInline())->toBeTrue();
});

it('determines process type is not inline if prefers separated but has a core process', function (): void {
    $server = Server::factory()->prefersSeparated()->create();

    Process::factory()->forServer($server)->core()->online()->create();

    expect($server->toViewModel()->processTypeIsInline())->toBeFalse();
});

it('should be able to start core', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    // No core process
    expect($subject->canStartCore())->toBeTrue();

    // Core process is online
    $process = Process::factory()->core()->online()->forServer($server)->create();
    $server->refresh();
    clearCache($server);
    expect($subject->canStartCore())->toBeFalse();

    // Core process is stopped
    $process->update(['status' => ProcessStatusEnum::STOPPED]);
    $process->save();
    $server->refresh();
    clearCache($server);
    expect($subject->canStartCore())->toBeTrue();

    // Core process is deleted
    $process->update(['status' => ProcessStatusEnum::DELETED]);
    $process->save();
    $server->refresh();
    clearCache($server);
    expect($subject->canStartCore())->toBeTrue();
});

it('should be able to restart core', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    // No core process
    expect($subject->canRestartCore())->toBeFalse();

    // Core process is online
    $process = Process::factory()->core()->online()->forServer($server)->create();
    $server->refresh();
    clearCache($server);
    expect($subject->canRestartCore())->toBeTrue();

    // Core process is errores
    $process->update(['status' => ProcessStatusEnum::ERRORED]);
    $process->save();
    $server->refresh();
    clearCache($server);
    expect($subject->canRestartCore())->toBeTrue();

    // Core process is stopped
    $process->update(['status' => ProcessStatusEnum::STOPPED]);
    $process->save();
    $server->refresh();
    clearCache($server);
    expect($subject->canRestartCore())->toBeFalse();
});

it('should be able to stop core', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    // No core process
    expect($subject->canStopCore())->toBeFalse();

    // Core process is online
    $process = Process::factory()->core()->online()->forServer($server)->create();
    $server->refresh();
    clearCache($server);
    expect($subject->canStopCore())->toBeTrue();

    // Core process is other status
    $process->update(['status' => ProcessStatusEnum::STOPPED]);
    $process->save();
    $server->refresh();
    clearCache($server);
    expect($subject->canRestartCore())->toBeFalse();
});

it('should be able to delete core', function (): void {
    $subject = new ServerViewModel($server = Server::factory()->create());

    // No core process
    expect($subject->canDeleteCore())->toBeFalse();

    // Core process is any status
    $process = Process::factory()->core()->online()->forServer($server)->create();
    $server->refresh();
    clearCache($server);
    expect($subject->canDeleteCore())->toBeTrue();

    // Core process is deleted
    $process->update(['status' => ProcessStatusEnum::DELETED]);
    $process->save();
    $server->refresh();
    clearCache($server);
    expect($subject->canRestartCore())->toBeFalse();
});

it('determines if it can update core', function (): void {
    $readonly = User::factory()->create(['username' => 'readonly']);
    $owner    = User::factory()->create(['username' => 'owner']);

    $subject = new ServerViewModel($server = Server::factory()->create([
        'user_id' => $owner->id,
    ]));

    Auth::logout();
    expect($subject->canUpdateCore())->toBeFalse();
    expect($subject->canUpdate(ServerTypeEnum::CORE))->toBeFalse();

    Auth::login($readonly);
    expect($subject->canUpdateCore())->toBeFalse();
    expect($subject->canUpdate(ServerTypeEnum::CORE))->toBeFalse();

    Auth::logout();
    Auth::login($owner);
    expect($subject->canUpdateCore())->toBeFalse();
    expect($subject->canUpdate(ServerTypeEnum::CORE))->toBeFalse();

    $subject = new ServerViewModel(tap($server, fn ($server) => $server->update([
        'core_version_current' => '1.0.0',
        'core_version_latest'  => '2.0.0',
        'extra_attributes'     => [
            'core_manager_current_version' => '1.0.0',
            'core_manager_latest_version'  => '2.0.0',
            'succeed'                      => [
                'updating_server_ping'                   => true,
                'updating_server_core'                   => true,
                'updating_server_height_manager_running' => true,
            ],
        ],
    ]))->fresh());

    expect($subject->canUpdateCore())->toBeTrue();
    expect($subject->canUpdate(ServerTypeEnum::CORE))->toBeTrue();

    $subject = new ServerViewModel(tap($server, fn ($server) => $server->update([
        'core_version_current' => '1.0.0',
        'core_version_latest'  => '2.0.0',
        'extra_attributes'     => [
            'core_manager_current_version' => '1.0.0',
            'core_manager_latest_version'  => '2.0.0',
            'succeed'                      => [
                'updating_server_ping'                   => false,
                'updating_server_core'                   => false,
                'updating_server_height_manager_running' => false,
            ],
        ],
    ]))->fresh());

    expect($subject->canUpdateCore())->toBeFalse();
    expect($subject->canUpdate(ServerTypeEnum::CORE))->toBeFalse();
});

it('determines if it can update core manager', function (): void {
    $readonly = User::factory()->create(['username' => 'readonly']);
    $owner    = User::factory()->create(['username' => 'owner']);

    $subject = new ServerViewModel($server = Server::factory()->create([
        'user_id' => $owner->id,
    ]));

    Auth::logout();
    expect($subject->canUpdateCoreManager())->toBeFalse();
    expect($subject->canUpdate(ServerTypeEnum::CORE_MANAGER))->toBeFalse();

    Auth::login($readonly);
    expect($subject->canUpdateCoreManager())->toBeFalse();
    expect($subject->canUpdate(ServerTypeEnum::CORE_MANAGER))->toBeFalse();

    Auth::logout();
    Auth::login($owner);
    expect($subject->canUpdateCoreManager())->toBeFalse();
    expect($subject->canUpdate(ServerTypeEnum::CORE_MANAGER))->toBeFalse();

    $subject = new ServerViewModel(tap($server, fn ($server) => $server->update([
        'extra_attributes' => [
            'core_manager_current_version' => '1.0.0',
            'core_manager_latest_version'  => '2.0.0',
            'succeed'                      => [
                'updating_server_ping'                   => false,
                'updating_server_core'                   => false,
                'updating_server_height_manager_running' => false,
            ],
        ],
    ]))->refresh());

    expect($subject->canUpdateCoreManager())->toBeFalse();
    expect($subject->canUpdate(ServerTypeEnum::CORE_MANAGER))->toBeFalse();

    $subject = new ServerViewModel(tap($server, fn ($server) => $server->update([
        'extra_attributes' => [
            'core_manager_current_version' => '1.0.0',
            'core_manager_latest_version'  => '2.0.0',
            'succeed'                      => [
                'updating_server_ping'                   => true,
                'updating_server_core'                   => true,
                'updating_server_height_manager_running' => true,
            ],
        ],
    ]))->refresh());

    expect($subject->canUpdateCoreManager())->toBeTrue();
    expect($subject->canUpdate(ServerTypeEnum::CORE_MANAGER))->toBeTrue();
});

it('determines if the server is silently updated', function (): void {
    $server = Server::factory()->create();

    expect($server->fresh()->toViewModel()->isSilentLoading())->toBeFalse();

    $server->setSilentUpdate();

    expect($server->fresh()->toViewModel()->isSilentLoading())->toBeTrue();

    $server->unsetSilentUpdate();

    expect($server->fresh()->toViewModel()->isSilentLoading())->toBeFalse();
});
