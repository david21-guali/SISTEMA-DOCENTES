<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\ResourceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceFactory extends Factory
{
    protected $model = Resource::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'resource_type_id' => ResourceType::factory(),
            'description' => $this->faker->sentence(),
            'cost' => $this->faker->randomFloat(2, 10, 1000),
            'file_path' => null,
        ];
    }
}
