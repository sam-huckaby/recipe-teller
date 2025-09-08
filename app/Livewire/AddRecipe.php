<?php

namespace App\Livewire;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Services\OpenAIService;
use App\Services\WebpageService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AddRecipe extends Component
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('nullable|url')]
    public $url = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('nullable|integer|min:0')]
    public $prep_time = null;

    #[Validate('nullable|integer|min:0')]
    public $cook_time = null;

    #[Validate('required|integer|min:1')]
    public $servings = null;

    public $ingredients = [];

    public $instructions = [];

    public function mount()
    {
        $this->servings = 1;
        $this->ingredients = [['name' => '', 'quantity' => '']];
        $this->instructions = [''];
    }

    public function addIngredient()
    {
        $this->ingredients[] = ['name' => '', 'quantity' => ''];
    }

    public function removeIngredient($index)
    {
        if (count($this->ingredients) > 1) {
            unset($this->ingredients[$index]);
            $this->ingredients = array_values($this->ingredients);
        }
    }

    public function addInstruction()
    {
        $this->instructions[] = '';
    }

    public function removeInstruction($index)
    {
        if (count($this->instructions) > 1) {
            unset($this->instructions[$index]);
            $this->instructions = array_values($this->instructions);
        }
    }

    public function searchIngredients($query)
    {
        if (strlen($query) < 2) {
            return [];
        }

        return Ingredient::where('user_id', auth()->id())
            ->where('name', 'like', '%'.$query.'%')
            ->distinct()
            ->pluck('name')
            ->take(10)
            ->toArray();
    }

    public function getCannotRetrieveProperty()
    {
        return empty($this->url) || ! filter_var($this->url, FILTER_VALIDATE_URL);
    }

    public function retrieveRecipe()
    {
        // Clear any previous errors
        $this->resetErrorBag();

        if (empty($this->url)) {
            $this->addError('url', 'Please enter a URL first.');

            return;
        }

        // Validate URL format
        if (! filter_var($this->url, FILTER_VALIDATE_URL)) {
            $this->addError('url', 'Please enter a valid URL.');

            return;
        }

        try {
            // Fetch webpage content
            $webpageService = new WebpageService;
            $webpageResult = $webpageService->fetchContent($this->url);

            if (! $webpageResult['success']) {
                $this->addError('url', $webpageResult['error']);

                return;
            }

            // Extract recipe using OpenAI
            $openAIService = new OpenAIService;
            $recipeResult = $openAIService->extractRecipeFromContent(
                $webpageResult['content'],
                $this->url
            );

            if (! $recipeResult['success']) {
                $this->addError('url', $recipeResult['error']);

                return;
            }

            $recipe = $recipeResult['recipe'];

            // Handle case where no recipe was found
            if (isset($recipe['error'])) {
                $this->addError('url', $recipe['error']);

                return;
            }

            // Populate form fields with extracted data
            $this->populateFormWithRecipe($recipe);

            // Show success message
            session()->flash('message', 'Recipe retrieved successfully! Please review and adjust the details as needed.');

        } catch (\Exception $e) {
            $this->addError('url', 'An unexpected error occurred while retrieving the recipe. Please try again.');
        }
    }

    private function populateFormWithRecipe(array $recipe)
    {
        // Only populate if the field is empty to avoid overwriting user input
        if (empty($this->name) && ! empty($recipe['name'])) {
            $this->name = $recipe['name'];
        }

        if (empty($this->description) && ! empty($recipe['description'])) {
            $this->description = $recipe['description'];
        }

        if (is_null($this->prep_time) && ! is_null($recipe['prep_time'])) {
            $this->prep_time = $recipe['prep_time'];
        }

        if (is_null($this->cook_time) && ! is_null($recipe['cook_time'])) {
            $this->cook_time = $recipe['cook_time'];
        }

        if (is_null($this->servings) && ! is_null($recipe['servings'])) {
            $this->servings = $recipe['servings'];
        }

        // Replace ingredients if we have better data
        if (! empty($recipe['ingredients'])) {
            $this->ingredients = $recipe['ingredients'];
        }

        // Replace instructions if we have better data
        if (! empty($recipe['instructions'])) {
            $this->instructions = $recipe['instructions'];
        }
    }

    public function save()
    {
        $this->validate();

        $filteredIngredients = array_filter($this->ingredients, function ($ingredient) {
            return ! empty(trim($ingredient['name'])) && ! empty(trim($ingredient['quantity']));
        });

        $filteredInstructions = array_filter($this->instructions, function ($instruction) {
            return ! empty(trim($instruction));
        });

        if (empty($filteredIngredients)) {
            $this->addError('ingredients', 'At least one ingredient is required.');

            return;
        }

        if (empty($filteredInstructions)) {
            $this->addError('instructions', 'At least one instruction is required.');

            return;
        }

        $recipe = Recipe::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'url' => $this->url ?: null,
            'description' => $this->description ?: null,
            'prep_time' => $this->prep_time,
            'cook_time' => $this->cook_time,
            'servings' => $this->servings,
            'ingredients' => array_values($filteredIngredients),
            'instructions' => array_values($filteredInstructions),
        ]);

        foreach ($filteredIngredients as $ingredient) {
            Ingredient::firstOrCreate([
                'user_id' => auth()->id(),
                'name' => trim($ingredient['name']),
            ]);
        }

        session()->flash('message', 'Recipe created successfully!');

        return redirect()->route('recipes');
    }

    public function render()
    {
        return view('livewire.add-recipe')->title('Add Recipe');
    }
}
