<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'objectives' => $this->faker->paragraph(),
            'category_id' => Category::factory(),
            'profile_id' => Profile::factory(),
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'status' => 'planificacion',
            'budget' => $this->faker->randomFloat(2, 1000, 10000),
        ];
    }
}
