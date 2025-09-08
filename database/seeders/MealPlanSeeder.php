<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MealPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        $recipes = \App\Models\Recipe::all();

        if (! $user || $recipes->isEmpty()) {
            return;
        }

        // Create meal plans for the next 7 days
        for ($i = 0; $i < 7; $i++) {
            $date = now()->addDays($i)->toDateString();

            // Breakfast
            \App\Models\MealPlan::create([
                'user_id' => $user->id,
                'date' => $date,
                'meal_type' => 'breakfast',
                'recipe_id' => $recipes->random()->id,
            ]);

            // Lunch
            \App\Models\MealPlan::create([
                'user_id' => $user->id,
                'date' => $date,
                'meal_type' => 'lunch',
                'recipe_id' => $recipes->random()->id,
            ]);

            // Dinner
            \App\Models\MealPlan::create([
                'user_id' => $user->id,
                'date' => $date,
                'meal_type' => 'dinner',
                'recipe_id' => $recipes->random()->id,
            ]);
        }
    }
}
