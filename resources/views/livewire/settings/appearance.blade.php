<section class="w-full" 
    x-data="{ 
        theme: @entangle('theme'),
        init() {
            this.applyTheme(this.theme);
            this.$watch('theme', (value) => {
                this.applyTheme(value);
            });
        },
        applyTheme(theme) {
            const html = document.documentElement;
            
            // Remove existing theme classes
            html.classList.remove('light', 'dark');
            
            if (theme === 'system') {
                // Use system preference
                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                html.classList.add(systemTheme);
            } else {
                html.classList.add(theme);
            }
        }
    }"
    @theme-changed.window="applyTheme($event.detail.theme)">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <flux:radio.group variant="segmented" wire:model.live="theme">
            <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
        </flux:radio.group>
    </x-settings.layout>
</section>
