<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\TeamMemberRole;
use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    public function run()
    {
        $user = User::factory()->create(['username' => 'nodem']);

        User::factory()
            ->create(['username' => 'admin'])
            ->joinAs(TeamMemberRole::ADMIN, $user);

        User::factory()
            ->create(['username' => 'maintainer'])
            ->joinAs(TeamMemberRole::MAINTAINER, $user);

        User::factory()
            ->create(['username' => 'readonly'])
            ->joinAs(TeamMemberRole::READONLY, $user);
    }
}
