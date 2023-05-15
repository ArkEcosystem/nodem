<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProcessStatusEnum;
use App\Models\Process;
use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ProcessFactory extends Factory
{
    protected $model = Process::class;

    public function definition()
    {
        return [
            'server_id' => fn (): Server => Server::factory()->create(),
            'type'      => $this->faker->randomElement(['core', 'relay', 'forger']),
            'name'      => 'ark-'.$this->faker->randomElement(['core', 'relay', 'forger']),
            'pid'       => $this->faker->randomNumber(),
            'cpu'       => $this->faker->numberBetween(1, 2),
            'ram'       => $this->faker->numberBetween(128, 256),
            'status'    => $this->faker->randomElement([
                ProcessStatusEnum::UNDEFINED,
                ProcessStatusEnum::ONLINE,
                ProcessStatusEnum::STOPPED,
                ProcessStatusEnum::STOPPING,
                ProcessStatusEnum::WAITING_RESTART,
                ProcessStatusEnum::LAUNCHING,
                ProcessStatusEnum::ERRORED,
                ProcessStatusEnum::ONE_LAUNCH_STATUS,
            ]),
        ];
    }

    public function forServer(Server $server): self
    {
        return $this->state(fn () => [
            'server_id' => $server->id,
        ]);
    }

    public function core(): self
    {
        return $this->state(fn () => [
            'type' => 'core',
        ]);
    }

    public function relay(): self
    {
        return $this->state(fn () => [
            'type' => 'relay',
        ]);
    }

    public function forger(): self
    {
        return $this->state(fn () => [
            'type' => 'forger',
        ]);
    }

    public function online(): self
    {
        return $this->state(fn () => [
            'status' => ProcessStatusEnum::ONLINE,
        ]);
    }

    public function stopped(): self
    {
        return $this->state(fn () => [
            'status' => ProcessStatusEnum::STOPPED,
        ]);
    }
}
