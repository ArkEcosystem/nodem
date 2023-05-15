<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Server;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View as Contract;
use Illuminate\Support\Facades\View;
use Livewire\Component;

final class ServerHeader extends Component
{
    public Server $model;

    public function render(): Contract
    {
        return View::make('livewire.server-header', [
            'server' => ViewModelFactory::make($this->model),
        ]);
    }

    protected function getListeners(): array
    {
        return [
            'serverActionTriggered'.$this->model->id => '$refresh',
            'serverReloadDetails'.$this->model->id   => '$refresh',
        ];
    }
}
