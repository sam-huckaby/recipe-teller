<?php

namespace Tests\Feature;

use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShoppingTest extends TestCase
{
    use RefreshDatabase;

    public function test_shopping_page_loads_successfully()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/shopping');

        $response->assertStatus(200)
            ->assertSee('Shopping List')
            ->assertSee('No meals planned');
    }

    public function test_shopping_list_displays_ingredients_from_meal_plans()
    {
        $user = User::factory()->create();

        $recipe = Recipe::create([
            'user_id' => $user->id,
            'name' => 'Test Recipe',
            'description' => 'Test description',
            'ingredients' => ['2 cups flour', '1 egg', '1 cup milk'],
            'instructions' => ['Mix ingredients'],
            'servings' => 4,
        ]);

        MealPlan::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'date' => Carbon::today(),
            'meal_type' => 'breakfast',
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Shopping::class)
            ->assertSee('2 cups flour')
            ->assertSee('1 egg')
            ->assertSee('1 cup milk')
            ->assertSee('Test Recipe')
            ->assertSee('Total items: 3');
    }

    public function test_shopping_list_includes_only_next_seven_days()
    {
        $user = User::factory()->create();

        $recipe = Recipe::create([
            'user_id' => $user->id,
            'name' => 'Test Recipe',
            'description' => 'Test description',
            'ingredients' => ['test ingredient'],
            'instructions' => ['Mix ingredients'],
            'servings' => 4,
        ]);

        // Meal plan for today (should be included)
        MealPlan::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'date' => Carbon::today(),
            'meal_type' => 'breakfast',
        ]);

        // Meal plan for 8 days from now (should not be included)
        MealPlan::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'date' => Carbon::today()->addDays(8),
            'meal_type' => 'breakfast',
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Shopping::class)
            ->assertSee('Total items: 1');
    }

    public function test_shopping_list_sorts_ingredients_alphabetically()
    {
        $user = User::factory()->create();

        $recipe = Recipe::create([
            'user_id' => $user->id,
            'name' => 'Test Recipe',
            'description' => 'Test description',
            'ingredients' => ['Zucchini', 'Apple', 'Banana'],
            'instructions' => ['Mix ingredients'],
            'servings' => 4,
        ]);

        MealPlan::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'date' => Carbon::today(),
            'meal_type' => 'breakfast',
        ]);

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Shopping::class);

        $shoppingList = $component->get('shoppingList');

        $this->assertEquals('Apple', $shoppingList[0]['ingredient']);
        $this->assertEquals('Banana', $shoppingList[1]['ingredient']);
        $this->assertEquals('Zucchini', $shoppingList[2]['ingredient']);
    }
}
