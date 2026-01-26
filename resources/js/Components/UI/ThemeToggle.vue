<template>
    <div class="theme-toggle">
        <Button
            :icon="isDark() ? 'pi pi-sun' : 'pi pi-moon'"
            @click="toggleTheme"
            severity="secondary"
            text
            rounded
            :aria-label="isDark() ? 'Switch to light mode' : 'Switch to dark mode'"
            v-tooltip.bottom="isDark() ? 'Light mode' : 'Dark mode'"
            class="theme-toggle-btn"
        />
    </div>
</template>

<script setup>
import { useTheme } from '@/composables/useTheme';

const { toggleTheme, isDark } = useTheme();
</script>

<style scoped>
.theme-toggle-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.theme-toggle-btn:hover {
    transform: scale(1.05);
}

.theme-toggle-btn:active {
    transform: scale(0.95);
}

/* Icon animation */
.theme-toggle-btn :deep(.pi) {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.theme-toggle-btn:hover :deep(.pi) {
    transform: rotate(20deg);
}

/* Smooth color transitions */
:global(.app-dark) .theme-toggle-btn :deep(.pi) {
    color: var(--p-primary-color);
}
</style>
