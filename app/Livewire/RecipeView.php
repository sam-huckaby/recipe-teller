<?php

namespace App\Livewire;

use App\Models\Recipe;
use App\Models\RecipeVersion;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class RecipeView extends Component
{
    public Recipe $recipe;

    public ?RecipeVersion $selectedVersion = null;

    public ?string $selectedVersionId = 'current';

    public function mount(Recipe $recipe, ?int $version = null)
    {
        if ($recipe->user_id !== auth()->id()) {
            abort(403);
        }

        $this->recipe = $recipe->load('categories');

        if ($version) {
            $this->selectedVersion = $recipe->versions()->where('id', $version)->first();
            if (! $this->selectedVersion) {
                abort(404, 'Version not found');
            }
            $this->selectedVersionId = (string) $this->selectedVersion->id;
        } else {
            $this->selectedVersion = null;
            $this->selectedVersionId = 'current';
        }
    }

    public function updatedSelectedVersionId($versionId)
    {
        if ($versionId === 'current') {
            $this->selectedVersion = null;
        } else {
            $this->selectedVersion = $this->recipe->versions()->where('id', $versionId)->first();
        }
    }

    public function getCurrentData()
    {
        return $this->selectedVersion ?? $this->recipe;
    }

    public function delete()
    {
        if ($this->recipe->user_id !== auth()->id()) {
            abort(403);
        }

        $this->recipe->delete();

        session()->flash('message', 'Recipe deleted successfully!');

        return redirect()->route('recipes');
    }

    public function render()
    {
        return view('livewire.recipe-view')->title($this->recipe->name);
    }
}
