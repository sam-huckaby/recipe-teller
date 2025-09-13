<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(2),
            'description' => fake()->paragraph(),
            'ingredients' => [
                fake()->sentence(3),
                fake()->sentence(2),
                fake()->sentence(4),
            ],
            'instructions' => [
                fake()->sentence(),
                fake()->sentence(),
                fake()->sentence(),
            ],
            'prep_time' => fake()->numberBetween(5, 60),
            'cook_time' => fake()->numberBetween(10, 120),
            'servings' => fake()->numberBetween(1, 8),
            'tags' => [fake()->word(), fake()->word()],
        ];
    }
}
