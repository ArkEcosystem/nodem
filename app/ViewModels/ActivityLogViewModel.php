<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

final class ActivityLogViewModel implements ViewModel
{
    private Activity $model;

    public function __construct(Activity $activityLog)
    {
        $this->model = $activityLog;
    }

    public function id(): int
    {
        return $this->model->id;
    }

    public function date(): string
    {
        /** @var Carbon $createdAt */
        $createdAt = $this->model->created_at;

        return $createdAt->format('d-m-Y');
    }

    public function time(): string
    {
        /** @var Carbon $createdAt */
        $createdAt = $this->model->created_at;

        return $createdAt->format('h:i:s');
    }

    public function userName(): string
    {
        return $this->model->getExtraProperty('username');
    }

    public function description(): string
    {
        return $this->model->description;
    }
}
