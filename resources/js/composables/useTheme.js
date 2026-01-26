import { ref, watch } from 'vue';

const THEME_KEY = 'trade-plan-theme';
const DARK_CLASS = 'app-dark';

// Reactive state for the current theme
const theme = ref('light');

// Initialize theme from localStorage or default to light
const initTheme = () => {
    const stored = localStorage.getItem(THEME_KEY);
    theme.value = stored === 'dark' ? 'dark' : 'light';
    applyTheme(theme.value);
};

// Apply theme by adding/removing class on HTML element
const applyTheme = (newTheme) => {
    const html = document.documentElement;

    if (newTheme === 'dark') {
        html.classList.add(DARK_CLASS);
    } else {
        html.classList.remove(DARK_CLASS);
    }
};

// Watch for theme changes and persist to localStorage
watch(theme, (newTheme) => {
    localStorage.setItem(THEME_KEY, newTheme);
    applyTheme(newTheme);
});

export function useTheme() {
    const toggleTheme = () => {
        theme.value = theme.value === 'light' ? 'dark' : 'light';
    };

    const setTheme = (newTheme) => {
        if (newTheme === 'light' || newTheme === 'dark') {
            theme.value = newTheme;
        }
    };

    const isDark = () => theme.value === 'dark';

    return {
        theme,
        toggleTheme,
        setTheme,
        isDark,
        initTheme
    };
}
