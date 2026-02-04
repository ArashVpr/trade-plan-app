<template>
    <Message
        v-if="hasErrors"
        severity="error"
        :closable="closable"
        class="mb-4"
        @close="$emit('close')"
    >
        <template #default>
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2 font-semibold">
                    <i class="pi pi-exclamation-circle"></i>
                    <span>{{ title }}</span>
                </div>
                <ul class="list-disc pl-6 space-y-1 text-sm">
                    <li
                        v-for="(error, field) in errors"
                        :key="field"
                        class="cursor-pointer hover:underline"
                        @click="scrollToField(field)"
                    >
                        <span class="font-medium capitalize">{{ formatFieldName(field) }}:</span>
                        {{ Array.isArray(error) ? error[0] : error }}
                    </li>
                </ul>
                <div v-if="showFixHint" class="text-xs opacity-80 mt-1">
                    <i class="pi pi-info-circle mr-1"></i>
                    Click on an error to jump to that field
                </div>
            </div>
        </template>
    </Message>
</template>

<script setup>
import { computed, nextTick } from 'vue'

const props = defineProps({
    /** Object containing form errors (key: field name, value: error message or array) */
    errors: {
        type: Object,
        default: () => ({})
    },
    /** Title shown at the top of the error summary */
    title: {
        type: String,
        default: 'Please fix the following errors:'
    },
    /** Whether the error summary can be closed */
    closable: {
        type: Boolean,
        default: true
    },
    /** Whether to show the hint about clicking to scroll */
    showFixHint: {
        type: Boolean,
        default: true
    },
    /** Custom field name mappings for display */
    fieldLabels: {
        type: Object,
        default: () => ({})
    },
    /** Prefix for field IDs (e.g., 'form-' would look for 'form-email') */
    fieldIdPrefix: {
        type: String,
        default: ''
    }
})

defineEmits(['close'])

const hasErrors = computed(() => {
    return Object.keys(props.errors).length > 0
})

const formatFieldName = (field) => {
    // Check for custom label first
    if (props.fieldLabels[field]) {
        return props.fieldLabels[field]
    }

    // Convert snake_case or camelCase to readable format
    return field
        .replace(/_/g, ' ')
        .replace(/([a-z])([A-Z])/g, '$1 $2')
        .toLowerCase()
}

const scrollToField = async (field) => {
    await nextTick()

    // Try multiple strategies to find the field
    const selectors = [
        `#${props.fieldIdPrefix}${field}`,
        `[name="${field}"]`,
        `[data-field="${field}"]`,
        `#${field}`,
        `.field-${field}`,
    ]

    let element = null

    for (const selector of selectors) {
        try {
            element = document.querySelector(selector)
            if (element) break
        } catch (e) {
            // Invalid selector, continue to next
        }
    }

    if (element) {
        // Scroll element into view with some offset for headers
        const headerOffset = 100
        const elementPosition = element.getBoundingClientRect().top
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        })

        // Focus the element after scrolling
        setTimeout(() => {
            if (typeof element.focus === 'function') {
                element.focus()
            }

            // Add a brief highlight animation
            element.classList.add('ring-2', 'ring-red-400', 'ring-offset-2')
            setTimeout(() => {
                element.classList.remove('ring-2', 'ring-red-400', 'ring-offset-2')
            }, 2000)
        }, 500)
    }
}

// Expose scroll function for external use
defineExpose({ scrollToField })
</script>

<style scoped>
:deep(.p-message-content) {
    width: 100%;
}
</style>
