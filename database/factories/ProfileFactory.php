<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department' => $this->faker->word(),
            'specialty' => $this->faker->word(),
            'position' => $this->faker->word(),
            'location' => $this->faker->city(),
            'is_active' => true,
        ];
    }
}
