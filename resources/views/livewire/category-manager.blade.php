<div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Categories</h1>
                <p class="text-zinc-600 dark:text-zinc-400 mt-1">Organize your recipes with custom categories</p>
            </div>
            <flux:button icon="plus" wire:click="openCreateModal" variant="primary">
                Add Category
            </flux:button>
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div class="bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800 rounded-lg p-4">
                <div class="text-green-800 dark:text-green-200">{{ session('message') }}</div>
            </div>
        @endif

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($categories as $category)
                <div class="bg-white dark:bg-zinc-700 rounded-lg border border-zinc-200 dark:border-zinc-500 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-4 h-4 rounded-full border border-zinc-300 dark:border-zinc-600"
                                style="background-color: {{ $category->color }}"
                            ></div>
                            <div>
                                <h3 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $category->name }}</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $category->recipes_count }} {{ Str::plural('recipe', $category->recipes_count) }}
                                </p>
                            </div>
                        </div>
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                            <flux:menu class="w-32">
                                <flux:menu.item wire:click="openEditModal({{ $category->id }})" icon="pencil">
                                    Edit
                                </flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item
                                    wire:click="openDeleteModal({{ $category->id }})"
                                    icon="trash"
                                    variant="danger"
                                >
                                    Delete
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white dark:bg-zinc-700 rounded-lg border border-zinc-200 dark:border-zinc-500 text-center py-12">
                        <flux:icon.folder-git-2 class="size-12 text-zinc-400 dark:text-zinc-500 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No categories yet</h3>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-4">Create your first category to organize your recipes</p>
                        <flux:button wire:click="openCreateModal" variant="primary">
                            Add Category
                        </flux:button>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Create Category Modal -->
        <flux:modal wire:model="showCreateModal" class="max-w-md">
            <form wire:submit="createCategory" class="space-y-6">
                <flux:heading size="lg">Create Category</flux:heading>

                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model="name" placeholder="Category name" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Color</flux:label>
                    <div class="flex items-center space-x-3">
                        <input
                            type="color"
                            wire:model="color"
                            class="w-12 h-10 rounded border border-zinc-300 dark:border-zinc-600"
                        />
                        <flux:input wire:model="color" class="flex-1" />
                    </div>
                    <flux:error name="color" />
                </flux:field>

                <div class="flex justify-end space-x-3">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Create Category</flux:button>
                </div>
            </form>
        </flux:modal>

        <!-- Edit Category Modal -->
        <flux:modal wire:model="showEditModal" class="max-w-md">
            <form wire:submit="updateCategory" class="space-y-6">
                <flux:heading size="lg">Edit Category</flux:heading>

                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model="name" placeholder="Category name" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Color</flux:label>
                    <div class="flex items-center space-x-3">
                        <input
                            type="color"
                            wire:model="color"
                            class="w-12 h-10 rounded border border-zinc-300 dark:border-zinc-600"
                        />
                        <flux:input wire:model="color" class="flex-1" />
                    </div>
                    <flux:error name="color" />
                </flux:field>

                <div class="flex justify-end space-x-3">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Update Category</flux:button>
                </div>
            </form>
        </flux:modal>

        <!-- Delete Category Modal -->
        <flux:modal wire:model="showDeleteModal" class="max-w-md">
            @if($deletingCategory)
                <div class="space-y-6">
                    <flux:heading size="lg">Delete Category</flux:heading>

                    <div class="space-y-4">
                        <p class="text-zinc-600 dark:text-zinc-400">
                            Are you sure you want to delete the category "{{ $deletingCategory->name }}"?
                        </p>

                        @if($deletingCategory->recipes_count > 0)
                            <div class="bg-amber-50 border border-amber-200 dark:bg-amber-900/20 dark:border-amber-800 rounded-lg p-4">
                                <div class="flex items-start space-x-3">
                                    <flux:icon.exclamation-triangle class="size-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" />
                                    <div class="text-amber-800 dark:text-amber-200">
                                        <p class="font-medium">This category has {{ $deletingCategory->recipes_count }} {{ Str::plural('recipe', $deletingCategory->recipes_count) }}.</p>
                                        <p class="text-sm mt-1">The recipes will not be deleted, but they will no longer be associated with this category.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end space-x-3">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button wire:click="deleteCategory" variant="danger">
                            Delete Category
                        </flux:button>
                    </div>
                </div>
            @endif
        </flux:modal>
    </div>
