<?php

declare(strict_types=1);

/* @var $factory \Illuminate\Database\Eloquent\Factory */

namespace Database\Factories;

use App\Models\DatabaseNotification;
use App\Models\Server;
use App\Models\User;
use BadMethodCallException;
use Illuminate\Database\Eloquent\Factories\Factory;

final class DatabaseNotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DatabaseNotification::class;

    protected $server;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if (is_null($this->server)) {
            throw new BadMethodCallException('Call [ownedBy] before creating an model.');
        }

        return [
            'id'              => $this->faker->uuid,
            'type'            => "App\TestNotification",
            'notifiable_type' => User::class,
            'notifiable_id'   => $this->server->user->id,
            'data'            => [
                'relatable_type' => (new Server())->getMorphClass(),
                'relatable_id'   => $this->server->id,
                'serverId'       => $this->server->id,
                'name'           => $this->server->name,
                'content'        => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'type'           => 'success',
                'action'         => [
                    'title' => 'View',
                    'url'   => route('home'),
                ],
            ],
        ];
    }

    public function ownedBy(Server $server)
    {
        $this->server = $server;

        return $this;
    }
}
