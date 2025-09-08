<?php

namespace App\Livewire;

use App\Models\Recipe;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class RecipeManager extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $recipes = Recipe::where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.recipe-manager', [
            'recipes' => $recipes,
        ])->title('My Recipes');
    }
}
