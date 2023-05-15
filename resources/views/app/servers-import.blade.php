@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])
    @section('content')
        <livewire:import-servers />
    @endsection

@endcomponent
