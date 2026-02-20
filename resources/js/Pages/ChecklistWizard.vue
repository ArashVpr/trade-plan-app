<template>
    <AppLayout>
        <div class="max-w-6xl mx-auto px-2 sm:px-4 lg:px-0">
            <!-- Header Section -->
            <div class="mb-8 text-center relative z-10">
                <h1
                    class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-900 to-blue-600 dark:from-blue-100 dark:to-blue-400 mb-2 font-display uppercase tracking-tight">
                    Trade Setup Checklist
                </h1>
                <p class="text-surface-500 dark:text-surface-400 text-sm font-medium">Systematic Evaluation Protocol</p>
            </div>

            <!-- Mobile Step Indicator (compact circles) -->
            <div class="md:hidden mb-6">
                <!-- ... existing mobile nav ... -->
                <div
                    class="flex items-center justify-between bg-surface-0 dark:bg-surface-800 shadow-sm rounded-xl p-4 border border-surface-100 dark:border-surface-700">
                    <button v-for="(step, index) in steps" :key="index" @click="currentStep = index + 1"
                        class="flex flex-col items-center flex-1 transition-all"
                        :class="currentStep === index + 1 ? 'text-primary-600 dark:text-primary-400' : 'text-surface-400'">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold mb-1 transition-all border-2"
                            :class="currentStep === index + 1
                                ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-600 text-blue-600 ring-2 ring-blue-100 dark:ring-blue-900/20'
                                : currentStep > index + 1
                                    ? 'bg-blue-600 border-blue-600 text-white'
                                    : 'bg-surface-50 dark:bg-surface-800 border-surface-200 text-surface-400'">
                            <i v-if="currentStep > index + 1" class="pi pi-check text-xs font-bold"></i>
                            <span v-else>{{ index + 1 }}</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider mt-1">{{ step.split(' ')[0]
                        }}</span>
                    </button>
                </div>
            </div>

            <!-- Desktop Step Indicator - Custom Design -->
            <div class="mb-10 hidden md:block px-4">
                <div class="flex items-center w-full">
                    <template v-for="(step, index) in steps" :key="index">
                        <!-- Step Circle & Label -->
                        <div class="relative flex flex-col items-center group">
                            <button @click="currentStep = index + 1"
                                class="flex flex-col items-center gap-3 focus:outline-none transition-transform active:scale-95">
                                <!-- Circle Indicator -->
                                <div class="w-14 h-14 rounded-full flex items-center justify-center transition-all duration-300 shadow-sm border-[3px] z-20"
                                    :class="[
                                        currentStep > index + 1
                                            ? 'bg-blue-600 border-blue-600 text-white shadow-blue-200 dark:shadow-none scale-100'
                                            : currentStep === index + 1
                                                ? 'bg-white dark:bg-slate-900 border-blue-600 text-blue-600 ring-4 ring-blue-100 dark:ring-blue-900/40 scale-110 shadow-lg'
                                                : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-300 hover:border-slate-300'
                                    ]">
                                    <i v-if="currentStep > index + 1"
                                        class="pi pi-check text-xl font-bold animate-flip" />
                                    <span v-else class="text-lg font-black font-mono">{{ index + 1 }}</span>
                                </div>

                                <!-- Label -->
                                <span
                                    class="absolute top-16 w-32 text-center text-sm font-bold tracking-wide transition-colors duration-300 uppercase"
                                    :class="[
                                        currentStep >= index + 1
                                            ? 'text-blue-900 dark:text-blue-100'
                                            : 'text-slate-400 dark:text-slate-600'
                                    ]">
                                    {{ step }}
                                </span>
                            </button>
                        </div>

                        <!-- Connector Line (between steps) -->
                        <div v-if="index < steps.length - 1"
                            class="flex-1 h-[4px] mx-4 bg-slate-100 dark:bg-slate-800 rounded-full relative -z-10">
                            <div class="h-full bg-blue-600 rounded-full transition-all duration-700 ease-out"
                                :class="{ 'w-full': currentStep > index + 1, 'w-0': currentStep <= index + 1 }"></div>
                        </div>
                    </template>
                </div>
                <!-- Spacing for absolute labels -->
                <div class="h-8"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Wizard Steps -->
                <div class="col-span-2">
                    <Card>
                        <template #content>
                            <!-- Step 1: Zone Qualifiers -->
                            <ZoneQualifiersStep v-if="currentStep === 1" v-model="zoneQualifiersData"
                                :instruments="instruments" :show-symbol-error="showSymbolError"
                                @progress-updated="updateProgress" />

                            <!-- Step 2: Technicals -->
                            <TechnicalsStep v-if="currentStep === 2" v-model="technicalsData"
                                @progress-updated="updateProgress" />

                            <!-- Step 3: Fundamentals -->
                            <FundamentalsStep v-if="currentStep === 3" v-model="fundamentalsData"
                                v-model:exclude-fundamentals="excludeFundamentals"
                                :exclude-fundamentals="excludeFundamentals" @progress-updated="updateProgress" />

                            <!-- Step 4: Order Entry -->
                            <OrderEntryStep v-if="currentStep === 4" v-model="orderEntryData"
                                :symbol="zoneQualifiersData.symbol" @progress-updated="updateProgress" />

                            <!-- Navigation Buttons -->
                            <div
                                class="mt-6 sm:mt-8 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
                                <Button label="Reset" icon="pi pi-refresh" severity="secondary" @click="resetWizard"
                                    class="order-3 sm:order-1" />

                                <div class="flex gap-2 order-1 sm:order-2">
                                    <Button v-if="currentStep > 1" label="Previous" icon="pi pi-arrow-left"
                                        severity="secondary" @click="currentStep--" class="flex-1 sm:flex-initial" />
                                    <Button v-if="currentStep < steps.length" label="Next" icon="pi pi-arrow-right"
                                        iconPos="right" @click="currentStep++" :disabled="!canProceed"
                                        class="flex-1 sm:flex-initial" />
                                    <Button v-if="currentStep === steps.length" label="Submit" icon="pi pi-check"
                                        @click="submitChecklist" :disabled="!canSubmit"
                                        class="flex-1 sm:flex-initial" />
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
                <!-- Summary Section - Desktop always visible, Mobile collapsible -->
                <div class="lg:col-span-1">
                    <!-- Mobile: Collapsible Summary with Score Preview -->
                    <div class="lg:hidden mb-4 mx-auto max-w-sm">
                        <button @click="showMobileSummary = !showMobileSummary"
                            class="w-full flex items-center justify-between p-4 bg-surface-0 dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-700 transition-all shadow-sm">
                            <div class="flex items-center gap-3">
                                <i class="pi pi-chart-bar text-blue-900 dark:text-blue-300"></i>
                                <span class="font-medium text-surface-700 dark:text-surface-200">Summary</span>
                                <Tag :value="`${evaluationScore}%`"
                                    :severity="evaluationScore < 50 ? 'danger' : evaluationScore < 80 ? 'warn' : 'success'"
                                    class="text-xs" />
                            </div>
                            <i :class="showMobileSummary ? 'pi pi-chevron-up' : 'pi pi-chevron-down'"
                                class="text-surface-400"></i>
                        </button>
                        <transition name="slide">
                            <div v-show="showMobileSummary" class="mt-2">
                                <Card>
                                    <template #content>
                                        <SummaryContent :technicalsData="technicalsData"
                                            :fundamentalsData="fundamentalsData"
                                            :zoneQualifiersData="zoneQualifiersData"
                                            :selectedZoneQualifiersCount="selectedZoneQualifiersCount"
                                            :directionalBias="directionalBias" :settings="settings"
                                            :exclude-fundamentals="excludeFundamentals" />
                                    </template>
                                </Card>
                            </div>
                        </transition>
                    </div>

                    <!-- Desktop: Always visible Card -->
                    <div class="hidden lg:block">
                        <Card>
                            <template #title>
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-chart-bar text-blue-900 dark:text-blue-300"></i>
                                    <span class="text-blue-900 dark:text-blue-300">Trade Setup Summary</span>
                                </div>
                            </template>
                            <template #content>
                                <SummaryContent ref="summaryContentRef" :technicalsData="technicalsData"
                                    :fundamentalsData="fundamentalsData" :zoneQualifiersData="zoneQualifiersData"
                                    :selectedZoneQualifiersCount="selectedZoneQualifiersCount"
                                    :directionalBias="directionalBias" :settings="settings"
                                    :exclude-fundamentals="excludeFundamentals" />
                            </template>
                        </Card>
                    </div>
                </div>

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
import { ref, computed, watch, onMounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useToast } from 'primevue/usetoast';

// Import step components
import ZoneQualifiersStep from '@/Components/ChecklistSteps/ZoneQualifiersStep.vue';
import TechnicalsStep from '@/Components/ChecklistSteps/TechnicalsStep.vue';
import FundamentalsStep from '@/Components/ChecklistSteps/FundamentalsStep.vue';
import OrderEntryStep from '@/Components/ChecklistSteps/OrderEntryStep.vue';
import SummaryContent from '@/Components/ChecklistSteps/SummaryContent.vue';

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
    },
    prefilledData: {
        type: Object,
        default: null
    },
    symbol: {
        type: String,
        default: ''
    }
})

const toast = useToast();
const toastRef = ref(null);
const summaryContentRef = ref(null);
const showMobileSummary = ref(false);
const hasAttemptedSubmit = ref(false);

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

const excludeFundamentals = ref(false);

const orderEntryData = ref({
    entryDate: '',
    positionType: '',
    entryPrice: '',
    stopPrice: '',
    targetPrice: '',
    rrr: '',
    trade_status: '',
    screenshots: [],
    notes: ''
});

const progressCount = ref(0);
const message = ref('');
const messageType = ref('');

// Initialize data with prefilled values if available
onMounted(() => {
    if (props.prefilledData) {
        // Initialize zone qualifiers
        if (props.prefilledData.zone_qualifiers) {
            zoneQualifiersData.value.selectedZoneQualifiers = [...props.prefilledData.zone_qualifiers]
        }

        // Initialize technicals
        if (props.prefilledData.technicals) {
            technicalsData.value.location = props.prefilledData.technicals.location || ''
            technicalsData.value.direction = props.prefilledData.technicals.direction || ''
        }

        // Initialize fundamentals
        if (props.prefilledData.fundamentals) {
            fundamentalsData.value.valuation = props.prefilledData.fundamentals.valuation || ''
            fundamentalsData.value.seasonalConfluence = props.prefilledData.fundamentals.seasonalConfluence || ''
            fundamentalsData.value.nonCommercials = props.prefilledData.fundamentals.nonCommercials || ''
            fundamentalsData.value.cotIndex = props.prefilledData.fundamentals.cotIndex || ''
        }

        // Initialize exclude_fundamentals
        if (props.prefilledData.exclude_fundamentals !== undefined) {
            excludeFundamentals.value = props.prefilledData.exclude_fundamentals
        }
    }

    // Set symbol if provided
    if (props.symbol) {
        zoneQualifiersData.value.symbol = props.symbol
    }
})

// Zone qualifiers for reference
const zoneQualifiers = [
    'Fresh',
    'Original',
    'Flip',
    'LOL',
    'Minimum 1:2 Profit Margin',
    'Big Brother Coverage'
];

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

const showSymbolError = computed(() => {
    return !zoneQualifiersData.value.symbol && (selectedZoneQualifiersCount.value > 0 || hasAttemptedSubmit.value)
})

const canProceed = computed(() => {
    if (currentStep.value === 1) {
        return zoneQualifiersData.value.symbol && selectedZoneQualifiersCount.value > 0;
    }
    if (currentStep.value === 2) {
        return technicalsData.value.location && technicalsData.value.direction;
    }
    if (currentStep.value === 3) {
        // Skip validation if fundamentals are excluded
        if (excludeFundamentals.value) return true;

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
    const hasZones = zoneQualifiersData.value.symbol && selectedZoneQualifiersCount.value > 0;
    const hasTechnicals = technicalsData.value.location && technicalsData.value.direction;
    const hasFundamentals = excludeFundamentals.value || (
        fundamentalsData.value.valuation &&
        fundamentalsData.value.seasonalConfluence &&
        fundamentalsData.value.nonCommercials &&
        fundamentalsData.value.cotIndex
    );

    return hasZones && hasTechnicals && hasFundamentals;
});

const evaluationScore = computed(() => {
    return summaryContentRef.value?.evaluationScoreComponent?.calculatedScore || 0;
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
        (orderEntryData.value.screenshots?.length ? 1 : 0);
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
    excludeFundamentals.value = false;
    orderEntryData.value = {
        entryDate: '',
        positionType: '',
        entryPrice: '',
        stopPrice: '',
        targetPrice: '',
        rrr: '',
        trade_status: '',
        screenshots: [],
        notes: ''
    };
    progressCount.value = 0;
}

function submitChecklist() {
    if (!canSubmit.value) {
        hasAttemptedSubmit.value = true;
        if (!zoneQualifiersData.value.symbol) {
            message.value = 'Please select a symbol before submitting.'
        } else {
            message.value = 'Please complete the required fields before submitting.'
        }
        messageType.value = 'error'
        return;
    }

    const submitData = {
        zone_qualifiers: zoneQualifiersData.value.selectedZoneQualifiers,
        technicals: technicalsData.value,
        fundamentals: fundamentalsData.value,
        exclude_fundamentals: excludeFundamentals.value,
        score: evaluationScore.value,
        symbol: zoneQualifiersData.value.symbol,
        notes: orderEntryData.value.notes,
        entry_date: formatDate(orderEntryData.value.entryDate),
        entry_price: orderEntryData.value.entryPrice,
        stop_price: orderEntryData.value.stopPrice,
        target_price: orderEntryData.value.targetPrice,
        position_type: orderEntryData.value.positionType,
        trade_status: orderEntryData.value.trade_status,
        rrr: orderEntryData.value.rrr,
    };

    // Add screenshots as individual file objects for proper FormData handling
    if (orderEntryData.value.screenshots && orderEntryData.value.screenshots.length > 0) {
        orderEntryData.value.screenshots.forEach((file, index) => {
            submitData[`screenshots[${index}]`] = file;
        });
    }

    router.post(route('checklists.store'), submitData, {
        forceFormData: true,
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

/* Mobile summary slide animation */
.slide-enter-active,
.slide-leave-active {
    transition: all 0.3s ease;
    overflow: hidden;
}

.slide-enter-from,
.slide-leave-to {
    opacity: 0;
    max-height: 0;
    transform: translateY(-10px);
}

.slide-enter-to,
.slide-leave-from {
    opacity: 1;
    max-height: 1000px;
    transform: translateY(0);
}
</style>