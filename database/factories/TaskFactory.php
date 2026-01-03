<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'assigned_to' => Profile::factory(),
            'due_date' => now()->addDays(7),
            'status' => 'pendiente',
            'priority' => 'media',
        ];
    }
}
