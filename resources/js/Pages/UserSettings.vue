<template>
    <div class="max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-2xl font-bold mb-6">User Settings</h1>
        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Zone Qualifiers Category -->
                <div>
                    <h2 class="text-xl font-semibold text-blue-900 mb-2 mt-2">Zone Qualifiers</h2>
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
                <div>
                    <h2 class="text-xl font-semibold text-blue-900 mb-2 mt-2">Technicals</h2>
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
                <div>
                    <h2 class="text-xl font-semibold text-blue-900 mb-2 mt-2">Fundamentals</h2>
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
            <div class="mt-6 flex flex-wrap justify-between items-center">
                <div>
                    <Link :href="'/'" method="get" as="button"
                        class="px-4 py-2 mr-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors hover:cursor-pointer">
                    Back
                    </Link>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 cursor-pointer transition-colors">
                        Save Settings
                    </button>
                </div>
                <div>
                    <div
                        :class="['px-4 py-2 rounded-md font-bold', totalWeight <= 100 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
                        Total Weight: {{ totalWeight }}
                    </div>
                </div>
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
    zone_fresh_weight: props.settings.zone_fresh_weight,
    zone_original_weight: props.settings.zone_original_weight,
    zone_flip_weight: props.settings.zone_flip_weight,
    zone_lol_weight: props.settings.zone_lol_weight,
    zone_min_profit_margin_weight: props.settings.zone_min_profit_margin_weight,
    zone_big_brother_weight: props.settings.zone_big_brother_weight,
    technical_very_exp_chp_weight: props.settings.technical_very_exp_chp_weight,
    technical_exp_chp_weight: props.settings.technical_exp_chp_weight,
    technical_direction_impulsive_weight: props.settings.technical_direction_impulsive_weight,
    technical_direction_correction_weight: props.settings.technical_direction_correction_weight,
    fundamental_valuation_weight: props.settings.fundamental_valuation_weight,
    fundamental_seasonal_weight: props.settings.fundamental_seasonal_weight,
    fundamental_cot_index_weight: props.settings.fundamental_cot_index_weight,
    fundamental_noncommercial_divergence_weight: props.settings.fundamental_noncommercial_divergence_weight,
});

const totalWeight = computed(() => {
    return Number(form.zone_fresh_weight) +
        Number(form.zone_original_weight) +
        Number(form.zone_flip_weight) +
        Number(form.zone_lol_weight) +
        Number(form.zone_min_profit_margin_weight) +
        Number(form.zone_big_brother_weight) +
        Number(form.technical_very_exp_chp_weight) +
        Number(form.technical_exp_chp_weight) +
        Number(form.technical_direction_impulsive_weight) +
        Number(form.technical_direction_correction_weight) +
        Number(form.fundamental_valuation_weight) +
        Number(form.fundamental_seasonal_weight) +
        Number(form.fundamental_cot_index_weight) +
        Number(form.fundamental_noncommercial_divergence_weight);
});

function submit() {
    form.post('/user-settings');
}
</script>

<style>
/* Add any custom styles here */
</style>
