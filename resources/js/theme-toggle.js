/**
 * Theme toggle functionality with localStorage persistence
 * Supports light, dark, and system preference modes
 */

// Initialize theme IMMEDIATELY (before DOMContentLoaded to prevent flash)
(function() {
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
})();

// Listen for system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
    if (!localStorage.getItem('theme')) {
        if (e.matches) {
            document.documentElement.classList.add('dark');
            window.dispatchEvent(new CustomEvent('theme-changed'));
        } else {
            document.documentElement.classList.remove('dark');
            window.dispatchEvent(new CustomEvent('theme-changed'));
        }
    }
});

// Theme toggle function (called from Alpine component)
window.toggleTheme = function() {
    const isDark = document.documentElement.classList.contains('dark');

    if (isDark) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }

    // Dispatch custom event for Alpine to react to
    window.dispatchEvent(new CustomEvent('theme-changed'));
};

// Get current theme state
window.isDarkMode = function() {
    return document.documentElement.classList.contains('dark');
};
