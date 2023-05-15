<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in(__DIR__);

/**
 * Since 2FA route-guard is disabled by default (to prevent having to call this all the time), this function will enable the 2FA route-guard for the test.
 *
 * @return void
 */
function enableTwoFactorGuard() : void
{
    config(['web.two_factor_enabled' => true]);
}

function toKilobyte(float | int $gb) : int
{
    return (int) ($gb * 1000000);
}
