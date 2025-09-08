<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();

        if (! $user) {
            return;
        }

        $recipes = [
            [
                'user_id' => $user->id,
                'name' => 'Avocado Toast',
                'description' => 'Simple and healthy breakfast with creamy avocado on toasted bread',
                'ingredients' => [
                    '2 slices whole grain bread',
                    '1 ripe avocado',
                    '1 tbsp lemon juice',
                    'Salt and pepper to taste',
                    'Optional: cherry tomatoes, red pepper flakes',
                ],
                'instructions' => [
                    'Toast the bread slices until golden brown',
                    'Mash the avocado with lemon juice, salt, and pepper',
                    'Spread avocado mixture on toast',
                    'Top with cherry tomatoes and red pepper flakes if desired',
                ],
                'prep_time' => 5,
                'cook_time' => 3,
                'servings' => 2,
                'tags' => ['healthy', 'vegetarian', 'quick'],
            ],
            [
                'user_id' => $user->id,
                'name' => 'Chicken Stir Fry',
                'description' => 'Quick and colorful chicken stir fry with vegetables',
                'ingredients' => [
                    '1 lb chicken breast, sliced',
                    '2 cups mixed vegetables',
                    '2 tbsp soy sauce',
                    '1 tbsp olive oil',
                    '2 cloves garlic, minced',
                    '1 tsp ginger, grated',
                ],
                'instructions' => [
                    'Heat oil in a large pan or wok',
                    'Add chicken and cook until golden',
                    'Add garlic and ginger, cook for 1 minute',
                    'Add vegetables and stir fry for 3-4 minutes',
                    'Add soy sauce and toss to combine',
                ],
                'prep_time' => 10,
                'cook_time' => 15,
                'servings' => 4,
                'tags' => ['protein', 'quick', 'healthy'],
            ],
            [
                'user_id' => $user->id,
                'name' => 'Spaghetti Carbonara',
                'description' => 'Classic Italian pasta dish with eggs, cheese, and pancetta',
                'ingredients' => [
                    '400g spaghetti',
                    '200g pancetta, diced',
                    '4 large eggs',
                    '100g Parmesan cheese, grated',
                    '2 cloves garlic, minced',
                    'Black pepper to taste',
                ],
                'instructions' => [
                    'Cook spaghetti according to package directions',
                    'Cook pancetta until crispy',
                    'Whisk eggs with Parmesan and black pepper',
                    'Drain pasta, reserving 1 cup pasta water',
                    'Toss hot pasta with pancetta and egg mixture',
                    'Add pasta water as needed for creaminess',
                ],
                'prep_time' => 10,
                'cook_time' => 20,
                'servings' => 4,
                'tags' => ['pasta', 'italian', 'comfort food'],
            ],
            [
                'user_id' => $user->id,
                'name' => 'Greek Salad',
                'description' => 'Fresh Mediterranean salad with feta cheese and olives',
                'ingredients' => [
                    '2 large tomatoes, chopped',
                    '1 cucumber, sliced',
                    '1 red onion, thinly sliced',
                    '200g feta cheese, cubed',
                    '1/2 cup Kalamata olives',
                    '3 tbsp olive oil',
                    '1 tbsp red wine vinegar',
                    'Oregano, salt, and pepper',
                ],
                'instructions' => [
                    'Combine tomatoes, cucumber, and onion in a large bowl',
                    'Add feta cheese and olives',
                    'Whisk together olive oil, vinegar, oregano, salt, and pepper',
                    'Pour dressing over salad and toss gently',
                ],
                'prep_time' => 15,
                'cook_time' => 0,
                'servings' => 4,
                'tags' => ['salad', 'vegetarian', 'mediterranean', 'healthy'],
            ],
            [
                'user_id' => $user->id,
                'name' => 'Chocolate Chip Cookies',
                'description' => 'Classic homemade chocolate chip cookies',
                'ingredients' => [
                    '2 1/4 cups all-purpose flour',
                    '1 tsp baking soda',
                    '1 cup butter, softened',
                    '3/4 cup brown sugar',
                    '1/2 cup white sugar',
                    '2 large eggs',
                    '2 tsp vanilla extract',
                    '2 cups chocolate chips',
                ],
                'instructions' => [
                    'Preheat oven to 375°F',
                    'Mix flour and baking soda in a bowl',
                    'Cream butter and sugars until fluffy',
                    'Beat in eggs and vanilla',
                    'Gradually add flour mixture',
                    'Stir in chocolate chips',
                    'Drop spoonfuls on baking sheet',
                    'Bake 9-11 minutes until golden',
                ],
                'prep_time' => 15,
                'cook_time' => 11,
                'servings' => 24,
                'tags' => ['dessert', 'baking', 'sweet'],
            ],
        ];

        foreach ($recipes as $recipeData) {
            $recipe = \App\Models\Recipe::create($recipeData);

            // Attach categories based on tags
            $categories = \App\Models\Category::whereIn('name', [
                'Breakfast', 'Lunch', 'Dinner', 'Snacks', 'Desserts',
                'Vegetarian', 'Quick & Easy', 'Healthy',
            ])->get();

            if (in_array('healthy', $recipeData['tags'])) {
                $recipe->categories()->attach($categories->where('name', 'Healthy')->first());
            }
            if (in_array('vegetarian', $recipeData['tags'])) {
                $recipe->categories()->attach($categories->where('name', 'Vegetarian')->first());
            }
            if (in_array('quick', $recipeData['tags'])) {
                $recipe->categories()->attach($categories->where('name', 'Quick & Easy')->first());
            }
            if (in_array('dessert', $recipeData['tags'])) {
                $recipe->categories()->attach($categories->where('name', 'Desserts')->first());
            }
        }
    }
}
