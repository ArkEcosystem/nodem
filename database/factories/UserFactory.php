<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

final class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = vsprintf('%s %s', [
            $this->faker->firstName,
            $this->faker->lastName,
        ]);

        $username = $this->getUniqueUsername($name);

        return [
            'username'          => $username,
            'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token'    => Str::random(10),
        ];
    }

    private function getUniqueUsername(string $name): string
    {
        $index            = 0;
        $username         = (string) Str::of($name)->lower()->slug('.');
        $originalUsername = $username;

        do {
            if ($index > 0) {
                $username = $originalUsername.'.'.$index;
            }

            $index = $index + 1;
        } while (User::where('username', $username)->exists());

        return $username;
    }
}
