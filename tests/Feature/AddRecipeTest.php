<?php

use App\Livewire\AddRecipe;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('add recipe page can be rendered', function () {
    $response = $this->get('/recipes/add');

    $response->assertStatus(200);
    $response->assertSee('Add Recipe');
});

test('can create a recipe with all fields', function () {
    Livewire::test(AddRecipe::class)
        ->set('name', 'Test Recipe')
        ->set('url', 'https://example.com/recipe')
        ->set('description', 'A test recipe')
        ->set('prep_time', 15)
        ->set('cook_time', 30)
        ->set('servings', 4)
        ->set('image_url', 'https://example.com/recipe-image.jpg')
        ->set('ingredients', [
            ['name' => 'Flour', 'quantity' => '2 cups'],
            ['name' => 'Sugar', 'quantity' => '1 cup'],
        ])
        ->set('instructions', [
            'Mix ingredients',
            'Bake for 30 minutes',
        ])
        ->call('save')
        ->assertRedirect('/recipes');

    $this->assertDatabaseHas('recipes', [
        'name' => 'Test Recipe',
        'url' => 'https://example.com/recipe',
        'description' => 'A test recipe',
        'prep_time' => 15,
        'cook_time' => 30,
        'servings' => 4,
        'image_url' => 'https://example.com/recipe-image.jpg',
        'user_id' => $this->user->id,
    ]);

    $recipe = Recipe::where('name', 'Test Recipe')->first();
    expect($recipe->ingredients)->toEqual([
        ['name' => 'Flour', 'quantity' => '2 cups'],
        ['name' => 'Sugar', 'quantity' => '1 cup'],
    ]);
    expect($recipe->instructions)->toEqual([
        'Mix ingredients',
        'Bake for 30 minutes',
    ]);
});

test('can create a recipe with minimal required fields', function () {
    Livewire::test(AddRecipe::class)
        ->set('name', 'Simple Recipe')
        ->set('servings', 2)
        ->set('ingredients', [
            ['name' => 'Salt', 'quantity' => '1 tsp'],
        ])
        ->set('instructions', [
            'Add salt',
        ])
        ->call('save')
        ->assertRedirect('/recipes');

    $this->assertDatabaseHas('recipes', [
        'name' => 'Simple Recipe',
        'servings' => 2,
        'user_id' => $this->user->id,
    ]);
});

test('validates required fields', function () {
    Livewire::test(AddRecipe::class)
        ->set('servings', null)
        ->call('save')
        ->assertHasErrors(['name', 'servings']);
});

test('validates url format', function () {
    Livewire::test(AddRecipe::class)
        ->set('url', 'not-a-valid-url')
        ->call('save')
        ->assertHasErrors(['url']);
});

test('validates image url format', function () {
    Livewire::test(AddRecipe::class)
        ->set('image_url', 'not-a-valid-url')
        ->call('save')
        ->assertHasErrors(['image_url']);
});

test('requires at least one ingredient', function () {
    Livewire::test(AddRecipe::class)
        ->set('name', 'Test Recipe')
        ->set('servings', 4)
        ->set('ingredients', [['name' => '', 'quantity' => '']])
        ->set('instructions', ['Do something'])
        ->call('save')
        ->assertHasErrors(['ingredients']);
});

test('requires at least one instruction', function () {
    Livewire::test(AddRecipe::class)
        ->set('name', 'Test Recipe')
        ->set('servings', 4)
        ->set('ingredients', [['name' => 'Salt', 'quantity' => '1 tsp']])
        ->set('instructions', [''])
        ->call('save')
        ->assertHasErrors(['instructions']);
});

test('can add and remove ingredients', function () {
    Livewire::test(AddRecipe::class)
        ->call('addIngredient')
        ->assertCount('ingredients', 2)
        ->call('removeIngredient', 1)
        ->assertCount('ingredients', 1);
});

test('can add and remove instructions', function () {
    Livewire::test(AddRecipe::class)
        ->call('addInstruction')
        ->assertCount('instructions', 2)
        ->call('removeInstruction', 1)
        ->assertCount('instructions', 1);
});

test('cannot remove last ingredient or instruction', function () {
    Livewire::test(AddRecipe::class)
        ->call('removeIngredient', 0)
        ->assertCount('ingredients', 1)
        ->call('removeInstruction', 0)
        ->assertCount('instructions', 1);
});

test('creates ingredient entries for type-ahead', function () {
    Livewire::test(AddRecipe::class)
        ->set('name', 'Test Recipe')
        ->set('servings', 4)
        ->set('ingredients', [
            ['name' => 'Unique Ingredient', 'quantity' => '1 cup'],
        ])
        ->set('instructions', ['Mix well'])
        ->call('save');

    $this->assertDatabaseHas('ingredients', [
        'name' => 'Unique Ingredient',
        'user_id' => $this->user->id,
    ]);
});

test('ingredient search returns matching ingredients', function () {
    Ingredient::create([
        'user_id' => $this->user->id,
        'name' => 'All Purpose Flour',
    ]);

    Ingredient::create([
        'user_id' => $this->user->id,
        'name' => 'Almond Flour',
    ]);

    $component = Livewire::test(AddRecipe::class);
    $results = $component->instance()->searchIngredients('flour');

    expect($results)->toContain('All Purpose Flour', 'Almond Flour');
});

test('ingredient search is case insensitive', function () {
    Ingredient::create([
        'user_id' => $this->user->id,
        'name' => 'Vanilla Extract',
    ]);

    $component = Livewire::test(AddRecipe::class);
    $results = $component->instance()->searchIngredients('VANILLA');

    expect($results)->toContain('Vanilla Extract');
});
