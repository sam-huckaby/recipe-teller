<?php

use App\Livewire\EditRecipe;
use App\Models\Recipe;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Create a test recipe
    $this->recipe = Recipe::create([
        'user_id' => $this->user->id,
        'name' => 'Original Recipe',
        'url' => 'https://example.com/original',
        'description' => 'Original description',
        'prep_time' => 10,
        'cook_time' => 20,
        'servings' => 2,
        'image_url' => 'https://example.com/original-image.jpg',
        'ingredients' => [
            ['name' => 'Original Ingredient', 'quantity' => '1 cup'],
        ],
        'instructions' => [
            'Original instruction',
        ],
    ]);
});

test('edit recipe page can be rendered', function () {
    $response = $this->get('/recipes/'.$this->recipe->id.'/edit');

    $response->assertStatus(200);
    $response->assertSee('Edit Recipe');
    $response->assertSee($this->recipe->name);
});

test('can update recipe with all fields including image url', function () {
    Livewire::test(EditRecipe::class, ['recipe' => $this->recipe])
        ->set('name', 'Updated Recipe')
        ->set('url', 'https://example.com/updated')
        ->set('description', 'Updated description')
        ->set('prep_time', 15)
        ->set('cook_time', 25)
        ->set('servings', 4)
        ->set('image_url', 'https://example.com/updated-image.jpg')
        ->set('ingredients', [
            ['name' => 'Updated Ingredient', 'quantity' => '2 cups'],
        ])
        ->set('instructions', [
            'Updated instruction',
        ])
        ->call('save')
        ->assertRedirect('/recipes/'.$this->recipe->id);

    $this->recipe->refresh();

    expect($this->recipe->name)->toBe('Updated Recipe');
    expect($this->recipe->url)->toBe('https://example.com/updated');
    expect($this->recipe->description)->toBe('Updated description');
    expect($this->recipe->prep_time)->toBe(15);
    expect($this->recipe->cook_time)->toBe(25);
    expect($this->recipe->servings)->toBe(4);
    expect($this->recipe->image_url)->toBe('https://example.com/updated-image.jpg');
    expect($this->recipe->ingredients)->toEqual([
        ['name' => 'Updated Ingredient', 'quantity' => '2 cups'],
    ]);
    expect($this->recipe->instructions)->toEqual([
        'Updated instruction',
    ]);
});

test('can clear image url when editing', function () {
    Livewire::test(EditRecipe::class, ['recipe' => $this->recipe])
        ->set('image_url', '')
        ->call('save')
        ->assertRedirect('/recipes/'.$this->recipe->id);

    $this->recipe->refresh();
    expect($this->recipe->image_url)->toBeNull();
});

test('validates image url format in edit', function () {
    Livewire::test(EditRecipe::class, ['recipe' => $this->recipe])
        ->set('image_url', 'not-a-valid-url')
        ->call('save')
        ->assertHasErrors(['image_url']);
});

test('recipe form is populated with existing data including image url', function () {
    $component = Livewire::test(EditRecipe::class, ['recipe' => $this->recipe]);

    expect($component->get('name'))->toBe('Original Recipe');
    expect($component->get('url'))->toBe('https://example.com/original');
    expect($component->get('description'))->toBe('Original description');
    expect($component->get('prep_time'))->toBe(10);
    expect($component->get('cook_time'))->toBe(20);
    expect($component->get('servings'))->toBe(2);
    expect($component->get('image_url'))->toBe('https://example.com/original-image.jpg');
    expect($component->get('ingredients'))->toEqual([
        ['name' => 'Original Ingredient', 'quantity' => '1 cup'],
    ]);
    expect($component->get('instructions'))->toEqual([
        'Original instruction',
    ]);
});

// Note: Authorization is handled at the route level in web.php

test('creates version when recipe is updated', function () {
    expect($this->recipe->versions()->count())->toBe(0);

    Livewire::test(EditRecipe::class, ['recipe' => $this->recipe])
        ->set('name', 'Updated Recipe')
        ->call('save');

    $this->recipe->refresh();
    expect($this->recipe->versions()->count())->toBe(1);

    $version = $this->recipe->versions()->first();
    expect($version->name)->toBe('Original Recipe');
    expect($version->image_url)->toBe('https://example.com/original-image.jpg');
});

test('retrieve recipe overwrites existing image url', function () {
    // Mock the services to simulate retrieving a recipe with a new image URL
    $mockRecipe = [
        'name' => 'Retrieved Recipe',
        'description' => 'Retrieved description',
        'prep_time' => 20,
        'cook_time' => 40,
        'servings' => 6,
        'image_url' => 'https://example.com/new-retrieved-image.jpg',
        'ingredients' => [
            ['name' => 'New Ingredient', 'quantity' => '3 cups'],
        ],
        'instructions' => [
            'New instruction',
        ],
    ];

    // Test that image URL gets updated even when recipe already has one
    $component = Livewire::test(EditRecipe::class, ['recipe' => $this->recipe]);

    // Verify the original image URL is loaded
    expect($component->get('image_url'))->toBe('https://example.com/original-image.jpg');

    // Use reflection to access the private method
    $reflection = new ReflectionClass($component->instance());
    $method = $reflection->getMethod('populateFormWithRecipe');
    $method->setAccessible(true);
    $method->invoke($component->instance(), $mockRecipe);

    // The image URL should be updated to the new one
    expect($component->get('image_url'))->toBe('https://example.com/new-retrieved-image.jpg');
});
