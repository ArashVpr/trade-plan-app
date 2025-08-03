<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Sidebar Drawer -->
        <Drawer v-model:visible="sidebarVisible" position="left">
            <template #container="{ closeCallback }">
                <div class="flex flex-col h-full">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 pt-4 shrink-0">
                        <span class="inline-flex items-center gap-2">
                            <i class="pi pi-chart-line text-primary text-2xl"></i>
                            <span class="font-semibold text-2xl text-primary">TradePlan</span>
                        </span>
                        <span>
                            <Button type="button" @click="closeCallback" icon="pi pi-times" rounded outlined></Button>
                        </span>
                    </div>

                    <!-- Navigation Content -->
                    <div class="overflow-y-auto">
                        <ul class="list-none p-4 m-0">
                            <li>
                                <div v-ripple v-styleclass="{
                                    selector: '@next',
                                    enterFromClass: 'hidden',
                                    enterActiveClass: 'animate-slidedown',
                                    leaveToClass: 'hidden',
                                    leaveActiveClass: 'animate-slideup'
                                }"
                                    class="p-4 flex items-center justify-between text-surface-500 dark:text-surface-400 cursor-pointer p-ripple">
                                    <span class="font-medium">TRADING</span>
                                    <i class="pi pi-chevron-down"></i>
                                </div>
                                <ul class="list-none p-0 m-0 overflow-hidden">
                                    <li>
                                        <a v-ripple @click="navigateTo(route('dashboard'))" :class="[
                                            'flex items-center cursor-pointer p-4 rounded duration-150 transition-colors p-ripple',
                                            isActive(route('dashboard')) ? 'bg-primary text-primary-contrast' : 'text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800'
                                        ]">
                                            <i class="pi pi-home mr-2"></i>
                                            <span class="font-medium">Dashboard</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a v-ripple @click="navigateTo(route('home'))" :class="[
                                            'flex items-center cursor-pointer p-4 rounded duration-150 transition-colors p-ripple',
                                            isActive(route('home')) ? 'bg-primary text-primary-contrast' : 'text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800'
                                        ]">
                                            <i class="pi pi-plus mr-2"></i>
                                            <span class="font-medium">New Checklist</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a v-ripple @click="navigateTo(route('checklists.index'))" :class="[
                                            'flex items-center cursor-pointer p-4 rounded duration-150 transition-colors p-ripple',
                                            isActive(route('checklists.index')) ? 'bg-primary text-primary-contrast' : 'text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800'
                                        ]">
                                            <i class="pi pi-history mr-2"></i>
                                            <span class="font-medium">History</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a v-ripple
                                            class="flex items-center cursor-pointer p-4 rounded text-surface-400 opacity-50 duration-150 transition-colors p-ripple">
                                            <i class="pi pi-chart-bar mr-2"></i>
                                            <span class="font-medium">Analytics</span>
                                            <span
                                                class="ml-auto text-xs bg-surface-200 dark:bg-surface-700 px-2 py-1 rounded">Soon</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                        <ul class="list-none p-4 m-0">
                            <li>
                                <div v-ripple v-styleclass="{
                                    selector: '@next',
                                    enterFromClass: 'hidden',
                                    enterActiveClass: 'animate-slidedown',
                                    leaveToClass: 'hidden',
                                    leaveActiveClass: 'animate-slideup'
                                }"
                                    class="p-4 flex items-center justify-between text-surface-500 dark:text-surface-400 cursor-pointer p-ripple">
                                    <span class="font-medium">ACCOUNT</span>
                                    <i class="pi pi-chevron-down"></i>
                                </div>
                                <ul class="list-none p-0 m-0 overflow-hidden">
                                    <li>
                                        <a v-ripple @click="navigateTo(route('user-settings.index'))" :class="[
                                            'flex items-center cursor-pointer p-4 rounded duration-150 transition-colors p-ripple',
                                            isActive(route('user-settings.index')) ? 'bg-primary text-primary-contrast' : 'text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800'
                                        ]">
                                            <i class="pi pi-cog mr-2"></i>
                                            <span class="font-medium">Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <form @submit.prevent="logout" class="w-full">
                                            <button type="submit"
                                                class="flex items-center cursor-pointer p-4 rounded text-surface-700 hover:bg-surface-100 dark:text-surface-0 dark:hover:bg-surface-800 duration-150 transition-colors p-ripple w-full text-left">
                                                <i class="pi pi-sign-out mr-2"></i>
                                                <span class="font-medium">Logout</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <!-- User Profile at Bottom -->
                    <div class="mt-auto">
                        <hr class="mb-4 mx-4 border-t border-0 border-surface-200 dark:border-surface-700" />
                        <div class="m-4 flex items-center p-4 gap-2 rounded text-surface-700 dark:text-surface-0">
                            <Avatar icon="pi pi-user" shape="circle" />
                            <div class="flex-1">
                                <span class="font-bold block">{{ $page.props.auth.user?.name || 'Trader' }}</span>
                                <small class="text-surface-500 dark:text-surface-400">{{ $page.props.auth.user?.email ||
                                    'trader@example.com' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </Drawer>

        <!-- Top Bar with Menu Button -->
        <div class="bg-white shadow-sm border-b border-surface-200 dark:border-surface-700">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    <Button icon="pi pi-bars" @click="sidebarVisible = true" text />
                    <h1 class="text-xl font-semibold text-surface-900 dark:text-surface-0">{{ pageTitle }}</h1>
                </div>
                <div class="flex items-center gap-2">
                    <Button icon="pi pi-user" text severity="secondary" @click="toggleUserMenu"
                        aria-label="User Menu" />
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="p-6">
            <slot />
        </main>

        <!-- Global Toast for notifications -->
        <Toast />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const page = usePage()
const sidebarVisible = ref(false)

// Helper function to navigate and close sidebar
const navigateTo = (url) => {
    if (url && !url.includes('analytics')) {
        router.get(url)  // SPA navigation with Inertia.js
        sidebarVisible.value = false // Close sidebar after navigation
    }
}

// Logout function
const logout = () => {
    router.post(route('logout'))
}

// Helper function to check if route is active
const isActive = (routeUrl) => {
    const currentUrl = page.url
    if (routeUrl === route('dashboard') && currentUrl === '/dashboard') {
        return true
    }
    if (routeUrl === route('home') && (currentUrl === '/' || currentUrl === route('home'))) {
        return true
    }
    if (routeUrl === route('checklists.index') && currentUrl.includes('/checklists')) {
        return true
    }
    if (routeUrl === route('user-settings.index') && currentUrl.includes('/user-settings')) {
        return true
    }
    return false
}

// Compute page title based on current route
const pageTitle = computed(() => {
    const currentUrl = page.url
    if (currentUrl === '/dashboard') return 'Dashboard'
    if (currentUrl === '/' || currentUrl === route('home')) return 'New Checklist'
    if (currentUrl.includes('/checklists')) {
        if (currentUrl.includes('/checklists/')) return 'Checklist Details'
        return 'Trading History'
    }
    if (currentUrl.includes('/user-settings')) return 'Settings'
    return 'TradePlan'
})

// User menu toggle (future implementation)
const toggleUserMenu = () => {
    // Future: Implement user dropdown menu
    console.log('User menu clicked - future feature')
}
</script>
// Future: Implement user dropdown menu
console.log('User menu clicked - future feature')


<style scoped>
/* Custom styling for active sidebar items */
:deep(.p-drawer .p-drawer-content) {
    padding: 0;
}

/* Smooth animations for collapsible sections */
.animate-slidedown {
    animation: slidedown 0.3s ease-in-out;
}

.animate-slideup {
    animation: slideup 0.3s ease-in-out;
}

@keyframes slidedown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideup {
    from {
        opacity: 1;
        transform: translateY(0);
    }

    to {
        opacity: 0;
        transform: translateY(-10px);
    }
}

/* Responsive adjustments */
@media (min-width: 768px) {
    :deep(.p-drawer) {
        width: 300px;
    }
}

@media (max-width: 767px) {
    :deep(.p-drawer) {
        width: 280px;
    }
}
</style>
