<?php

declare(strict_types=1);

use App\Enums\ServerTypeEnum;
use function Spatie\Snapshots\assertMatchesSnapshot;

it('should determine if the value is a core instance', function (): void {
    expect(ServerTypeEnum::isCore(ServerTypeEnum::CORE))->toBeTrue();
    expect(ServerTypeEnum::isCore(ServerTypeEnum::RELAY))->toBeFalse();
});

it('should determine if the value is a core manager instance', function (): void {
    expect(ServerTypeEnum::isCoreManager(ServerTypeEnum::CORE_MANAGER))->toBeTrue();
    expect(ServerTypeEnum::isCoreManager(ServerTypeEnum::RELAY))->toBeFalse();
});

it('should determine if the value is aServerTypeEnum::RELAY instance', function (): void {
    expect(ServerTypeEnum::isRelay(ServerTypeEnum::RELAY))->toBeTrue();
    expect(ServerTypeEnum::isRelay(ServerTypeEnum::FORGER))->toBeFalse();
});

it('should determine if the value is a forger instance', function (): void {
    expect(ServerTypeEnum::isForger(ServerTypeEnum::FORGER))->toBeTrue();
    expect(ServerTypeEnum::isForger(ServerTypeEnum::RELAY))->toBeFalse();
});

it('should turn the enum into an array', function (): void {
    assertMatchesSnapshot(ServerTypeEnum::toArray());
});
