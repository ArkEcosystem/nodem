<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class ToggleViewOption extends Component
{
    use InteractsWithUser;

    /**
     * The default view if user hasn't ever updated their view settings.
     */
    public const DEFAULT_OPTION = 'list';

    public string $tableView;

    /** @var mixed */
    protected $listeners = [
        'serverAdded' => '$refresh',
    ];

    /**
     * Mount the Livewire component.
     *
     * @param string $tableView
     *
     * @return void
     */
    public function mount(string $tableView) : void
    {
        $this->tableView = $tableView;
    }

    /**
     * Change the user's table view when the value is updated.
     *
     * @return void
     */
    public function updatedTableView() : void
    {
        $this->tableView = $this->tableView === 'grid' ? 'grid' : 'list';

        // Handles 419 session expired and user got logged out...
        if ($this->user !== null) {
            $this->user->setDefaultTableView($this->tableView);
        }
    }

    public function render() : View
    {
        return view('livewire.toggle-view-option', [
            'disabled' => $this->user?->servers()->count() === 0,
        ]);
    }
}
