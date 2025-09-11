// Alpine.js is handled by Livewire/Flux - don't load it separately

// Theme handling
document.addEventListener('DOMContentLoaded', function() {
    // Apply initial theme based on user preference or system
    function applyInitialTheme() {
        const html = document.documentElement;
        const userTheme = html.dataset.theme;
        
        if (userTheme === 'system' || !userTheme) {
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            html.classList.add(systemTheme);
        } else {
            html.classList.add(userTheme);
        }
    }
    
    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        const html = document.documentElement;
        const currentTheme = html.dataset.theme;
        
        if (currentTheme === 'system' || !currentTheme) {
            html.classList.remove('light', 'dark');
            html.classList.add(e.matches ? 'dark' : 'light');
        }
    });
    
    applyInitialTheme();
});

// Make Laravel helpers available to Alpine.js expressions
window.__ = function(key, replacements = {}) {
    // Simple translation function - in a real app you'd fetch from Laravel
    const translations = {
        'Email address': 'Email address',
        'Password': 'Password', 
        'Remember me': 'Remember me',
        'Log in': 'Log in',
        'Sign up': 'Sign up',
        'Create account': 'Create account',
        'Name': 'Name',
        'Full name': 'Full name',
        'Confirm password': 'Confirm password',
        'Already have an account?': 'Already have an account?',
        'Don\'t have an account?': 'Don\'t have an account?',
        'Forgot your password?': 'Forgot your password?'
    };
    
    let translation = translations[key] || key;
    
    // Simple replacement handling
    for (let placeholder in replacements) {
        translation = translation.replace(`:${placeholder}`, replacements[placeholder]);
    }
    
    return translation;
};

// Make route helper available to Alpine.js expressions  
window.route = function(name, params = {}) {
    // Simple route function - maps route names to URLs
    const routes = {
        'login': '/login',
        'register': '/register', 
        'password.request': '/forgot-password',
        'home': '/',
        'dashboard': '/dashboard'
    };
    
    return routes[name] || '/';
};