<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'latitude' => fake()->latitude(31, 34),
            'longitude' => fake()->longitude(-8, -5),
            'status' => 'En attente',
            'priority' => 'Moyenne',
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'ai_summary' => null,
            'ai_suggested_category' => null,
        ];
    }
}