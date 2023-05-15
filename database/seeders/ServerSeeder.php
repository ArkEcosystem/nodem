<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ServerProcessTypeEnum;
use App\Models\Process;
use App\Models\ResourceIndicator;
use App\Models\Server;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

final class ServerSeeder extends Seeder
{
    public function run()
    {
        $user                 = User::where('username', 'nodem')->first();
        $availabilitySequence = new Sequence(
            [
                'extra_attributes->manager_is_running'     => true,
                'extra_attributes->server_is_online'       => true,
                'extra_attributes->has_height_mismatch'    => true,
            ],
            [
                'extra_attributes->manager_is_running'     => true,
                'extra_attributes->server_is_online'       => true,
                'extra_attributes->has_height_mismatch'    => false,
            ],
            [
                'extra_attributes->manager_is_running' => false,
                'extra_attributes->server_is_online'   => true,
            ],
            [
                'extra_attributes->manager_is_running' => false,
                'extra_attributes->server_is_online'   => false,
            ],
        );

        $versioningSequence = new Sequence(
            [
                'core_version_current'                           => '4.0.0-next.0',
                'core_version_latest'                            => '4.0.0-next.0',
                'extra_attributes->core_manager_current_version' => '3.0.2',
                'extra_attributes->core_manager_latest_version'  => '3.0.2',
            ],
            [
                'core_version_current'                           => '4.0.0-next.0',
                'core_version_latest'                            => '4.0.0-next.0',
                'extra_attributes->core_manager_current_version' => '3.0.0',
                'extra_attributes->core_manager_latest_version'  => '3.0.2',
            ],
            [
                'core_version_current'                           => '3.0.0-next.8',
                'core_version_latest'                            => '4.0.0-next.0',
                'extra_attributes->core_manager_current_version' => '3.0.2',
                'extra_attributes->core_manager_latest_version'  => '3.0.2',
            ],
            [
                'core_version_current'                           => '3.0.0-next.8',
                'core_version_latest'                            => '4.0.0-next.0',
                'extra_attributes->core_manager_current_version' => '3.0.0',
                'extra_attributes->core_manager_latest_version'  => '3.0.2',
            ],
        );

        $separated = Server::factory(7)
            ->state($availabilitySequence)
            ->state($versioningSequence)
            ->create(['user_id' => $user->id]);

        $combined  = Server::factory(7)
            ->state($availabilitySequence)
            ->state($versioningSequence)
            ->prefersCombined()
            ->create(['user_id' => $user->id]);

        $separated->concat($combined)->each(function ($server) use ($user): void {
            $entries = [];
            if ($server->process_type === ServerProcessTypeEnum::SEPARATE) {
                Process::factory()->create(['server_id' => $server->id, 'type' => 'forger', 'name' => 'ark-forger']);
                Process::factory()->create(['server_id' => $server->id, 'type' => 'relay', 'name' => 'ark-relay']);

                $entries = [
                    trans('logs.process_started', ['type' => 'Forger']),
                    trans('logs.process_stopped', ['type' => 'Forger']),
                    trans('logs.process_started', ['type' => 'Relay']),
                    trans('logs.process_stopped', ['type' => 'Relay']),
                ];
            } else {
                Process::factory()->create(['server_id' => $server->id, 'type' => 'core', 'name' => 'ark-core']);

                $entries = [
                    trans('logs.process_started', ['type' => 'Core']),
                    trans('logs.process_stopped', ['type' => 'Core']),
                ];
            }

            foreach ($entries as $entry) {
                activity()
                    ->performedOn($server)
                    ->causedBy($user)
                    ->withProperties(['username' => $user->username])
                    ->log($entry);
            }

            for ($y = 0; $y < 365; $y++) {
                $indicators = [];

                for ($h = 0; $h < 24; $h++) {
                    $indicators[] = ResourceIndicator::factory()->raw([
                        'server_id'  => $server,
                        'created_at' => Carbon::now()->subDays($y)->subHours($h),
                    ]);
                }

                ResourceIndicator::insert($indicators);
            }
        });
    }
}
