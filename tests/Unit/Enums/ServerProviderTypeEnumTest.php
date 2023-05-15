<?php

declare(strict_types=1);

use App\Enums\ServerProviderTypeEnum;
use function Spatie\Snapshots\assertMatchesSnapshot;

it('should determine if the value is aws', function (): void {
    expect(ServerProviderTypeEnum::isAWS(ServerProviderTypeEnum::AWS))->toBeTrue();
    expect(ServerProviderTypeEnum::isAWS('custom'))->toBeFalse();
});

it('should determine if the value is azure', function (): void {
    expect(ServerProviderTypeEnum::isAzure(ServerProviderTypeEnum::AZURE))->toBeTrue();
    expect(ServerProviderTypeEnum::isAzure('custom'))->toBeFalse();
});

it('should determine if the value is digitalocean', function (): void {
    expect(ServerProviderTypeEnum::isDigitalOcean(ServerProviderTypeEnum::DIGITAL_OCEAN))->toBeTrue();
    expect(ServerProviderTypeEnum::isDigitalOcean('custom'))->toBeFalse();
});

it('should determine if the value is google', function (): void {
    expect(ServerProviderTypeEnum::isGoogle(ServerProviderTypeEnum::GOOGLE))->toBeTrue();
    expect(ServerProviderTypeEnum::isGoogle('custom'))->toBeFalse();
});

it('should determine if the value is hetzner', function (): void {
    expect(ServerProviderTypeEnum::isHetzner(ServerProviderTypeEnum::HETZNER))->toBeTrue();
    expect(ServerProviderTypeEnum::isHetzner('custom'))->toBeFalse();
});

it('should determine if the value is linode', function (): void {
    expect(ServerProviderTypeEnum::isLinode(ServerProviderTypeEnum::LINODE))->toBeTrue();
    expect(ServerProviderTypeEnum::isLinode('custom'))->toBeFalse();
});

it('should determine if the value is netcup', function (): void {
    expect(ServerProviderTypeEnum::isNetcup(ServerProviderTypeEnum::NETCUP))->toBeTrue();
    expect(ServerProviderTypeEnum::isNetcup('custom'))->toBeFalse();
});

it('should determine if the value is ovh', function (): void {
    expect(ServerProviderTypeEnum::isOVH(ServerProviderTypeEnum::OVH))->toBeTrue();
    expect(ServerProviderTypeEnum::isOVH('custom'))->toBeFalse();
});

it('should determine if the value is vultr', function (): void {
    expect(ServerProviderTypeEnum::isVultr(ServerProviderTypeEnum::VULTR))->toBeTrue();
    expect(ServerProviderTypeEnum::isVultr('custom'))->toBeFalse();
});

it('should determine if the value is other', function (): void {
    expect(ServerProviderTypeEnum::isOther(ServerProviderTypeEnum::OTHER))->toBeTrue();
    expect(ServerProviderTypeEnum::isOther('custom'))->toBeFalse();
});

it('should determine if the value is custom', function (): void {
    expect(ServerProviderTypeEnum::isCustom('custom'))->toBeTrue();
    expect(ServerProviderTypeEnum::isCustom(ServerProviderTypeEnum::AWS))->toBeFalse();
});

it('should turn the enum into an array', function (): void {
    assertMatchesSnapshot(ServerProviderTypeEnum::toArray());
});

it('should return the provider icon name', function ($provider, $iconName): void {
    expect(ServerProviderTypeEnum::iconName($provider))->toBe($iconName);
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
