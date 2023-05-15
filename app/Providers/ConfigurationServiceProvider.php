<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\CreateNewUser;
use App\Http\Livewire\RegisterForm;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Livewire\Livewire;

final class ConfigurationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('auth.register-form', RegisterForm::class);

        Fortify::createUsersUsing(CreateNewUser::class);
    }
}
