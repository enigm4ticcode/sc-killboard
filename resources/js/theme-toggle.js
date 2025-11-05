/**
 * Theme toggle functionality with localStorage persistence
 * Supports light, space-black (OLED), and space-blue (Deep Space Blue) themes
 */

// Theme cycle order: light → space-black → space-blue → light
const THEMES = ['light', 'space-black', 'space-blue'];

// Apply theme to document
function applyTheme(theme) {
    const html = document.documentElement;

    if (theme === 'light') {
        html.classList.remove('dark');
        html.removeAttribute('data-theme');
    } else {
        html.classList.add('dark');
        html.setAttribute('data-theme', theme);
    }
}

// Initialize theme IMMEDIATELY (before DOMContentLoaded to prevent flash)
(function() {
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    // Default to space-blue if no theme saved and system prefers dark
    let theme = savedTheme || (systemPrefersDark ? 'space-blue' : 'light');

    // Handle legacy 'dark' value (convert to space-blue)
    if (theme === 'dark') {
        theme = 'space-blue';
        localStorage.setItem('theme', theme);
    }

    applyTheme(theme);
})();

// Listen for system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
    if (!localStorage.getItem('theme')) {
        applyTheme(e.matches ? 'space-blue' : 'light');
        window.dispatchEvent(new CustomEvent('theme-changed'));
    }
});

// Set theme directly (called from theme selector dropdown)
window.setTheme = function(theme) {
    if (!THEMES.includes(theme)) {
        console.warn(`Invalid theme: ${theme}. Valid themes are:`, THEMES);
        return;
    }

    applyTheme(theme);
    localStorage.setItem('theme', theme);

    // Dispatch custom event for Alpine to react to
    window.dispatchEvent(new CustomEvent('theme-changed'));
};

// Theme toggle function (called from Alpine component)
// Cycles through: light → space-black → space-blue → light
window.toggleTheme = function() {
    const currentTheme = localStorage.getItem('theme') || 'light';
    const currentIndex = THEMES.indexOf(currentTheme);
    const nextIndex = (currentIndex + 1) % THEMES.length;
    const nextTheme = THEMES[nextIndex];

    window.setTheme(nextTheme);
};

// Get current theme state
window.isDarkMode = function() {
    return document.documentElement.classList.contains('dark');
};

// Get current theme name
window.getCurrentTheme = function() {
    return localStorage.getItem('theme') || 'light';
};
