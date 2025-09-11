<template>
    <div class="space-y-6">
        <h2 class="text-2xl font-semibold text-blue-900 mb-4 flex items-center gap-2">
            <i class="pi pi-map-marker text-blue-900"></i>
            Zone Qualifiers
        </h2>

        <div class="field">
            <IftaLabel>
                <Select :model-value="modelValue.symbol" @update:model-value="updateData('symbol', $event)"
                    :options="symbolOptions" filter filterPlaceholder="Search instruments..." optionLabel="label"
                    optionValue="code" placeholder="" :loading="!symbolOptions.length" class="w-full">
                    <!-- Custom option template to show both symbol and name -->
                    <template #option="slotProps">
                        <div class="flex flex-col">
                            <span class="font-semibold">{{ slotProps.option.code }}</span>
                            <span class="text-sm text-gray-500">{{ slotProps.option.label.split(' - ')[1] ||
                                slotProps.option.label }}</span>
                        </div>
                    </template>
                </Select>
                <label for="instrument">Select Instrument</label>
            </IftaLabel>
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
import { computed } from 'vue'
import IftaLabel from 'primevue/iftalabel';


const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    },
    instruments: {
        type: Array,
        default: () => []
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

// Format symbols for Select component
const symbolOptions = computed(() => {
    if (props.instruments && props.instruments.length > 0) {
        // Use full instrument data for enhanced search
        return props.instruments.map(inst => ({
            code: inst.symbol,  // Value to store (XAUUSD)
            label: `${inst.symbol} - ${inst.name}` // For display and search: "XAUUSD - Gold vs US Dollar"
        }))
    } else {
        // Fallback to original hardcoded list
        const fallbackSymbols = ['EURUSD', 'GBPUSD', 'USDJPY', 'AUDUSD', 'USDCAD', 'NZDUSD', 'EURGBP', 'EURJPY', 'GBPJPY', 'AUDJPY']
        return fallbackSymbols.map(symbol => ({
            code: symbol,
            label: symbol
        }))
    }
})

const updateData = (key, value) => {
    const updatedData = { ...props.modelValue, [key]: value }
    emit('update:modelValue', updatedData)
    emit('progress-updated')
}
</script>
