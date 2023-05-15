<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TeamMemberRole;
use App\Models\InvitationCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class InvitationCodeFactory extends Factory
{
    protected $model = InvitationCode::class;

    public function definition(): array
    {
        return [
            'issuer_id' => fn () => User::factory()->create(),
            'username'  => $this->faker->userName,
            'code'      => Str::random(),
            'role'      => TeamMemberRole::READONLY,
        ];
    }

    public function redeemed(): Factory
    {
        return $this->state(fn () => ['redeemed_at' => now()]);
    }
}
