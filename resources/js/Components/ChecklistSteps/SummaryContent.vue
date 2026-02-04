<template>
    <div class="space-y-4 sm:space-y-6">
        <div>
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Technicals</h3>
            <div class="space-y-1">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Location: {{ technicalsData.location || 'Not selected' }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Direction: {{ technicalsData.direction || 'Not selected' }}
                </p>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fundamentals</h3>
            <div class="space-y-1">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Valuation: {{ fundamentalsData.valuation || 'Not selected' }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Seasonality: {{ fundamentalsData.seasonalConfluence || 'Not selected' }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Non-Commercials: {{ fundamentalsData.nonCommercials || 'Not selected' }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    CoT Index: {{ fundamentalsData.cotIndex || 'Not selected' }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Commercials: {{ fundamentalsData.commercials || 'Not selected' }}
                </p>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Zone Qualifiers ({{ selectedZoneQualifiersCount }})
            </h3>
            <ul class="list-disc pl-5 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                <li v-for="qualifier in zoneQualifiersData.selectedZoneQualifiers" :key="qualifier">
                    {{ qualifier }}
                </li>
                <li v-if="zoneQualifiersData.selectedZoneQualifiers.length === 0"
                    class="text-gray-500 dark:text-gray-400 list-none ml-[-20px]">
                    None selected
                </li>
            </ul>
        </div>

        <!-- Directional Bias -->
        <div>
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Directional Bias</h3>
            <div v-if="directionalBias.hasEnoughData" class="space-y-1">
                <div class="flex items-center gap-2">
                    <Tag :value="directionalBias.biasDisplay" :severity="directionalBias.severity"
                        class="font-bold text-sm" />
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                        {{ directionalBias.confidence }}%
                    </span>
                </div>
            </div>
            <div v-else class="text-sm text-gray-500 dark:text-gray-400">
                No directional signals selected
            </div>
        </div>

        <div>
            <EvaluationScore ref="evaluationScoreComponent" :zone-qualifiers="zoneQualifiersData.selectedZoneQualifiers"
                :technicals="technicalsData" :fundamentals="fundamentalsData" :settings="settings"
                title="Evaluation Score" />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import EvaluationScore from '@/Components/UI/EvaluationScore.vue';

const evaluationScoreComponent = ref(null);

defineExpose({
    evaluationScoreComponent
})

defineProps({
    technicalsData: {
        type: Object,
        required: true
    },
    fundamentalsData: {
        type: Object,
        required: true
    },
    zoneQualifiersData: {
        type: Object,
        required: true
    },
    selectedZoneQualifiersCount: {
        type: Number,
        required: true
    },
    directionalBias: {
        type: Object,
        required: true
    },
    settings: {
        type: Object,
        default: null
    }
});
</script>
