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

    <!-- Search -->
    <div class="max-w-md">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Search recipes..."
            type="search"
        />
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
