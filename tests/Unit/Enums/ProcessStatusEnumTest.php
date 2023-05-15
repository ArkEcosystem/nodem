<?php

declare(strict_types=1);

use App\Enums\ProcessStatusEnum;

it('should determine if the value is undefined', function (): void {
    expect(ProcessStatusEnum::isUndefined(ProcessStatusEnum::UNDEFINED))->toBeTrue();
    expect(ProcessStatusEnum::isUndefined('relay'))->toBeFalse();
});

it('should determine if the value is online', function (): void {
    expect(ProcessStatusEnum::isOnline(ProcessStatusEnum::ONLINE))->toBeTrue();
    expect(ProcessStatusEnum::isOnline('relay'))->toBeFalse();
});

it('should determine if the value is stopped', function (): void {
    expect(ProcessStatusEnum::isStopped(ProcessStatusEnum::STOPPED))->toBeTrue();
    expect(ProcessStatusEnum::isStopped('relay'))->toBeFalse();
});

it('should determine if the value is stopping', function (): void {
    expect(ProcessStatusEnum::isStopping(ProcessStatusEnum::STOPPING))->toBeTrue();
    expect(ProcessStatusEnum::isStopping('relay'))->toBeFalse();
});

it('should determine if the value is waiting restart', function (): void {
    expect(ProcessStatusEnum::isWaitingRestart(ProcessStatusEnum::WAITING_RESTART))->toBeTrue();
    expect(ProcessStatusEnum::isWaitingRestart('relay'))->toBeFalse();
});

it('should determine if the value is launching', function (): void {
    expect(ProcessStatusEnum::isLaunching(ProcessStatusEnum::LAUNCHING))->toBeTrue();
    expect(ProcessStatusEnum::isLaunching('relay'))->toBeFalse();
});

it('should determine if the value is errored', function (): void {
    expect(ProcessStatusEnum::isErrored(ProcessStatusEnum::ERRORED))->toBeTrue();
    expect(ProcessStatusEnum::isErrored('relay'))->toBeFalse();
});

it('should determine if the value is one launch status', function (): void {
    expect(ProcessStatusEnum::isOneLaunchStatus(ProcessStatusEnum::ONE_LAUNCH_STATUS))->toBeTrue();
    expect(ProcessStatusEnum::isOneLaunchStatus('relay'))->toBeFalse();
});

it('should determine if the value is deleted', function (): void {
    expect(ProcessStatusEnum::isDeleted(ProcessStatusEnum::DELETED))->toBeTrue();
    expect(ProcessStatusEnum::isDeleted('relay'))->toBeFalse();
});
