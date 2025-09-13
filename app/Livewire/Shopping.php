<?php

namespace App\Livewire;

use App\Models\MealPlan;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Shopping extends Component
{
    public $shoppingList = [];

    public function mount()
    {
        $this->loadShoppingList();
    }

    public function loadShoppingList()
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(6);

        $mealPlans = MealPlan::with(['recipe'])
            ->where('user_id', auth()->id())
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $ingredients = [];

        foreach ($mealPlans as $mealPlan) {
            if (! $mealPlan->recipe || ! $mealPlan->recipe->ingredients) {
                continue;
            }

            foreach ($mealPlan->recipe->ingredients as $ingredient) {
                // Handle both string and array ingredient formats
                $ingredientText = is_array($ingredient) ? implode(' ', $ingredient) : $ingredient;

                $ingredients[] = [
                    'ingredient' => trim($ingredientText),
                    'recipe_name' => $mealPlan->recipe->name,
                    'recipe_id' => $mealPlan->recipe->id,
                    'date' => $mealPlan->date,
                    'meal_type' => $mealPlan->meal_type,
                ];
            }
        }

        // Sort alphabetically by ingredient name
        usort($ingredients, function ($a, $b) {
            return strcasecmp($a['ingredient'], $b['ingredient']);
        });

        $this->shoppingList = $ingredients;
    }

    public function render()
    {
        return view('livewire.shopping')->title('Shopping List');
    }
}
