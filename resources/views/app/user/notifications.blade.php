@component('layouts.app')
    @section('breadcrumbs')
        <x-ark-breadcrumbs :crumbs="[
            ['route' => 'home', 'label' => trans('menus.dashboard')],
            ['label' => trans('menus.user-settings.security')],
        ]" />
    @endsection

    @section('content')
        <livewire:manage-notifications />
    @endsection
@endcomponent
