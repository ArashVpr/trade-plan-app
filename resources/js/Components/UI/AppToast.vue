<template>
    <Toast :position="position" />
</template>

<script setup>
import { useToast } from 'primevue/usetoast'

const props = defineProps({
    position: {
        type: String,
        default: 'top-right'
    }
})

const toast = useToast()

// Expose toast methods for parent components
const showSuccess = (summary = 'Success', detail = '', life = 3000) => {
    toast.add({
        severity: 'success',
        summary,
        detail,
        life
    })
}

const showError = (summary = 'Error', detail = '', life = 3000) => {
    toast.add({
        severity: 'error',
        summary,
        detail,
        life
    })
}

const showInfo = (summary = 'Info', detail = '', life = 3000) => {
    toast.add({
        severity: 'info',
        summary,
        detail,
        life
    })
}

const showWarning = (summary = 'Warning', detail = '', life = 3000) => {
    toast.add({
        severity: 'warn',
        summary,
        detail,
        life
    })
}

defineExpose({
    showSuccess,
    showError,
    showInfo,
    showWarning
})
</script>
