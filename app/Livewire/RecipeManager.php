<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Recipe;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class RecipeManager extends Component
{
    use WithPagination;

    public $search = '';

    #[Url(as: 'category')]
    public $selectedCategories = [];

    public $categorySearch = '';

    public function mount()
    {
        if (request()->has('category')) {
            $categoryId = (int) request()->get('category');
            if (Category::find($categoryId)) {
                $this->selectedCategories = [$categoryId];
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategories()
    {
        $this->resetPage();
    }

    public function toggleCategory($categoryId)
    {
        if (in_array($categoryId, $this->selectedCategories)) {
            $this->selectedCategories = array_values(array_filter(
                $this->selectedCategories,
                fn ($id) => $id != $categoryId
            ));
        } else {
            $this->selectedCategories[] = $categoryId;
        }
        $this->resetPage();
    }

    public function clearCategories()
    {
        $this->selectedCategories = [];
        $this->resetPage();
    }

    public function clearCategorySearch()
    {
        $this->categorySearch = '';
    }

    public function render()
    {
        $recipes = Recipe::where('user_id', auth()->id())
            ->with('categories')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->when(! empty($this->selectedCategories), function ($query) {
                $query->whereHas('categories', function ($q) {
                    $q->whereIn('categories.id', $this->selectedCategories);
                });
            })
            ->latest()
            ->paginate(12);

        $allCategories = Category::orderBy('name')->get();

        if (! empty($this->categorySearch)) {
            $filteredCategories = $allCategories->filter(function ($category) {
                return str_contains(strtolower($category->name), strtolower($this->categorySearch));
            });
        } else {
            $filteredCategories = $allCategories;
        }

        $selectedCategoryModels = $allCategories->whereIn('id', $this->selectedCategories);

        return view('livewire.recipe-manager', [
            'recipes' => $recipes,
            'allCategories' => $allCategories,
            'filteredCategories' => $filteredCategories,
            'selectedCategoryModels' => $selectedCategoryModels,
        ])->title('My Recipes');
    }
}
