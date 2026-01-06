<?php

namespace Database\Factories;

use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ForumPostFactory extends Factory
{
    protected $model = ForumPost::class;

    public function definition()
    {
        return [
            'topic_id' => ForumTopic::factory(),
            'profile_id' => Profile::factory(),
            'user_id' => \App\Models\User::factory(),
            'content' => $this->faker->paragraph(),
        ];
    }
}
