<template>
    <div class="checklist-container max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Trade Setup Checklist</h1>

        <!-- Step Indicator -->
        <div class="flex justify-center mb-8">
            <div class="flex space-x-4">
                <div v-for="(step, index) in steps" :key="index"
                    :class="['px-4 py-2 rounded-full text-sm font-medium cursor-pointer transition-all duration-300',
                        currentStep === index + 1 ? 'bg-blue-900 text-white' : 'bg-gray-200 text-gray-600 hover:bg-gray-300']" @click="currentStep = index + 1">
                    Step {{ index + 1 }}: {{ step }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Wizard Steps -->
            <div class="col-span-2 bg-white p-6 rounded-lg shadow-md">
                <!-- Step 1: Zone Qualifiers -->
                <div v-if="currentStep === 1" class="space-y-4">
                    <h2 class="text-2xl font-semibold text-blue-900">Zone Qualifiers</h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asset</label>
                        <select v-model="asset"
                            class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                            <option value="" disabled>Select Asset</option>
                            <option value="EUR/USD">EUR/USD</option>
                            <option value="GBP/USD">GBP/USD</option>
                            <option value="USD/JPY">USD/JPY</option>
                            <option value="AUD/USD">AUD/USD</option>
                        </select>
                    </div>
                    <ul class="space-y-3">
                        <li v-for="(qualifier, index) in zoneQualifiers" :key="index" class="flex items-center">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" v-model="checkedZoneQualifiers[index]"
                                    class="form-checkbox h-5 w-5 text-blue-900 rounded focus:ring-blue-900"
                                    @change="updateProgress" />
                                <span class="text-gray-700">{{ qualifier }}</span>
                            </label>
                        </li>
                    </ul>

                </div>

                <!-- Step 2: Technicals -->
                <div v-if="currentStep === 2" class="space-y-6">
                    <h2 class="text-2xl font-semibold text-blue-900">Technicals</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <select v-model="technicals.location"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                @change="updateProgress">
                                <option value="" disabled>Select Location</option>
                                <option value="Very Expensive">Very Expensive</option>
                                <option value="Expensive">Expensive</option>
                                <option value="EQ">EQ</option>
                                <option value="Cheap">Cheap</option>
                                <option value="Very Cheap">Very Cheap</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                            <select v-model="technicals.direction"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                @change="updateProgress">
                                <option value="" disabled>Select Direction</option>
                                <option value="Correction">Correction</option>
                                <option value="Impulsion">Impulsion</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Fundamentals -->
                <div v-if="currentStep === 3" class="space-y-6">
                    <h2 class="text-2xl font-semibold text-blue-900">Fundamentals</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valuation</label>
                            <select v-model="fundamentals.valuation"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                @change="updateProgress">
                                <option value="" disabled>Select Valuation</option>
                                <option value="Overvalued">Overvalued</option>
                                <option value="Neutral">Neutral</option>
                                <option value="Undervalued">Undervalued</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Seasonal Confluence</label>
                            <select v-model="fundamentals.seasonalConfluence"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                @change="updateProgress">
                                <option value="" disabled>Select Seasonal Confluence</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Non-Commercials</label>
                            <select v-model="fundamentals.nonCommercials"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                @change="updateProgress">
                                <option value="" disabled>Select Non-Commercials</option>
                                <option value="Divergence">Divergence</option>
                                <option value="No-Divergence">No-Divergence</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CoT Index</label>
                            <select v-model="fundamentals.cotIndex"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                @change="updateProgress">
                                <option value="" disabled>Select CoT Index</option>
                                <option value="Bullish">Bullish</option>
                                <option value="Neutral">Neutral</option>
                                <option value="Bearish">Bearish</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Order Entry -->
                <div v-if="currentStep === 4" class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-semibold text-blue-900">Order Entry</h2>
                        <span class="text-sm font-medium text-gray-700">Instrument: {{ asset || '—' }}</span>
                    </div>
                    <div class="space-y-4">
                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Entry Date</label>
                                <input type="date" v-model="entryDate" @change="updateProgress"
                                    class="form-input w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Position Type</label>
                                <select v-model="positionType" @change="updateProgress"
                                    class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                                    <option value="" disabled>Select Position Type</option>
                                    <option value="Long">Long</option>
                                    <option value="Short">Short</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Entry Price</label>
                                <input type="number" step="0.0001" v-model="entryPrice" @input="updateProgress"
                                    class="form-input w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stop Price</label>
                                <input type="number" step="0.0001" v-model="stopPrice" @input="updateProgress"
                                    class="form-input w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Target Price</label>
                                <input type="number" step="0.0001" v-model="targetPrice" @input="updateProgress"
                                    class="form-input w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Outcome</label>
                            <select v-model="outcome" @change="updateProgress"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                                <option value="" disabled>Select Outcome</option>
                                <option value="win">Win</option>
                                <option value="loss">Loss</option>
                                <option value="breakeven">Breakeven</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Screenshot</label>
                            <input type="file" @change="e => screenshot = e.target.files[0]"
                                class="form-input w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea v-model="notes" rows="3"
                                class="form-textarea w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                placeholder="Add any notes about this order"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-6 flex justify-between">
                    <button @click="resetWizard"
                        class="px-4 py-2 bg-gray-400 text-white rounded-md hover:bg-red-500 transition-colors hover:cursor-pointer">
                        Reset
                    </button>
                    <span>
                        <button v-if="currentStep > 1" @click="currentStep--"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors hover:cursor-pointer">
                            Previous
                        </button>
                        <button v-if="currentStep < steps.length" @click="currentStep++" :disabled="!canProceed"
                            class="px-4 py-2 ml-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors hover:cursor-pointer">
                            Next
                        </button>
                        <button v-if="currentStep === steps.length" @click="submitChecklist" :disabled="!canSubmit"
                            class="px-4 py-2 ml-2 bg-blue-900 text-white rounded-md transition-colors hover:bg-blue-800 hover:cursor-pointer">
                            Submit
                        </button>
                    </span>
                </div>

                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="progress-bar-container bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="progress-bar h-full transition-all duration-300"
                            :style="{ width: evaluationScore + '%' }" :class="{
                                'bg-red-500': evaluationScore < 50,
                                'bg-yellow-500': evaluationScore >= 50 && evaluationScore <= 80,
                                'bg-emerald-500': evaluationScore > 80
                            }">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-blue-900 mb-4">Trade Setup Summary</h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Technicals</h3>
                        <p class="text-sm text-gray-600">Location: {{ technicals.location || 'Not selected' }}</p>
                        <p class="text-sm text-gray-600">Direction: {{ technicals.direction || 'Not selected' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Fundamentals</h3>
                        <p class="text-sm text-gray-600">Valuation: {{ fundamentals.valuation || 'Not selected' }}</p>
                        <p class="text-sm text-gray-600">Seasonal Confluence: {{ fundamentals.seasonalConfluence || "Not selected" }} </p>
                        <p class="text-sm text-gray-600">Non-Commercials: {{ fundamentals.nonCommercials || 'Not selected' }} </p>
                        <p class="text-sm text-gray-600">CoT Index: {{ fundamentals.cotIndex || 'Not selected' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Zone Qualifiers ({{ selectedZoneQualifiersCount
                        }})</h3>
                        <ul class="list-disc pl-5 text-sm text-gray-600">
                            <li v-for="qualifier in filteredZoneQualifiers" :key="qualifier">
                                {{ qualifier }}
                            </li>
                            <li v-if="filteredZoneQualifiers.length === 0" class="text-gray-500">None selected</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-blue-900">Evaluation Score</h3>
                        <p :class="['text-2xl font-bold px-4 py-2 rounded-md inline-block',
                            evaluationScore < 50 ? 'text-red-600 bg-red-100' :
                                evaluationScore <= 70 ? 'text-yellow-600 bg-yellow-100' :
                                    'text-emerald-600 bg-emerald-100']">
                            {{ evaluationScore }}/100
                        </p>
                        <div v-if="evaluationScore === 100" class="text-yellow-500 mt-2 font-bold text-lg text-center">★
                            All Stars Aligned ★</div>
                    </div>
                </div>
            </div>
            <Link :href="'/user-settings'" as="button"
                class="px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-700 transition-colors hover:cursor-pointer">
            modify weights
            </Link>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object
})

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
const asset = ref('');
const notes = ref('');
const entryDate = ref('');
const entryPrice = ref('');
const stopPrice = ref('');
const targetPrice = ref('');
const positionType = ref('');
const outcome = ref('');
const screenshot = ref(null);
const checkedZoneQualifiers = ref(Array(6).fill(false));
const progressCount = ref(0);
// total fields: 6 zones + 2 technicals + 4 fundamentals + 4 entry + 1 position + 1 outcome + 1 notes + 1 screenshot = 20
const totalInputs = 20;
const message = ref('');
const messageType = ref('');


const progressPercentage = computed(() => {
    return Math.round((progressCount.value / totalInputs) * 100);
});
const selectedZoneQualifiersCount = computed(() => checkedZoneQualifiers.value.filter(Boolean).length);
const filteredZoneQualifiers = computed(() => zoneQualifiers.filter((_, index) => checkedZoneQualifiers.value[index]));
const evaluationScore = computed(() => {
    // 1. Compute raw weighted score
    let raw = 0;
    const zoneKeys = ['fresh', 'original', 'flip', 'lol', 'min_profit_margin', 'big_brother'];
    zoneKeys.forEach((key, idx) => {
        if (checkedZoneQualifiers.value[idx]) {
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
        return entryDate.value && entryPrice.value && stopPrice.value && targetPrice.value && positionType.value && outcome.value;
    }
    return false;
});
const canSubmit = computed(() => {
    return selectedZoneQualifiersCount.value > 0 &&
        technicals.value.location &&
        technicals.value.direction &&
        fundamentals.value.valuation &&
        fundamentals.value.seasonalConfluence &&
        fundamentals.value.nonCommercials &&
        fundamentals.value.cotIndex &&
        entryDate.value && entryPrice.value && stopPrice.value && targetPrice.value && positionType.value && outcome.value;
});

function updateProgress() {
    progressCount.value = checkedZoneQualifiers.value.filter(Boolean).length +
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
        + + (screenshot.value ? 1 : 0);
}
function resetWizard() {
    currentStep.value = 1;
    technicals.value = { location: '', direction: '' };
    fundamentals.value = { valuation: '', seasonalConfluence: '', nonCommercials: '', cotIndex: '' };
    checkedZoneQualifiers.value = Array(6).fill(false);
    asset.value = '';
    notes.value = '';
    entryDate.value = '';
    entryPrice.value = '';
    stopPrice.value = '';
    targetPrice.value = '';
    positionType.value = '';
    outcome.value = '';
    screenshot.value = null;
    progressCount.value = 0;
}
function submitChecklist() {
    if (!canSubmit.value) return;
    message.value = '';
    messageType.value = '';
    Inertia.post('/checklists', {
        zone_qualifiers: zoneQualifiers.filter((_, index) => checkedZoneQualifiers.value[index]),
        technicals: technicals.value,
        fundamentals: fundamentals.value,
        score: evaluationScore.value,
        asset: asset.value,
        notes: notes.value,
        entry_date: entryDate.value,
        entry_price: entryPrice.value,
        stop_price: stopPrice.value,
        target_price: targetPrice.value,
        position_type: positionType.value,
        outcome: outcome.value,
        screenshot: screenshot.value
    }, {
        preserveState: true,
        onSuccess: () => {
            message.value = 'Checklist saved successfully!';
            messageType.value = 'success';
            resetWizard();
        },
        onError: (errors) => {
            message.value = 'Failed to save checklist: ' + (errors.message || Object.values(errors).join(', '));
            messageType.value = 'error';
            console.error('Submission error:', errors);
        }
    });
}

</script>

<style scoped>
/* Scoped styles for the component */
.form-select {
    transition: all 0.3s ease;
}

.form-select:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.2);
}
</style>