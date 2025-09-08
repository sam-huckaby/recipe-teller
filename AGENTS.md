# Agent Guidelines for Recipe Teller

## Build/Test/Lint Commands
- **Development**: `composer run dev` (starts server, queue, logs, and vite)
- **Build**: `npm run build` (Vite build for production)
- **Test All**: `composer run test` or `php artisan test`
- **Test Single**: `php artisan test --filter=TestName` or `php artisan test tests/Feature/SpecificTest.php`
- **Lint**: `./vendor/bin/pint` (Laravel Pint for PHP formatting)

## Project Structure
- Laravel 12 + Livewire + Flux UI components
- PHP 8.2+ with Pest testing framework
- Frontend: Vite + TailwindCSS 4.x
- Authentication: Livewire components with rate limiting

## Code Style Guidelines
- **Indentation**: 4 spaces (PHP), 2 spaces (YAML)
- **PHP**: PSR-4 autoloading, type hints required, DocBlocks for public methods
- **Naming**: PascalCase for classes, camelCase for methods/properties, snake_case for routes
- **Imports**: Group by vendor, Laravel, app - alphabetical within groups
- **Error Handling**: Use ValidationException for user errors, proper HTTP status codes
- **Livewire**: Use attributes (#[Validate], #[Layout]) over properties where possible
- **Tests**: Pest syntax preferred, feature tests for user flows, unit tests for logic