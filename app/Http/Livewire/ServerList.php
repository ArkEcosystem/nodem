<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\Constants;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Contracts\View\View as Contract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View;
use Livewire\Component;

/**
 * @property \App\Models\User $user
 */
final class ServerList extends Component
{
    use HasPagination;
    use InteractsWithUser;

    /** @var mixed */
    protected $listeners = [
        'serverAdded'         => '$refresh',
        'serverReloadDetails' => '$refresh',
    ];

    public function render(): Contract
    {
        /** @var LengthAwarePaginator $servers */
        $servers = ViewModelFactory::paginate(
            $this->user
                ->servers()
                ->latest('id')
                ->paginate(Constants::SERVERS_PER_PAGE)
        );

        return View::make('livewire.server-list', [
            'servers' => $servers,
        ]);
    }
}
