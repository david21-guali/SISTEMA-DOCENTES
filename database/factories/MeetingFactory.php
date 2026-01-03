<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'meeting_date' => now()->addDay(),
            'location' => $this->faker->city(),
            'created_by' => Profile::factory(),
            'status' => 'pendiente',
        ];
    }
}
