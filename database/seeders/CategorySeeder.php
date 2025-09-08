<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Breakfast', 'color' => '#FFD700'],
            ['name' => 'Lunch', 'color' => '#FF6347'],
            ['name' => 'Dinner', 'color' => '#4169E1'],
            ['name' => 'Snacks', 'color' => '#32CD32'],
            ['name' => 'Desserts', 'color' => '#FF69B4'],
            ['name' => 'Vegetarian', 'color' => '#177245'],
            ['name' => 'Quick & Easy', 'color' => '#FFA500'],
            ['name' => 'Healthy', 'color' => '#90EE90'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
