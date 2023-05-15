<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\TeamMemberPermission;
use App\Models\User;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;

final class ManageTeam extends Component
{
    use AuthorizesRequests;
    use HasModal;
    use InteractsWithUser;

    public ?int $removalId = null;

    public ?User $selectedTeamMember;

    /** @var mixed */
    protected $listeners = [
        'inviteAdded'       => '$refresh',
        'inviteRemoved'     => '$refresh',
        'teamMemberRemoved' => '$refresh',
        'teamMemberUpdated' => '$refresh',
    ];

    public function render(): View
    {
        return view('livewire.manage-team');
    }

    public function getTeamMembersProperty(): LengthAwarePaginator
    {
        return ViewModelFactory::paginate(User::with('roles')->orderBy('id')->paginate());
    }

    public function openConfirm(int $id): void
    {
        $this->removalId          = $id;
        $this->selectedTeamMember = User::findOrFail($this->removalId);
        $this->openModal();
    }

    public function closeConfirm(): void
    {
        $this->removalId          = null;
        $this->selectedTeamMember = null;
        $this->closeModal();
    }

    public function remove(): void
    {
        if ($this->modalShown) {
            $this->authorize(TeamMemberPermission::TEAM_MEMBERS_DELETE);

            /** @var User $user */
            $user = $this->selectedTeamMember;

            $user->delete();

            $this->closeConfirm();
            $this->emit('toastMessage', [trans('pages.team.remove-team-member-modal.remove_success'), 'success']);
            $this->emitSelf('teamMemberRemoved');
        }
    }
}
