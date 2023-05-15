<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\InvitationCode;
use Illuminate\Support\Str;

final class CreateInvitationCode
{
    public function __invoke(int $length = 16): string
    {
        do {
            $code = Str::random($length);
        } while (InvitationCode::where('code', $code)->exists());

        return $code;
    }
}
