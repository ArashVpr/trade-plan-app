<template>
    <div class="max-w-6xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Trade Setup Checklist</h1>

        <!-- Step Indicator -->
        <Card class="mb-6">
            <template #content>
                <Steps :model="stepItems" :readonly="false" class="mb-4" />
            </template>
        </Card>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Wizard Steps -->
            <div class="col-span-2">
                <Card>
                    <template #content>
                        <!-- Step 1: Zone Qualifiers -->
                        <ZoneQualifiersStep v-if="currentStep === 1" v-model="zoneQualifiersData"
                            @progress-updated="updateProgress" />

                        <!-- Step 2: Technicals -->
                        <TechnicalsStep v-if="currentStep === 2" v-model="technicalsData"
                            @progress-updated="updateProgress" />

                        <!-- Step 3: Fundamentals -->
                        <FundamentalsStep v-if="currentStep === 3" v-model="fundamentalsData"
                            @progress-updated="updateProgress" />

                        <!-- Step 4: Order Entry -->
                        <OrderEntryStep v-if="currentStep === 4" v-model="orderEntryData"
                            :asset="zoneQualifiersData.asset" @progress-updated="updateProgress" />

                        <!-- Navigation Buttons -->
                        <div class="mt-8 flex justify-between items-center">
                            <Button label="Reset" icon="pi pi-refresh" severity="secondary" @click="resetWizard" />

                            <div class="flex gap-2">
                                <Button v-if="currentStep > 1" label="Previous" icon="pi pi-arrow-left"
                                    severity="secondary" @click="currentStep--" />
                                <Button v-if="currentStep < steps.length" label="Next" icon="pi pi-arrow-right"
                                    iconPos="right" @click="currentStep++" :disabled="!canProceed" />
                                <Button v-if="currentStep === steps.length" label="Submit Checklist" icon="pi pi-check"
                                    @click="submitChecklist" :disabled="!canSubmit" />
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-6">
                            <div class="flex justify-between text-sm mb-2">
                                <span>Setup Progress</span>
                                <span>{{ evaluationScore }}/100</span>
                            </div>
                            <ProgressBar :value="evaluationScore" :class="{
                                'progress-danger': evaluationScore < 50,
                                'progress-warning': evaluationScore >= 50 && evaluationScore <= 80,
                                'progress-success': evaluationScore > 80
                            }" />
                        </div>
                    </template>
                </Card>
            </div>
            <!-- Summary Section -->
            <Card>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="pi pi-chart-bar text-blue-900"></i>
                        <span class="text-blue-900">Trade Setup Summary</span>
                    </div>
                </template>
                <template #content>
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Technicals</h3>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-600">Location: {{ technicalsData.location || 'Not selected'
                                    }}
                                </p>
                                <p class="text-sm text-gray-600">Direction: {{ technicalsData.direction || 'Not                                    selected' }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Fundamentals</h3>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-600">Valuation: {{ fundamentalsData.valuation || 'Not                                    selected'
                                    }}</p>
                                <p class="text-sm text-gray-600">Seasonal Confluence: {{
                                    fundamentalsData.seasonalConfluence
                                    || 'Not selected' }}</p>
                                <p class="text-sm text-gray-600">Non-Commercials: {{ fundamentalsData.nonCommercials ||
                                    'Not selected' }}</p>
                                <p class="text-sm text-gray-600">CoT Index: {{ fundamentalsData.cotIndex || 'Not                                    selected'
                                    }}</p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Zone Qualifiers ({{
                                selectedZoneQualifiersCount }})</h3>
                            <ul class="list-disc pl-5 text-sm text-gray-600 space-y-1">
                                <li v-for="qualifier in zoneQualifiersData.selectedZoneQualifiers" :key="qualifier">
                                    {{ qualifier }}
                                </li>
                                <li v-if="zoneQualifiersData.selectedZoneQualifiers.length === 0" class="text-gray-500">
                                    None selected</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-blue-900 mb-2">Evaluation Score</h3>
                            <Tag :value="`${evaluationScore}/100`" :severity="getScoreSeverity(evaluationScore)"
                                class="text-2xl font-bold px-4 py-2" />
                            <div v-if="evaluationScore === 100"
                                class="text-yellow-500 mt-3 font-bold text-lg text-center">
                                ★ All Stars Aligned ★
                            </div>
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Settings Link -->
            <Button label="Modify Weights" icon="pi pi-cog" severity="secondary" outlined
                @click="router.get(route('user-settings.index'))" class="w-full" />
        </div>

        <!-- Success/Error Messages -->
        <Message v-if="message" :severity="messageType" :closable="true" class="mt-6">
            {{ message }}
        </Message>

        <!-- Toast for notifications -->
        <Toast />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useToast } from 'primevue/usetoast';

// Import step components
import ZoneQualifiersStep from '@/Components/ChecklistSteps/ZoneQualifiersStep.vue';
import TechnicalsStep from '@/Components/ChecklistSteps/TechnicalsStep.vue';
import FundamentalsStep from '@/Components/ChecklistSteps/FundamentalsStep.vue';
import OrderEntryStep from '@/Components/ChecklistSteps/OrderEntryStep.vue';

const props = defineProps({
    settings: Object
})

const toast = useToast();

// Reactive data
const currentStep = ref(1);
const steps = ['Zone Qualifiers', 'Technicals', 'Fundamentals', 'Order Entry'];

// Step data objects
const zoneQualifiersData = ref({
    asset: '',
    selectedZoneQualifiers: []
});

const technicalsData = ref({
    location: '',
    direction: ''
});

const fundamentalsData = ref({
    valuation: '',
    seasonalConfluence: '',
    nonCommercials: '',
    cotIndex: ''
});

const orderEntryData = ref({
    entryDate: '',
    positionType: '',
    entryPrice: '',
    stopPrice: '',
    targetPrice: '',
    rrr: '',
    outcome: '',
    screenshot: null,
    notes: ''
});

const progressCount = ref(0);
const message = ref('');
const messageType = ref('');

// Zone qualifiers for reference
const zoneQualifiers = [
    'Fresh',
    'Original',
    'Flip',
    'LOL',
    'Minimum 1:2 Profit Margin',
    'Big Brother Coverage'
];

// Step items for PrimeVue Steps component
const stepItems = computed(() =>
    steps.map((step, index) => ({
        label: step,
        to: '#',
        command: () => { currentStep.value = index + 1 }
    }))
);

// Helper functions
const getScoreSeverity = (score) => {
    if (score < 50) return 'danger'
    if (score <= 80) return 'warning'
    return 'success'
}

// Date formatting helper
const formatDate = (date) => {
    if (!date) return ''
    if (typeof date === 'string') return date
    if (date instanceof Date) {
        return date.toISOString().split('T')[0] // Convert to YYYY-MM-DD format
    }
    return date
}

// Constants
const totalInputs = 20; // 6 zones + 2 technicals + 4 fundamentals + 8 order entry fields

// Computed properties
const progressPercentage = computed(() => {
    return Math.round((progressCount.value / totalInputs) * 100);
});

const selectedZoneQualifiersCount = computed(() => zoneQualifiersData.value.selectedZoneQualifiers.length);

const evaluationScore = computed(() => {
    // 1. Compute raw weighted score
    let raw = 0;
    const zoneKeys = ['fresh', 'original', 'flip', 'lol', 'min_profit_margin', 'big_brother'];
    zoneKeys.forEach((key, idx) => {
        const qualifierName = zoneQualifiers[idx];
        if (zoneQualifiersData.value.selectedZoneQualifiers.includes(qualifierName)) {
            raw += Number(props.settings[`zone_${key}_weight`] || 0);
        }
    });

    // Technicals: Location
    if (['Very Cheap', 'Very Expensive'].includes(technicalsData.value.location)) {
        raw += Number(props.settings.technical_very_exp_chp_weight || 0);
    } else if (['Cheap', 'Expensive'].includes(technicalsData.value.location)) {
        raw += Number(props.settings.technical_exp_chp_weight || 0);
    }

    // Technicals: Direction
    if (technicalsData.value.direction === 'Impulsion') {
        raw += Number(props.settings.technical_direction_impulsive_weight || 0);
    } else if (technicalsData.value.direction === 'Correction') {
        raw += Number(props.settings.technical_direction_correction_weight || 0);
    }

    // Fundamentals
    if (['Undervalued', 'Overvalued'].includes(fundamentalsData.value.valuation)) {
        raw += Number(props.settings.fundamental_valuation_weight || 0);
    }
    if (fundamentalsData.value.seasonalConfluence === 'Yes') {
        raw += Number(props.settings.fundamental_seasonal_weight || 0);
    }
    if (fundamentalsData.value.nonCommercials === 'Divergence') {
        raw += Number(props.settings.fundamental_noncommercial_divergence_weight || 0);
    }
    if (['Bullish', 'Bearish'].includes(fundamentalsData.value.cotIndex)) {
        raw += Number(props.settings.fundamental_cot_index_weight || 0);
    }

    // 2. Compute max possible score based on one selection per category
    const zoneMax = zoneKeys.reduce((sum, key) => sum + Number(props.settings[`zone_${key}_weight`] || 0), 0);

    // Technicals: Location (pick the higher of very_exp or exp)
    const locHigh = Math.max(
        Number(props.settings.technical_very_exp_chp_weight || 0),
        Number(props.settings.technical_exp_chp_weight || 0)
    );

    // Technicals: Direction (pick the higher of impulsive or correction)
    const dirHigh = Math.max(
        Number(props.settings.technical_direction_impulsive_weight || 0),
        Number(props.settings.technical_direction_correction_weight || 0)
    );

    // Fundamentals: sum of all criteria
    const fundMax =
        Number(props.settings.fundamental_valuation_weight || 0) +
        Number(props.settings.fundamental_seasonal_weight || 0) +
        Number(props.settings.fundamental_noncommercial_divergence_weight || 0) +
        Number(props.settings.fundamental_cot_index_weight || 0);

    const max = zoneMax + locHigh + dirHigh + fundMax;

    // 3. Normalize to a 0–100 scale
    return max > 0 ? Math.round((raw / max) * 100) : 0;
});

const canProceed = computed(() => {
    if (currentStep.value === 1) {
        return selectedZoneQualifiersCount.value > 0;
    }
    if (currentStep.value === 2) {
        return technicalsData.value.location && technicalsData.value.direction;
    }
    if (currentStep.value === 3) {
        return fundamentalsData.value.valuation &&
            fundamentalsData.value.seasonalConfluence &&
            fundamentalsData.value.nonCommercials &&
            fundamentalsData.value.cotIndex;
    }
    if (currentStep.value === 4) {
        // Order Entry is now optional - always allow proceeding
        return true;
    }
    return false;
});

const canSubmit = computed(() => {
    // Only require the analysis sections, Order Entry is optional
    return selectedZoneQualifiersCount.value > 0 &&
        technicalsData.value.location &&
        technicalsData.value.direction &&
        fundamentalsData.value.valuation &&
        fundamentalsData.value.seasonalConfluence &&
        fundamentalsData.value.nonCommercials &&
        fundamentalsData.value.cotIndex;
});

function updateProgress() {
    progressCount.value = zoneQualifiersData.value.selectedZoneQualifiers.length +
        (technicalsData.value.location ? 1 : 0) +
        (technicalsData.value.direction ? 1 : 0) +
        (fundamentalsData.value.valuation ? 1 : 0) +
        (fundamentalsData.value.seasonalConfluence ? 1 : 0) +
        (fundamentalsData.value.nonCommercials ? 1 : 0) +
        (fundamentalsData.value.cotIndex ? 1 : 0) +
        (orderEntryData.value.entryDate ? 1 : 0) +
        (orderEntryData.value.entryPrice ? 1 : 0) +
        (orderEntryData.value.stopPrice ? 1 : 0) +
        (orderEntryData.value.targetPrice ? 1 : 0) +
        (orderEntryData.value.positionType ? 1 : 0) +
        (orderEntryData.value.outcome ? 1 : 0) +
        (orderEntryData.value.rrr ? 1 : 0) +
        (orderEntryData.value.screenshot ? 1 : 0);
}

function resetWizard() {
    currentStep.value = 1;
    zoneQualifiersData.value = {
        asset: '',
        selectedZoneQualifiers: []
    };
    technicalsData.value = {
        location: '',
        direction: ''
    };
    fundamentalsData.value = {
        valuation: '',
        seasonalConfluence: '',
        nonCommercials: '',
        cotIndex: ''
    };
    orderEntryData.value = {
        entryDate: '',
        positionType: '',
        entryPrice: '',
        stopPrice: '',
        targetPrice: '',
        rrr: '',
        outcome: '',
        screenshot: null,
        notes: ''
    };
    progressCount.value = 0;
}

function submitChecklist() {
    if (!canSubmit.value) return;

    router.post(route('checklists.store'), {
        zone_qualifiers: zoneQualifiersData.value.selectedZoneQualifiers,
        technicals: technicalsData.value,
        fundamentals: fundamentalsData.value,
        score: evaluationScore.value,
        asset: zoneQualifiersData.value.asset,
        notes: orderEntryData.value.notes,
        entry_date: formatDate(orderEntryData.value.entryDate),
        entry_price: orderEntryData.value.entryPrice,
        stop_price: orderEntryData.value.stopPrice,
        target_price: orderEntryData.value.targetPrice,
        position_type: orderEntryData.value.positionType,
        outcome: orderEntryData.value.outcome,
        rrr: orderEntryData.value.rrr,
        screenshot: orderEntryData.value.screenshot
    }, {
        preserveState: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Checklist saved successfully!',
                life: 3000
            });
            resetWizard();
        },
        onError: (errors) => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Failed to save checklist. Please try again.',
                life: 3000
            });
            console.error('Submission error:', errors);
        }
    });
}
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