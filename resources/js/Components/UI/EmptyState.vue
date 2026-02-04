<template>
    <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
        <!-- Icon Container -->
        <div :class="iconContainerClass">
            <i :class="[iconClass, 'text-4xl']"></i>
        </div>

        <!-- Title -->
        <h3 class="text-lg font-medium text-slate-900 dark:text-slate-100 mb-1">
            {{ title }}
        </h3>

        <!-- Description -->
        <p class="text-slate-500 dark:text-slate-400 mb-6 max-w-sm">
            {{ description }}
        </p>

        <!-- Primary Action -->
        <div v-if="actionLabel" class="flex flex-col sm:flex-row gap-3">
            <Button
                :label="actionLabel"
                :icon="actionIcon"
                :severity="actionSeverity"
                @click="$emit('action')"
            />
            <!-- Secondary Action -->
            <Button
                v-if="secondaryLabel"
                :label="secondaryLabel"
                :icon="secondaryIcon"
                severity="secondary"
                outlined
                @click="$emit('secondary-action')"
            />
        </div>

        <!-- Custom slot for additional content -->
        <slot />
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    /** The main title text */
    title: {
        type: String,
        default: 'No data found'
    },
    /** The description text below the title */
    description: {
        type: String,
        default: 'There are no items to display at this time.'
    },
    /** The icon class (e.g., 'pi pi-inbox') */
    icon: {
        type: String,
        default: 'pi pi-inbox'
    },
    /** Visual variant: 'default', 'search', 'error', 'success' */
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'search', 'error', 'success'].includes(value)
    },
    /** Primary action button label */
    actionLabel: {
        type: String,
        default: ''
    },
    /** Primary action button icon */
    actionIcon: {
        type: String,
        default: 'pi pi-plus'
    },
    /** Primary action button severity */
    actionSeverity: {
        type: String,
        default: 'primary'
    },
    /** Secondary action button label */
    secondaryLabel: {
        type: String,
        default: ''
    },
    /** Secondary action button icon */
    secondaryIcon: {
        type: String,
        default: ''
    }
})

defineEmits(['action', 'secondary-action'])

const iconContainerClass = computed(() => {
    const baseClasses = 'rounded-full p-4 mb-4'

    switch (props.variant) {
        case 'search':
            return `${baseClasses} bg-slate-50 dark:bg-slate-900/40`
        case 'error':
            return `${baseClasses} bg-red-50 dark:bg-red-900/20`
        case 'success':
            return `${baseClasses} bg-green-50 dark:bg-green-900/20`
        default:
            return `${baseClasses} bg-blue-50 dark:bg-blue-900/20`
    }
})

const iconClass = computed(() => {
    // If custom icon provided, use it
    if (props.icon !== 'pi pi-inbox') {
        return `${props.icon} ${getIconColorClass()}`
    }

    // Otherwise use variant-based icons
    switch (props.variant) {
        case 'search':
            return 'pi pi-search text-slate-300 dark:text-slate-500'
        case 'error':
            return 'pi pi-exclamation-triangle text-red-400 dark:text-red-500'
        case 'success':
            return 'pi pi-check-circle text-green-400 dark:text-green-500'
        default:
            return 'pi pi-inbox text-blue-400 dark:text-blue-500'
    }
})

const getIconColorClass = () => {
    switch (props.variant) {
        case 'search':
            return 'text-slate-300 dark:text-slate-500'
        case 'error':
            return 'text-red-400 dark:text-red-500'
        case 'success':
            return 'text-green-400 dark:text-green-500'
        default:
            return 'text-blue-400 dark:text-blue-500'
    }
}
</script>
