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
                        <div v-if="currentStep === 1" class="space-y-6">
                            <h2 class="text-2xl font-semibold text-blue-900 mb-4 flex items-center gap-2">
                                <i class="pi pi-map-marker text-blue-900"></i>
                                Zone Qualifiers
                            </h2>

                            <div class="field">
                                <label class="block text-sm font-medium mb-2">Instrument</label>
                                <Dropdown v-model="asset" :options="assetOptions" placeholder="Select Instrument"
                                    class="w-full" @change="updateProgress" />
                            </div>

                            <div class="space-y-4">
                                <div v-for="(qualifier, index) in zoneQualifiers" :key="index"
                                    class="flex items-center">
                                    <Checkbox v-model="selectedZoneQualifiers" :value="qualifier"
                                        :inputId="`qualifier-${index}`" @change="updateProgress" class="mr-3" />
                                    <label :for="`qualifier-${index}`" class="text-gray-700">
                                        {{ qualifier }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Technicals -->
                        <div v-if="currentStep === 2" class="space-y-6">
                            <h2 class="text-2xl font-semibold text-blue-900 mb-4 flex items-center gap-2">
                                <i class="pi pi-chart-line text-blue-900"></i>
                                Technical Analysis
                            </h2>

                            <div class="grid grid-cols-1 gap-6">
                                <div class="field">
                                    <label class="block text-sm font-medium mb-2">Location</label>
                                    <Dropdown v-model="technicals.location" :options="locationOptions"
                                        placeholder="Select Location" class="w-full" @change="updateProgress" />
                                </div>

                                <div class="field">
                                    <label class="block text-sm font-medium mb-2">Direction</label>
                                    <Dropdown v-model="technicals.direction" :options="directionOptions"
                                        placeholder="Select Direction" class="w-full" @change="updateProgress" />
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Fundamentals -->
                        <div v-if="currentStep === 3" class="space-y-6">
                            <h2 class="text-2xl font-semibold text-blue-900 mb-4 flex items-center gap-2">
                                <i class="pi pi-globe text-blue-900"></i>
                                Fundamental Analysis
                            </h2>

                            <div class="grid grid-cols-1 gap-6">
                                <div class="field">
                                    <label class="block text-sm font-medium mb-2">Valuation</label>
                                    <Dropdown v-model="fundamentals.valuation" :options="valuationOptions"
                                        placeholder="Select Valuation" class="w-full" @change="updateProgress" />
                                </div>

                                <div class="field">
                                    <label class="block text-sm font-medium mb-2">Seasonal Confluence</label>
                                    <Dropdown v-model="fundamentals.seasonalConfluence" :options="seasonalOptions"
                                        placeholder="Select Seasonal Confluence" class="w-full"
                                        @change="updateProgress" />
                                </div>

                                <div class="field">
                                    <label class="block text-sm font-medium mb-2">Non-Commercials</label>
                                    <Dropdown v-model="fundamentals.nonCommercials" :options="nonCommercialsOptions"
                                        placeholder="Select Non-Commercials" class="w-full" @change="updateProgress" />
                                </div>

                                <div class="field">
                                    <label class="block text-sm font-medium mb-2">CoT Index</label>
                                    <Dropdown v-model="fundamentals.cotIndex" :options="cotIndexOptions"
                                        placeholder="Select CoT Index" class="w-full" @change="updateProgress" />
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Order Entry -->
                        <div v-if="currentStep === 4" class="space-y-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-2xl font-semibold text-blue-900 flex items-center gap-2">
                                    <i class="pi pi-money-bill text-blue-900"></i>
                                    Order Entry (Optional)
                                </h2>
                                <Chip :label="`Instrument: ${asset || '—'}`" />
                            </div>

                            <Message severity="info" :closable="false" class="mb-4">
                                <strong>Optional:</strong> You can submit your analysis without filling out the order
                                entry details.
                                Complete this section only if you've actually placed or plan to place a trade.
                            </Message>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="field">
                                    <label class="block text-sm font-medium mb-2">Entry Date</label>
                                    <DatePicker v-model="entryDate" dateFormat="yy-mm-dd" class="w-full" showIcon fluid
                                        iconDisplay="input" @date-select="updateProgress" />
                                </div>

                                <div class="field">
                                    <label class="block text-sm font-medium mb-2">Position Type</label>
                                    <Dropdown v-model="positionType" :options="positionOptions"
                                        placeholder="Select Position Type" class="w-full" @change="updateProgress" />
                                </div>

                                <div class="field">
                                    <label class="block text-sm font-medium mb-2">Entry Price</label>
                                    <InputText v-model="entryPrice" type="number" step="0.0001" placeholder="0.0000"
                                        class="w-full" @input="updateProgress" />
                                </div>

                                <div class="field">
                                    <label class="block text-sm font-medium mb-2">Stop Price</label>
                                    <InputText v-model="stopPrice" type="number" step="0.0001" placeholder="0.0000"
                                        class="w-full" @input="updateProgress" />
                                </div>

                                <div class="field">
                                    <label class="block text-sm font-medium mb-2">Target Price</label>
                                    <InputText v-model="targetPrice" type="number" step="0.0001" placeholder="0.0000"
                                        class="w-full" @input="updateProgress" />
                                </div>

                                <div class="field">
                                    <label class="block text-sm font-medium mb-2">R:R Ratio</label>
                                    <InputText v-model="rrr" type="number" step="0.01" placeholder="1.50" class="w-full"
                                        @input="updateProgress" />
                                </div>
                            </div>

                            <div class="field">
                                <label class="block text-sm font-medium mb-2">Outcome</label>
                                <Dropdown v-model="outcome" :options="outcomeOptions" placeholder="Select Outcome"
                                    class="w-full" @change="updateProgress" />
                            </div>

                            <div class="field">
                                <label class="block text-sm font-medium mb-2">Chart Screenshot</label>
                                <FileUpload mode="basic" name="screenshot" :auto="false" accept="image/*"
                                    :maxFileSize="10000000" @select="onFileSelect" chooseLabel="Choose File"
                                    class="w-full" />
                            </div>

                            <div class="field">
                                <label class="block text-sm font-medium mb-2">Notes</label>
                                <Textarea v-model="notes" rows="3" placeholder="Add any notes about this trade"
                                    class="w-full" />
                            </div>
                        </div>

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
                                <p class="text-sm text-gray-600">Location: {{ technicals.location || 'Not selected' }}
                                </p>
                                <p class="text-sm text-gray-600">Direction: {{ technicals.direction || 'Not selected' }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Fundamentals</h3>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-600">Valuation: {{ fundamentals.valuation || 'Not selected'
                                }}</p>
                                <p class="text-sm text-gray-600">Seasonal Confluence: {{ fundamentals.seasonalConfluence
                                    || 'Not selected' }}</p>
                                <p class="text-sm text-gray-600">Non-Commercials: {{ fundamentals.nonCommercials || 'Not                                    selected' }}</p>
                                <p class="text-sm text-gray-600">CoT Index: {{ fundamentals.cotIndex || 'Not selected'
                                }}</p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Zone Qualifiers ({{
                                selectedZoneQualifiersCount }})</h3>
                            <ul class="list-disc pl-5 text-sm text-gray-600 space-y-1">
                                <li v-for="qualifier in filteredZoneQualifiers" :key="qualifier">
                                    {{ qualifier }}
                                </li>
                                <li v-if="filteredZoneQualifiers.length === 0" class="text-gray-500">None selected</li>
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

const props = defineProps({
    settings: Object
})

const toast = useToast();

// Reactive data
const currentStep = ref(1);
const steps = ['Zone Qualifiers', 'Technicals', 'Fundamentals', 'Order Entry'];
const technicals = ref({ location: '', direction: '' });
const fundamentals = ref({ valuation: '', seasonalConfluence: '', nonCommercials: '', cotIndex: '' });
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

// Form data
const asset = ref('');
const notes = ref('');
const entryDate = ref('');
const entryPrice = ref('');
const stopPrice = ref('');
const targetPrice = ref('');
const positionType = ref('');
const outcome = ref('');
const rrr = ref('');
const screenshot = ref(null);
const selectedZoneQualifiers = ref([]);
const progressCount = ref(0);
const message = ref('');
const messageType = ref('');

// Options for dropdowns
const assetOptions = ['EUR/USD', 'GBP/USD', 'USD/JPY', 'AUD/USD'];
const locationOptions = ['Very Expensive', 'Expensive', 'EQ', 'Cheap', 'Very Cheap'];
const directionOptions = ['Correction', 'Impulsion'];
const valuationOptions = ['Overvalued', 'Neutral', 'Undervalued'];
const seasonalOptions = ['Yes', 'No'];
const nonCommercialsOptions = ['Divergence', 'No-Divergence'];
const cotIndexOptions = ['Bullish', 'Neutral', 'Bearish'];
const positionOptions = ['Long', 'Short'];
const outcomeOptions = ['win', 'loss', 'breakeven'];

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

const onFileSelect = (event) => {
    screenshot.value = event.files[0];
    updateProgress();
};

// Constants
const totalInputs = 20; // 6 zones + 2 technicals + 4 fundamentals + 8 order entry fields

// Computed properties
const progressPercentage = computed(() => {
    return Math.round((progressCount.value / totalInputs) * 100);
});
const selectedZoneQualifiersCount = computed(() => selectedZoneQualifiers.value.length);
const filteredZoneQualifiers = computed(() => selectedZoneQualifiers.value);
const evaluationScore = computed(() => {
    // 1. Compute raw weighted score
    let raw = 0;
    const zoneKeys = ['fresh', 'original', 'flip', 'lol', 'min_profit_margin', 'big_brother'];
    zoneKeys.forEach((key, idx) => {
        const qualifierName = zoneQualifiers[idx];
        if (selectedZoneQualifiers.value.includes(qualifierName)) {
            raw += Number(props.settings[`zone_${key}_weight`] || 0);
        }
    });
    // Technicals: Location
    if (['Very Cheap', 'Very Expensive'].includes(technicals.value.location)) {
        raw += Number(props.settings.technical_very_exp_chp_weight || 0);
    } else if (['Cheap', 'Expensive'].includes(technicals.value.location)) {
        raw += Number(props.settings.technical_exp_chp_weight || 0);
    }
    // Technicals: Direction
    if (technicals.value.direction === 'Impulsion') {
        raw += Number(props.settings.technical_direction_impulsive_weight || 0);
    } else if (technicals.value.direction === 'Correction') {
        raw += Number(props.settings.technical_direction_correction_weight || 0);
    }
    // Fundamentals
    if (['Undervalued', 'Overvalued'].includes(fundamentals.value.valuation)) {
        raw += Number(props.settings.fundamental_valuation_weight || 0);
    }
    if (fundamentals.value.seasonalConfluence === 'Yes') {
        raw += Number(props.settings.fundamental_seasonal_weight || 0);
    }
    if (fundamentals.value.nonCommercials === 'Divergence') {
        raw += Number(props.settings.fundamental_noncommercial_divergence_weight || 0);
    }
    if (['Bullish', 'Bearish'].includes(fundamentals.value.cotIndex)) {
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
        return technicals.value.location && technicals.value.direction;
    }
    if (currentStep.value === 3) {
        return fundamentals.value.valuation && fundamentals.value.seasonalConfluence && fundamentals.value.nonCommercials && fundamentals.value.cotIndex;
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
        technicals.value.location &&
        technicals.value.direction &&
        fundamentals.value.valuation &&
        fundamentals.value.seasonalConfluence &&
        fundamentals.value.nonCommercials &&
        fundamentals.value.cotIndex;
});

function updateProgress() {
    progressCount.value = selectedZoneQualifiers.value.length +
        (technicals.value.location ? 1 : 0) +
        (technicals.value.direction ? 1 : 0) +
        (fundamentals.value.valuation ? 1 : 0) +
        (fundamentals.value.seasonalConfluence ? 1 : 0) +
        (fundamentals.value.nonCommercials ? 1 : 0) +
        (fundamentals.value.cotIndex ? 1 : 0)
        + + (entryDate.value ? 1 : 0)
        + + (entryPrice.value ? 1 : 0)
        + + (stopPrice.value ? 1 : 0)
        + + (targetPrice.value ? 1 : 0)
        + + (positionType.value ? 1 : 0)
        + + (outcome.value ? 1 : 0)
        + + (rrr.value ? 1 : 0)
        + + (screenshot.value ? 1 : 0);
}
function resetWizard() {
    currentStep.value = 1;
    technicals.value = { location: '', direction: '' };
    fundamentals.value = { valuation: '', seasonalConfluence: '', nonCommercials: '', cotIndex: '' };
    selectedZoneQualifiers.value = [];
    asset.value = '';
    notes.value = '';
    entryDate.value = '';
    entryPrice.value = '';
    stopPrice.value = '';
    targetPrice.value = '';
    positionType.value = '';
    outcome.value = '';
    rrr.value = '';
    screenshot.value = null;
    progressCount.value = 0;
}
function submitChecklist() {
    if (!canSubmit.value) return;

    router.post(route('checklists.store'), {
        zone_qualifiers: selectedZoneQualifiers.value,
        technicals: technicals.value,
        fundamentals: fundamentals.value,
        score: evaluationScore.value,
        asset: asset.value,
        notes: notes.value,
        entry_date: formatDate(entryDate.value),
        entry_price: entryPrice.value,
        stop_price: stopPrice.value,
        target_price: targetPrice.value,
        position_type: positionType.value,
        outcome: outcome.value,
        rrr: rrr.value,
        screenshot: screenshot.value
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