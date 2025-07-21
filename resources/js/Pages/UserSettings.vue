<template>
    <div class="max-w-5xl mx-auto p-6 bg-gray-100 min-h-screen">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">User Settings</h1>
        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Zone Qualifiers Category -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Zone Qualifiers</h2>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Zone Fresh Weight</label>
                        <input v-model="form.zone_fresh_weight" type="number" class="form-input mt-1 block w-full" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Zone Original Weight</label>
                        <input v-model="form.zone_original_weight" type="number" class="form-input mt-1 block w-full" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Zone Flip Weight</label>
                        <input v-model="form.zone_flip_weight" type="number" class="form-input mt-1 block w-full" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Zone LOL Weight</label>
                        <input v-model="form.zone_lol_weight" type="number" class="form-input mt-1 block w-full" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Zone Min Profit Margin Weight</label>
                        <input v-model="form.zone_min_profit_margin_weight" type="number"
                            class="form-input mt-1 block w-full" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Zone Big Brother Weight</label>
                        <input v-model="form.zone_big_brother_weight" type="number"
                            class="form-input mt-1 block w-full" />
                    </div>
                </div>
                <!-- Technicals Category -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Technicals</h2>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Technical Very Exp/Chp Weight</label>
                        <input v-model="form.technical_very_exp_chp_weight" type="number"
                            class="form-input mt-1 block w-full" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Technical Exp/Chp Weight</label>
                        <input v-model="form.technical_exp_chp_weight" type="number"
                            class="form-input mt-1 block w-full" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Technical Direction Impulsive
                            Weight</label>
                        <input v-model="form.technical_direction_impulsive_weight" type="number"
                            class="form-input mt-1 block w-full" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Technical Direction Correction
                            Weight</label>
                        <input v-model="form.technical_direction_correction_weight" type="number"
                            class="form-input mt-1 block w-full" />
                    </div>
                </div>
                <!-- Fundamentals Category -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Fundamentals</h2>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Fundamental Valuation Weight</label>
                        <input v-model="form.fundamental_valuation_weight" type="number"
                            class="form-input mt-1 block w-full" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Fundamental Seasonal Weight</label>
                        <input v-model="form.fundamental_seasonal_weight" type="number"
                            class="form-input mt-1 block w-full" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Fundamental COT Index Weight</label>
                        <input v-model="form.fundamental_cot_index_weight" type="number"
                            class="form-input mt-1 block w-full" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Fundamental Noncommercial Divergence
                            Weight
                        </label>
                        <input v-model="form.fundamental_noncommercial_divergence_weight" type="number"
                            class="form-input mt-1 block w-full" />
                    </div>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                <div class="flex justify-between items-center space-x-4">
                    <div>
                        <button :disabled="totalWeight !== 100"
                            class="px-6 py-2 mr-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 cursor-pointer transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            Save Settings
                        </button>
                        <Link :href="'/'" method="get" as="button"
                            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors cursor-pointer">
                        Cancel
                        </Link>
                    </div>
                    <div>
                        <div
                            :class="['px-4 py-2 rounded-md font-medium', totalWeight === 100 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                            Total: {{ totalWeight }}
                        </div>
                    </div>
                </div>
                <p v-if="totalWeight !== 100" class="text-red-600 text-right">Total weight must be less than
                    <strong>100</strong>
                </p>
            </div>
        </form>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({ settings: Object })
const form = useForm({
    ...props.settings
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
    form.post('/user-settings')
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
