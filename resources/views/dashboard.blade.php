<x-layouts.app :title="__('Meal Planner')">
    <div class="space-y-6">
        <!-- Welcome Header -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
            <h1 class="text-2xl font-bold mb-2">Welcome to Recipe Teller</h1>
            <p class="text-green-100">Plan your meals for the week and organize your favorite recipes</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg border border-zinc-200 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <flux:icon.book-open-text class="size-6 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600">Total Recipes</p>
                        <p class="text-2xl font-bold text-zinc-900">{{ auth()->user()->recipes()->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-zinc-200 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <flux:icon.layout-grid class="size-6 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600">This Week's Meals</p>
                        <p class="text-2xl font-bold text-zinc-900">{{ auth()->user()->mealPlans()->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-zinc-200 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <flux:icon.folder-git-2 class="size-6 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-zinc-600">Categories</p>
                        <p class="text-2xl font-bold text-zinc-900">{{ \App\Models\Category::count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meal Planner -->
        <div class="bg-white rounded-xl border border-zinc-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-zinc-900">Weekly Meal Planner</h2>
                <flux:button href="/recipes" variant="outline" size="sm">
                    Manage Recipes
                </flux:button>
            </div>
            
            <livewire:meal-planner />
        </div>
    </div>
</x-layouts.app>
