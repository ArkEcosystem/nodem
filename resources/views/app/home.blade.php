@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <livewire:trigger-server-action />

        <livewire:server-list />

        <livewire:bip38-password-modal />

        <livewire:add-server-modal />
    @endsection

@endcomponent
