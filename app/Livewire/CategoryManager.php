<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CategoryManager extends Component
{
    public $categories = [];

    public $showCreateModal = false;

    public $showEditModal = false;

    public $showDeleteModal = false;

    #[Validate('required|min:2|max:255')]
    public $name = '';

    #[Validate('required|regex:/^#[0-9A-Fa-f]{6}$/')]
    public $color = '#177245';

    public $editingCategory = null;

    public $deletingCategory = null;

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::withCount('recipes')
            ->orderBy('name')
            ->get();
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'color']);
        $this->color = '#177245';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['name', 'color']);
        $this->resetErrorBag();
    }

    public function openEditModal($categoryId)
    {
        $this->editingCategory = Category::findOrFail($categoryId);
        $this->name = $this->editingCategory->name;
        $this->color = $this->editingCategory->color;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['name', 'color', 'editingCategory']);
        $this->resetErrorBag();
    }

    public function openDeleteModal($categoryId)
    {
        $this->deletingCategory = Category::withCount('recipes')->findOrFail($categoryId);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingCategory = null;
    }

    public function createCategory()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'color' => $this->color,
        ]);

        $this->loadCategories();
        $this->closeCreateModal();

        session()->flash('message', 'Category created successfully!');
    }

    public function updateCategory()
    {
        $this->validate();

        $this->editingCategory->update([
            'name' => $this->name,
            'color' => $this->color,
        ]);

        $this->loadCategories();
        $this->closeEditModal();

        session()->flash('message', 'Category updated successfully!');
    }

    public function deleteCategory()
    {
        $this->deletingCategory->delete();

        $this->loadCategories();
        $this->closeDeleteModal();

        session()->flash('message', 'Category deleted successfully!');
    }

    public function viewCategoryRecipes($categoryId)
    {
        return $this->redirect(route('recipes', ['category' => $categoryId]), navigate: true);
    }

    public function render()
    {
        return view('livewire.category-manager')
            ->title('Categories');
    }
}
