<?php

declare(strict_types=1);

use App\Actions\CreateInvitationCode;
use App\Models\InvitationCode;

it('creates a new unique invitation code', function (): void {
    $codes = InvitationCode::factory(3)->create()->pluck('code');

    $code = (new CreateInvitationCode())();

    expect($code)->toBeString();
    expect($codes)->not->toHaveKey($code);
});
