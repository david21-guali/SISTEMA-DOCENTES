<?php

namespace Database\Factories;

use App\Models\ForumTopic;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ForumTopicFactory extends Factory
{
    protected $model = ForumTopic::class;

    public function definition()
    {
        return [
            'profile_id' => Profile::factory(),
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
        ];
    }
}
