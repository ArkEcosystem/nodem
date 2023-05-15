<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\ServerProcessTypeEnum;
use App\Enums\ServerProviderTypeEnum;
use App\Models\Server as Model;
use Illuminate\Validation\Rule;

final class Server
{
    public static function provider(array $attributes = []): array
    {
        return self::setRules(['string', Rule::in(ServerProviderTypeEnum::toArray())], $attributes);
    }

    public static function name(array $attributes = []): array
    {
        return self::setRules(['string', 'min:3', 'max:30'], $attributes);
    }

    public static function host(array $attributes = [], ?Model $server = null): array
    {
        $rule = Rule::unique('servers', 'host');
        if ($server !== null) {
            $rule->ignore($server->id);
        }

        return self::setRules(['string', 'min:3', $rule, new Host()], $attributes);
    }

    public static function processType(array $attributes = []): array
    {
        return self::setRules(['string', Rule::in(ServerProcessTypeEnum::toArray())], $attributes);
    }

    public static function authUsername(array $attributes = []): array
    {
        return self::setRules(['nullable', 'string', 'max:500'], $attributes);
    }

    public static function authPassword(array $attributes = []): array
    {
        return self::setRules(['nullable', 'string', 'max:500'], $attributes);
    }

    public static function authAccessKey(array $attributes = []): array
    {
        return self::setRules(['nullable', 'string', 'max:500'], $attributes);
    }

    public static function bip38(array $attributes = []): array
    {
        return self::setRules(['required', 'boolean'], $attributes);
    }

    private static function setRules(array $rules, array $attributes = []): array
    {
        return collect($rules)->prepend($attributes)->toArray();
    }
}
