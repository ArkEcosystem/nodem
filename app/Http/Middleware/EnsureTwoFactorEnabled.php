<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class EnsureTwoFactorEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Guard against authenticated users, with disabled 2FA and globally enabled 2FA as a feature...
        if ($request->user() !== null && ! $request->user()->enabledTwoFactor() && (bool) config('web.two_factor_enabled')) {
            return redirect()->route('account.settings.password');
        }

        return $next($request);
    }
}
