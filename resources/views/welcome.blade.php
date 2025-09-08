<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recipe Teller - Stop Hoarding Recipes, Start Cooking!</title>
    
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    @vite(['resources/css/app.css'])
</head>
<body class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border-b border-zinc-200 dark:border-zinc-800 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <img src="/Horizontal_white_bg.png" alt="Recipe Teller" class="h-8">
                </div>
                
                @if (Route::has('login'))
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                                Sign In
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    Get Started Free
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <!-- Free Badge -->
                <div class="inline-flex items-center px-4 py-2 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-full text-sm font-semibold mb-8">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    100% FREE FOREVER • No Subscriptions • No Catch
                </div>

                <!-- Main Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-zinc-900 dark:text-zinc-100 mb-6 leading-tight">
                    Finally, a home for all those
                    <span class="text-green-600 relative">
                        recipes you've been hoarding
                        <svg class="absolute -bottom-2 left-0 w-full h-3 text-green-200 dark:text-green-800" viewBox="0 0 300 12" fill="currentColor">
                            <path d="M5 6c50-3 100-3 150 0s100 3 150 0" stroke="currentColor" stroke-width="3" fill="none"/>
                        </svg>
                    </span>
                </h1>

                <!-- Subheadline -->
                <p class="text-xl text-zinc-600 dark:text-zinc-400 mb-12 max-w-3xl mx-auto leading-relaxed">
                    You know that folder of screenshots, bookmarked recipes, and "I'll definitely make this someday" saves? 
                    We built Recipe Teller for people just like you. <strong>Plan your meals, organize your collection, and actually use those amazing recipes.</strong>
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Go to Your Recipes
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Start Organizing for Free
                        </a>
                        <a href="{{ route('login') }}" class="border-2 border-zinc-300 dark:border-zinc-700 hover:border-green-600 dark:hover:border-green-400 text-zinc-700 dark:text-zinc-300 px-8 py-4 rounded-lg font-semibold text-lg transition-all">
                            I Already Have an Account
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Visual Recipe Showcase -->
            <div class="relative max-w-5xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Recipe Card 1 -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 transform rotate-1 hover:rotate-0 transition-transform">
                        <div class="w-full h-32 bg-gradient-to-br from-orange-200 to-red-300 rounded-lg mb-4 flex items-center justify-center">
                            <span class="text-2xl">🍝</span>
                        </div>
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Grandma's Pasta</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-3">30 min • 4 servings</p>
                        <div class="flex items-center text-xs text-green-600 dark:text-green-400">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Planned for Tuesday
                        </div>
                    </div>

                    <!-- Recipe Card 2 -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 transform -rotate-1 hover:rotate-0 transition-transform">
                        <div class="w-full h-32 bg-gradient-to-br from-green-200 to-blue-300 rounded-lg mb-4 flex items-center justify-center">
                            <span class="text-2xl">🥗</span>
                        </div>
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Buddha Bowl</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-3">15 min • 2 servings</p>
                        <div class="flex items-center text-xs text-green-600 dark:text-green-400">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Planned for Friday
                        </div>
                    </div>

                    <!-- Recipe Card 3 -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg p-6 transform rotate-2 hover:rotate-0 transition-transform">
                        <div class="w-full h-32 bg-gradient-to-br from-purple-200 to-pink-300 rounded-lg mb-4 flex items-center justify-center">
                            <span class="text-2xl">🧁</span>
                        </div>
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Chocolate Muffins</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-3">45 min • 12 servings</p>
                        <div class="flex items-center text-xs text-zinc-500 dark:text-zinc-500">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            Saved for later
                        </div>
                    </div>
                </div>

                <!-- Arrow pointing down -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center text-green-600 dark:text-green-400 font-medium">
                        <span class="mr-2">From chaos to organized meal plans</span>
                        <svg class="w-5 h-5 animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-zinc-50 dark:bg-zinc-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-zinc-900 dark:text-zinc-100 mb-4">
                    Everything you need, completely free
                </h2>
                <p class="text-xl text-zinc-600 dark:text-zinc-400">
                    Because we know you have 47 screenshots of recipes on your phone 📱
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Weekly Meal Planning</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">
                        Plan breakfast, lunch, and dinner for the entire week. No more 5pm "what's for dinner?" panic attacks.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Recipe Collection</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">
                        Store all your recipes in one organized place. Your digital recipe box that actually makes sense.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Smart Organization</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">
                        Find recipes instantly by ingredients, cooking time, or dietary needs. No more endless scrolling through screenshots.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial/Fun Section -->
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-green-50 dark:bg-green-900/20 rounded-2xl p-8 mb-12">
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-4">
                    Sound familiar? 🤔
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-zinc-600 dark:text-zinc-400">
                    <div class="flex items-center justify-center space-x-2">
                        <span class="text-2xl">📱</span>
                        <span>47 recipe screenshots</span>
                    </div>
                    <div class="flex items-center justify-center space-x-2">
                        <span class="text-2xl">🔖</span>
                        <span>23 bookmarked food blogs</span>
                    </div>
                    <div class="flex items-center justify-center space-x-2">
                        <span class="text-2xl">🛒</span>
                        <span>Still ordering takeout</span>
                    </div>
                </div>
                <p class="text-lg text-zinc-700 dark:text-zinc-300 mt-6 font-medium">
                    We get it. That's exactly why we built Recipe Teller.
                </p>
            </div>

            <div class="text-center">
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-4">
                    Ready to turn your recipe chaos into meal planning magic?
                </h3>
                <p class="text-lg text-zinc-600 dark:text-zinc-400 mb-8">
                    Join thousands of recovering recipe hoarders who finally cook the food they save.
                </p>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                        Start Cooking Your Saved Recipes
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                        Get Started Free - No Credit Card Required
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-zinc-900 dark:bg-zinc-950 text-zinc-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-8">
                <h4 class="text-2xl font-bold text-green-400 mb-2">Recipe Teller</h4>
                <p class="text-lg">
                    ✨ <strong class="text-white">No subscriptions</strong> • 
                    <strong class="text-white">No premium tiers</strong> • 
                    <strong class="text-white">No hidden fees</strong> • 
                    Just free meal planning forever
                </p>
            </div>
            <p class="text-sm">
                Made with ❤️ for people who save recipes but never cook them (until now)
            </p>
        </div>
    </footer>
</body>
</html>