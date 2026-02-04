<template>
    <div class="evaluation-score flex flex-col items-center animate-fade-in">
        <h3
            class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-900 to-blue-600 dark:from-blue-300 dark:to-blue-100 mb-6 font-display uppercase tracking-wide">
            {{ title }}
        </h3>

        <div class="relative group">
            <!-- Animate the Knob value -->
            <Knob :model-value="animatedScore" :min="0" :max="100" :valueColor="getScoreColor(animatedScore)" readonly
                :size="140" valueTemplate="{value}%" :strokeWidth="8" rangeColor="var(--p-surface-200)"
                class="transition-transform duration-500 group-hover:scale-105" />
            <!-- Glow Effect -->
            <div class="absolute inset-0 rounded-full blur-2xl opacity-30 -z-10 transition-colors duration-500"
                :style="{ backgroundColor: getScoreColor(animatedScore) }"></div>
        </div>

        <div v-if="animatedScore >= 100 && showPerfectMessage"
            class="mt-6 font-bold text-lg text-center flex items-center justify-center gap-2 animate-bounce text-amber-500">
            <i class="pi pi-star-fill text-xl"></i>
            <span>All System Green!</span>
            <i class="pi pi-star-fill text-xl"></i>
        </div>

        <div v-else-if="animatedScore >= SCORE_THRESHOLDS.WARNING"
            class="mt-4 text-emerald-600 font-medium animate-pulse">
            Strong Setup
        </div>

        <!-- Score breakdown -->
        <div v-if="showBreakdown && breakdown"
            class="mt-8 w-full max-w-sm text-sm text-gray-600 dark:text-gray-400 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <h4 class="font-semibold mb-3 text-xs uppercase tracking-wider text-slate-500">Score Audit</h4>
            <div v-for="(item, key) in breakdown" :key="key"
                class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                <span>{{ item.label }}</span>
                <Badge :value="item.value" :severity="item.severity" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import Knob from 'primevue/knob'
import { SCORE_THRESHOLDS, getScoreSeverity as getSeverityFromConstants } from '@/constants/evaluationScores'

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

// Animated Score State
const animatedScore = ref(0)
const animationDuration = 800 // ms

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

// Watch for score changes and animate
watch(calculatedScore, (newValue) => {
    animateValue(newValue)
}, { immediate: true })

function animateValue(target) {
    const start = animatedScore.value
    const change = target - start
    const startTime = performance.now()

    function update(currentTime) {
        const elapsed = currentTime - startTime
        const progress = Math.min(elapsed / animationDuration, 1)

        // Easing function: easeOutQuart
        const ease = 1 - Math.pow(1 - progress, 4)

        animatedScore.value = Math.round(start + (change * ease))

        if (progress < 1) {
            requestAnimationFrame(update)
        }
    }

    requestAnimationFrame(update)
}

const getScoreSeverity = (score) => {
    return getSeverityFromConstants(score)
}

const getScoreColor = (score) => {
    if (score < SCORE_THRESHOLDS.DANGER) return '#ef4444' // red-500
    if (score < SCORE_THRESHOLDS.WARNING) return '#f59e0b' // amber-500
    return '#10b981' // emerald-500
}

// Expose the calculated score for parent components
defineExpose({
    calculatedScore
})
</script>

<style scoped>
/* Custom animations if not in global css */
@keyframes fade-in-up {
    0% {
        opacity: 0;
        transform: translateY(10px);
    }

    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.5s ease-out forwards;
}
</style>
