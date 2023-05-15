<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\TeamMemberPermission;
use App\Http\Livewire\Concerns\ManagesRoles;
use App\Models\User;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;

final class UpdateTeamMember extends Component
{
    use AuthorizesRequests;
    use InteractsWithUser;
    use HasModal;
    use ManagesRoles;

    public ?User $member;

    public ?string $role = null;

    /** @var mixed */
    protected $listeners = [
        'openUpdateTeamMember' => 'open',
    ];

    public function render(): View
    {
        return view('livewire.update-team-member');
    }

    public function open(int $memberId): void
    {
        $this->resetErrorBag();

        $this->member = User::findOrFail($memberId);

        $this->role = $this->member->getRoleNames()->first();

        $this->openModal();
    }

    public function close(): void
    {
        $this->closeModal();

        $this->member = null;
        $this->role   = null;
    }

    public function save(): void
    {
        $this->authorize(TeamMemberPermission::TEAM_MEMBERS_EDIT);

        $data = $this->validate([
            'role' => $this->getRoleValidationRules(),
        ]);

        /** @var User */
        $member = $this->member;

        if ($this->role !== $member->getRoleNames()->first()) {
            $member->changeRole($data['role']);
        }

        $this->close();

        $this->emit('toastMessage', [trans('pages.team.edit-modal.update_success'), 'success']);
        $this->emit('teamMemberUpdated');
    }
}
