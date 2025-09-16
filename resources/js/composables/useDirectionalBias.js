import { computed } from 'vue'

/**
 * Composable for calculating directional bias and confidence from checklist inputs
 * Uses weighted approach based on ChecklistWeights to determine trading direction
 */
export function useDirectionalBias(technicals, fundamentals, settings) {
    /**
     * Map checklist selections to directional signal values
     */
    const getSignalValue = (category, selection) => {
        if (!selection) return 0
        
        switch (category) {
            case 'location':
                return {
                    'Very Cheap': +1,
                    'Cheap': +1,
                    'EQ': 0,
                    'Expensive': -1,
                    'Very Expensive': -1
                }[selection] || 0
                
            case 'valuation':
                return {
                    'Undervalued': +1,
                    'Neutral': 0,
                    'Overvalued': -1
                }[selection] || 0
                
            case 'seasonality':
                return {
                    'Bullish': +1,
                    'Neutral': 0,
                    'Bearish': -1
                }[selection] || 0
                
            case 'nonCommercials':
                return {
                    'Bullish Divergence': +1,
                    'Neutral': 0,
                    'Bearish Divergence': -1
                }[selection] || 0
                
            case 'cotIndex':
                return {
                    'Bullish': +1,
                    'Neutral': 0,
                    'Bearish': -1
                }[selection] || 0
                
            default:
                return 0
        }
    }

    /**
     * Calculate weighted directional bias and confidence
     */
    const directionalBias = computed(() => {
        if (!settings.value || Object.keys(settings.value).length === 0) {
            return {
                bias: 'Neutral',
                side: null,
                confidence: 0,
                reasons: [],
                severity: 'secondary',
                hasEnoughData: false
            }
        }

        // Get signal values
        const techValue = getSignalValue('location', technicals.value?.location)
        const valuationValue = getSignalValue('valuation', fundamentals.value?.valuation)
        const seasonalityValue = getSignalValue('seasonality', fundamentals.value?.seasonalConfluence)
        const nonCommercialsValue = getSignalValue('nonCommercials', fundamentals.value?.nonCommercials)
        const cotValue = getSignalValue('cotIndex', fundamentals.value?.cotIndex)

        // Get weights from settings (simplified since all tech signals now use ±1)
        const techWeight = techValue !== 0 ? (
            ['Very Cheap', 'Very Expensive'].includes(technicals.value?.location)
                ? Number(settings.value.technical_very_exp_chp_weight || 0)
                : Number(settings.value.technical_exp_chp_weight || 0)
        ) : 0
        
        const valuationWeight = Number(settings.value.fundamental_valuation_weight || 0)
        const seasonalityWeight = Number(settings.value.fundamental_seasonal_weight || 0)
        const nonCommercialsWeight = Number(settings.value.fundamental_noncommercial_divergence_weight || 0)
        const cotWeight = Number(settings.value.fundamental_cot_index_weight || 0)

        // Calculate weighted contributions
        const techContribution = techValue * techWeight
        const valuationContribution = valuationValue * valuationWeight
        const seasonalityContribution = seasonalityValue * seasonalityWeight
        const nonCommercialsContribution = nonCommercialsValue * nonCommercialsWeight
        const cotContribution = cotValue * cotWeight

        const weightedSum = techContribution + valuationContribution + seasonalityContribution + 
                           nonCommercialsContribution + cotContribution

        // Calculate maximum possible weighted sum for confidence calculation
        const maxTechWeight = Math.max(
            Number(settings.value.technical_very_exp_chp_weight || 0),
            Number(settings.value.technical_exp_chp_weight || 0)
        )
        
        const maxSum = (1 * maxTechWeight) + valuationWeight + seasonalityWeight + 
                       nonCommercialsWeight + cotWeight

        // Determine bias and confidence
        const bias = weightedSum > 0 ? 'Bullish' : weightedSum < 0 ? 'Bearish' : 'Neutral'
        const side = bias === 'Bullish' ? 'Long' : bias === 'Bearish' ? 'Short' : null
        const confidence = maxSum > 0 ? Math.round((Math.abs(weightedSum) / maxSum) * 100) : 0
        
        // Enhanced bias display with strength levels
        const getBiasDisplay = () => {
            if (confidence <= 20) return { label: 'NEUTRAL', severity: 'secondary' }
            
            if (bias === 'Bullish') {
                if (confidence >= 81) return { label: 'STRONG BUY', severity: 'success' }
                if (confidence >= 51) return { label: 'BUY', severity: 'success' }
                return { label: 'LEAN BUY', severity: 'info' }
            } else {
                if (confidence >= 81) return { label: 'STRONG SELL', severity: 'danger' }
                if (confidence >= 51) return { label: 'SELL', severity: 'danger' }
                return { label: 'LEAN SELL', severity: 'warn' }
            }
        }
        
        const biasDisplay = getBiasDisplay()
        const severity = biasDisplay.severity

        // Check if we have enough directional data (count non-zero contributions)
        const directionalInputCount = [techContribution, valuationContribution, seasonalityContribution, 
                                     nonCommercialsContribution, cotContribution].filter(c => c !== 0).length
        const hasEnoughData = directionalInputCount > 0 && confidence > 0

        return {
            bias,
            side,
            confidence,
            biasDisplay: biasDisplay.label,
            severity,
            hasEnoughData,
            directionalInputCount,
            weightedSum: Math.round(weightedSum),
            maxSum: Math.round(maxSum)
        }
    })

    return {
        directionalBias
    }
}