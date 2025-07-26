<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Main Navigation Bar -->
        <Menubar :model="menuItems" class="border-0 shadow-sm bg-white">
            <template #start>
                <div class="flex items-center">
                    <i class="pi pi-chart-line text-blue-900 text-2xl mr-3"></i>
                    <span class="text-xl font-bold text-blue-900">TradePlan</span>
                </div>
            </template>

            <template #end>
                <div class="flex items-center gap-2">
                    <!-- User Menu -->
                    <Button icon="pi pi-user" text severity="secondary" @click="toggleUserMenu" aria-label="User Menu"
                        class="text-gray-600" />
                    <!-- Future: Add user dropdown menu here -->
                </div>
            </template>
        </Menubar>

        <!-- Page Content -->
        <main>
            <slot />
        </main>

        <!-- Global Toast for notifications -->
        <Toast />
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const page = usePage()

// Navigation menu items
const menuItems = computed(() => [
    {
        label: 'New Checklist',
        icon: 'pi pi-plus',
        command: () => navigateTo(route('home')),
        class: getCurrentRouteClass(route('home'))
    },
    {
        label: 'History',
        icon: 'pi pi-history',
        command: () => navigateTo(route('checklists.index')),
        class: getCurrentRouteClass(route('checklists.index'))
    },
    {
        label: 'Analytics',
        icon: 'pi pi-chart-bar',
        command: () => navigateTo('/analytics'),
        class: getCurrentRouteClass('/analytics'),
        disabled: true // Future feature
    },
    {
        label: 'Settings',
        icon: 'pi pi-cog',
        command: () => navigateTo(route('user-settings.index')),
        class: getCurrentRouteClass(route('user-settings.index'))
    }
])

// Helper function to navigate
const navigateTo = (url) => {
    if (url && !url.includes('analytics')) {
        router.get(url)  // SPA navigation with Inertia.js
    }
}

// Helper function to get active route styling
const getCurrentRouteClass = (routeUrl) => {
    const currentUrl = page.url
    if (routeUrl === route('home') && (currentUrl === '/' || currentUrl === route('home'))) {
        return 'text-blue-900 font-semibold'
    }
    if (routeUrl === route('checklists.index') && currentUrl.includes('/checklists')) {
        return 'text-blue-900 font-semibold'
    }
    if (routeUrl === route('user-settings.index') && currentUrl.includes('/user-settings')) {
        return 'text-blue-900 font-semibold'
    }
    return 'text-gray-700 hover:text-blue-900'
}

// User menu toggle (future implementation)
const toggleUserMenu = () => {
    // Future: Implement user dropdown menu
    console.log('User menu clicked - future feature')
}
</script>

<style scoped>
/* Custom styling for active menu items */
:deep(.p-menubar .p-menuitem-link) {
    transition: all 0.2s ease;
}

:deep(.p-menubar .p-menuitem-link:hover) {
    background-color: #eff6ff;
}

/* Brand logo hover effect */
.text-blue-900:hover {
    color: #1e3a8a;
}
</style>
