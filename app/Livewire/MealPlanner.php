<?php

namespace App\Livewire;

use App\Models\MealPlan;
use App\Models\Recipe;
use Livewire\Component;

class MealPlanner extends Component
{
    public $selectedWeek;

    public $mealPlans = [];

    public $showMealModal = false;

    public $selectedDate;

    public $selectedMealType;

    public $selectedMealPlan;

    public $recipeSearch = '';

    public $availableRecipes = [];

    public function mount()
    {
        $this->selectedWeek = now()->startOfWeek();
        $this->loadMealPlans();
        $this->loadAvailableRecipes();
    }

    public function loadMealPlans()
    {
        $startDate = $this->selectedWeek;
        $endDate = $this->selectedWeek->copy()->endOfWeek();

        $mealPlans = MealPlan::with('recipe')
            ->where('user_id', auth()->id())
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($mealPlan) {
                return $mealPlan->date->toDateString();
            })
            ->map(function ($dayPlans) {
                return $dayPlans->groupBy('meal_type');
            });

        $this->mealPlans = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateString = $date->toDateString();

            $this->mealPlans[$dateString] = [
                'date' => $date,
                'breakfast' => $mealPlans->get($dateString, collect())->get('breakfast', collect()),
                'lunch' => $mealPlans->get($dateString, collect())->get('lunch', collect()),
                'dinner' => $mealPlans->get($dateString, collect())->get('dinner', collect()),
            ];
        }
    }

    public function previousWeek()
    {
        $this->selectedWeek = $this->selectedWeek->subWeek();
        $this->loadMealPlans();
    }

    public function nextWeek()
    {
        $this->selectedWeek = $this->selectedWeek->addWeek();
        $this->loadMealPlans();
    }

    public function currentWeek()
    {
        $this->selectedWeek = now()->startOfWeek();
        $this->loadMealPlans();
    }

    public function loadAvailableRecipes()
    {
        $this->availableRecipes = Recipe::where('user_id', auth()->id())
            ->when($this->recipeSearch, function ($query) {
                $query->where('name', 'like', '%'.$this->recipeSearch.'%')
                    ->orWhere('description', 'like', '%'.$this->recipeSearch.'%');
            })
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    public function updatedRecipeSearch()
    {
        $this->loadAvailableRecipes();
    }

    public function openMealModal($date, $mealType, $existingMealPlanId = null)
    {
        $this->selectedDate = $date;
        $this->selectedMealType = $mealType;
        $this->selectedMealPlan = $existingMealPlanId ? MealPlan::find($existingMealPlanId) : null;
        $this->recipeSearch = '';
        $this->loadAvailableRecipes();
        $this->showMealModal = true;
    }

    public function closeMealModal()
    {
        $this->showMealModal = false;
        $this->selectedDate = null;
        $this->selectedMealType = null;
        $this->selectedMealPlan = null;
        $this->recipeSearch = '';
    }

    public function assignRecipe($recipeId)
    {
        if ($this->selectedMealPlan) {
            $this->selectedMealPlan->update([
                'recipe_id' => $recipeId,
            ]);
        } else {
            MealPlan::create([
                'user_id' => auth()->id(),
                'date' => $this->selectedDate,
                'meal_type' => $this->selectedMealType,
                'recipe_id' => $recipeId,
            ]);
        }

        $this->loadMealPlans();
        $this->closeMealModal();
    }

    public function removeMeal()
    {
        if ($this->selectedMealPlan) {
            $this->selectedMealPlan->delete();
            $this->loadMealPlans();
            $this->closeMealModal();
        }
    }

    public function render()
    {
        return view('livewire.meal-planner');
    }
}
