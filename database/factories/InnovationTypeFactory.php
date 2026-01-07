<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InnovationTypeFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => $this->faker->sentence(),
        ];
    }
}
