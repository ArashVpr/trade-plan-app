<template>
    <div class="max-w-6xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Edit Checklist</h1>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center mb-6">
            <Button label="Back" icon="pi pi-arrow-left" severity="secondary"
                @click="$inertia.get(`/checklists/${checklist.id}`)" />
            <div class="flex gap-3">
                <Button label="Cancel" icon="pi pi-times" severity="secondary" outlined
                    @click="$inertia.get(`/checklists/${checklist.id}`)" />
                <Button label="Save Changes" icon="pi pi-save" severity="success" @click="submitForm"
                    :disabled="!canSubmit" />
            </div>
        </div>

        <!-- Main Content -->
        <Card class="w-full">
            <template #title>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="pi pi-edit text-blue-900"></i>
                        <span class="text-blue-900">Edit Trade Setup</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <Tag :value="`Score: ${form.score}/100`" :severity="getScoreSeverity(form.score)"
                            class="text-lg font-bold px-4 py-2" />
                        <div v-if="form.score === 100" class="text-yellow-500 font-bold text-sm">
                            ★ All Stars Aligned ★
                        </div>
                    </div>
                </div>
            </template>
            <template #content>
                <form @submit.prevent="submitForm" class="space-y-8">
                    <!-- Basic Information Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-gray-50 rounded-lg">
                        <div class="field">
                            <label class="block text-sm font-medium mb-2">Asset</label>
                            <Dropdown v-model="form.asset" :options="['EUR/USD', 'GBP/USD', 'USD/JPY', 'AUD/USD']"
                                placeholder="Select Asset" class="w-full" />
                        </div>
                        <div class="field">
                            <label class="block text-sm font-medium mb-2">Entry Date</label>
                            <DatePicker v-model="form.entry_date" dateFormat="yy-mm-dd" class="w-full" showIcon fluid
                                iconDisplay="input" />
                        </div>
                        <div class="field">
                            <label class="block text-sm font-medium mb-2">Created</label>
                            <InputText :value="new Date(checklist.created_at).toLocaleDateString()" readonly
                                class="w-full" />
                        </div>
                    </div>

                    <!-- Analysis Sections -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column: Technical & Fundamental Analysis -->
                        <div class="space-y-6">
                            <!-- Technical Analysis -->
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="pi pi-chart-line text-blue-900"></i>
                                    Technical Analysis
                                </h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Location</label>
                                        <Dropdown v-model="form.technicals.location"
                                            :options="['Very Expensive', 'Expensive', 'EQ', 'Cheap', 'Very Cheap']"
                                            placeholder="Select Location" class="w-full" />
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Direction</label>
                                        <Dropdown v-model="form.technicals.direction"
                                            :options="['Correction', 'Impulsion']" placeholder="Select Direction"
                                            class="w-full" />
                                    </div>
                                </div>
                            </div>

                            <!-- Fundamental Analysis -->
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="pi pi-globe text-blue-900"></i>
                                    Fundamental Analysis
                                </h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Valuation</label>
                                        <Dropdown v-model="form.fundamentals.valuation"
                                            :options="['Overvalued', 'Neutral', 'Undervalued']"
                                            placeholder="Select Valuation" class="w-full" />
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Seasonal Confluence</label>
                                        <Dropdown v-model="form.fundamentals.seasonalConfluence"
                                            :options="['Yes', 'No']" placeholder="Select Seasonal Confluence"
                                            class="w-full" />
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Non-Commercials</label>
                                        <Dropdown v-model="form.fundamentals.nonCommercials"
                                            :options="['Divergence', 'No-Divergence']"
                                            placeholder="Select Non-Commercials" class="w-full" />
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">CoT Index</label>
                                        <Dropdown v-model="form.fundamentals.cotIndex"
                                            :options="['Bullish', 'Neutral', 'Bearish']" placeholder="Select CoT Index"
                                            class="w-full" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Zone Qualifiers & Order Details -->
                        <div class="space-y-6">
                            <!-- Zone Qualifiers -->
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="pi pi-map-marker text-blue-900"></i>
                                    Zone Qualifiers ({{ form.zone_qualifiers.length }})
                                </h3>
                                <div class="grid grid-cols-1 gap-2">
                                    <div v-for="(qualifier, index) in zoneQualifiers" :key="index"
                                        class="flex items-center">
                                        <Checkbox v-model="form.zone_qualifiers" :value="qualifier"
                                            :inputId="`qualifier-${index}`" class="mr-3" />
                                        <label :for="`qualifier-${index}`" class="text-gray-700 text-sm">
                                            {{ qualifier }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Entry Details -->
                            <div ref="orderEntryRef"
                                class="p-4 border border-gray-200 rounded-lg transition-all duration-500"
                                :class="{ 'border-blue-500 bg-blue-50': shouldHighlightOrderEntry }">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="pi pi-money-bill text-blue-900"></i>
                                    Order Entry Details
                                    <Tag v-if="shouldHighlightOrderEntry" value="Focus" severity="info" class="ml-2" />
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Position Type</label>
                                        <Dropdown v-model="form.position_type" :options="['Long', 'Short']"
                                            placeholder="Select Position Type" class="w-full" />
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Trade Outcome</label>
                                        <Dropdown v-model="form.outcome" :options="['win', 'loss', 'breakeven']"
                                            placeholder="Select Outcome" class="w-full" />
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Entry Price</label>
                                        <InputText v-model="form.entry_price" type="number" step="0.0001"
                                            placeholder="0.0000" class="w-full" />
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Stop Loss</label>
                                        <InputText v-model="form.stop_price" type="number" step="0.0001"
                                            placeholder="0.0000" class="w-full" />
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Take Profit</label>
                                        <InputText v-model="form.target_price" type="number" step="0.0001"
                                            placeholder="0.0000" class="w-full" />
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">R:R Ratio</label>
                                        <InputText v-model="form.rrr" type="number" step="0.01" placeholder="1.50"
                                            class="w-full" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes & Screenshot Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Notes -->
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="pi pi-file-edit text-blue-900"></i>
                                Trading Notes
                            </h3>
                            <div class="field">
                                <Textarea v-model="form.notes" rows="6" class="w-full"
                                    placeholder="Document your analysis, reasoning, and any additional context for this trade..." />
                            </div>
                        </div>

                        <!-- Screenshot Upload -->
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="pi pi-image text-blue-900"></i>
                                Chart Screenshot
                            </h3>
                            <div class="field">
                                <div
                                    class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition-colors">
                                    <i class="pi pi-upload text-gray-400 text-2xl mb-2"></i>
                                    <input type="file" @change="onFileChange" accept="image/*"
                                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                    <p class="text-xs text-gray-500 mt-2">
                                        Upload a screenshot of your trading chart
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </template>
        </Card>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, watch, onMounted, ref } from 'vue';

const orderEntryRef = ref(null)

const props = defineProps({
    checklist: Object,
    settings: Object,
    tradeEntry: Object
})

// Check if we should focus on order entry section
const shouldHighlightOrderEntry = ref(false)

// Handle focus on order entry when coming from "Add Trade Details"
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search)
    const focusParam = urlParams.get('focus')

    if (focusParam === 'order-entry') {
        shouldHighlightOrderEntry.value = true

        // Scroll to order entry section after a brief delay
        setTimeout(() => {
            if (orderEntryRef.value) {
                orderEntryRef.value.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                })
            }
        }, 300)

        // Remove highlight after 3 seconds
        setTimeout(() => {
            shouldHighlightOrderEntry.value = false
        }, 3000)
    }
})

// Helper functions for PrimeVue components
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

// File upload handler
const onFileChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.screenshot = file
    }
}

// Form submission handler
const submitForm = () => {
    // Format the entry_date before submission
    const originalDate = form.entry_date
    form.entry_date = formatDate(form.entry_date)

    form.put(`/checklists/${props.checklist.id}`)
}

const zoneQualifiers = [
    'Fresh', 'Original', 'Flip', 'LOL', 'Minimum 1:2 Profit Margin', 'Big Brother Coverage'
]

const form = useForm({
    zone_qualifiers: [...props.checklist.zone_qualifiers],
    technicals: { ...props.checklist.technicals },
    fundamentals: { ...props.checklist.fundamentals },
    score: props.checklist.score,
    asset: props.checklist.asset,
    notes: props.tradeEntry?.notes || '',
    entry_date: props.tradeEntry?.entry_date || '',
    position_type: props.tradeEntry?.position_type || '',
    entry_price: props.tradeEntry?.entry_price || '',
    stop_price: props.tradeEntry?.stop_price || '',
    target_price: props.tradeEntry?.target_price || '',
    outcome: props.tradeEntry?.outcome || '',
    rrr: props.tradeEntry?.rrr || '',
    screenshot: null
})

const canSubmit = computed(() => {
    return form.technicals.location &&
        form.technicals.direction &&
        form.fundamentals.valuation &&
        form.fundamentals.seasonalConfluence &&
        form.fundamentals.nonCommercials &&
        form.fundamentals.cotIndex &&
        form.zone_qualifiers.length > 0 &&
        form.entry_date &&
        form.position_type &&
        form.entry_price &&
        form.stop_price &&
        form.target_price &&
        form.outcome &&
        form.rrr
})

const evaluationScore = () => {
    // 1. Raw weighted score
    let raw = 0;
    const zoneKeys = ['fresh', 'original', 'flip', 'lol', 'min_profit_margin', 'big_brother'];
    zoneKeys.forEach((key, idx) => {
        if (form.zone_qualifiers.includes(zoneQualifiers[idx])) {
            raw += Number(props.settings[`zone_${key}_weight`] || 0);
        }
    });
    // Technicals: Location
    if (['Very Cheap', 'Very Expensive'].includes(form.technicals.location)) {
        raw += Number(props.settings.technical_very_exp_chp_weight || 0);
    } else if (['Cheap', 'Expensive'].includes(form.technicals.location)) {
        raw += Number(props.settings.technical_exp_chp_weight || 0);
    }
    // Technicals: Direction
    if (form.technicals.direction === 'Impulsion') {
        raw += Number(props.settings.technical_direction_impulsive_weight || 0);
    } else if (form.technicals.direction === 'Correction') {
        raw += Number(props.settings.technical_direction_correction_weight || 0);
    }
    // Fundamentals: Valuation
    if (['Undervalued', 'Overvalued'].includes(form.fundamentals.valuation)) {
        raw += Number(props.settings.fundamental_valuation_weight || 0);
    }
    // Fundamentals: Seasonal
    if (form.fundamentals.seasonalConfluence === 'Yes') {
        raw += Number(props.settings.fundamental_seasonal_weight || 0);
    }
    // Fundamentals: Non-Commercial
    if (form.fundamentals.nonCommercials === 'Divergence') {
        raw += Number(props.settings.fundamental_noncommercial_divergence_weight || 0);
    }
    // Fundamentals: CoT Index
    if (['Bullish', 'Bearish'].includes(form.fundamentals.cotIndex)) {
        raw += Number(props.settings.fundamental_cot_index_weight || 0);
    }
    // 2. Max possible score based on one selection per category
    const zoneMax = zoneKeys.reduce((sum, key) => sum + Number(props.settings[`zone_${key}_weight`] || 0), 0);
    const locHigh = Math.max(
        Number(props.settings.technical_very_exp_chp_weight || 0),
        Number(props.settings.technical_exp_chp_weight || 0)
    );
    const dirHigh = Math.max(
        Number(props.settings.technical_direction_impulsive_weight || 0),
        Number(props.settings.technical_direction_correction_weight || 0)
    );
    const fundMax =
        Number(props.settings.fundamental_valuation_weight || 0) +
        Number(props.settings.fundamental_seasonal_weight || 0) +
        Number(props.settings.fundamental_noncommercial_divergence_weight || 0) +
        Number(props.settings.fundamental_cot_index_weight || 0);
    const max = zoneMax + locHigh + dirHigh + fundMax;
    // 3. Normalize to 0-100
    form.score = max > 0 ? Math.round((raw / max) * 100) : 0;
};

// Watch for changes in form fields and update score
watch(() => form, evaluationScore, { deep: true })
</script>
