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
                            form.score <= 80 ? 'text-yellow-600 bg-yellow-100' :
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
            </form>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    checklist: {
        type: Object,
        required: true
    }
})

const zoneQualifiers = [
    'Fresh', 'Original', 'Flip', 'LOL', 'Minimum 1:2 Profit Margin', 'Big Brother Coverage'
]

const form = useForm({
    zone_qualifiers: [...props.checklist.zone_qualifiers],
    technicals: { ...props.checklist.technicals },
    fundamentals: { ...props.checklist.fundamentals },
    score: props.checklist.score,
    asset: props.checklist.asset,
    notes: props.checklist.notes
})

const canSubmit = computed(() => {
    return form.technicals.location &&
        form.technicals.direction &&
        form.fundamentals.valuation &&
        form.fundamentals.seasonalConfluence &&
        form.fundamentals.nonCommercials &&
        form.fundamentals.cotIndex &&
        form.zone_qualifiers.length > 0
})

const evaluationScore = () => {
    let score = 0;
    score += form.zone_qualifiers.filter(Boolean).length * 5 // Each checked zone qualifier contributes 5 points
    score += ['Very Cheap', 'Very Expensive'].includes(form.technicals.location) ? 12 : // 12 points for 'Very Cheap' and 'Very Expensive'
        ['Cheap', 'Expensive'].includes(form.technicals.location) ? 7 : 0
    score += form.technicals.direction === 'Correction' ? 6 :
        form.technicals.direction === 'Impulsion' ? 12 : 0
    score += ['Undervalued', 'Overvalued'].includes(form.fundamentals.valuation) ? 13 : 0
    score += form.fundamentals.seasonalConfluence === 'Yes' ? 6 : 0
    score += form.fundamentals.nonCommercials === 'Divergence' ? 15 : 0
    score += ['Bullish', 'Bearish'].includes(form.fundamentals.cotIndex) ? 12 : 0
    form.score = score
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