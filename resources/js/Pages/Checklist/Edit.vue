<template>
    <div class="max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Edit Checklist</h1>
        <div class="flex justify-between mb-4">
            <Link :href="'/checklists/' + checklist.id" method="get" as="button"
                class="px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors hover:cursor-pointer">
            Back
            </Link>
            <button type="submit" form="edit-form" :disabled="!canSubmit"
                class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors hover:cursor-pointer">
                Save
            </button>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md mt-6">
            <h2 class="text-xl font-semibold text-blue-900 mb-4">Trade Setup Details</h2>
            <form @submit.prevent="form.put('/checklists/' + checklist.id)" id="edit-form" class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Asset</h3>
                    <select v-model="form.asset"
                        class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                        <option value="" disabled>Select Asset</option>
                        <option v-for="asset in ['EUR/USD', 'GBP/USD', 'USD/JPY', 'AUD/USD']" :key="asset"
                            :value="asset">
                            {{ asset }}
                        </option>
                    </select>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Score</h3>
                    <p :class="['text-xl font-bold px-4 py-2 rounded-md inline-block',
                        form.score < 50 ? 'text-red-600 bg-red-100' :
                            form.score <= 70 ? 'text-yellow-600 bg-yellow-100' :
                                'text-emerald-600 bg-emerald-100']">
                        {{ form.score }}/100
                    </p>
                    <div v-if="form.score === 100" class="text-yellow-500 mt-2 font-bold text-lg text-center">★ All
                        Stars Aligned ★</div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Created</h3>
                    <p class="text-sm text-gray-600">{{ new Date(checklist.created_at).toLocaleDateString() }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Technicals</h3>
                    <div class="space-y-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Location</label>
                            <select v-model="form.technicals.location"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                                <option value="" disabled>Select Location</option>
                                <option v-for="loc in ['Very Expensive', 'Expensive', 'EQ', 'Cheap', 'Very Cheap']"
                                    :key="loc" :value="loc">
                                    {{ loc }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Direction</label>
                            <select v-model="form.technicals.direction"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                                <option value="" disabled>Select Direction</option>
                                <option v-for="dir in ['Correction', 'Impulsion']" :key="dir" :value="dir">
                                    {{ dir }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Fundamentals</h3>
                    <div class="space-y-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Valuation</label>
                            <select v-model="form.fundamentals.valuation"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                                <option value="" disabled>Select Valuation</option>
                                <option v-for="val in ['Overvalued', 'Neutral', 'Undervalued']" :key="val" :value="val">
                                    {{ val }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Seasonal Confluence</label>
                            <select v-model="form.fundamentals.seasonalConfluence"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                                <option value="" disabled>Select Seasonal Confluence</option>
                                <option v-for="sc in ['Yes', 'No']" :key="sc" :value="sc">
                                    {{ sc }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Non-Commercials</label>
                            <select v-model="form.fundamentals.nonCommercials"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                                <option value="" disabled>Select Non-Commercials</option>
                                <option v-for="nc in ['Divergence', 'No-Divergence']" :key="nc" :value="nc">
                                    {{ nc }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CoT Index</label>
                            <select v-model="form.fundamentals.cotIndex"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                                <option value="" disabled>Select CoT Index</option>
                                <option v-for="ci in ['Bullish', 'Neutral', 'Bearish']" :key="ci" :value="ci">
                                    {{ ci }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Zone Qualifiers ({{ form.zone_qualifiers.length }})
                    </h3>
                    <ul class="space-y-2">
                        <li v-for="(qualifier, index) in zoneQualifiers" :key="index" class="flex items-center">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" v-model="form.zone_qualifiers" :value="qualifier"
                                    class="form-checkbox h-5 w-5 text-blue-900 rounded focus:ring-blue-900" />
                                <span class="text-gray-700">{{ qualifier }}</span>
                            </label>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Notes</h3>
                    <textarea v-model="form.notes"
                        class="form-textarea w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                        rows="4" placeholder="Add any notes about this trade setup"></textarea>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Order Entry Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Entry Date</label>
                            <input type="date" v-model="form.entry_date"
                                class="form-input w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Position Type</label>
                            <select v-model="form.position_type"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                                <option value="" disabled>Select Position Type</option>
                                <option value="Long">Long</option>
                                <option value="Short">Short</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Entry Price</label>
                            <input type="number" v-model="form.entry_price" step="any"
                                class="form-input w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stop Price</label>
                            <input type="number" v-model="form.stop_price" step="any"
                                class="form-input w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Target Price</label>
                            <input type="number" v-model="form.target_price" step="any"
                                class="form-input w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Outcome</label>
                            <select v-model="form.outcome"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                                <option value="" disabled>Select Outcome</option>
                                <option value="win">Win</option>
                                <option value="loss">Loss</option>
                                <option value="breakeven">Breakeven</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Screenshot</label>
                            <input type="file" @change="onFileChange" accept="image/*"
                                class="form-input w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    checklist: Object,
    settings: Object,
    tradeEntry: Object
})
console.log(props.tradeEntry.notes)


const zoneQualifiers = [
    'Fresh', 'Original', 'Flip', 'LOL', 'Minimum 1:2 Profit Margin', 'Big Brother Coverage'
]

const form = useForm({
    zone_qualifiers: [...props.checklist.zone_qualifiers],
    technicals: { ...props.checklist.technicals },
    fundamentals: { ...props.checklist.fundamentals },
    score: props.checklist.score,
    asset: props.checklist.asset,
    notes: props.tradeEntry.notes,
    entry_date: props.tradeEntry?.entry_date || '',
    position_type: props.tradeEntry?.position_type || '',
    entry_price: props.tradeEntry?.entry_price || '',
    stop_price: props.tradeEntry?.stop_price || '',
    target_price: props.tradeEntry?.target_price || '',
    outcome: props.tradeEntry?.outcome || '',
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
        form.outcome
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

<style scoped>
.form-select,
.form-textarea {
    transition: all 0.3s ease
}

.form-select:focus,
.form-textarea:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.2)
}
</style>