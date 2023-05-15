<div>
    @if($this->invites->count() > 0)
        <x-team.pending-invites :invites="$this->invites" />
    @endif

    @if($this->modalShown)
        <x-modals.delete
            :title="trans('pages.team.pending_invitations.delete_modal.title')"
            :description="trans('pages.team.pending_invitations.delete_modal.description')"
            action-method="deleteInvitationCode"
            close-method="closeDeleteInvitationCode"
            :can-submit="$this->user->can(TeamMemberPermission::TEAM_MEMBERS_DELETE)"
        />
    @endif
</div>
