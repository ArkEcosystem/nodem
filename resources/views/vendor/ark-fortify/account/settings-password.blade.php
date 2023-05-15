@component('layouts.user-settings', ['title' => trans('pages.user-settings.security.page_title')])
    @push('scripts')
        <script src="{{ mix('js/file-download.js')}}"></script>
    @endpush

    @section('breadcrumbs')
        <x-ark-breadcrumbs :crumbs="[
            ['route' => 'home', 'label' => trans('menus.dashboard')],
            ['label' => trans('menus.user-settings.security')],
        ]" />
    @endsection

    <livewire:two-factor-authentication-prompt />

    <div class="flex flex-col space-y-5">
        @if (! Auth::user()->enabledTwoFactor())
            <x-ark-alert type="warning">
                {{ trans('pages.user-settings.security.two_factor_alert') }}
            </x-ark-alert>
        @endif

        <div class="px-8 pb-8 rounded-xl border border-theme-secondary-300">
            <livewire:profile.update-password-form />
        </div>

        <div class="p-8 rounded-xl border border-theme-secondary-300">
            <livewire:profile.two-factor-authentication-form />
        </div>
    </div>
@endcomponent
