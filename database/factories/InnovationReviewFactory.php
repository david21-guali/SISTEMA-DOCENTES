<?php

namespace Database\Factories;

use App\Models\InnovationReview;
use App\Models\Innovation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InnovationReview>
 */
class InnovationReviewFactory extends Factory
{
    protected $model = InnovationReview::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'innovation_id' => Innovation::factory(),
            'reviewer_id'   => User::factory(),
            'vote'          => $this->faker->randomElement(['approved', 'rejected']),
            'comment'       => $this->faker->text(70),
        ];
    }
}
