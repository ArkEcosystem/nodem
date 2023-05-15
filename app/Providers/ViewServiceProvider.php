<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

final class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view): void {
            $view->with('currentUser', Auth::user());
        });

        View::composer('hermes::navbar-notifications', function ($view): void {
            $view->with('notificationCount', Auth::user()?->notifications()->count());
        });
    }
}
