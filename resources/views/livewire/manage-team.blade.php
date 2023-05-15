<div>
    <x-ark-container container-class="flex flex-col">
        <div class="flex flex-col justify-between pt-0 pb-6 space-y-4 sm:flex-row sm:items-center sm:space-y-0">
            <h3>@lang('pages.team.members.title')</h3>

            @unless (InvitationCode::hasPending())
                <div class="sm:justify-end">
                    <button
                        type="button"
                        class="w-full button-primary"
                        onclick="livewire.emit('openInviteTeamMember')"
                    >
                        @lang('actions.invite')
                    </button>
                </div>
            @endunless
        </div>

        <x-tables.desktop.team-members :team-members="$this->teamMembers" />
        <x-tables.mobile.team-members :team-members="$this->teamMembers" />
    </x-ark-container>

    @if ($this->modalShown)
        <x-modals.confirm-danger
            :show-confirm="!! $this->modalShown"
            :title="trans('pages.team.remove-team-member-modal.title')"
            :message="trans('pages.team.remove-team-member-modal.description', ['user' => $this->selectedTeamMember->username])"
            confirm-action="remove"
            :confirm-button="trans('actions.remove')"
            confirm-icon="trash"
        />
    @endif
</div>
