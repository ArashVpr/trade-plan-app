<template>
    <div class="space-y-8 animate-fade-in-up">
        <h2
            class="text-2xl font-bold bg-gradient-to-r from-blue-900 to-blue-700 bg-clip-text text-transparent mb-6 flex items-center gap-3">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg shadow-sm">
                <i class="pi pi-map-marker text-blue-700 dark:text-blue-300 text-xl"></i>
            </div>
            Zone Qualifiers
        </h2>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm">
            <label
                class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">Trading
                Instrument</label>
            <Select :model-value="modelValue.symbol" @update:model-value="updateData('symbol', $event)"
                :options="symbolOptions" filter filterPlaceholder="Search or type..." optionLabel="label"
                optionValue="code" placeholder="Select an Asset" :loading="!symbolOptions.length" :pt="{
                    root: { class: 'w-full md:w-1/2 p-fluid bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 focus:ring-blue-500 text-slate-700 dark:text-slate-200' },
                    label: { class: 'text-slate-700 dark:text-slate-200' },
                    header: { class: 'bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 p-2 text-slate-700 dark:text-slate-200' },
                    list: { class: 'bg-white dark:bg-slate-900 p-0' },
                    option: { class: 'cursor-pointer px-4 py-2 transition-colors text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 [&[data-p-focused=\'true\']]:bg-slate-100 dark:[&[data-p-focused=\'true\']]:bg-slate-800 [&[data-p-highlight=\'true\']]:bg-blue-50 [&[data-p-highlight=\'true\']]:text-blue-700 dark:[&[data-p-highlight=\'true\']]:bg-blue-900/30 dark:[&[data-p-highlight=\'true\']]:text-blue-300' },
                    pcFilter: { root: { class: 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700' } }
                }">
                <template #option="slotProps">
                    <div class="flex flex-col py-1">
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ slotProps.option.code }}</span>
                        <span class="text-xs text-slate-500">{{ slotProps.option.label.split(' - ')[1] ||
                            slotProps.option.label }}</span>
                    </div>
                </template>
                <template #value="slotProps">
                    <div v-if="slotProps.value" class="flex items-center gap-2">
                        <i class="pi pi-chart-line text-blue-500"></i>
                        <span class="font-bold">{{ slotProps.value }}</span>
                    </div>
                    <span v-else class="text-slate-400">Select an Asset</span>
                </template>
            </Select>
            <p v-if="showSymbolError" class="mt-2 text-sm text-rose-600 dark:text-rose-400">
                Please select a symbol before submitting.
            </p>
        </div>

        <div>
            <div class="flex items-center justify-between mb-4 px-1">
                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Zone Characteristics</h3>
                <span class="text-xs font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-slate-500">
                    {{ modelValue.selectedZoneQualifiers.length }}/{{ zoneQualifiers.length }}
                </span>
            </div>

            <!-- Zone Qualifiers Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="(qualifier, index) in zoneQualifiers" :key="index" @click="toggleQualifier(qualifier.name)"
                    class="cursor-pointer relative overflow-hidden rounded-xl border-2 transition-all duration-200 p-4 flex items-start gap-4 group hover:shadow-lg active:scale-[0.99]"
                    :class="isSelected(qualifier.name)
                        ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20 shadow-md ring-1 ring-blue-200'
                        : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-slate-50 dark:hover:bg-slate-700'">
                    <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center transition-colors duration-300"
                        :class="isSelected(qualifier.name)
                            ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300'
                            : 'bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-400 group-hover:bg-white dark:group-hover:bg-slate-600 group-hover:text-blue-500 dark:group-hover:text-blue-400'">
                        <i :class="qualifier.icon" class="text-xl"></i>
                    </div>

                    <div class="flex-1 min-w-0 pt-0.5">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-bold text-base transition-colors"
                                :class="isSelected(qualifier.name) ? 'text-blue-900 dark:text-blue-100' : 'text-slate-700 dark:text-slate-200'">
                                {{ qualifier.name }}
                            </h4>
                            <i v-if="isSelected(qualifier.name)"
                                class="pi pi-check-circle text-blue-600 animate-zoom-in"></i>
                        </div>
                        <p class="text-xs leading-relaxed transition-colors"
                            :class="isSelected(qualifier.name) ? 'text-blue-800/80 dark:text-blue-200/70' : 'text-slate-500 dark:text-slate-400'">
                            {{ qualifier.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    },
    instruments: {
        type: Array,
        default: () => []
    },
    showSymbolError: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue', 'progress-updated'])

const zoneQualifiers = [
    { name: 'Fresh', icon: 'pi pi-sparkles', description: 'Zone has not been touched yet (Virgin Zone).' },
    { name: 'Original', icon: 'pi pi-star', description: 'The origin of the move not a reaction to another zone.' },
    { name: 'Flip', icon: 'pi pi-sort-alt', description: 'Previous Demand becoming Supply (or vice-versa).' },
    { name: 'LOL', icon: 'pi pi-clone', description: 'Level on top of a level.' },
    { name: 'Minimum 1:2 Profit Margin', icon: 'pi pi-chart-line', description: 'Favorable risk to reward ratio.' },
    { name: 'Big Brother Coverage', icon: 'pi pi-eye', description: 'The zone is covered by a higher timeframe zone.' }
]

// Check if a qualifier is selected
const isSelected = (qualifierName) => {
    return props.modelValue.selectedZoneQualifiers.includes(qualifierName)
}

// Toggle qualifier selection
const toggleQualifier = (qualifierName) => {
    const currentSelected = [...props.modelValue.selectedZoneQualifiers]
    const index = currentSelected.indexOf(qualifierName)

    if (index > -1) {
        currentSelected.splice(index, 1)
    } else {
        currentSelected.push(qualifierName)
    }

    updateData('selectedZoneQualifiers', currentSelected)
}

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
