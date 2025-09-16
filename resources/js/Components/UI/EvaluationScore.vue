<template>
    <div class="evaluation-score">
        <h3 class="text-xl font-semibold text-blue-900 mb-2">{{ title }}</h3>
        <Tag :value="`${calculatedScore}/100`" :severity="getScoreSeverity(calculatedScore)" :class="tagClass" />
        <div v-if="calculatedScore === 100 && showPerfectMessage"
            class="text-yellow-500 mt-3 font-bold text-lg text-center">
            ★ All Stars Aligned ★
        </div>

        <!-- Score breakdown (optional) -->
        <div v-if="showBreakdown && breakdown" class="mt-3 text-sm text-gray-600">
            <div v-for="(item, key) in breakdown" :key="key" class="flex justify-between">
                <span>{{ item.label }}:</span>
                <span class="font-medium">{{ item.value }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    // Option 1: Pass pre-calculated score
    score: {
        type: Number,
        default: null,
        validator: (value) => value === null || (value >= 0 && value <= 100)
    },

    // Option 2: Pass data for calculation
    zoneQualifiers: {
        type: Array,
        default: () => []
    },
    technicals: {
        type: Object,
        default: () => ({ location: '', direction: '' })
    },
    fundamentals: {
        type: Object,
        default: () => ({
            valuation: '',
            seasonalConfluence: '',
            nonCommercials: '',
            cotIndex: ''
        })
    },
    settings: {
        type: Object,
        default: () => ({})
    },

    // Display options
    title: {
        type: String,
        default: 'Evaluation Score'
    },
    showPerfectMessage: {
        type: Boolean,
        default: true
    },
    showProgressBar: {
        type: Boolean,
        default: false
    },
    progressLabel: {
        type: String,
        default: 'Setup Progress'
    },
    showBreakdown: {
        type: Boolean,
        default: false
    },
    breakdown: {
        type: Object,
        default: null
    },
    size: {
        type: String,
        default: 'normal',
        validator: (value) => ['small', 'normal', 'large'].includes(value)
    }
})

// Zone qualifiers mapping
const zoneQualifierNames = [
    'Fresh',
    'Original',
    'Flip',
    'LOL',
    'Minimum 1:2 Profit Margin',
    'Big Brother Coverage'
]

const calculatedScore = computed(() => {
    // If pre-calculated score is provided, use it
    if (props.score !== null) {
        return props.score
    }

    // Otherwise, calculate the score based on provided data
    if (!props.settings || Object.keys(props.settings).length === 0) {
        return 0
    }

    // 1. Compute raw weighted score
    let raw = 0
    const zoneKeys = ['fresh', 'original', 'flip', 'lol', 'min_profit_margin', 'big_brother']

    zoneKeys.forEach((key, idx) => {
        const qualifierName = zoneQualifierNames[idx]
        if (props.zoneQualifiers.includes(qualifierName)) {
            raw += Number(props.settings[`zone_${key}_weight`] || 0)
        }
    })

    // Technicals: Location
    if (['Very Cheap', 'Very Expensive'].includes(props.technicals.location)) {
        raw += Number(props.settings.technical_very_exp_chp_weight || 0)
    } else if (['Cheap', 'Expensive'].includes(props.technicals.location)) {
        raw += Number(props.settings.technical_exp_chp_weight || 0)
    }

    // Technicals: Direction
    if (props.technicals.direction === 'Impulsion') {
        raw += Number(props.settings.technical_direction_impulsive_weight || 0)
    } else if (props.technicals.direction === 'Correction') {
        raw += Number(props.settings.technical_direction_correction_weight || 0)
    }

    // Fundamentals
    if (['Undervalued', 'Overvalued'].includes(props.fundamentals.valuation)) {
        raw += Number(props.settings.fundamental_valuation_weight || 0)
    }
    if (['Bullish', 'Bearish'].includes(props.fundamentals.seasonalConfluence)) {
        raw += Number(props.settings.fundamental_seasonal_weight || 0)
    }
    if (['Bullish Divergence', 'Bearish Divergence'].includes(props.fundamentals.nonCommercials)) {
        raw += Number(props.settings.fundamental_noncommercial_divergence_weight || 0)
    }
    if (['Bullish', 'Bearish'].includes(props.fundamentals.cotIndex)) {
        raw += Number(props.settings.fundamental_cot_index_weight || 0)
    }

    // 2. Compute max possible score based on one selection per category
    const zoneMax = zoneKeys.reduce((sum, key) => sum + Number(props.settings[`zone_${key}_weight`] || 0), 0)

    // Technicals: Location (pick the higher of very_exp or exp)
    const locHigh = Math.max(
        Number(props.settings.technical_very_exp_chp_weight || 0),
        Number(props.settings.technical_exp_chp_weight || 0)
    )

    // Technicals: Direction (pick the higher of impulsive or correction)
    const dirHigh = Math.max(
        Number(props.settings.technical_direction_impulsive_weight || 0),
        Number(props.settings.technical_direction_correction_weight || 0)
    )

    // Fundamentals: sum of all criteria
    const fundMax =
        Number(props.settings.fundamental_valuation_weight || 0) +
        Number(props.settings.fundamental_seasonal_weight || 0) +
        Number(props.settings.fundamental_noncommercial_divergence_weight || 0) +
        Number(props.settings.fundamental_cot_index_weight || 0)

    const max = zoneMax + locHigh + dirHigh + fundMax

    // 3. Normalize to a 0–100 scale
    return max > 0 ? Math.round((raw / max) * 100) : 0
})

const tagClass = computed(() => {
    const classes = ['font-bold']

    if (props.size === 'small') {
        classes.push('text-lg px-3 py-1')
    } else if (props.size === 'large') {
        classes.push('text-3xl px-6 py-3')
    } else {
        classes.push('text-2xl px-4 py-2')
    }

    return classes.join(' ')
})

const getScoreSeverity = (score) => {
    if (score < 50) return 'danger'
    if (score <= 80) return 'warning'
    return 'success'
}

// Expose the calculated score for parent components
defineExpose({
    calculatedScore
})
</script>

<style scoped>
/* Custom ProgressBar colors */
:deep(.progress-danger .p-progressbar-value) {
    background: #dc2626;
}

:deep(.progress-warning .p-progressbar-value) {
    background: #eab308;
}

:deep(.progress-success .p-progressbar-value) {
    background: #16a34a;
}
</style>
