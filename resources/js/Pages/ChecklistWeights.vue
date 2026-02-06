<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto">
                <!-- Header Section -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-blue-900 dark:text-blue-300 mb-2">Checklist Weights</h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Customize the scoring weights for your trade analysis. Higher weights give more influence on the
                        final score.
                    </p>
                </div>

                <!-- Progress/Status Bar -->
                <Card class="mb-6">
                    <template #content>
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-6">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-calculator text-blue-600"></i>
                                    <span class="font-medium">Total Weight:</span>
                                    <Chip :label="`${totalWeight} points`" :class="totalWeightClass" />
                                </div>
                                <div v-if="hasUnsavedChanges" class="flex items-center gap-2 text-amber-600">
                                    <i class="pi pi-exclamation-circle"></i>
                                    <span class="text-sm">Unsaved changes</span>
                                </div>

                                <!-- Quick Status -->
                                <div class="hidden lg:flex items-center gap-2">
                                    <i :class="balanceIcon" class="text-sm"></i>
                                    <span class="text-sm font-medium">{{ balanceMessage }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <Button label="Reset to Defaults" icon="pi pi-refresh" severity="secondary" outlined
                                    size="small" @click="resetDefaults" />
                                <Button label="Save Changes" icon="pi pi-save" :loading="form.processing"
                                    :disabled="!hasUnsavedChanges" @click="submit" />
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Weight Distribution Chart -->
                <Card class="mb-6">
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-chart-pie text-blue-600"></i>
                            Weight Distribution
                        </div>
                    </template>
                    <template #content>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-center">
                            <!-- Chart -->
                            <div class="flex items-center justify-center">
                                <div class="w-48 sm:w-64 md:w-80 lg:w-96 h-48 sm:h-64 md:h-80 lg:h-80 max-w-full">
                                    <Chart type="doughnut" :data="chartData" :options="chartOptions"
                                        class="w-full h-full" />
                                </div>
                            </div>

                            <!-- Chart Legend & Stats -->
                            <div class="space-y-4">
                                <div class="space-y-3">
                                    <div
                                        class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <div class="w-4 h-4 bg-blue-100 dark:bg-blue-900/40 rounded-full"></div>
                                            <span class="font-medium">Zone Qualifiers</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-blue-600">{{ zoneWeightTotal }} </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ zonePercentage }}%
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                                            <span class="font-medium">Technical Analysis</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-green-600">{{ technicalWeightTotal }} </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ technicalPercentage
                                            }}%</div>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                                            <span class="font-medium">Fundamental Analysis</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-purple-600">{{ fundamentalWeightTotal }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{
                                                fundamentalPercentage }}%</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Balance Indicator -->
                                <div class="mt-4 p-3 rounded-lg" :class="balanceIndicatorClass">
                                    <div class="flex items-center gap-2">
                                        <i :class="balanceIcon"></i>
                                        <span class="font-medium">{{ balanceMessage }}</span>
                                    </div>
                                    <p class="text-sm mt-1 opacity-80">{{ balanceDescription }}</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>

                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Zone Qualifiers Category -->
                        <Card>
                            <template #title>
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-map-marker text-blue-600"></i>
                                    Zone Qualifiers
                                </div>
                            </template>
                            <template #content>
                                <div class="space-y-4">
                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                            <span>Fresh Zone</span>
                                            <span class="text-blue-600 font-semibold">{{ form.zone_fresh_weight }}
                                            </span>
                                        </label>
                                        <Slider v-model="form.zone_fresh_weight" :min="0" :max="100" :step="1"
                                            class="w-full" />
                                        <small v-if="errors.zone_fresh_weight" class="p-error">{{
                                            errors.zone_fresh_weight }}</small>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                            <span>Original Zone</span>
                                            <span class="text-blue-600 font-semibold">{{ form.zone_original_weight }}
                                            </span>
                                        </label>
                                        <Slider v-model="form.zone_original_weight" :min="0" :max="100" :step="1"
                                            class="w-full" />
                                        <small v-if="errors.zone_original_weight" class="p-error">{{
                                            errors.zone_original_weight }}</small>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                            <span>Flip Zone</span>
                                            <span class="text-blue-600 font-semibold">{{ form.zone_flip_weight }}
                                            </span>
                                        </label>
                                        <Slider v-model="form.zone_flip_weight" :min="0" :max="100" :step="1"
                                            class="w-full" />
                                        <small v-if="errors.zone_flip_weight" class="p-error">{{ errors.zone_flip_weight
                                            }}</small>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                            <span>Liquidity Zone</span>
                                            <span class="text-blue-600 font-semibold">{{ form.zone_lol_weight }}
                                            </span>
                                        </label>
                                        <Slider v-model="form.zone_lol_weight" :min="0" :max="100" :step="1"
                                            class="w-full" />
                                        <small v-if="errors.zone_lol_weight" class="p-error">{{ errors.zone_lol_weight
                                            }}</small>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                            <span>Min Profit Margin</span>
                                            <span class="text-blue-600 font-semibold">{{
                                                form.zone_min_profit_margin_weight }} </span>
                                        </label>
                                        <Slider v-model="form.zone_min_profit_margin_weight" :min="0" :max="100"
                                            :step="1" class="w-full" />
                                        <small v-if="errors.zone_min_profit_margin_weight" class="p-error">{{
                                            errors.zone_min_profit_margin_weight }}</small>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                            <span>Big Brother</span>
                                            <span class="text-blue-600 font-semibold">{{ form.zone_big_brother_weight }}
                                            </span>
                                        </label>
                                        <Slider v-model="form.zone_big_brother_weight" :min="0" :max="100" :step="1"
                                            class="w-full" />
                                        <small v-if="errors.zone_big_brother_weight" class="p-error">{{
                                            errors.zone_big_brother_weight }}</small>
                                    </div>
                                </div>
                            </template>
                        </Card>
                        <!-- Technical Analysis Category -->
                        <Card>
                            <template #title>
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-chart-line text-green-600"></i>
                                    Technical Analysis
                                </div>
                            </template>
                            <template #content>
                                <div class="space-y-6">
                                    <!-- Location Section -->
                                    <div>
                                        <h3
                                            class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                                            <i class="pi pi-map text-gray-500 dark:text-gray-400 text-sm"></i>
                                            Location
                                        </h3>
                                        <div class="space-y-4">
                                            <div class="flex flex-col gap-2">
                                                <label
                                                    class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                                    <span>Very Expensive / Very Cheap</span>
                                                    <span class="text-green-600 font-semibold">{{
                                                        form.technical_very_exp_chp_weight }} </span>
                                                </label>
                                                <Slider v-model="form.technical_very_exp_chp_weight" :min="0" :max="100"
                                                    :step="1" class="w-full" />
                                                <small v-if="errors.technical_very_exp_chp_weight" class="p-error">{{
                                                    errors.technical_very_exp_chp_weight }}</small>
                                            </div>

                                            <div class="flex flex-col gap-2">
                                                <label
                                                    class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                                    <span>Expensive / Cheap</span>
                                                    <span class="text-green-600 font-semibold">{{
                                                        form.technical_exp_chp_weight }} </span>
                                                </label>
                                                <Slider v-model="form.technical_exp_chp_weight" :min="0" :max="100"
                                                    :step="1" class="w-full" />
                                                <small v-if="errors.technical_exp_chp_weight" class="p-error">{{
                                                    errors.technical_exp_chp_weight }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Direction Section -->
                                    <div>
                                        <h3
                                            class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                                            <i class="pi pi-arrow-right text-gray-500 dark:text-gray-400 text-sm"></i>
                                            Direction
                                        </h3>
                                        <div class="space-y-4">
                                            <div class="flex flex-col gap-2">
                                                <label
                                                    class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                                    <span>Impulsive</span>
                                                    <span class="text-green-600 font-semibold">{{
                                                        form.technical_direction_impulsive_weight }} </span>
                                                </label>
                                                <Slider v-model="form.technical_direction_impulsive_weight" :min="0"
                                                    :max="100" :step="1" class="w-full" />
                                                <small v-if="errors.technical_direction_impulsive_weight"
                                                    class="p-error">{{ errors.technical_direction_impulsive_weight
                                                    }}</small>
                                            </div>

                                            <div class="flex flex-col gap-2">
                                                <label
                                                    class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                                    <span>Correction</span>
                                                    <span class="text-green-600 font-semibold">{{
                                                        form.technical_direction_correction_weight }} </span>
                                                </label>
                                                <Slider v-model="form.technical_direction_correction_weight" :min="0"
                                                    :max="100" :step="1" class="w-full" />
                                                <small v-if="errors.technical_direction_correction_weight"
                                                    class="p-error">{{ errors.technical_direction_correction_weight
                                                    }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>
                        <!-- Fundamentals Category -->
                        <Card>
                            <template #title>
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-globe text-purple-600"></i>
                                    Fundamental Analysis
                                    <i v-tooltip.top="redistributionTooltip" class="pi pi-info-circle text-gray-400 cursor-help text-sm ml-auto"></i>
                                </div>
                            </template>
                            <template #content>
                                <Message severity="info" :closable="false" class="mb-4">
                                    <template #icon>
                                        <i class="pi pi-info-circle text-sm"></i>
                                    </template>
                                    <div class="text-xs">
                                        When fundamentals are excluded from a checklist, these weights are removed from the calculation. The score redistributes proportionally across zones and technicals only.
                                    </div>
                                </Message>
                                <div class="space-y-4">
                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                            <span>Valuation</span>
                                            <span class="text-purple-600 font-semibold">{{
                                                form.fundamental_valuation_weight }} </span>
                                        </label>
                                        <Slider v-model="form.fundamental_valuation_weight" :min="0" :max="100"
                                            :step="1" class="w-full" />
                                        <small v-if="errors.fundamental_valuation_weight" class="p-error">{{
                                            errors.fundamental_valuation_weight }}</small>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                            <span>Seasonality</span>
                                            <span class="text-purple-600 font-semibold">{{
                                                form.fundamental_seasonal_weight }} </span>
                                        </label>
                                        <Slider v-model="form.fundamental_seasonal_weight" :min="0" :max="100" :step="1"
                                            class="w-full" />
                                        <small v-if="errors.fundamental_seasonal_weight" class="p-error">{{
                                            errors.fundamental_seasonal_weight }}</small>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                            <span>COT Index</span>
                                            <span class="text-purple-600 font-semibold">{{
                                                form.fundamental_cot_index_weight }} </span>
                                        </label>
                                        <Slider v-model="form.fundamental_cot_index_weight" :min="0" :max="100"
                                            :step="1" class="w-full" />
                                        <small v-if="errors.fundamental_cot_index_weight" class="p-error">{{
                                            errors.fundamental_cot_index_weight }}</small>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="font-medium text-gray-700 dark:text-gray-300 flex justify-between">
                                            <span>Non-Commercial Divergence</span>
                                            <span class="text-purple-600 font-semibold">{{
                                                form.fundamental_noncommercial_divergence_weight }} </span>
                                        </label>
                                        <Slider v-model="form.fundamental_noncommercial_divergence_weight" :min="0"
                                            :max="100" :step="1" class="w-full" />
                                        <small v-if="errors.fundamental_noncommercial_divergence_weight"
                                            class="p-error">{{ errors.fundamental_noncommercial_divergence_weight
                                            }}</small>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </form>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import { useToast } from 'primevue/usetoast'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    weights: Object,
    defaults: Object,
})

const toast = useToast()

// Initialize form with persisted weights
const form = useForm({
    ...props.weights
})

// Track original values to detect changes
const originalValues = ref({ ...props.weights })

// Computed properties
const totalWeight = computed(() =>
    Object.keys(form).reduce((sum, key) => {
        if (key.endsWith('_weight')) {
            return sum + Number(form[key] || 0)
        }
        return sum
    }, 0)
)

const zoneWeightTotal = computed(() => {
    return Number(form.zone_fresh_weight || 0) +
        Number(form.zone_original_weight || 0) +
        Number(form.zone_flip_weight || 0) +
        Number(form.zone_lol_weight || 0) +
        Number(form.zone_min_profit_margin_weight || 0) +
        Number(form.zone_big_brother_weight || 0);
}); const technicalWeightTotal = computed(() => {
    return Number(form.technical_s_r_horizontal_weight || 0) +
        Number(form.technical_s_r_diagonal_weight || 0) +
        Number(form.technical_s_r_curved_weight || 0) +
        Number(form.technical_very_exp_chp_weight || 0) +
        Number(form.technical_exp_chp_weight || 0) +
        Number(form.technical_direction_impulsive_weight || 0) +
        Number(form.technical_direction_correction_weight || 0);
});

const fundamentalWeightTotal = computed(() => {
    return Number(form.fundamental_valuation_weight || 0) +
        Number(form.fundamental_seasonal_weight || 0) +
        Number(form.fundamental_cot_index_weight || 0) +
        Number(form.fundamental_noncommercial_divergence_weight || 0);
});

// Percentage calculations for chart (relative distribution - always adds to 100%)
const categoryTotal = computed(() => {
    return zoneWeightTotal.value + technicalWeightTotal.value + fundamentalWeightTotal.value;
});

const zonePercentage = computed(() => {
    const total = categoryTotal.value;
    return total > 0 ? Math.round((zoneWeightTotal.value / total) * 100) : 0;
});

const technicalPercentage = computed(() => {
    const total = categoryTotal.value;
    return total > 0 ? Math.round((technicalWeightTotal.value / total) * 100) : 0;
});

const fundamentalPercentage = computed(() => {
    const total = categoryTotal.value;
    return total > 0 ? Math.round((fundamentalWeightTotal.value / total) * 100) : 0;
});

// Tooltip explaining weight redistribution
const redistributionTooltip = computed(() => {
    const zonesOnly = zoneWeightTotal.value;
    const technicalsOnly = technicalWeightTotal.value;
    const totalWithoutFundamentals = zonesOnly + technicalsOnly;
    
    if (totalWithoutFundamentals === 0) return 'Configure weights to see redistribution example';
    
    const zonesPercent = Math.round((zonesOnly / totalWithoutFundamentals) * 100);
    const technicalsPercent = Math.round((technicalsOnly / totalWithoutFundamentals) * 100);
    
    return `When fundamentals are excluded:\n\nZones: ${zonesPercent}%\nTechnicals: ${technicalsPercent}%\n\nScores are calculated only from zones and technicals, maintaining accurate 0-100% range.`;
});

// Chart data
const chartData = computed(() => {
    return {
        labels: ['Zone Qualifiers', 'Technical Analysis', 'Fundamental Analysis'],
        datasets: [
            {
                data: [zoneWeightTotal.value, technicalWeightTotal.value, fundamentalWeightTotal.value],
                backgroundColor: ['#3B82F6', '#10B981', '#8B5CF6'],
                borderColor: ['#2563EB', '#059669', '#7C3AED'],
                borderWidth: 6,
                hoverBackgroundColor: ['#60A5FA', '#34D399', '#A78BFA'],
                hoverBorderColor: ['#1D4ED8', '#047857', '#6D28D9'],
            }
        ]
    };
});

const chartOptions = computed(() => {
    return {
        responsive: true,
        maintainAspectRatio: false,
        aspectRatio: 1,
        plugins: {
            legend: {
                display: false, // We'll use custom legend
            },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const categorySum = zoneWeightTotal.value + technicalWeightTotal.value + fundamentalWeightTotal.value;
                        const percentage = categorySum > 0 ? Math.round((value / categorySum) * 100) : 0;
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        },
        layout: { padding: 0 },
        cutout: '50%', // Makes it a doughnut instead of pie (thicker ring)
    };
});

// Balance indicator
const balanceIndicatorClass = computed(() => {
    const zone = zoneWeightTotal.value;
    const technical = technicalWeightTotal.value;
    const fundamental = fundamentalWeightTotal.value;

    // Check if all three categories have exactly the same points
    if (zone === technical && technical === fundamental && zone > 0) {
        return 'bg-gradient-to-r from-blue-50 via-green-50 to-purple-50 dark:from-blue-900/30 dark:via-green-900/30 dark:to-purple-900/30 border border-gray-300 dark:border-gray-700 text-gray-800 dark:text-gray-100';
    }

    const zonePercent = zonePercentage.value;
    const technicalPercent = technicalPercentage.value;
    const fundamentalPercent = fundamentalPercentage.value;

    // Find the category with the highest percentage
    if (zonePercent >= technicalPercent && zonePercent >= fundamentalPercent) {
        return 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200';
    }
    if (technicalPercent >= zonePercent && technicalPercent >= fundamentalPercent) {
        return 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200';
    }
    return 'bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 text-purple-800 dark:text-purple-200';
});

const balanceIcon = computed(() => {
    const zone = zoneWeightTotal.value;
    const technical = technicalWeightTotal.value;
    const fundamental = fundamentalWeightTotal.value;

    // Check if all three categories have exactly the same points
    if (zone === technical && technical === fundamental && zone > 0) {
        return 'pi pi-balance-scale';
    }

    const zonePercent = zonePercentage.value;
    const technicalPercent = technicalPercentage.value;
    const fundamentalPercent = fundamentalPercentage.value;

    // Find the category with the highest percentage
    if (zonePercent >= technicalPercent && zonePercent >= fundamentalPercent) {
        return 'pi pi-map-marker';
    }
    if (technicalPercent >= zonePercent && technicalPercent >= fundamentalPercent) {
        return 'pi pi-chart-line';
    }
    return 'pi pi-globe';
});

const balanceMessage = computed(() => {
    const zone = zoneWeightTotal.value;
    const technical = technicalWeightTotal.value;
    const fundamental = fundamentalWeightTotal.value;

    // Check if all three categories have exactly the same points
    if (zone === technical && technical === fundamental && zone > 0) {
        return 'Perfectly Balanced Strategy';
    }

    const zonePercent = zonePercentage.value;
    const technicalPercent = technicalPercentage.value;
    const fundamentalPercent = fundamentalPercentage.value;

    // Find the category with the highest percentage
    if (zonePercent >= technicalPercent && zonePercent >= fundamentalPercent) {
        return 'Zone-Focused Strategy';
    }
    if (technicalPercent >= zonePercent && technicalPercent >= fundamentalPercent) {
        return 'Technical-Driven Approach';
    }
    return 'Fundamental-Based Analysis';
});

const balanceDescription = computed(() => {
    const zone = zoneWeightTotal.value;
    const technical = technicalWeightTotal.value;
    const fundamental = fundamentalWeightTotal.value;

    // Check if all three categories have exactly the same points
    if (zone === technical && technical === fundamental && zone > 0) {
        return `All analysis categories are equally weighted at ${zone} points each (33.3% each). This creates a comprehensive, well-rounded approach that gives equal importance to zone qualifiers, technical indicators, and fundamental factors.`;
    }

    const zonePercent = zonePercentage.value;
    const technicalPercent = technicalPercentage.value;
    const fundamentalPercent = fundamentalPercentage.value;

    // Find the category with the highest percentage and provide specific message
    if (zonePercent >= technicalPercent && zonePercent >= fundamentalPercent) {
        return `Your analysis is weighted towards zone qualifiers (${zonePercent}%). This emphasizes location-based trade setups and market structure analysis.`;
    }
    if (technicalPercent >= zonePercent && technicalPercent >= fundamentalPercent) {
        return `Your analysis favors technical indicators (${technicalPercent}%). This approach prioritizes price action patterns and market direction analysis.`;
    }
    return `Your analysis leans on fundamental factors (${fundamentalPercent}%). This strategy emphasizes market sentiment and economic drivers.`;
});

const totalWeightClass = computed(() => {
    const total = totalWeight.value
    if (total < 50) return 'bg-red-100 text-red-800'
    if (total < 80) return 'bg-yellow-100 text-yellow-800'
    return 'bg-green-100 text-green-800'
})

const hasUnsavedChanges = computed(() => {
    return Object.keys(form).some(key => {
        if (key.endsWith('_weight')) {
            return form[key] !== originalValues.value[key]
        }
        return false
    })
})

// Error handling
const errors = ref({})

// Validation
const validateForm = () => {
    errors.value = {}
    let isValid = true

    Object.keys(form).forEach(key => {
        if (key.endsWith('_weight')) {
            const value = Number(form[key])
            if (value < 0) {
                errors.value[key] = 'Weight cannot be negative'
                isValid = false
            }
            if (value > 100) {
                errors.value[key] = 'Weight cannot exceed 100'
                isValid = false
            }
        }
    })

    return isValid
}

// Methods
function submit() {
    if (!validateForm()) {
        toast.add({
            severity: 'error',
            summary: 'Validation Error',
            detail: 'Please fix the errors before saving',
            life: 3000,
        })
        return
    }

    form.post('/checklist-weights', {
        onSuccess: () => {
            originalValues.value = { ...form }
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Checklist weights updated successfully',
                life: 3000,
            })
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Failed to update checklist weights',
                life: 3000,
            })
        },
    })
}

function resetDefaults() {
    Object.entries(props.defaults).forEach(([key, value]) => {
        if (key in form) {
            form[key] = value
        }
    })

    toast.add({
        severity: 'info',
        summary: 'Reset',
        detail: 'Weights reset to default values',
        life: 3000,
    })
}

// Watch for form changes to update validation
watch(form, () => {
    if (Object.keys(errors.value).length > 0) {
        validateForm()
    }
}, { deep: true })
</script>
