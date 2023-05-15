<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Contracts\ViewModel;
use App\ViewModels\ViewModelFactory;

trait HasViewModel
{
    final public function toViewModel(): ViewModel
    {
        return ViewModelFactory::make($this);
    }
}
