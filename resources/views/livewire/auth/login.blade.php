<div class="w-full max-w-md mx-auto">
    <!-- Welcome Section -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Welcome back!</h1>
        <p class="text-gray-600 dark:text-gray-300">Sign in to continue your recipe journey</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6 text-center" :status="session('status')" />

    <!-- Login Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8 backdrop-blur-sm">
        <form method="POST" wire:submit="login" class="space-y-6">
            <!-- Email Address -->
            <div class="space-y-2">
                <flux:input
                    wire:model="email"
                    :label="__('Email address')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="Enter your email"
                    class="rounded-xl border-gray-200 dark:border-gray-600 focus:border-green-400 focus:ring-green-400"
                />
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <div class="relative">
                    <flux:input
                        wire:model="password"
                        :label="__('Password')"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        viewable
                        class="rounded-xl border-gray-200 dark:border-gray-600 focus:border-green-400 focus:ring-green-400"
                    />

                    @if (Route::has('password.request'))
                        <flux:link class="absolute right-0 top-0 text-sm text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 transition-colors" :href="route('password.request')" wire:navigate>
                            Forgot password?
                        </flux:link>
                    @endif
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <flux:checkbox wire:model="remember" :label="__('Keep me signed in')" class="text-green-600" />
            </div>

            <!-- Login Button -->
            <div class="pt-4">
                <flux:button
                    variant="primary"
                    type="submit"
                    class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                >
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        {{ __('Sign in') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </div>

    <!-- Sign Up Link -->
    @if (Route::has('register'))
        <div class="text-center mt-8 p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl border border-green-100 dark:border-gray-600">
            <p class="text-gray-600 dark:text-gray-300">
                New to Recipe Teller?
                <flux:link :href="route('register')" wire:navigate class="font-semibold text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 transition-colors ml-1">
                    Create your account →
                </flux:link>
            </p>
        </div>
    @endif
</div>
