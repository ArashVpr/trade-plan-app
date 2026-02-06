import { computed } from 'vue'

/**
 * Calculate directional bias from technical and fundamental analysis
 * @param {Ref<Object>} technicals - Technical analysis data
 * @param {Ref<Object>} fundamentals - Fundamental analysis data
 * @param {Ref<Object>} settings - ChecklistWeights settings
 * @param {Ref<Boolean>} excludeFundamentals - Whether fundamentals are excluded
 * @returns {Object} Directional bias with display, severity, and confidence
 */
export function useDirectionalBias(technicals, fundamentals, settings, excludeFundamentals = null) {
    const directionalBias = computed(() => {
        let signals = 0
        let maxSignals = 0
        let biasType = 'Neutral'

        // Technical Location Analysis
        const location = technicals.value?.location
        const direction = technicals.value?.direction

        // Get weights
        const techVeryExpChp = Number(settings.value?.technical_very_exp_chp_weight || 0)
        const techExpChp = Number(settings.value?.technical_exp_chp_weight || 0)
        const fundValuation = Number(settings.value?.fundamental_valuation_weight || 0)
        const fundSeasonal = Number(settings.value?.fundamental_seasonal_weight || 0)
        const fundNonComm = Number(settings.value?.fundamental_noncommercial_divergence_weight || 0)
        const fundCot = Number(settings.value?.fundamental_cot_index_weight || 0)

        // Technical Location signals
        if (location === 'Very Cheap') {
            signals += techVeryExpChp
            biasType = 'Bullish'
        } else if (location === 'Cheap') {
            signals += techExpChp
            biasType = 'Bullish'
        } else if (location === 'Very Expensive') {
            signals -= techVeryExpChp
            biasType = 'Bearish'
        } else if (location === 'Expensive') {
            signals -= techExpChp
            biasType = 'Bearish'
        }

        maxSignals += Math.max(techVeryExpChp, techExpChp)

        // Technical Direction signals
        if (direction === 'Correction') {
            signals += 6
            if (biasType === 'Neutral') biasType = 'Bullish'
        } else if (direction === 'Impulsion') {
            signals += 12
            if (biasType === 'Neutral') biasType = 'Bullish'
        }

        maxSignals += 12

        // Only process fundamentals if not excluded
        if (!excludeFundamentals?.value) {
            const valuation = fundamentals.value?.valuation
            const seasonality = fundamentals.value?.seasonalConfluence
            const nonCommercials = fundamentals.value?.nonCommercials
            const cotIndex = fundamentals.value?.cotIndex

            // Fundamental Valuation signals
            if (valuation === 'Undervalued') {
                signals += fundValuation
                if (biasType === 'Neutral') biasType = 'Bullish'
            } else if (valuation === 'Overvalued') {
                signals -= fundValuation
                if (biasType === 'Neutral') biasType = 'Bearish'
            }

            maxSignals += fundValuation

            // Fundamental Seasonality signals
            if (seasonality === 'Bullish') {
                signals += fundSeasonal
                if (biasType === 'Neutral') biasType = 'Bullish'
            } else if (seasonality === 'Bearish') {
                signals -= fundSeasonal
                if (biasType === 'Neutral') biasType = 'Bearish'
            }

            maxSignals += fundSeasonal

            // Fundamental Non-Commercials signals
            if (nonCommercials === 'Bullish Divergence') {
                signals += fundNonComm
                if (biasType === 'Neutral') biasType = 'Bullish'
            } else if (nonCommercials === 'Bearish Divergence') {
                signals -= fundNonComm
                if (biasType === 'Neutral') biasType = 'Bearish'
            }

            maxSignals += fundNonComm

            // Fundamental CoT Index signals
            if (cotIndex === 'Bullish') {
                signals += fundCot
                if (biasType === 'Neutral') biasType = 'Bullish'
            } else if (cotIndex === 'Bearish') {
                signals -= fundCot
                if (biasType === 'Neutral') biasType = 'Bearish'
            }

            maxSignals += fundCot
        }

        // Calculate confidence as percentage
        const confidence = maxSignals > 0 ? Math.round((Math.abs(signals) / maxSignals) * 100) : 0
        const hasEnoughData = confidence > 0

        // Refine bias based on actual signals when there's conflicting data
        if (signals > 0) {
            biasType = 'Bullish'
        } else if (signals < 0) {
            biasType = 'Bearish'
        } else {
            biasType = 'Neutral'
        }

        // Determine severity for PrimeVue components
        let severity = 'secondary'
        if (biasType === 'Bullish' && confidence >= 51) severity = 'success'
        else if (biasType === 'Bullish' && confidence <= 50) severity = 'info'
        else if (biasType === 'Bearish' && confidence >= 51) severity = 'danger'
        else if (biasType === 'Bearish' && confidence <= 50) severity = 'warning'
        else severity = 'secondary'

        return {
            biasDisplay: biasType,
            confidence,
            hasEnoughData,
            severity,
        }
    })

    return { directionalBias }
}
