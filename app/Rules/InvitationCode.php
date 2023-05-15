<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\InvitationCode as Model;
use Illuminate\Contracts\Validation\Rule;
use Throwable;

final class InvitationCode implements Rule
{
    private bool $notFound = false;

    public function passes($attribute, $value): bool
    {
        $username = session()->get('username', '');

        try {
            return ! Model::findByCodeAndUsername($value, $username)->hasBeenRedeemed();
        } catch (Throwable) {
            $this->notFound = true;

            return false;
        }
    }

    public function message(): string
    {
        if ($this->notFound) {
            return trans('validation.invitation_code.not_found');
        }

        return trans('validation.invitation_code.redeemed');
    }
}
