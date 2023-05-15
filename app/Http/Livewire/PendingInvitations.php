<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\TeamMemberPermission;
use App\Models\InvitationCode;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View as Contract;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\View;
use Livewire\Component;

final class PendingInvitations extends Component
{
    use AuthorizesRequests;
    use InteractsWithUser;
    use HasModal;

    public ?string $selectedInvitationCode = null;

    /** @var mixed */
    protected $listeners = [
        'refreshPendingInvitations' => '$refresh',
    ];

    public function render(): Contract
    {
        return View::make('livewire.pending-invitations');
    }

    public function getInvitesProperty(): LengthAwarePaginator
    {
        return ViewModelFactory::paginate(InvitationCode::where('redeemed_at', null)->paginate());
    }

    public function openDeleteInvitationCode(string $invitationCode): void
    {
        if (! InvitationCode::whereCode($invitationCode)->exists()) {
            return;
        }

        $this->openModal();

        $this->selectedInvitationCode = $invitationCode;
    }

    public function closeDeleteInvitationCode(): void
    {
        $this->selectedInvitationCode = null;

        $this->closeModal();
    }

    public function deleteInvitationCode(): void
    {
        $this->authorize(TeamMemberPermission::TEAM_MEMBERS_INVITE);

        InvitationCode::whereCode($this->selectedInvitationCode)->delete();

        $this->emit('inviteRemoved');

        $this->closeDeleteInvitationCode();
    }
}
