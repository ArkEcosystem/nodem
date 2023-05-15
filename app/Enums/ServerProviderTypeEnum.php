<?php

declare(strict_types=1);

namespace App\Enums;

final class ServerProviderTypeEnum
{
    public const AWS = 'aws';

    public const AZURE = 'azure';

    public const DIGITAL_OCEAN = 'digitalocean';

    public const GOOGLE = 'google';

    public const HETZNER = 'hetzner';

    public const LINODE = 'linode';

    public const NETCUP = 'netcup';

    public const OVH = 'ovh';

    public const VULTR = 'vultr';

    public const OTHER = 'other';

    public static function isAWS(string $value): bool
    {
        return $value === static::AWS;
    }

    public static function isAzure(string $value): bool
    {
        return $value === static::AZURE;
    }

    public static function isDigitalOcean(string $value): bool
    {
        return $value === static::DIGITAL_OCEAN;
    }

    public static function isGoogle(string $value): bool
    {
        return $value === static::GOOGLE;
    }

    public static function isHetzner(string $value): bool
    {
        return $value === static::HETZNER;
    }

    public static function isLinode(string $value): bool
    {
        return $value === static::LINODE;
    }

    public static function isNetcup(string $value): bool
    {
        return $value === static::NETCUP;
    }

    public static function isOVH(string $value): bool
    {
        return $value === static::OVH;
    }

    public static function isVultr(string $value): bool
    {
        return $value === static::VULTR;
    }

    public static function isOther(string $value): bool
    {
        return $value === static::OTHER;
    }

    public static function isCustom(string $value): bool
    {
        return ! in_array($value, [
            static::AWS,
            static::AZURE,
            static::DIGITAL_OCEAN,
            static::GOOGLE,
            static::HETZNER,
            static::LINODE,
            static::NETCUP,
            static::OVH,
            static::VULTR,
            static::OTHER,
        ], true);
    }

    public static function toArray(): array
    {
        return [
            static::AWS,
            static::AZURE,
            static::DIGITAL_OCEAN,
            static::GOOGLE,
            static::HETZNER,
            static::LINODE,
            static::NETCUP,
            static::OVH,
            static::VULTR,
            static::OTHER,
        ];
    }

    public static function iconName(string $provider): string
    {
        if ($provider === static::OTHER) {
            return 'server';
        }

        return 'app-provider.'.$provider;
    }
}
