<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'profile_id' => Profile::factory(),
            'content' => $this->faker->paragraph(),
            'commentable_type' => Project::class,
            'commentable_id' => Project::factory(),
        ];
    }
}
