<div class="space-y-6">
    <!-- Week Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <flux:button wire:click="previousWeek" variant="ghost" size="sm">
                <flux:icon.chevron-left class="size-4" />
                Previous
            </flux:button>
            <flux:button wire:click="currentWeek" variant="outline" size="sm">
                This Week
            </flux:button>
            <flux:button wire:click="nextWeek" variant="ghost" size="sm">
                Next
                <flux:icon.chevron-right class="size-4" />
            </flux:button>
        </div>
        <h2 class="text-lg font-semibold text-zinc-900">
            {{ $selectedWeek->format('M j') }} - {{ $selectedWeek->copy()->endOfWeek()->format('M j, Y') }}
        </h2>
    </div>

    <!-- Meal Planner Grid -->
    <div class="grid grid-cols-7 gap-4">
        @foreach($mealPlans as $dateString => $dayPlan)
            <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden">
                <!-- Day Header -->
                <div class="bg-green-500 text-white px-3 py-2 text-center">
                    <div class="font-medium">{{ $dayPlan['date']->format('D') }}</div>
                    <div class="text-sm">{{ $dayPlan['date']->format('M j') }}</div>
                </div>

                <!-- Meals -->
                <div class="p-3 space-y-3">
                    <!-- Breakfast -->
                    <div class="meal-slot">
                        <div class="text-xs font-medium text-zinc-500 mb-1">Breakfast</div>
                        @if($dayPlan['breakfast']->isNotEmpty())
                            @foreach($dayPlan['breakfast'] as $mealPlan)
                                <div
                                    class="bg-green-50 border border-green-200 rounded p-2 text-xs cursor-pointer hover:bg-green-100 transition-colors"
                                    wire:click="openMealModal('{{ $dateString }}', 'breakfast', {{ $mealPlan->id }})"
                                >
                                    <div class="font-medium text-green-800">{{ $mealPlan->recipe->name }}</div>
                                    @if($mealPlan->recipe->total_time)
                                        <div class="text-green-600">{{ $mealPlan->recipe->total_time }}min</div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div
                                class="bg-zinc-50 border-2 border-dashed border-zinc-200 rounded p-2 text-xs text-zinc-400 text-center cursor-pointer hover:bg-zinc-100 transition-colors"
                                wire:click="openMealModal('{{ $dateString }}', 'breakfast', null)"
                            >
                                + Add meal
                            </div>
                        @endif
                    </div>

                    <!-- Lunch -->
                    <div class="meal-slot">
                        <div class="text-xs font-medium text-zinc-500 mb-1">Lunch</div>
                        @if($dayPlan['lunch']->isNotEmpty())
                            @foreach($dayPlan['lunch'] as $mealPlan)
                                <div
                                    class="bg-green-100 border border-green-300 rounded p-2 text-xs cursor-pointer hover:bg-green-200 transition-colors"
                                    wire:click="openMealModal('{{ $dateString }}', 'lunch', {{ $mealPlan->id }})"
                                >
                                    <div class="font-medium text-green-800">{{ $mealPlan->recipe->name }}</div>
                                    @if($mealPlan->recipe->total_time)
                                        <div class="text-green-700">{{ $mealPlan->recipe->total_time }}min</div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div
                                class="bg-zinc-50 border-2 border-dashed border-zinc-200 rounded p-2 text-xs text-zinc-400 text-center cursor-pointer hover:bg-zinc-100 transition-colors"
                                wire:click="openMealModal('{{ $dateString }}', 'lunch', null)"
                            >
                                + Add meal
                            </div>
                        @endif
                    </div>

                    <!-- Dinner -->
                    <div class="meal-slot">
                        <div class="text-xs font-medium text-zinc-500 mb-1">Dinner</div>
                        @if($dayPlan['dinner']->isNotEmpty())
                            @foreach($dayPlan['dinner'] as $mealPlan)
                                <div
                                    class="bg-green-200 border border-green-400 rounded p-2 text-xs cursor-pointer hover:bg-green-300 transition-colors"
                                    wire:click="openMealModal('{{ $dateString }}', 'dinner', {{ $mealPlan->id }})"
                                >
                                    <div class="font-medium text-green-900">{{ $mealPlan->recipe->name }}</div>
                                    @if($mealPlan->recipe->total_time)
                                        <div class="text-green-800">{{ $mealPlan->recipe->total_time }}min</div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div
                                class="bg-zinc-50 border-2 border-dashed border-zinc-200 rounded p-2 text-xs text-zinc-400 text-center cursor-pointer hover:bg-zinc-100 transition-colors"
                                wire:click="openMealModal('{{ $dateString }}', 'dinner')"
                            >
                                + Add meal
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Meal Update Modal -->
    <flux:modal wire:model="showMealModal" class="max-w-2xl">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">
                        {{ $selectedMealPlan ? 'Update' : 'Add' }} {{ ucfirst($selectedMealType) }}
                        @if($selectedDate)
                            - {{ \Carbon\Carbon::parse($selectedDate)->format('M j') }}
                        @endif
                    </flux:heading>
                    <flux:button variant="ghost" icon="x-mark" size="sm" wire:click="closeMealModal"></flux:button>
                </div>

                <!-- Search Bar -->
                <div>
                    <flux:input
                        wire:model.live.debounce.300ms="recipeSearch"
                        placeholder="Search your recipes..."
                        type="search"
                    />
                </div>

                <!-- Recipe List -->
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @if($availableRecipes->count() > 0)
                        @foreach($availableRecipes as $recipe)
                            <div
                                class="flex items-center justify-between p-3 bg-white border border-zinc-200 rounded-lg hover:bg-zinc-50 cursor-pointer transition-colors"
                                wire:click="assignRecipe({{ $recipe->id }})"
                            >
                                <div class="flex-1">
                                    <div class="font-medium text-zinc-900">{{ $recipe->name }}</div>
                                    @if($recipe->description)
                                        <div class="text-sm text-zinc-500 mt-1">{{ Str::limit($recipe->description, 100) }}</div>
                                    @endif
                                    <div class="flex items-center space-x-4 mt-2 text-xs text-zinc-400">
                                        @if($recipe->total_time)
                                            <span>{{ $recipe->total_time }}min</span>
                                        @endif
                                        @if($recipe->servings)
                                            <span>{{ $recipe->servings }} servings</span>
                                        @endif
                                    </div>
                                </div>
                                @if($recipe->image_url)
                                    <img src="{{ $recipe->image_url }}" alt="{{ $recipe->name }}" class="w-16 h-16 rounded object-cover ml-4">
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <div class="text-zinc-500 mb-2">No recipes found</div>
                            @if($recipeSearch)
                                <div class="text-sm text-zinc-400">Try adjusting your search terms</div>
                            @else
                                <div class="text-sm text-zinc-400">You don't have any recipes yet</div>
                                <flux:button variant="primary" size="sm" wire:navigate href="{{ route('recipes.add') }}" class="mt-3">
                                    Add Your First Recipe
                                </flux:button>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex justify-between pt-4 border-t border-zinc-200">
                    <div>
                        @if($selectedMealPlan)
                            <flux:button variant="danger" wire:click="removeMeal">
                                Remove Meal
                            </flux:button>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        <flux:button variant="ghost" wire:click="closeMealModal">Cancel</flux:button>
                    </div>
                </div>
            </div>
    </flux:modal>
</div>
