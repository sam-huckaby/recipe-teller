<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="dark:text-zinc-300 text-2xl font-bold text-zinc-900">My Recipes</h1>
            <p class="dark:text-zinc-400 text-zinc-600">Manage your personal recipe collection</p>
        </div>
        <flux:button variant="primary" wire:navigate href="{{ route('recipes.add') }}">
            <flux:icon.plus class="size-4" />
            Add Recipe
        </flux:button>
    </div>

    <!-- Search and Filters -->
    <div class="flex flex-col sm:flex-row gap-4 max-w-2xl">
        <!-- Text Search -->
        <div class="flex-1 max-w-md">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search recipes..."
                type="search"
            />
        </div>

        <!-- Category Filter -->
        <div class="relative" x-data="{ open: false, selectCategory(categoryId) { $wire.toggleCategory(categoryId); } }">
            <!-- Category Filter Button -->
            <flux:button
                variant="outline"
                @click="open = !open"
                icon="tag"
                icon:trailing="chevrons-up-down"
                class="flex items-center gap-2 min-w-fit"
            >
                Categories
                @if(count($selectedCategories) > 0)
                    <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">
                        {{ count($selectedCategories) }}
                    </span>
                @endif
            </flux:button>

            <!-- Category Dropdown -->
            <div
                x-show="open"
                x-transition
                @click.away="open = false"
                class="absolute top-full mt-2 w-72 bg-white dark:bg-zinc-600 rounded-lg border border-zinc-200 shadow-lg z-50"
            >
                <div class="p-3">
                    <!-- Search within categories -->
                    <flux:input
                        wire:model.live.debounce.200ms="categorySearch"
                        placeholder="Search categories..."
                        class="mb-3 border-zinc-400"
                    />

                    <!-- Selected Categories -->
                    @if(count($selectedCategories) > 0)
                        <div class="mb-3 pb-3 border-b border-zinc-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-zinc-700">Selected:</span>
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    wire:click.stop="clearCategories"
                                    class="text-xs"
                                >
                                    Clear all
                                </flux:button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedCategoryModels as $category)
                                    <div class="flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-zinc-100">
                                        <div
                                            class="w-2 h-2 rounded-full flex-shrink-0"
                                            style="background-color: {{ $category->color }}"
                                        ></div>
                                        <span>{{ $category->name }}</span>
                                        <button
                                            wire:click.stop="toggleCategory({{ $category->id }})"
                                            class="text-zinc-500 hover:text-zinc-700 ml-1"
                                        >
                                            ×
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Available Categories -->
                    <div class="max-h-64 overflow-y-auto">
                        @forelse($filteredCategories as $category)
                            <div
                                wire:click.stop="toggleCategory({{ $category->id }})"
                                class="flex items-center gap-3 p-2 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-400 cursor-pointer {{ in_array($category->id, $selectedCategories) ? 'bg-zinc-50' : '' }}"
                            >
                                <div
                                    class="w-4 h-4 rounded-full flex-shrink-0"
                                    style="background-color: {{ $category->color }}"
                                ></div>
                                <span class="text-sm">{{ $category->name }}</span>
                                @if(in_array($category->id, $selectedCategories))
                                    <flux:icon.check class="size-4 text-green-600 ml-auto" />
                                @endif
                            </div>
                        @empty
                            <div class="text-sm text-zinc-500 text-center py-4">
                                No categories found
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recipes Grid -->
    @if($recipes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($recipes as $recipe)
                <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Clickable Recipe Content -->
                    <div wire:navigate href="{{ route('recipes.view', $recipe) }}" class="cursor-pointer">
                        <!-- Recipe Image -->
                        <div class="aspect-video bg-zinc-100 flex items-center justify-center">
                            @if($recipe->image_url)
                                <img src="{{ $recipe->image_url }}" alt="{{ $recipe->name }}" class="w-full h-full object-cover">
                            @else
                                <flux:icon.book-open-text class="size-12 text-zinc-400" />
                            @endif
                        </div>

                        <!-- Recipe Content -->
                        <div class="p-4">
                            <h3 class="font-semibold text-zinc-900 mb-2">{{ $recipe->name }}</h3>

                            @if($recipe->description)
                                <p class="text-sm text-zinc-600 mb-3 line-clamp-2">{{ $recipe->description }}</p>
                            @endif

                            <!-- Recipe Meta -->
                            <div class="flex items-center justify-between text-xs text-zinc-500 mb-3">
                                <div class="flex items-center space-x-3">
                                    @if($recipe->total_time)
                                        <span>{{ $recipe->total_time }}min</span>
                                    @endif
                                    <span>{{ $recipe->servings }} servings</span>
                                </div>
                            </div>

                            <!-- Category Dots -->
                            @if($recipe->categories->count() > 0)
                                <div class="flex items-center gap-1 mb-3">
                                    @foreach($recipe->categories as $category)
                                        <div
                                            class="w-3 h-3 rounded-full flex-shrink-0 cursor-help"
                                            style="background-color: {{ $category->color }}"
                                            title="{{ $category->name }}"
                                        ></div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Tags -->
                            @if($recipe->tags && count($recipe->tags) > 0)
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach(array_slice($recipe->tags, 0, 3) as $tag)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                    @if(count($recipe->tags) > 3)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-600">
                                            +{{ count($recipe->tags) - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions (outside clickable area) -->
                    <div class="px-4 pb-4">
                        <div class="flex items-center justify-between">
                            <flux:button variant="ghost" size="sm" wire:navigate href="{{ route('recipes.view', $recipe) }}">
                                View Recipe
                            </flux:button>
                            <div class="flex items-center space-x-1">
                                <flux:button variant="ghost" size="sm">
                                    <flux:icon.pencil class="size-4" />
                                </flux:button>
                                <flux:button variant="ghost" size="sm" class="text-red-600 hover:text-red-700">
                                    <flux:icon.trash class="size-4" />
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $recipes->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <flux:icon.book-open-text class="size-16 text-zinc-400 mx-auto mb-4" />
            <h3 class="text-lg font-medium text-zinc-900 mb-2">No recipes found</h3>
            <p class="text-zinc-600 mb-6">
                @if($search)
                    No recipes match your search criteria.
                @else
                    Get started by adding your first recipe.
                @endif
            </p>
            @if(!$search)
                <flux:button variant="primary" wire:navigate href="{{ route('recipes.add') }}">
                    <flux:icon.plus class="size-4" />
                    Add Your First Recipe
                </flux:button>
            @endif
        </div>
    @endif
</div>
