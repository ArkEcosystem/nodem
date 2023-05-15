<?php

declare(strict_types=1);

use App\Enums\ProcessStatusEnum;
use App\Models\Process;
use App\Models\Server;
use App\ViewModels\ProcessViewModel;

it('should get the status', function (): void {
    $subject = new ProcessViewModel(Process::factory()->forger()->create(['status' => ProcessStatusEnum::UNDEFINED]));

    expect($subject->status())->toBe('undefined');
});

it('should get the status icon', function (): void {
    $subject = new ProcessViewModel(Process::factory()->forger()->create(['status' => ProcessStatusEnum::WAITING_RESTART]));

    expect($subject->statusIcon())->toBe('waiting restart');
});

it('should determine if the value is undefined', function (): void {
    $subject = new ProcessViewModel(Process::factory()->forger()->create(['status' => ProcessStatusEnum::UNDEFINED]));

    expect($subject->isUndefined())->toBeTrue();
    expect($subject->isOnline())->toBeFalse();
    expect($subject->isStopped())->toBeFalse();
    expect($subject->isStopping())->toBeFalse();
    expect($subject->isWaitingRestart())->toBeFalse();
    expect($subject->isLaunching())->toBeFalse();
    expect($subject->isErrored())->toBeFalse();
    expect($subject->isOneLaunchStatus())->toBeFalse();
});

it('should determine if the value is online', function (): void {
    $subject = new ProcessViewModel(Process::factory()->forger()->create(['status' => ProcessStatusEnum::ONLINE]));

    expect($subject->isUndefined())->toBeFalse();
    expect($subject->isOnline())->toBeTrue();
    expect($subject->isStopped())->toBeFalse();
    expect($subject->isStopping())->toBeFalse();
    expect($subject->isWaitingRestart())->toBeFalse();
    expect($subject->isLaunching())->toBeFalse();
    expect($subject->isErrored())->toBeFalse();
    expect($subject->isOneLaunchStatus())->toBeFalse();
});

it('should determine if the value is stopped', function (): void {
    $subject = new ProcessViewModel(Process::factory()->forger()->create(['status' => ProcessStatusEnum::STOPPED]));

    expect($subject->isUndefined())->toBeFalse();
    expect($subject->isOnline())->toBeFalse();
    expect($subject->isStopped())->toBeTrue();
    expect($subject->isStopping())->toBeFalse();
    expect($subject->isWaitingRestart())->toBeFalse();
    expect($subject->isLaunching())->toBeFalse();
    expect($subject->isErrored())->toBeFalse();
    expect($subject->isOneLaunchStatus())->toBeFalse();
});

it('should determine if the value is stopping', function (): void {
    $subject = new ProcessViewModel(Process::factory()->forger()->create(['status' => ProcessStatusEnum::STOPPING]));

    expect($subject->isUndefined())->toBeFalse();
    expect($subject->isOnline())->toBeFalse();
    expect($subject->isStopped())->toBeFalse();
    expect($subject->isStopping())->toBeTrue();
    expect($subject->isWaitingRestart())->toBeFalse();
    expect($subject->isLaunching())->toBeFalse();
    expect($subject->isErrored())->toBeFalse();
    expect($subject->isOneLaunchStatus())->toBeFalse();
});

it('should determine if the value is waiting restart', function (): void {
    $subject = new ProcessViewModel(Process::factory()->forger()->create(['status' => ProcessStatusEnum::WAITING_RESTART]));

    expect($subject->isUndefined())->toBeFalse();
    expect($subject->isOnline())->toBeFalse();
    expect($subject->isStopped())->toBeFalse();
    expect($subject->isStopping())->toBeFalse();
    expect($subject->isWaitingRestart())->toBeTrue();
    expect($subject->isLaunching())->toBeFalse();
    expect($subject->isErrored())->toBeFalse();
    expect($subject->isOneLaunchStatus())->toBeFalse();
});

it('should determine if the value is launching', function (): void {
    $subject = new ProcessViewModel(Process::factory()->forger()->create(['status' => ProcessStatusEnum::LAUNCHING]));

    expect($subject->isUndefined())->toBeFalse();
    expect($subject->isOnline())->toBeFalse();
    expect($subject->isStopped())->toBeFalse();
    expect($subject->isStopping())->toBeFalse();
    expect($subject->isWaitingRestart())->toBeFalse();
    expect($subject->isLaunching())->toBeTrue();
    expect($subject->isErrored())->toBeFalse();
    expect($subject->isOneLaunchStatus())->toBeFalse();
});

it('should determine if the value is errored', function (): void {
    $subject = new ProcessViewModel(Process::factory()->forger()->create(['status' => ProcessStatusEnum::ERRORED]));

    expect($subject->isUndefined())->toBeFalse();
    expect($subject->isOnline())->toBeFalse();
    expect($subject->isStopped())->toBeFalse();
    expect($subject->isStopping())->toBeFalse();
    expect($subject->isWaitingRestart())->toBeFalse();
    expect($subject->isLaunching())->toBeFalse();
    expect($subject->isErrored())->toBeTrue();
    expect($subject->isOneLaunchStatus())->toBeFalse();
});

it('should determine if the value is one launch status', function (): void {
    $subject = new ProcessViewModel(Process::factory()->forger()->create(['status' => ProcessStatusEnum::ONE_LAUNCH_STATUS]));

    expect($subject->isUndefined())->toBeFalse();
    expect($subject->isOnline())->toBeFalse();
    expect($subject->isStopped())->toBeFalse();
    expect($subject->isStopping())->toBeFalse();
    expect($subject->isWaitingRestart())->toBeFalse();
    expect($subject->isLaunching())->toBeFalse();
    expect($subject->isErrored())->toBeFalse();
    expect($subject->isOneLaunchStatus())->toBeTrue();
});

it('should get the status tooltip', function (string $status, string $tooltip): void {
    expect(
        Process::factory()
            ->forger()
            ->create(['status' => $status])
            ->toViewModel()
            ->statusTooltip()
    )->toBe(trans($tooltip));
})->with([
    [ProcessStatusEnum::ONLINE, 'server.status.online'],
    [ProcessStatusEnum::ERRORED, 'server.status.errored'],
    [ProcessStatusEnum::UNDEFINED, 'server.status.undefined'],
    [ProcessStatusEnum::STOPPED, 'server.status.stopped'],
    [ProcessStatusEnum::STOPPING, 'server.status.stopping'],
    [ProcessStatusEnum::WAITING_RESTART, 'server.status.waiting_restart'],
    [ProcessStatusEnum::LAUNCHING, 'server.status.launching'],
    [ProcessStatusEnum::ONE_LAUNCH_STATUS, 'server.status.one_launch_status'],
    [ProcessStatusEnum::DELETED, 'server.status.deleted'],
    ['foo', 'server.status.offline'],
]);

it('should get the status tooltip when server is not inline', function (): void {
    $server = Server::factory()->prefersCombined()->create();

    $process = Process::factory()->forServer($server)->forger()->online()->create();

    expect($process->toViewModel()->statusTooltip())->toBe(trans('server.status.online').' ('.trans('tooltips.unexpected_process').')');
});

it('should get the status tooltip when server height is behind', function (): void {
    $server = Server::factory()->heightMismatch()->create();

    $process = Process::factory()->forServer($server)->forger()->online()->create();

    expect($process->toViewModel()->statusTooltip())->toBe(trans('server.status.server_height_mismatch'));
});

it('should get the status icon when server is not inline', function (): void {
    $server = Server::factory()->prefersCombined()->create();

    $process = Process::factory()->forServer($server)->forger()->online()->create();

    expect($process->toViewModel()->statusIcon())->toBe('not-inline');
});

it('should get the status icon when server height is behind', function (): void {
    $server = Server::factory()->heightMismatch()->create();

    $process = Process::factory()->forServer($server)->relay()->online()->create();

    expect($process->toViewModel()->statusIcon())->toBe('height-mismatch');
});

it('should get the status tooltip when Nodem is unable to fetch the height', function (): void {
    $server = Server::factory()->unableToFetchHeight()->create();

    $process = Process::factory()->forServer($server)->forger()->online()->create();

    expect($process->toViewModel()->statusTooltip())->toBe(trans('server.status.unable_to_fetch_height'));
});

it('should get the status icon when Nodem is unable to fetch the height', function (): void {
    $server = Server::factory()->unableToFetchHeight()->create();

    $process = Process::factory()->forServer($server)->relay()->online()->create();

    expect($process->toViewModel()->statusIcon())->toBe('unable-to-fetch-height');
});
