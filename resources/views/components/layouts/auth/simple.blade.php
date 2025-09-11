<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 antialiased">
        <!-- Decorative Background Pattern -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-green-200/30 to-emerald-200/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-teal-200/30 to-green-200/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-r from-green-100/20 to-emerald-100/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Main Content -->
        <div class="relative flex min-h-svh flex-col items-center justify-center p-6 md:p-10">
            <!-- Logo Section -->
            <div class="mb-8">
                <a href="{{ route('home') }}" class="block transition-transform hover:scale-105 duration-200" wire:navigate>
                    <img src="/Centered_green_bg.png" alt="Recipe Teller" class="h-24 w-auto rounded-xl shadow-lg">
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
            </div>

            <!-- Content Wrapper -->
            <div class="w-full max-w-lg">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-12 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Made with ❤️ for food lovers everywhere
                </p>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
