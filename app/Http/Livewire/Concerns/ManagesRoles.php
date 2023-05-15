<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Enums\TeamMemberRole;
use Illuminate\Validation\Rule;

trait ManagesRoles
{
    public function getRoleOptions(): array
    {
        return [
            TeamMemberRole::ADMIN      => trans('roles.admin'),
            TeamMemberRole::MAINTAINER => trans('roles.maintainer'),
            TeamMemberRole::READONLY   => trans('roles.readonly'),
        ];
    }

    public function getRoleValidationRules(): array
    {
        return [
            'required',
            'string',
            Rule::in(array_keys($this->getRoleOptions())),
        ];
    }
}
