<template>
    <AppLayout>
        <div class="max-w-6xl mx-auto">
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
                                :instruments="instruments" @progress-updated="updateProgress" />

                            <!-- Step 2: Technicals -->
                            <TechnicalsStep v-if="currentStep === 2" v-model="technicalsData"
                                @progress-updated="updateProgress" />

                            <!-- Step 3: Fundamentals -->
                            <FundamentalsStep v-if="currentStep === 3" v-model="fundamentalsData"
                                @progress-updated="updateProgress" />

                            <!-- Step 4: Order Entry -->
                            <OrderEntryStep v-if="currentStep === 4" v-model="orderEntryData"
                                :symbol="zoneQualifiersData.symbol" @progress-updated="updateProgress" />

                            <!-- Navigation Buttons -->
                            <div class="mt-8 flex justify-between items-center">
                                <Button label="Reset" icon="pi pi-refresh" severity="secondary" @click="resetWizard" />

                                <div class="flex gap-2">
                                    <Button v-if="currentStep > 1" label="Previous" icon="pi pi-arrow-left"
                                        severity="secondary" @click="currentStep--" />
                                    <Button v-if="currentStep < steps.length" label="Next" icon="pi pi-arrow-right"
                                        iconPos="right" @click="currentStep++" :disabled="!canProceed" />
                                    <Button v-if="currentStep === steps.length" label="Submit Checklist"
                                        icon="pi pi-check" @click="submitChecklist" :disabled="!canSubmit" />
                                </div>
                            </div>
                            <!-- Progress Bar -->
                            <div class="mt-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span>Setup Progress</span>
                                </div>
                                <ProgressBar :value="evaluationScore" :class="{
                                    'progress-danger': evaluationScore < 50,
                                    'progress-warning': evaluationScore >= 50 && evaluationScore < 80,
                                    'progress-success': evaluationScore => 80
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
                                    <p class="text-sm text-gray-600">Location: {{ technicalsData.location || 'Not selected' }}
                                    </p>
                                    <p class="text-sm text-gray-600">Direction: {{ technicalsData.direction || 'Not selected' }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Fundamentals</h3>
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-600">Valuation: {{ fundamentalsData.valuation || 'Not selected' }}</p>
                                    <p class="text-sm text-gray-600">Seasonality: {{ fundamentalsData.seasonalConfluence || 'Not selected' }}</p>
                                    <p class="text-sm text-gray-600">Non-Commercials: {{ fundamentalsData.nonCommercials || 'Not selected' }}</p>
                                    <p class="text-sm text-gray-600">CoT Index: {{ fundamentalsData.cotIndex || 'Not selected' }}</p>
                                    <p class="text-sm text-gray-600">Commercials: {{ fundamentalsData.commercials || 'Not selected' }}</p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Zone Qualifiers ({{selectedZoneQualifiersCount }})</h3>
                                <ul class="list-disc pl-5 text-sm text-gray-600 space-y-1">
                                    <li v-for="qualifier in zoneQualifiersData.selectedZoneQualifiers" :key="qualifier">
                                        {{ qualifier }}
                                    </li>
                                    <li v-if="zoneQualifiersData.selectedZoneQualifiers.length === 0"
                                        class="text-gray-500">
                                        None selected</li>
                                </ul>
                            </div>

                            <!-- Directional Bias -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Directional Bias</h3>
                                <div v-if="directionalBias.hasEnoughData" class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <Tag :value="directionalBias.biasDisplay" :severity="directionalBias.severity"
                                            class="font-bold text-sm" />
                                        <span class="text-sm text-gray-600 font-medium">
                                            {{ directionalBias.confidence }}%
                                        </span>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-500">
                                    No directional signals selected
                                </div>
                            </div>
                            <div>
                                <EvaluationScore ref="evaluationScoreRef"
                                    :zone-qualifiers="zoneQualifiersData.selectedZoneQualifiers"
                                    :technicals="technicalsData" :fundamentals="fundamentalsData" :settings="settings"
                                    title="Evaluation Score" />
                            </div>
                        </div>
                    </template>
                </Card>

            </div>

            <!-- Success/Error Messages -->
            <Message v-if="message" :severity="messageType" :closable="true" class="mt-6">
                {{ message }}
            </Message>

            <!-- Toast for notifications -->
            <AppToast ref="toastRef" />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useToast } from 'primevue/usetoast';

// Import step components
import ZoneQualifiersStep from '@/Components/ChecklistSteps/ZoneQualifiersStep.vue';
import TechnicalsStep from '@/Components/ChecklistSteps/TechnicalsStep.vue';
import FundamentalsStep from '@/Components/ChecklistSteps/FundamentalsStep.vue';
import OrderEntryStep from '@/Components/ChecklistSteps/OrderEntryStep.vue';

// Import UI components
import AppLayout from '@/Layouts/AppLayout.vue';
import AppToast from '@/Components/UI/AppToast.vue';
import EvaluationScore from '@/Components/UI/EvaluationScore.vue';

// Import composables
import { useDirectionalBias } from '@/composables/useDirectionalBias.js';

const props = defineProps({
    settings: Object,
    instruments: {
        type: Array,
        default: () => []
    }
})

const toast = useToast();
const toastRef = ref(null);
const evaluationScoreRef = ref(null);

// Reactive data
const currentStep = ref(1);
const steps = ['Zone Qualifiers', 'Technicals', 'Fundamentals', 'Order Entry'];

// Step data objects
const zoneQualifiersData = ref({
    symbol: '',
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
    trade_status: '',
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

// Directional bias calculation
const { directionalBias } = useDirectionalBias(
    computed(() => technicalsData.value),
    computed(() => fundamentalsData.value),
    computed(() => props.settings)
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
const totalInputs = 12; // 6 zones + 2 technicals + 4 fundamentals

// Computed properties
const progressPercentage = computed(() => {
    return Math.round((progressCount.value / totalInputs) * 100);
});

const selectedZoneQualifiersCount = computed(() => zoneQualifiersData.value.selectedZoneQualifiers.length);

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

const evaluationScore = computed(() => {
    return evaluationScoreRef.value?.calculatedScore || 0;
});

// Auto-suggest position type based on directional bias (only if not manually set)
const userHasSetPositionType = ref(false);

watch(() => directionalBias.value, (newBias) => {
    // Only auto-suggest if user hasn't manually set position type and we have decent confidence
    if (!userHasSetPositionType.value && newBias.side && newBias.confidence > 20) {
        orderEntryData.value.positionType = newBias.side;
    }
}, { immediate: true });

// Track when user manually sets position type
watch(() => orderEntryData.value.positionType, () => {
    userHasSetPositionType.value = true;
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
        (orderEntryData.value.trade_status ? 1 : 0) +
        (orderEntryData.value.rrr ? 1 : 0) +
        (orderEntryData.value.screenshot ? 1 : 0);
}

function resetWizard() {
    currentStep.value = 1;
    userHasSetPositionType.value = false; // Reset position type tracking
    zoneQualifiersData.value = {
        symbol: '',
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
        trade_status: '',
        screenshot: null,
        notes: ''
    };
    progressCount.value = 0;
}

function submitChecklist() {
    if (!canSubmit.value) return;

    const submitData = {
        zone_qualifiers: zoneQualifiersData.value.selectedZoneQualifiers,
        technicals: technicalsData.value,
        fundamentals: fundamentalsData.value,
        score: evaluationScoreRef.value?.calculatedScore || 0,
        symbol: zoneQualifiersData.value.symbol,
        notes: orderEntryData.value.notes,
        entry_date: formatDate(orderEntryData.value.entryDate),
        entry_price: orderEntryData.value.entryPrice,
        stop_price: orderEntryData.value.stopPrice,
        target_price: orderEntryData.value.targetPrice,
        position_type: orderEntryData.value.positionType,
        trade_status: orderEntryData.value.trade_status,
        rrr: orderEntryData.value.rrr,
        screenshot: orderEntryData.value.screenshot
    };

    router.post(route('checklists.store'), submitData, {
        preserveState: true,
        onSuccess: () => {
            toastRef.value?.showSuccess('Success', 'Checklist saved successfully!')
            resetWizard();
        },
        onError: (errors) => {
            toastRef.value?.showError('Error', 'Failed to save checklist. Please try again.')
            console.error('Submission error:', errors);
        }

    });
    console.log(zoneQualifiersData.value.symbol);
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