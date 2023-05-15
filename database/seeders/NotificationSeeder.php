<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DatabaseNotification;
use App\Models\User;
use Illuminate\Database\Seeder;

final class NotificationSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('username', 'nodem')->first();

        DatabaseNotification::factory()->count(5)->ownedBy($user->servers->first())->create();
    }
}
