@props(['invites'])

<x-ark-container container-class="flex flex-col pt-6 pb-8 border-b border-theme-secondary-300">
    <div class="flex flex-col justify-between pb-6 space-y-4 sm:flex-row sm:items-center sm:space-y-0">
        <h3>@lang('pages.team.pending_invitations.title')</h3>

        <div class="sm:justify-end">
            <button
                type="button"
                class="w-full button-primary"
                onclick="livewire.emit('openInviteTeamMember')"
            >
                @lang('actions.invite')
            </button>
        </div>
    </div>

    <x-tables.desktop.pending-invites :invites="$invites" />
    <x-tables.mobile.pending-invites :invites="$invites" />
</x-ark-container>
