<template>
    <div class="space-y-6">
        <h2 class="text-2xl font-semibold text-blue-900 mb-4 flex items-center gap-2">
            <i class="pi pi-globe text-blue-900"></i>
            Fundamental Analysis
        </h2>

        <div class="grid grid-cols-1 gap-6">
            <div class="field">
                <label class="block text-sm font-medium mb-2">Valuation</label>
                <Select :model-value="modelValue.valuation" @update:model-value="updateData('valuation', $event)"
                    :options="valuationOptions" placeholder="Select Valuation" class="w-full" />
            </div>

            <div class="field">
                <label class="block text-sm font-medium mb-2">Seasonal Confluence</label>
                <Select :model-value="modelValue.seasonalConfluence"
                    @update:model-value="updateData('seasonalConfluence', $event)" :options="seasonalOptions"
                    placeholder="Select Seasonal Confluence" class="w-full" />
            </div>

            <div class="field">
                <label class="block text-sm font-medium mb-2">Non-Commercials</label>
                <Select :model-value="modelValue.nonCommercials"
                    @update:model-value="updateData('nonCommercials', $event)" :options="nonCommercialsOptions"
                    placeholder="Select Non-Commercials" class="w-full" />
            </div>

            <div class="field">
                <label class="block text-sm font-medium mb-2">CoT Index</label>
                <Select :model-value="modelValue.cotIndex" @update:model-value="updateData('cotIndex', $event)"
                    :options="cotIndexOptions" placeholder="Select CoT Index" class="w-full" />
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

const valuationOptions = ['Overvalued', 'Neutral', 'Undervalued']
const seasonalOptions = ['Yes', 'No']
const nonCommercialsOptions = ['Divergence', 'No-Divergence']
const cotIndexOptions = ['Bullish', 'Neutral', 'Bearish']

const updateData = (key, value) => {
    const updatedData = { ...props.modelValue, [key]: value }
    emit('update:modelValue', updatedData)
    emit('progress-updated')
}
</script>
