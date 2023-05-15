<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureTwoFactorEnabled;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * To simulate the request to unit test the middleware, we can manually create the
 * request instance and configure the request to handle authenticated user.
 *
 * @param \App\Models\User|null $user
 *
 * @return \Illuminate\Http\Request
 */
function makeRequest(?User $user = null) : Request
{
    return Request::create('/dashboard', 'GET')->setUserResolver(fn () => $user);
};

it('ignores if 2fa is globally disabled', function () {
    config(['web.two_factor_enabled' => false]);

    $response = (new EnsureTwoFactorEnabled())->handle(
        makeRequest(User::factory()->create()),
        fn ($request) => response('OK')
    );

    expect($response->status())->toBe(200);
});

it('ignores if user is not logged in', function () {
    // Because `auth` middleware should handle that...

    config(['web.two_factor_enabled' => true]);

    $response = (new EnsureTwoFactorEnabled())->handle(makeRequest(), fn ($request) => response('OK'));

    expect($response->status())->toBe(200);
});

it('redirects if 2fa is disabled', function () {
    config(['web.two_factor_enabled' => true]);

    $response = (new EnsureTwoFactorEnabled())->handle(
        makeRequest(User::factory()->create([
            'two_factor_secret' => null,
        ])),
        fn ($request) => response('OK')
    );

    expect($response->status())->toBe(302);
});

it('continues if 2fa is enabled', function () {
    config(['web.two_factor_enabled' => true]);

    $response = (new EnsureTwoFactorEnabled())->handle(
        makeRequest(User::factory()->create([
            'two_factor_secret' => 'some-token',
        ])),
        fn ($request) => response('OK')
    );

    expect($response->status())->toBe(200);
});
