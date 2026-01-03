<?php

namespace Database\Factories;

use App\Models\Evaluation;
use App\Models\Project;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationFactory extends Factory
{
    protected $model = Evaluation::class;

    public function definition()
    {
        return [
            'project_id' => Project::factory(),
            'evaluator_id' => Profile::factory(),
            'innovation_score' => $this->faker->numberBetween(1, 5),
            'relevance_score' => $this->faker->numberBetween(1, 5),
            'results_score' => $this->faker->numberBetween(1, 5),
            'impact_score' => $this->faker->numberBetween(1, 5),
            'methodology_score' => $this->faker->numberBetween(1, 5),
            'final_score' => $this->faker->randomFloat(2, 1, 10),
            'status' => $this->faker->randomElement(['borrador', 'finalizada']),
        ];
    }
}
