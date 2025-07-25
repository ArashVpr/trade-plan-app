<template>
    <div class="space-y-6">
        <h2 class="text-2xl font-semibold text-blue-900 mb-4 flex items-center gap-2">
            <i class="pi pi-map-marker text-blue-900"></i>
            Zone Qualifiers
        </h2>

        <div class="field">
            <label class="block text-sm font-medium mb-2">Instrument</label>
            <Select :model-value="modelValue.asset" @update:model-value="updateData('asset', $event)"
                :options="assetOptions" placeholder="Select Instrument" class="w-full" />
        </div>

        <div class="space-y-4">
            <div v-for="(qualifier, index) in zoneQualifiers" :key="index" class="flex items-center">
                <Checkbox :model-value="modelValue.selectedZoneQualifiers"
                    @update:model-value="updateData('selectedZoneQualifiers', $event)" :value="qualifier"
                    :inputId="`qualifier-${index}`" class="mr-3" />
                <label :for="`qualifier-${index}`" class="text-gray-700">
                    {{ qualifier }}
                </label>
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

const zoneQualifiers = [
    'Fresh',
    'Original',
    'Flip',
    'LOL',
    'Minimum 1:2 Profit Margin',
    'Big Brother Coverage'
]

const assetOptions = ['EUR/USD', 'GBP/USD', 'USD/JPY', 'AUD/USD']

const updateData = (key, value) => {
    const updatedData = { ...props.modelValue, [key]: value }
    emit('update:modelValue', updatedData)
    emit('progress-updated')
}
</script>
