<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Shopping List</h1>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">
            Ingredients for the next 7 days ({{ now()->format('M j') }} - {{ now()->addDays(6)->format('M j, Y') }})
        </p>
    </div>

    <!-- Shopping List -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
        @if(empty($shoppingList))
            <div class="p-8 text-center">
                <flux:icon icon="list-bullet" class="h-12 w-12 text-zinc-400 mx-auto mb-4" />
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">No meals planned</h3>
                <p class="text-zinc-600 dark:text-zinc-400">
                    Add some meals to your 
                    <a href="{{ route('dashboard') }}" class="text-green-600 dark:text-green-400 hover:underline" wire:navigate>meal planner</a> 
                    to generate your shopping list.
                </p>
            </div>
        @else
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($shoppingList as $item)
                    <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $item['ingredient'] }}
                                </div>
                                <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    from 
                                    <a 
                                        href="{{ route('recipes.view', $item['recipe_id']) }}" 
                                        class="text-green-600 dark:text-green-400 hover:underline"
                                        wire:navigate
                                    >
                                        {{ $item['recipe_name'] }}
                                    </a>
                                    • {{ \Carbon\Carbon::parse($item['date'])->format('M j') }} ({{ ucfirst($item['meal_type']) }})
                                </div>
                            </div>
                            <div class="ml-4">
                                <flux:checkbox class="text-green-600" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Summary -->
            <div class="bg-zinc-50 dark:bg-zinc-700 px-4 py-3 border-t border-zinc-200 dark:border-zinc-600">
                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Total items: {{ count($shoppingList) }}
                </p>
            </div>
        @endif
    </div>
</div>