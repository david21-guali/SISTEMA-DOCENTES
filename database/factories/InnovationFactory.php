<?php

namespace Database\Factories;

use App\Models\InnovationType;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class InnovationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'profile_id' => Profile::factory(),
            'innovation_type_id' => InnovationType::factory(),
            'status' => 'propuesta',
        ];
    }
}
