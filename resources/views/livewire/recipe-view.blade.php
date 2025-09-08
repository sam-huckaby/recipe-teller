<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <flux:button variant="ghost" wire:navigate href="{{ route('recipes') }}">
                <flux:icon.arrow-left class="size-4" />
                Back to Recipes
            </flux:button>
        </div>
        <div class="flex items-center space-x-2">
            <!-- Version Selector -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-zinc-600">Version:</span>
                <flux:select wire:model.live="selectedVersionId" class="w-48">
                    <option value="current">Current</option>
                    @foreach($recipe->versions as $version)
                        <option value="{{ $version->id }}">
                            {{ $version->formatted_created_at }}
                        </option>
                    @endforeach
                </flux:select>
            </div>
            
            <flux:button variant="ghost" size="sm" wire:navigate href="{{ route('recipes.edit', $recipe) }}">
                <flux:icon.pencil class="size-4" />
                Edit
            </flux:button>
            <flux:button 
                variant="ghost" 
                size="sm" 
                class="text-red-600 hover:text-red-700"
                wire:click="delete"
                wire:confirm="Are you sure you want to delete this recipe? This action cannot be undone."
            >
                <flux:icon.trash class="size-4" />
                Delete
            </flux:button>
        </div>
    </div>

    <!-- Recipe Content -->
    <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden">
        <!-- Recipe Image -->
        @if($this->getCurrentData()->image_url)
            <div class="aspect-video bg-zinc-100">
                <img src="{{ $this->getCurrentData()->image_url }}" alt="{{ $this->getCurrentData()->name }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="p-6">
            <!-- Recipe Title and Description -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-zinc-900 mb-2">{{ $this->getCurrentData()->name }}</h1>
                @if($this->getCurrentData()->description)
                    <p class="text-zinc-600">{{ $this->getCurrentData()->description }}</p>
                @endif
            </div>

            <!-- Recipe Meta -->
            <div class="flex items-center space-x-6 mb-6 text-sm text-zinc-600">
                @if($this->getCurrentData()->prep_time)
                    <div class="flex items-center space-x-2">
                        <flux:icon.clock class="size-4" />
                        <span>Prep: {{ $this->getCurrentData()->prep_time }}min</span>
                    </div>
                @endif
                @if($this->getCurrentData()->cook_time)
                    <div class="flex items-center space-x-2">
                        <flux:icon.fire class="size-4" />
                        <span>Cook: {{ $this->getCurrentData()->cook_time }}min</span>
                    </div>
                @endif
                @if($this->getCurrentData()->total_time)
                    <div class="flex items-center space-x-2">
                        <flux:icon.clock class="size-4" />
                        <span>Total: {{ $this->getCurrentData()->total_time }}min</span>
                    </div>
                @endif
                <div class="flex items-center space-x-2">
                    <flux:icon.users class="size-4" />
                    <span>{{ $this->getCurrentData()->servings }} servings</span>
                </div>
            </div>

            <!-- Tags -->
            @if($this->getCurrentData()->tags && count($this->getCurrentData()->tags) > 0)
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach($this->getCurrentData()->tags as $tag)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif

            <!-- Recipe URL -->
            @if($this->getCurrentData()->url)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-zinc-900 mb-2">Source</h3>
                    <a href="{{ $this->getCurrentData()->url }}" target="_blank" class="text-blue-600 hover:text-blue-700 underline">
                        {{ $this->getCurrentData()->url }}
                    </a>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Ingredients -->
                @if($this->getCurrentData()->ingredients && count($this->getCurrentData()->ingredients) > 0)
                    <div>
                        <h3 class="text-lg font-semibold text-zinc-900 mb-4">Ingredients</h3>
                        <div class="space-y-2">
                            @foreach($this->getCurrentData()->ingredients as $ingredient)
                                @if($ingredient && isset($ingredient['name']) && $ingredient['name'])
                                    <div class="flex items-start space-x-2">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <span class="text-zinc-700">
                                            @if(isset($ingredient['quantity']) && $ingredient['quantity'])
                                                {{ $ingredient['quantity'] }} {{ $ingredient['name'] }}
                                            @else
                                                {{ $ingredient['name'] }}
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Instructions -->
                @if($this->getCurrentData()->instructions && count($this->getCurrentData()->instructions) > 0)
                    <div>
                        <h3 class="text-lg font-semibold text-zinc-900 mb-4">Instructions</h3>
                        <div class="space-y-4">
                            @foreach($this->getCurrentData()->instructions as $index => $instruction)
                                @if($instruction)
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                            {{ $index + 1 }}
                                        </div>
                                        <p class="text-zinc-700">{{ $instruction }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>