<template>
    <div class="space-y-6">
        <h2 class="text-2xl font-semibold text-blue-900 mb-4 flex items-center gap-2">
            <i class="pi pi-chart-line text-blue-900"></i>
            Technical Analysis
        </h2>

        <div class="grid grid-cols-1 gap-6">
            <div class="field">
                <label class="block text-sm font-medium mb-2">Location</label>
                <Select :model-value="modelValue.location" @update:model-value="updateData('location', $event)"
                    :options="locationOptions" placeholder="Select Location" class="w-full" />
            </div>

            <div class="field">
                <label class="block text-sm font-medium mb-2">Direction</label>
                <Select :model-value="modelValue.direction" @update:model-value="updateData('direction', $event)"
                    :options="directionOptions" placeholder="Select Direction" class="w-full" />
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['update:modelValue', 'progress-updated'])

const locationOptions = ['Very Expensive', 'Expensive', 'EQ', 'Cheap', 'Very Cheap']
const directionOptions = ['Correction', 'Impulsion']

const updateData = (key, value) => {
    const updatedData = { ...props.modelValue, [key]: value }
    emit('update:modelValue', updatedData)
    emit('progress-updated')
}
</script>
