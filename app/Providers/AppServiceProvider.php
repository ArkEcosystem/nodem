<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use ARKEcosystem\Foundation\DataBags\DataBag;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ((bool) $this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);

        Paginator::defaultView('vendor.ark.pagination-url');

        $this->registerDataBags();
    }

    /**
     * @TODO: update metatags databag
     */
    private function registerDataBags(): void
    {
        DataBag::register('metatags', [
            '*' => [
                'title'       => 'Nodem',
                'description' => '',
            ],
        ]);

        DataBag::register('fortify-content', [
            'register' => [
                'pageTitle'   => trans('metatags.sign-up.title'),
                'title'       => trans('pages.sign-up.page_title'),
                'description' => trans('pages.sign-up.page_description'),
            ],
            'login' => [
                'pageTitle'   => trans('metatags.sign-in.title'),
                'title'       => trans('pages.sign-in.page_title'),
                'description' => trans('pages.sign-in.page_description'),
                'signupLink'  => trans('pages.sign-in.sign_up_link', ['route' => '/register']),
            ],
            'password' => [
                'reset' => [
                    'pageTitle' => trans('metatags.password.reset.title'),
                ],
                'request' => [
                    'pageTitle' => trans('metatags.password.request.title'),
                ],
            ],
            'two-factor' => [
                'login' => [
                    'pageTitle' => trans('metatags.two-factor.login.title'),
                ],
            ],
        ]);
    }
}
