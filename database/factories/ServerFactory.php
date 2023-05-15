<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ServerProcessTypeEnum;
use App\Enums\ServerProviderTypeEnum;
use App\Enums\ServerUpdatingTasksEnum;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class ServerFactory extends Factory
{
    protected $model = Server::class;

    protected $user;

    public function definition()
    {
        return [
            'user_id'         => fn (): User => User::factory()->create(),
            'provider'        => $this->faker->randomElement([
                ServerProviderTypeEnum::AWS,
                ServerProviderTypeEnum::AZURE,
                ServerProviderTypeEnum::DIGITAL_OCEAN,
                ServerProviderTypeEnum::HETZNER,
                ServerProviderTypeEnum::LINODE,
                ServerProviderTypeEnum::VULTR,
                ServerProviderTypeEnum::OTHER,
                ServerProviderTypeEnum::OVH,
                ServerProviderTypeEnum::NETCUP,
                ServerProviderTypeEnum::GOOGLE,
            ]),
            'name'                                 => $this->getValidDisplayName(),
            'process_type'                         => ServerProcessTypeEnum::SEPARATE,
            'host'                                 => "http://{$this->faker->ipv4}:4005/api",
            'ping'                                 => $this->faker->numberBetween(1, 1000),
            'height'                               => $this->faker->numberBetween(1, 1000),
            'cpu_total'                            => $this->faker->numberBetween(6, 12),
            'cpu_used'                             => $this->faker->numberBetween(6, 12),
            'cpu_available'                        => $this->faker->numberBetween(6, 12),
            'ram_total'                            => $this->faker->numberBetween(1024, 4096),
            'ram_used'                             => $this->faker->numberBetween(1024, 4096),
            'ram_available'                        => $this->faker->numberBetween(1024, 4096),
            'disk_total'                           => $this->faker->numberBetween(2048, 8192),
            'disk_used'                            => $this->faker->numberBetween(512, 1024),
            'disk_available'                       => $this->faker->numberBetween(512, 1024),
            'core_version_current'                 => '3.0.0',
            'core_version_latest'                  => '3.0.0',
            'auth_username'                        => Str::limit($this->faker->userName, 32),
            'auth_password'                        => Str::limit($this->faker->password, 32),
            'auth_access_key'                      => Str::random(32),
            sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::UPDATING_SERVER_PING) => true,
            sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING) => true,
            'uses_bip38_encryption'                => false,
        ];
    }

    /**
     * Mark the server to be authenticated using access keys and not credentials.
     *
     * @return \Database\Factories\ServerFactory
     */
    public function authUsingAccessKey() : self
    {
        return $this->state(fn (array $attributes) => [
            'auth_username'   => null,
            'auth_password'   => null,
            'auth_access_key' => Str::random(32),
        ]);
    }

    /**
     * Mark the server to be authenticated using credentials and not access key.
     *
     * @return \Database\Factories\ServerFactory
     */
    public function authUsingCredentials() : self
    {
        return $this->state(fn (array $attributes) => [
            'auth_username'   => Str::limit($this->faker->userName, 32),
            'auth_password'   => Str::limit($this->faker->password, 32),
            'auth_access_key' => null,
        ]);
    }

    public function ownedBy(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function offline(): self
    {
        return $this->state(fn (array $attributes) => [
            sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::UPDATING_SERVER_PING) => null,
            sprintf('extra_attributes->failed->%s', ServerUpdatingTasksEnum::UPDATING_SERVER_PING) => true,
        ]);
    }

    public function notAvailable(): self
    {
        return $this->managerNotRunning();
    }

    public function prefersCombined(): self
    {
        return $this->state(fn () => [
            'process_type' => ServerProcessTypeEnum::COMBINED,
        ]);
    }

    public function prefersSeparated(): self
    {
        return $this->state(fn () => [
            'process_type' => ServerProcessTypeEnum::SEPARATE,
        ]);
    }

    public function usesBip38Encryption(): self
    {
        return $this->state(fn () => [
            'uses_bip38_encryption' => true,
        ]);
    }

    public function managerNotRunning(): self
    {
        return $this->state(fn (array $attributes) => [
            sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING) => null,
            sprintf('extra_attributes->failed->%s', ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING) => true,
        ]);
    }

    public function heightMismatch(): self
    {
        return $this->state(fn (array $attributes) => [
            'extra_attributes->has_height_mismatch' => true,
        ]);
    }

    public function unableToFetchHeight(): self
    {
        return $this->state(fn (array $attributes) => [
            'extra_attributes->unable_to_fetch_height' => true,
        ]);
    }

    private function getValidDisplayName(): string
    {
        return Str::limit($this->faker->unique()->slug, 30, '');
    }
}
