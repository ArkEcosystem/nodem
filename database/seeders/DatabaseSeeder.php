<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AccessControlSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(InvitationSeeder::class);
        $this->call(ServerSeeder::class);
        $this->call(NotificationSeeder::class);
    }
}
