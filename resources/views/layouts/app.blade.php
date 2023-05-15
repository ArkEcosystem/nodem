<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-ark-pages-includes-layout-head
        default-name="Nodem"
        mask-icon-color="#00b2aa"
        microsoft-tile-color="#00b2aa"
        theme-color="#00b2aa"
    >
        @stack('structured-data')

        <link href="https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@v2.5.1/dist/cookieconsent.css" rel="stylesheet">
        <livewire:frontend-settings />
    </x-ark-pages-includes-layout-head>

    <x-ark-pages-includes-layout-body>
        <x-ark-navbar
            title="Nodem"
            breakpoint="lg"
            :navigation="[
                ['route' => 'home', 'label' => trans('menus.dashboard')],
                [
                    'href' => 'https://ark.dev/docs/nodem/',
                    'label' => trans('menus.support'),
                    'icon' => 'arrows/arrow-external',
                    'attributes' => ['target' => '_blank', 'data-safe-external' => 'true'],
                ],
            ]"
            :profile-menu="[
                [
                    'label' => trans('menus.user-settings.settings'),
                    'route' => 'account.settings.password',
                    'icon'  => 'gear',
                ],
                [
                    'label' => trans('menus.team'),
                    'route' => 'user.teams',
                    'icon'  => 'users',
                ],
                [
                    'label'  => trans('actions.logout'),
                    'route'  => 'logout',
                    'isPost' => true,
                    'icon'   => 'exit',
                ]
            ]"
            profile-menu-class="w-50 profile-menu"
            :identifier="$currentUser ? $currentUser->username : false"
            show-identifier-letters
            dropdown-classes="md:w-120"
        >
            <x-slot name="logo">
                <span class="flex relative items-center">
                    <div class="relative">
                        <img src="{{ asset('images/logo.svg') }}" class="h-10 md:h-11 lg:ml-0" alt="" />
                    </div>

                    <span class="hidden ml-4 sm:text-2xl lg:block text-theme-secondary-900">
                        <span class="font-bold">{{ config('app.name', 'ARK') }}</span>
                    </span>
                </span>
            </x-slot>
            {{-- @auth
                <x-slot name="notifications">
                    <x-hermes-navbar-notifications />
                </x-slot>
            @endauth --}}
        </x-ark-navbar>

        <x-slot name="content">
            <x-ark-pages-includes-layout-content :slim="isset($fullWidth) && ! $fullWidth">
                @yield('content')
            </x-ark-pages-includes-layout-content>
        </x-slot>

        <x-slot name="includes">
            @auth
                <livewire:alerts />
            @endauth

            <x-ark-external-link-confirm />
        </x-slot>
    </x-ark-pages-includes-layout-body>
</html>
