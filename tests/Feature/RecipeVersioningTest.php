<?php

use App\Models\Recipe;
use App\Models\User;

test('recipe does not create version when initially created', function () {
    $user = User::factory()->create();

    $recipe = Recipe::create([
        'user_id' => $user->id,
        'name' => 'Test Recipe',
        'description' => 'A test recipe',
        'ingredients' => [['name' => 'Test Ingredient', 'quantity' => '1 cup']],
        'instructions' => ['Test instruction'],
        'servings' => 4,
    ]);

    expect($recipe->versions)->toHaveCount(0);
    expect($recipe->name)->toBe('Test Recipe');
});

test('recipe creates version with original data when updated', function () {
    $user = User::factory()->create();

    $recipe = Recipe::create([
        'user_id' => $user->id,
        'name' => 'Original Recipe',
        'description' => 'Original description',
        'ingredients' => [['name' => 'Original Ingredient', 'quantity' => '1 cup']],
        'instructions' => ['Original instruction'],
        'servings' => 4,
    ]);

    $recipe->createVersionFromCurrentState();

    $recipe->update([
        'name' => 'Updated Recipe',
        'description' => 'Updated description',
    ]);

    expect($recipe->versions)->toHaveCount(1);
    expect($recipe->latestVersion()->name)->toBe('Original Recipe');
    expect($recipe->latestVersion()->description)->toBe('Original description');
    expect($recipe->name)->toBe('Updated Recipe');
    expect($recipe->description)->toBe('Updated description');
});

test('recipe preserves all versions when updated multiple times', function () {
    $user = User::factory()->create();

    $recipe = Recipe::create([
        'user_id' => $user->id,
        'name' => 'Version 1',
        'description' => 'First version',
        'ingredients' => [['name' => 'Ingredient 1', 'quantity' => '1 cup']],
        'instructions' => ['Instruction 1'],
        'servings' => 4,
    ]);

    // First update: save current state, then update
    $recipe->createVersionFromCurrentState();
    $recipe->update(['name' => 'Version 2', 'description' => 'Second version']);

    // Second update: save current state, then update
    $recipe->createVersionFromCurrentState();
    $recipe->update(['name' => 'Version 3', 'description' => 'Third version']);

    $versions = $recipe->versions()->orderBy('version_number')->get();

    expect($recipe->versions)->toHaveCount(2);

    // Version 1 should contain "Version 1" data, Version 2 should contain "Version 2" data
    $version1 = $versions->where('version_number', 1)->first();
    $version2 = $versions->where('version_number', 2)->first();

    expect($version1->name)->toBe('Version 1');
    expect($version1->description)->toBe('First version');

    expect($version2->name)->toBe('Version 2');
    expect($version2->description)->toBe('Second version');

    // Current recipe should have the latest data
    expect($recipe->name)->toBe('Version 3');
    expect($recipe->description)->toBe('Third version');
});
