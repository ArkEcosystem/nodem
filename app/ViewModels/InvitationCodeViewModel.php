<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Models\InvitationCode;
use App\Models\User;

final class InvitationCodeViewModel implements ViewModel
{
    private InvitationCode $model;

    public function __construct(InvitationCode $model)
    {
        $this->model = $model;
    }

    public function id(): int
    {
        return $this->model->id;
    }

    public function username(): string
    {
        return $this->model->username;
    }

    public function issuer(): User | null
    {
        return $this->model->issuer;
    }

    public function role(): string
    {
        return $this->model->role;
    }

    public function code(): string
    {
        return $this->model->code;
    }

    public function dateGeneratedString(): string
    {
        return $this->model->created_at->format('d M Y');
    }
}
