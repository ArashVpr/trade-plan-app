<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Checklist Weights</h1>
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Zone Qualifiers Category -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Zone Qualifiers</h2>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fresh Weight</label>
                            <input v-model="form.zone_fresh_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Original Weight</label>
                            <input v-model="form.zone_original_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Flip Weight</label>
                            <input v-model="form.zone_flip_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">LOL Weight</label>
                            <input v-model="form.zone_lol_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Profit Margin
                                Weight</label>
                            <input v-model="form.zone_min_profit_margin_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Big Brother Weight</label>
                            <input v-model="form.zone_big_brother_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                    </div>
                    <!-- Technicals Category -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Technicals</h2>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Location</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Very Exp/Chp Weight</label>
                            <input v-model="form.technical_very_exp_chp_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Exp/Chp Weight</label>
                            <input v-model="form.technical_exp_chp_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Direction</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Impulsive Weight</label>
                            <input v-model="form.technical_direction_impulsive_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correction Weight</label>
                            <input v-model="form.technical_direction_correction_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                    </div>
                    <!-- Fundamentals Category -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Fundamentals</h2>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valuation Weight</label>
                            <input v-model="form.fundamental_valuation_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Seasonality Weight</label>
                            <input v-model="form.fundamental_seasonal_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">COT Index Weight</label>
                            <input v-model="form.fundamental_cot_index_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Non-commercial Divergence
                                Weight
                            </label>
                            <input v-model="form.fundamental_noncommercial_divergence_weight" type="number"
                                class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                        </div>
                    </div>
                </div>

                <div class="mt-8 space-y-4">
                    <div class="flex justify-between items-center space-x-4">
                        <div>
                            <button
                                class="px-6 py-2 mr-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 cursor-pointer transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                Save Settings
                            </button>
                            <Link :href="'/'"
                                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors cursor-pointer">
                            Cancel
                            </Link>
                            <button type="button" @click="resetDefaults"
                                class="px-6 py-2 ml-8 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors cursor-pointer">
                                Reset to Default
                            </button>
                        </div>
                        <div>
                            <div class="px-4 py-2 rounded-md font-medium bg-blue-100 text-blue-800">
                                Total: {{ totalWeight }}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    weights: Object,
    defaults: Object,
})

// Initialize form with persisted weights
const form = useForm({
    ...props.weights
})

// Computed total weight
const totalWeight = computed(() =>
    Object.keys(form).reduce((sum, key) => {
        if (key.endsWith('_weight')) {
            return sum + Number(form[key] || 0)
        }
        return sum
    }, 0)
)

function submit() {
    form.post('/checklist-weights')
}
// Reset form values to initial defaults from model
function resetDefaults() {
    Object.entries(props.defaults).forEach(([key, value]) => {
        if (key in form) {
            form[key] = value;
        }
    });
}
</script>

<style>
/* Improved input focus styling */
input[type="number"] {
    transition: box-shadow 0.2s;
}

input[type="number"]:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
}
</style>
