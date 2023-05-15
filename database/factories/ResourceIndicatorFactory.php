<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ResourceIndicator;
use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ResourceIndicatorFactory extends Factory
{
    protected $model = ResourceIndicator::class;

    public function definition()
    {
        return [
            'server_id' => Server::factory(),
            'cpu'       => $this->faker->randomFloat(2, 1, 100),
            'ram'       => $this->faker->numberBetween(500000, 2000000), // between 500 MB and 2 GB
            'disk'      => $this->faker->numberBetween(20000000, 40000000),  // between 20 GB and 40 GB
        ];
    }
}
