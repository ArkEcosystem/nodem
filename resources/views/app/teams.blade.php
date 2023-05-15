@component('layouts.app', ['fullWidth' => true, 'isLanding' => true])

    @section('content')
        <x-page-header :title="trans('pages.team.title')" />

        <livewire:invite-team-member />

        @can(TeamMemberPermission::TEAM_MEMBERS_ADD)
            <livewire:pending-invitations />
        @endcan

        <livewire:manage-team />
        <livewire:update-team-member />
    @endsection

@endcomponent
