<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\TeamMemberRole;
use App\Models\InvitationCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

final class InvitationSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('username', 'nodem')->first();

        InvitationCode::factory(5)->state(new Sequence(
            ['role' => TeamMemberRole::READONLY],
            ['role' => TeamMemberRole::MAINTAINER],
            ['role' => TeamMemberRole::ADMIN],
        ))->create(['issuer_id' => $user->id]);
    }
}
