/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./vendor/livewire/flux-pro/stubs/**/*.blade.php",
    "./vendor/livewire/flux/stubs/**/*.blade.php",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      colors: {
        // Green color palette based on #177245
        green: {
          50: '#f0f9f4',
          100: '#dcf4e3', 
          200: '#b8e8c7',
          300: '#8dd5a3',
          400: '#5bb97b',
          500: '#177245',
          600: '#145a38',
          700: '#11472b',
          800: '#0d341f',
          900: '#082114',
          950: '#041109',
        },
        accent: {
          DEFAULT: '#177245',
          foreground: '#ffffff',
        },
      },
    },
  },
  plugins: [],
}