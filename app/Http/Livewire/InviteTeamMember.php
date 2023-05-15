<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\CreateInvitationCode;
use App\Enums\TeamMemberPermission;
use App\Enums\TeamMemberRole;
use App\Http\Livewire\Concerns\ManagesRoles;
use App\Models\InvitationCode;
use App\Rules\Username;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;

/**
 * @property \App\Models\User $user
 */
final class InviteTeamMember extends Component
{
    use AuthorizesRequests;
    use HasModal;
    use InteractsWithUser;
    use ManagesRoles;

    public ?string $username = null;

    public ?string $code = null;

    public ?string $role = TeamMemberRole::READONLY;

    public bool $readonly = false;

    /** @var mixed */
    protected $listeners = [
        'openInviteTeamMember' => 'open',
    ];

    public function render(): View
    {
        return view('livewire.invite-team-member');
    }

    public function open(): void
    {
        $this->openModal();
    }

    public function close(): void
    {
        $this->closeModal();

        $this->reset();

        $this->resetErrorBag();
    }

    public function invite(): void
    {
        $this->authorize(TeamMemberPermission::TEAM_MEMBERS_INVITE);

        $this->validate();

        InvitationCode::create([
            'issuer_id' => $this->user->id,
            'username'  => $this->username,
            'code'      => $this->code = (new CreateInvitationCode())(),
            'role'      => $this->role,
        ]);

        $this->readonly = true;

        $this->emit('refreshPendingInvitations');
        $this->emit('inviteAdded');
    }

    protected function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                new Username(),
                function ($attribute, $value, $fail): void {
                    if (InvitationCode::userIsATeamMember($value)) {
                        $fail(trans('pages.team.already_member'));
                    }

                    if (InvitationCode::userHasBeenInvited($value)) {
                        $fail(trans('pages.team.already_invited'));
                    }
                },
            ],
            'role' => $this->getRoleValidationRules(),
        ];
    }
}
