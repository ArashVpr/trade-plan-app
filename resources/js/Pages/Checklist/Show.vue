<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Checklist Details</h1>

            <!-- Action Buttons -->
            <div class="flex justify-between mb-6">
                <Button label="Back" icon="pi pi-arrow-left" severity="secondary" @click="router.get(route('checklists.index'))" />
                <div class="flex gap-2">
                    <Button label="Edit" icon="pi pi-pencil" severity="success"
                        @click="router.get(route('checklists.edit', checklist.id))" />
                    <Button label="Delete" icon="pi pi-trash" severity="danger" @click="confirmDelete" />
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Trade Setup Details -->
                <Card>
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-chart-line text-blue-900"></i>
                            <span class="text-blue-900">Trade Setup Details</span>
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-4">
                            <!-- Symbol -->
                            <div class="field">
                                <label class="block text-sm font-medium mb-1">Symbol</label>
                                <Chip :label="checklist.symbol || 'N/A'" />
                            </div>

                            <!-- Score -->
                            <div class="field">
                                <label class="block text-sm font-medium mb-1">Score</label>
                                <div class="flex items-center gap-2">
                                    <Tag :value="`${checklist.score}/100`" :severity="getScoreSeverity(checklist.score)"
                                        class="text-lg font-bold px-4 py-2" />
                                    <div v-if="checklist.score === 100" class="text-yellow-500 font-bold">
                                        ★ All Stars Aligned ★
                                    </div>
                                </div>
                            </div>

                            <!-- Created Date -->
                            <div class="field">
                                <label class="block text-sm font-medium mb-1">Created</label>
                                <span class="text-sm text-gray-600">
                                    {{ new Date(checklist.created_at).toLocaleDateString() }}
                                </span>
                            </div>

                            <!-- Zone Qualifiers -->
                            <div class="field">
                                <label class="block text-sm font-medium mb-1">
                                    Zone Qualifiers ({{ checklist.zone_qualifiers.length }})
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <Chip v-for="qualifier in checklist.zone_qualifiers" :key="qualifier"
                                        :label="qualifier" class="mb-1" />
                                    <span v-if="checklist.zone_qualifiers.length === 0" class="text-gray-500 text-sm">
                                        None selected
                                    </span>
                                </div>
                            </div>

                            <!-- Technicals -->
                            <div class="field">
                                <label class="block text-sm font-medium mb-1">Technicals</label>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium">Location:</span>
                                        <Tag :value="checklist.technicals?.location || 'Not selected'"
                                            :severity="checklist.technicals?.location ? 'info' : 'secondary'" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium">Direction:</span>
                                        <Tag :value="checklist.technicals?.direction || 'Not selected'"
                                            :severity="checklist.technicals?.direction ? 'info' : 'secondary'" />
                                    </div>
                                </div>
                            </div>

                            <!-- Fundamentals -->
                            <div class="field">
                                <label class="block text-sm font-medium mb-1">Fundamentals</label>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium">Valuation:</span>
                                        <Tag :value="checklist.fundamentals?.valuation || 'Not selected'"
                                            :severity="checklist.fundamentals?.valuation ? 'info' : 'secondary'" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium">Seasonal Confluence:</span>
                                        <Tag :value="checklist.fundamentals?.seasonalConfluence || 'Not selected'"
                                            :severity="checklist.fundamentals?.seasonalConfluence ? 'info' : 'secondary'" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium">Non-Commercials:</span>
                                        <Tag :value="checklist.fundamentals?.nonCommercials || 'Not selected'"
                                            :severity="checklist.fundamentals?.nonCommercials ? 'info' : 'secondary'" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium">CoT Index:</span>
                                        <Tag :value="checklist.fundamentals?.cotIndex || 'Not selected'"
                                            :severity="checklist.fundamentals?.cotIndex ? 'info' : 'secondary'" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Analysis Only Placeholder Card -->
                <Card v-if="!tradeEntry">
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-chart-line text-blue-900"></i>
                            <span class="text-blue-900">Trade Status</span>
                        </div>
                    </template>
                    <template #content>
                        <div class="text-center py-8">
                            <div class="mb-4">
                                <i class="pi pi-info-circle text-blue-900 text-6xl opacity-50"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">Analysis Only</h3>
                            <p class="text-gray-600 mb-4">
                                This setup was analyzed but no trade was taken. You can edit this checklist to add order
                                entry details if you decide to trade this setup.
                            </p>
                            <div class="flex justify-center">
                                <Button label="Add Trade Details" icon="pi pi-plus" severity="info" outlined
                                    @click="router.get(route('checklists.edit', checklist.id) + '?focus=order-entry')" />
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Order Entry Details -->
                <Card v-if="tradeEntry">
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-money-bill text-blue-900"></i>
                            <span class="text-blue-900">Order Entry Details</span>
                        </div>
                    </template>
                    <template #content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Entry Date -->
                            <div class="field">
                                <label class="block text-sm font-medium mb-1">Entry Date</label>
                                <InputText :value="tradeEntry.entry_date || 'N/A'" readonly class="w-full" />
                            </div>

                            <!-- Position Type -->
                            <div class="field">
                                <label class="block text-sm font-medium mb-1">Position</label>
                                <Tag :value="getPositionDisplay(tradeEntry.position_type)"
                                    :severity="tradeEntry.position_type === 'Long' ? 'success' : 'danger'"
                                    class="w-full" />
                            </div>

                            <!-- Prices -->
                            <div class="field">
                                <label class="block text-sm font-medium mb-1">Entry Price</label>
                                <InputText
                                    :value="tradeEntry.entry_price ? Number(tradeEntry.entry_price).toFixed(4) : 'N/A'"
                                    readonly class="w-full" />
                            </div>

                            <div class="field">
                                <label class="block text-sm font-medium mb-1">Stop Price</label>
                                <InputText
                                    :value="tradeEntry.stop_price ? Number(tradeEntry.stop_price).toFixed(4) : 'N/A'"
                                    readonly class="w-full" />
                            </div>

                            <div class="field">
                                <label class="block text-sm font-medium mb-1">Target Price</label>
                                <InputText
                                    :value="tradeEntry.target_price ? Number(tradeEntry.target_price).toFixed(4) : 'N/A'"
                                    readonly class="w-full" />
                            </div>

                            
                            <!-- R:R -->
                            <div class="field">
                                <label class="block text-sm font-medium mb-1">Risk:Reward</label>
                                <InputText :value="tradeEntry.rrr ? Number(tradeEntry.rrr).toFixed(2) : 'N/A'" readonly
                                    class="w-full" />
                            </div>
                                <!-- Trade Status -->
                                <div class="field">
                                    <label class="block text-sm font-medium mb-1">Trade Status</label>
                                    <Tag :value="getTradeStatus(tradeEntry)"
                                        :severity="getTradeStatusSeverity(tradeEntry)" />
                                </div>
                        </div>

                        <!-- Notes -->
                        <div class="field mt-4">
                            <label class="block text-sm font-medium mb-1">Notes</label>
                            <Textarea :value="tradeEntry.notes || 'None'" readonly rows="3" class="w-full" />
                        </div>

                        <!-- Screenshot -->
                        <div v-if="tradeEntry.screenshot_path" class="field mt-4">
                            <label class="block text-sm font-medium mb-1">Screenshot</label>
                            <Image :src="`/storage/${tradeEntry.screenshot_path}`" alt="Trading Screenshot" preview
                                class="max-w-full h-auto rounded border" />
                        </div>
                    </template>
                </Card>

                <!-- Score Breakdown Chart -->
                <Card class="lg:col-span-2">
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-chart-pie text-blue-900"></i>
                            <span class="text-blue-900">Score Breakdown</span>
                        </div>
                    </template>
                    <template #content>
                        <div class="w-full h-80">
                            <Radar :data="chartData" :options="chartOptions" />
                        </div>
                    </template>
                </Card>
            </div>
        </div>

        <!-- PrimeVue Dialog Components -->
        <ConfirmDialog></ConfirmDialog>
        <Toast />
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { Radar } from 'vue-chartjs'
import { Chart, registerables } from 'chart.js'
import { computed } from 'vue'
import { route } from 'ziggy-js'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import AppLayout from '@/Layouts/AppLayout.vue'

Chart.register(...registerables)

const confirm = useConfirm()
const toast = useToast()

const props = defineProps({
    checklist: Object,
    tradeEntry: Object
})

// Helper functions for PrimeVue components
const getScoreSeverity = (score) => {
    if (score < 50) return 'danger'
    if (score <= 80) return 'warning'
    return 'success'
}

const getPositionDisplay = (positionType) => {
    if (positionType === 'Long') return '📈 Long'
    if (positionType === 'Short') return '📉 Short'
    return 'N/A'
}

/**
 * Get human-readable trade status - matches DashboardController logic
 */
const getTradeStatus = (tradeEntry) => {
    if (!tradeEntry) {
        return 'Analysis Only'
    }

    // Check if we have the new trade_status field (after migration)
    if (tradeEntry.trade_status) {
        switch (tradeEntry.trade_status) {
            case 'pending': return 'Pending'
            case 'active': return 'Open'
            case 'win': return 'Win'
            case 'loss': return 'Loss'
            case 'breakeven': return 'Breakeven'
            case 'cancelled': return 'Cancelled'
            default: return 'Unknown'
        }
    }

    return 'Trade Pending'
}

/**
 * Get PrimeVue severity for trade status - matches DashboardController logic
 */
const getTradeStatusSeverity = (tradeEntry) => {
    if (!tradeEntry) {
        return 'info' // Blue for analysis only
    }

    // Check if we have the new trade_status field
    if (tradeEntry.trade_status) {
        switch (tradeEntry.trade_status) {
            case 'pending': return 'warn'   // Yellow  
            case 'active': return 'info'    // Yellow
            case 'win': return 'success'     // Green
            case 'loss': return 'danger'     // Red  
            case 'breakeven': return 'warn' // Yellow
            case 'cancelled': return 'secondary' // Gray
            default: return 'secondary'
        }
    }

    return 'warn' // Yellow for pending
}

const confirmDelete = () => {
    confirm.require({
        message: 'Do you want to delete this checklist?',
        header: 'Danger Zone',
        icon: 'pi pi-info-circle',
        rejectLabel: 'Cancel',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger'
        },
        accept: () => {
            router.post(route('checklists.destroy', props.checklist.id))
            toast.add({ severity: 'success', summary: 'Success', detail: 'Checklist deleted', life: 3000 });
        },
        reject: () => {
            toast.add({ severity: 'info', summary: 'Cancelled', detail: 'Delete cancelled', life: 3000 });
        }
    })
}

// Calculate individual category scores with normalization
const calculateCategoryScores = (checklist) => {
    const maxValues = { ZoneQualifiers: 30, Technicals: 24, Fundamentals: 46 };
    let scores = {
        ZoneQualifiers: Math.min(checklist.zone_qualifiers.length * 5, maxValues.ZoneQualifiers),
        Technicals: 0,
        Fundamentals: 0
    };

    // Technicals score
    scores.Technicals += ['Very Cheap', 'Very Expensive'].includes(checklist.technicals.location) ? 12 :
        ['Cheap', 'Expensive'].includes(checklist.technicals.location) ? 7 : 0;
    scores.Technicals += checklist.technicals.direction === 'Correction' ? 6 :
        checklist.technicals.direction === 'Impulsion' ? 12 : 0;
    scores.Technicals = Math.min(scores.Technicals, maxValues.Technicals);

    // Fundamentals score
    scores.Fundamentals += ['Undervalued', 'Overvalued'].includes(checklist.fundamentals.valuation) ? 13 : 0;
    scores.Fundamentals += checklist.fundamentals.seasonalConfluence === 'Yes' ? 6 : 0;
    scores.Fundamentals += checklist.fundamentals.nonCommercials === 'Divergence' ? 15 : 0;
    scores.Fundamentals += ['Bullish', 'Bearish'].includes(checklist.fundamentals.cotIndex) ? 12 : 0;
    scores.Fundamentals = Math.min(scores.Fundamentals, maxValues.Fundamentals);

    return scores;
};

const chartData = computed(() => {
    const scores = calculateCategoryScores(props.checklist);
    const maxValues = { ZoneQualifiers: 30, Technicals: 24, Fundamentals: 46 };
    return {
        labels: ['Zone Qualifiers', 'Technicals', 'Fundamentals'],
        datasets: [{
            label: 'Score Contribution',
            data: [
                (scores.ZoneQualifiers / maxValues.ZoneQualifiers) * 100,
                (scores.Technicals / maxValues.Technicals) * 100,
                (scores.Fundamentals / maxValues.Fundamentals) * 100
            ],
            backgroundColor: (context) => {
                const index = context.dataIndex;
                const labels = ['ZoneQualifiers', 'Technicals', 'Fundamentals'];
                const value = scores[labels[index]];
                const max = maxValues[labels[index]];
                return value === max ? 'rgba(16, 185, 129, 0.2)' : 'rgba(16, 185, 129, 0.2)';
            },
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 2,
            pointBackgroundColor: 'rgba(16, 185, 129, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(16, 185, 129, 1)'
        }]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        r: {
            beginAtZero: true,
            max: 100,
            ticks: {
                stepSize: 25,
                callback: (value) => `${value}%`
            },
            pointLabels: {
                font: {
                    size: 14
                }
            }
        }
    },
    plugins: {
        legend: {
            display: true,
            position: 'top',
            labels: {
                font: {
                    size: 18
                },
                generateLabels: (chart) => {
                    const data = chart.data;
                    const scores = calculateCategoryScores(props.checklist); // Calculate scores for display
                    const maxValues = { ZoneQualifiers: 30, Technicals: 24, Fundamentals: 46 };
                    return data.labels.map((label, i) => {
                        const key = label.replace(' ', ''); // Ensure label matches scores key
                        const value = scores[key]; // Use corrected key to access scores
                        const max = maxValues[key];
                        const percentage = (value / max) * 100;
                        return {
                            text: `${label} (${value}/${max}, ${percentage.toFixed(1)}%)`,
                            fillStyle: value === max ? 'gold' : 'rgba(16, 185, 129, 1)',
                            font: { size: 18 }
                        };
                    });
                }
            }
        },
        tooltip: {
            callbacks: {
                label: (context) => {
                    const label = context.label || ''
                    const value = context.raw || 0
                    const scores = calculateCategoryScores(props.checklist)
                    const maxValues = { ZoneQualifiers: 30, Technicals: 24, Fundamentals: 46 }
                    const max = maxValues[label]
                    const rawScore = scores[label]
                    return `${label}: ${rawScore}/${max} (${value.toFixed(1)}%)`
                }
            }
        }
    }
}
</script>