<?php

declare(strict_types=1);

namespace App\Cache;

use App\DTO\Alert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

final class AlertStore
{
    public static function push(User $user, Alert $alert): void
    {
        $alerts = static::getAll($user);

        array_push($alerts, $alert);

        Cache::put('alerts-'.$user->id, $alerts, Carbon::now()->addMinute());
    }

    public static function getAll(User $user): array
    {
        return Cache::get('alerts-'.$user->id, []);
    }

    public static function pullAll(User $user): array
    {
        return Cache::pull('alerts-'.$user->id, []);
    }
}
