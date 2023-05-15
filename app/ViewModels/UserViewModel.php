<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Enums\TeamMemberRole;
use App\Models\User;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Spatie\Permission\Traits\HasRoles;

final class UserViewModel implements ViewModel
{
    use HasRoles;

    public function __construct(private User $model)
    {
    }

    public function id(): int
    {
        return $this->model->id;
    }

    public function username(): string
    {
        return $this->model->username;
    }

    public function role(): string|null
    {
        if ($this->isSuperAdmin()) {
            return TeamMemberRole::OWNER;
        }

        return $this->model->getRoleNames()->first();
    }

    public function createdAtLocal(): string
    {
        return $this->model->created_at_local->format(DateFormat::DATE);
    }

    public function isSuperAdmin(): bool
    {
        return $this->model->isSuperAdmin();
    }

    public function model(): User
    {
        return $this->model;
    }
}
