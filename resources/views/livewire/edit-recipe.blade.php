<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Edit Recipe</h1>
            <p class="text-zinc-600 dark:text-zinc-400">Update your recipe details</p>
        </div>
        <flux:button variant="ghost" wire:navigate href="{{ route('recipes.view', $recipe) }}">
            <flux:icon.arrow-left class="size-4" />
            Back to Recipe
        </flux:button>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex">
                <flux:icon.check-circle class="size-5 text-green-400" />
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ session('message') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit="save" class="space-y-8">
        <!-- Basic Information Section -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Basic Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recipe Name -->
                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label>Recipe Name *</flux:label>
                        <flux:input wire:model="name" placeholder="Enter recipe name" />
                        <flux:error name="name" />
                    </flux:field>
                </div>

                <!-- Recipe URL -->
                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label>Recipe URL (Optional)</flux:label>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <flux:input wire:model="url" type="url" placeholder="https://example.com/recipe" />
                            </div>
                            <flux:button
                                type="button"
                                class="cursor-pointer"
                                variant="outline"
                                wire:click="retrieveRecipe"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove wire:target="retrieveRecipe">
                                    🌐 Retrieve
                                </span>
                                <span wire:loading wire:target="retrieveRecipe">
                                    ⏳ Retrieving...
                                </span>
                            </flux:button>
                        </div>
                        <flux:error name="url" />
                        <flux:description>Link to the original recipe online. Click "Retrieve" to automatically fill the form.</flux:description>
                    </flux:field>
                </div>

                <!-- Prep Time -->
                <div>
                    <flux:field>
                        <flux:label>Prep Time (minutes)</flux:label>
                        <flux:input wire:model="prep_time" type="number" min="0" placeholder="15" />
                        <flux:error name="prep_time" />
                    </flux:field>
                </div>

                <!-- Cook Time -->
                <div>
                    <flux:field>
                        <flux:label>Cook Time (minutes)</flux:label>
                        <flux:input wire:model="cook_time" type="number" min="0" placeholder="30" />
                        <flux:error name="cook_time" />
                    </flux:field>
                </div>

                <!-- Servings -->
                <div>
                    <flux:field>
                        <flux:label>Servings *</flux:label>
                        <flux:input wire:model="servings" type="number" min="1" placeholder="4" />
                        <flux:error name="servings" />
                    </flux:field>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label>Description (Optional)</flux:label>
                        <flux:textarea wire:model="description" placeholder="Brief description of the recipe" rows="3" />
                        <flux:error name="description" />
                    </flux:field>
                </div>

                <!-- Categories -->
                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label>Categories (Optional)</flux:label>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mt-2">
                            @foreach($availableCategories as $category)
                                <label class="flex items-center space-x-2 p-2 rounded-md border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        wire:model="selectedCategories"
                                        value="{{ $category->id }}"
                                        class="rounded border-zinc-300 text-green-600 focus:border-green-500 focus:ring-green-500 dark:border-zinc-600 dark:bg-zinc-700"
                                    />
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <flux:error name="selectedCategories" />
                        <flux:description>Select one or more categories that describe this recipe</flux:description>
                    </flux:field>
                </div>
            </div>
        </div>

        <!-- Ingredients Section -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Ingredients</h2>
                <flux:button icon="plus" type="button" variant="ghost" size="sm" wire:click="addIngredient">
                    Add Ingredient
                </flux:button>
            </div>

            <div class="space-y-3">
                @foreach($ingredients as $index => $ingredient)
                    <div class="flex items-start gap-3">
                        <div class="flex-1">
                            <flux:input
                                wire:model="ingredients.{{ $index }}.name"
                                placeholder="Ingredient name"
                                list="ingredients-{{ $index }}"
                            />
                            <datalist id="ingredients-{{ $index }}">
                                {{-- Ingredient suggestions will be populated via JavaScript --}}
                            </datalist>
                        </div>
                        <div class="w-32">
                            <flux:input
                                wire:model="ingredients.{{ $index }}.quantity"
                                placeholder="Quantity"
                            />
                        </div>
                        @if(count($ingredients) > 1)
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                class="text-red-600 hover:text-red-700"
                                wire:click="removeIngredient({{ $index }})"
                            >
                                <flux:icon.trash class="size-4" />
                            </flux:button>
                        @endif
                    </div>
                @endforeach
            </div>

            @error('ingredients')
                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <!-- Instructions Section -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Instructions</h2>
                <flux:button icon="plus" type="button" variant="ghost" size="sm" wire:click="addInstruction">
                    Add Step
                </flux:button>
            </div>

            <div class="space-y-3">
                @foreach($instructions as $index => $instruction)
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center text-sm font-medium text-zinc-600 dark:text-zinc-300 mt-1">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <flux:textarea
                                wire:model="instructions.{{ $index }}"
                                placeholder="Describe this step..."
                                rows="2"
                            />
                        </div>
                        @if(count($instructions) > 1)
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                class="text-red-600 hover:text-red-700 mt-1"
                                wire:click="removeInstruction({{ $index }})"
                            >
                                <flux:icon.trash class="size-4" />
                            </flux:button>
                        @endif
                    </div>
                @endforeach
            </div>

            @error('instructions')
                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-700">
            <flux:button variant="ghost" wire:navigate href="{{ route('recipes.view', $recipe) }}">
                Cancel
            </flux:button>
            <flux:button icon="check" type="submit" variant="primary">
                Update Recipe
            </flux:button>
        </div>
    </form>
</div>
