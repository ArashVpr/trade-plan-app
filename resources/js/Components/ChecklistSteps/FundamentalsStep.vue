<template>
    <div class="space-y-8 animate-fade-in-up">
        <h2
            class="text-2xl font-bold bg-gradient-to-r from-blue-900 to-blue-700 bg-clip-text text-transparent mb-6 flex items-center gap-3">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg shadow-sm">
                <i class="pi pi-globe text-blue-700 dark:text-blue-300 text-xl"></i>
            </div>
            Fundamental Analysis
        </h2>

        <!-- Exclude Fundamentals Toggle -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5">
            <div class="flex items-start gap-3">
                <Checkbox v-model="excludeFundamentals" :binary="true" input-id="exclude_fundamentals"
                    @update:model-value="handleExcludeToggle" />
                <div class="flex-1">
                    <label for="exclude_fundamentals"
                        class="font-semibold text-slate-800 dark:text-slate-200 cursor-pointer block mb-1">
                        Skip fundamental analysis for this trade
                    </label>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Enable this if you're taking a purely technical trade. Your evaluation score will be calculated
                        based on zones and technicals only.
                    </p>
                </div>
            </div>
        </div>

        <!-- Helper Message when fundamentals are excluded -->
        <Message v-if="excludeFundamentals" severity="info" :closable="false" class="shadow-sm">
            <template #icon>
                <i class="pi pi-info-circle text-lg"></i>
            </template>
            <div class="ml-2">
                <strong>Technical Trade:</strong> These selections won't affect your score. Weights will be
                redistributed
                proportionally across zones and technicals.
            </div>
        </Message>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8"
            :class="{ 'opacity-50 pointer-events-none': excludeFundamentals }">
            <!-- Valuation -->
            <div class="bg-slate-50 dark:bg-slate-900/50 p-5 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2 mb-4">
                    <i
                        class="pi pi-dollar text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/30 p-1.5 rounded-md"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-200">Valuation</h3>
                    <i v-tooltip.top="valuationTooltip"
                        class="pi pi-question-circle text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 cursor-help ml-auto"></i>
                </div>
                <div class="space-y-2">
                    <button v-for="opt in valuationOptions" :key="opt.value" @click="updateData('valuation', opt.value)"
                        class="w-full text-left p-3 rounded-xl border transition-all duration-200 flex items-center justify-between group cursor-pointer"
                        :class="modelValue.valuation === opt.value
                            ? getSelectionClass(opt.sentiment)
                            : 'border-slate-200 bg-white hover:border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:hover:border-slate-600 dark:text-slate-300'">
                        <span class="font-medium">{{ opt.value }}</span>
                        <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                            :class="modelValue.valuation === opt.value ? 'border-current' : 'border-slate-300 dark:border-slate-600'">
                            <div v-if="modelValue.valuation === opt.value" class="w-2.5 h-2.5 rounded-full bg-current">
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Seasonality -->
            <div class="bg-slate-50 dark:bg-slate-900/50 p-5 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2 mb-4">
                    <i
                        class="pi pi-calendar text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 p-1.5 rounded-md"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-200">Seasonality</h3>
                    <i v-tooltip.top="seasonalityTooltip"
                        class="pi pi-question-circle text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 cursor-help ml-auto"></i>
                </div>
                <div class="space-y-2">
                    <button v-for="opt in seasonalOptions" :key="opt.value"
                        @click="updateData('seasonalConfluence', opt.value)"
                        class="w-full text-left p-3 rounded-xl border transition-all duration-200 flex items-center justify-between group cursor-pointer"
                        :class="modelValue.seasonalConfluence === opt.value
                            ? getSelectionClass(opt.sentiment)
                            : 'border-slate-200 bg-white hover:border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:hover:border-slate-600 dark:text-slate-300'">
                        <span class="font-medium">{{ opt.value }}</span>
                        <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                            :class="modelValue.seasonalConfluence === opt.value ? 'border-current' : 'border-slate-300 dark:border-slate-600'">
                            <div v-if="modelValue.seasonalConfluence === opt.value"
                                class="w-2.5 h-2.5 rounded-full bg-current">
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Non-Commercials -->
            <div class="bg-slate-50 dark:bg-slate-900/50 p-5 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2 mb-4">
                    <i
                        class="pi pi-users text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/30 p-1.5 rounded-md"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-200">Non-Commercials</h3>
                    <i v-tooltip.top="nonCommercialsTooltip"
                        class="pi pi-question-circle text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 cursor-help ml-auto"></i>
                </div>
                <div class="space-y-2">
                    <button v-for="opt in nonCommercialsOptions" :key="opt.value"
                        @click="updateData('nonCommercials', opt.value)"
                        class="w-full text-left p-3 rounded-xl border transition-all duration-200 flex items-center justify-between group cursor-pointer"
                        :class="modelValue.nonCommercials === opt.value
                            ? getSelectionClass(opt.sentiment)
                            : 'border-slate-200 bg-white hover:border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:hover:border-slate-600 dark:text-slate-300'">
                        <span class="font-medium">{{ opt.value }}</span>
                        <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                            :class="modelValue.nonCommercials === opt.value ? 'border-current' : 'border-slate-300 dark:border-slate-600'">
                            <div v-if="modelValue.nonCommercials === opt.value"
                                class="w-2.5 h-2.5 rounded-full bg-current">
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- CoT Index -->
            <div class="bg-slate-50 dark:bg-slate-900/50 p-5 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2 mb-4">
                    <i
                        class="pi pi-chart-bar text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/30 p-1.5 rounded-md"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-200">CoT Index</h3>
                    <i v-tooltip.top="cotIndexTooltip"
                        class="pi pi-question-circle text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 cursor-help ml-auto"></i>
                </div>
                <div class="space-y-2">
                    <button v-for="opt in cotIndexOptions" :key="opt.value" @click="updateData('cotIndex', opt.value)"
                        class="w-full text-left p-3 rounded-xl border transition-all duration-200 flex items-center justify-between group cursor-pointer"
                        :class="modelValue.cotIndex === opt.value
                            ? getSelectionClass(opt.sentiment)
                            : 'border-slate-200 bg-white hover:border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:hover:border-slate-600 dark:text-slate-300'">
                        <span class="font-medium">{{ opt.value }}</span>
                        <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                            :class="modelValue.cotIndex === opt.value ? 'border-current' : 'border-slate-300 dark:border-slate-600'">
                            <div v-if="modelValue.cotIndex === opt.value" class="w-2.5 h-2.5 rounded-full bg-current">
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    },
    excludeFundamentals: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue', 'update:excludeFundamentals', 'progress-updated'])

const excludeFundamentals = ref(props.excludeFundamentals)

watch(() => props.excludeFundamentals, (newVal) => {
    excludeFundamentals.value = newVal
})

const handleExcludeToggle = (value) => {
    emit('update:excludeFundamentals', value)
    emit('progress-updated')
}

// Sentiment helper: 1=Positive/Buy, -1=Negative/Sell, 0=Neutral
// Note: Sentiments are context dependent. 
// For Valuation: Undervalued is usually Bullish (Buy), Overvalued is Bearish (Sell).
// For simplicity, we'll map them to Green/Red concept.

const valuationOptions = [
    { value: 'Undervalued', sentiment: 'bullish' },
    { value: 'Neutral', sentiment: 'neutral' },
    { value: 'Overvalued', sentiment: 'bearish' }
]
const seasonalOptions = [
    { value: 'Bullish', sentiment: 'bullish' },
    { value: 'Neutral', sentiment: 'neutral' },
    { value: 'Bearish', sentiment: 'bearish' }
]
const nonCommercialsOptions = [
    { value: 'Bullish Divergence', sentiment: 'bullish' },
    { value: 'Neutral', sentiment: 'neutral' },
    { value: 'Bearish Divergence', sentiment: 'bearish' }
]
const cotIndexOptions = [
    { value: 'Bullish', sentiment: 'bullish' },
    { value: 'Neutral', sentiment: 'neutral' },
    { value: 'Bearish', sentiment: 'bearish' }
]

const valuationTooltip = 'Measures whether price is undervalued, neutral, or overvalued versus fair value.'
const seasonalityTooltip = 'Directional bias based on recurring seasonal patterns.'
const nonCommercialsTooltip = 'Large speculator positioning and divergence from the COT report.'
const cotIndexTooltip = 'COT Index showing extreme positioning relative to recent history.'

const getSelectionClass = (sentiment) => {
    switch (sentiment) {
        case 'bullish':
            return 'border-emerald-500 bg-emerald-50 text-emerald-700 shadow-sm ring-1 ring-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-500/50 dark:text-emerald-300 dark:ring-emerald-800/50 dark:shadow-none'
        case 'bearish':
            return 'border-rose-500 bg-rose-50 text-rose-700 shadow-sm ring-1 ring-rose-200 dark:bg-rose-900/20 dark:border-rose-500/50 dark:text-rose-300 dark:ring-rose-800/50 dark:shadow-none'
        default:
            return 'border-slate-500 bg-slate-50 text-slate-700 shadow-sm ring-1 ring-slate-200 dark:bg-slate-800 dark:border-slate-500 dark:text-slate-300 dark:ring-slate-700 dark:shadow-none'
    }
}

const updateData = (key, value) => {
    const updatedData = { ...props.modelValue, [key]: value }
    emit('update:modelValue', updatedData)
    emit('progress-updated')
}
</script>
