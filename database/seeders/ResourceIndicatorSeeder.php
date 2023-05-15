<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ResourceIndicator;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class ResourceIndicatorSeeder extends Seeder
{
    public function run()
    {
        // Which server?
        $server = Server::first();

        // Delete server's previous chart data...
        // ResourceIndicator::where('server_id', $server->id)->delete();

        // How long in the past do you want to seed data?
        $days = 100;

        for ($day = 0; $day < $days; $day++) {
            $indicators = [];

            for ($hour = 0; $hour < 24; $hour++) {
                for ($minute = 0; $minute < 59; $minute++) {
                    $time = $time = Carbon::now()->subDays($day)->subHours($hour)->subMinutes($minute);

                    $indicators[] = ResourceIndicator::factory()->raw([
                        'server_id'  => $server->id,
                        'created_at' => $time,
                        'updated_at' => $time,
                    ]);
                }
            }

            ResourceIndicator::insert($indicators);
        }
    }
}
