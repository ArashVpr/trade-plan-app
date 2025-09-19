<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-blue-900">Analysis Tracker</h1>
                <Button label="Add Symbol" icon="pi pi-plus" @click="showAddSymbolDialog = true" />
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <Card>
                    <template #content>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-900">{{ trackers.length }}</div>
                            <div class="text-sm text-gray-600">Tracked Instruments</div>
                        </div>
                    </template>
                </Card>
                <Card>
                    <template #content>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ readyForChecklistCount }}</div>
                            <div class="text-sm text-gray-600">Ready for Checklist</div>
                        </div>
                    </template>
                </Card>
                <Card>
                    <template #content>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ staleTrackersCount }}</div>
                            <div class="text-sm text-gray-600">Stale Analysis</div>
                        </div>
                    </template>
                </Card>
                <Card>
                    <template #content>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ Math.round(averageCompletion) }}%</div>
                            <div class="text-sm text-gray-600">Avg Completion</div>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Analysis Tracker Table -->
            <Card>
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="pi pi-search text-blue-900"></i>
                        <span class="text-blue-900">Market Analysis Overview</span>
                    </div>
                </template>
                <template #content>
                    <DataTable :value="trackers" :paginator="true" :rows="10" responsiveLayout="stack"
                        :globalFilterFields="['symbol']" v-model:filters="filters" dataKey="id">

                        <template #header>
                            <div class="flex justify-between">
                                <span class="p-input-icon-left">
                                    <i class="pi pi-search" />
                                    <InputText v-model="filters['global'].value" placeholder="Search symbols..." />
                                </span>
                            </div>
                        </template>

                        <Column field="symbol" header="Symbol" :sortable="true">
                            <template #body="{ data }">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-semibold">{{ data.symbol }}</span>
                                    <Tag v-if="data.completion_percentage >= 70" value="Ready" severity="success"
                                        size="small" />
                                    <Tag v-else-if="getDaysSinceUpdate(data) > 7" value="Stale" severity="warning"
                                        size="small" />
                                </div>
                            </template>
                        </Column>

                        <Column header="Zone Qualifiers" :style="{ width: '150px' }">
                            <template #body="{ data }">
                                <div class="flex flex-wrap gap-1">
                                    <Chip v-for="qualifier in getZoneQualifiers(data)" :key="qualifier"
                                        :label="qualifier" size="small" class="text-xs" />
                                </div>
                            </template>
                        </Column>

                        <Column header="Technicals" :style="{ width: '120px' }">
                            <template #body="{ data }">
                                <div class="space-y-1">
                                    <div v-if="data.tracked_metrics?.technicals?.location">
                                        <span class="text-xs text-gray-600">Location:</span>
                                        <span class="text-xs ml-1">{{ data.tracked_metrics.technicals.location }}</span>
                                    </div>
                                    <div v-if="data.tracked_metrics?.technicals?.direction">
                                        <span class="text-xs text-gray-600">Direction:</span>
                                        <span class="text-xs ml-1">{{ data.tracked_metrics.technicals.direction
                                        }}</span>
                                    </div>
                                </div>
                            </template>
                        </Column>

                        <Column header="Fundamentals" :style="{ width: '120px' }">
                            <template #body="{ data }">
                                <div class="space-y-1">
                                    <div v-if="data.tracked_metrics?.fundamentals?.valuation">
                                        <span class="text-xs text-gray-600">Valuation:</span>
                                        <span class="text-xs ml-1">{{ data.tracked_metrics.fundamentals.valuation
                                        }}</span>
                                    </div>
                                    <div v-if="data.tracked_metrics?.fundamentals?.seasonalConfluence">
                                        <span class="text-xs text-gray-600">Seasonal:</span>
                                        <span class="text-xs ml-1">{{
                                            data.tracked_metrics.fundamentals.seasonalConfluence }}</span>
                                    </div>
                                </div>
                            </template>
                        </Column>

                        <Column header="Progress" :style="{ width: '120px' }">
                            <template #body="{ data }">
                                <div class="space-y-2">
                                    <ProgressBar :value="data.completion_percentage"
                                        :class="getProgressBarClass(data.completion_percentage)" />
                                    <div class="text-xs text-center">{{ data.completion_percentage }}%</div>
                                </div>
                            </template>
                        </Column>

                        <Column header="Last Updated" :style="{ width: '100px' }">
                            <template #body="{ data }">
                                <div class="text-xs">
                                    <div>{{ formatDate(data.last_updated_at) }}</div>
                                    <div class="text-gray-500">({{ getDaysSinceUpdate(data) }}d ago)</div>
                                </div>
                            </template>
                        </Column>

                        <Column header="Actions" :style="{ width: '140px' }">
                            <template #body="{ data }">
                                <div class="flex gap-1">
                                    <Button icon="pi pi-pencil" size="small" severity="info" text
                                        v-tooltip="'Update Analysis'" @click="editTracker(data)" />
                                    <Button icon="pi pi-plus" size="small" severity="success" text
                                        v-tooltip="'Start Checklist'" :disabled="data.completion_percentage < 70"
                                        @click="startChecklist(data.symbol)" />
                                    <Button icon="pi pi-trash" size="small" severity="danger" text
                                        v-tooltip="'Delete Tracker'" @click="confirmDelete(data)" />
                                </div>
                            </template>
                        </Column>

                        <template #empty>
                            <div class="text-center py-8">
                                <div class="mb-4">
                                    <i class="pi pi-search text-gray-400 text-6xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Analysis Tracked Yet</h3>
                                <p class="text-gray-600 mb-4">
                                    Start tracking your market analysis for different instruments to get a bird's-eye
                                    view of opportunities.
                                </p>
                                <Button label="Add Your First Symbol" icon="pi pi-plus"
                                    @click="showAddSymbolDialog = true" />
                            </div>
                        </template>
                    </DataTable>
                </template>
            </Card>
        </div>

        <!-- Add Symbol Dialog -->
        <Dialog v-model:visible="showAddSymbolDialog" modal header="Add Symbol to Track" :style="{ width: '25rem' }">
            <div class="space-y-4">
                <div class="field">
                    <label class="block text-sm font-medium mb-2">Symbol</label>
                    <Select v-model="newSymbol" :options="symbolOptions" placeholder="Select a symbol" class="w-full" />
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" @click="showAddSymbolDialog = false" text />
                <Button label="Add Symbol" icon="pi pi-check" @click="addSymbol" />
            </template>
        </Dialog>

        <!-- Edit Tracker Dialog -->
        <Dialog v-model:visible="showEditDialog" modal header="Update Analysis" :style="{ width: '50rem' }">
            <AnalysisTrackerForm v-if="selectedTracker" :tracker="selectedTracker" @updated="onTrackerUpdated"
                @cancelled="showEditDialog = false" />
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog />
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import { FilterMatchMode } from '@primevue/core/api'
import AppLayout from '@/Layouts/AppLayout.vue'
import AnalysisTrackerForm from '@/Components/AnalysisTracker/AnalysisTrackerForm.vue'

const props = defineProps({
    trackers: Array,
    instruments: Array,
})

const confirm = useConfirm()
const toast = useToast()

// Reactive data
const showAddSymbolDialog = ref(false)
const showEditDialog = ref(false)
const selectedTracker = ref(null)
const newSymbol = ref('')

// Filters for DataTable
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

// Computed properties
const symbolOptions = computed(() => {
    if (props.instruments && props.instruments.length > 0) {
        return props.instruments.map(inst => inst.symbol)
    }
    return ['EURUSD', 'GBPUSD', 'USDJPY', 'AUDUSD', 'USDCAD', 'NZDUSD', 'EURGBP', 'EURJPY', 'GBPJPY', 'AUDJPY']
})

const readyForChecklistCount = computed(() => {
    return props.trackers.filter(t => t.completion_percentage >= 70).length
})

const staleTrackersCount = computed(() => {
    return props.trackers.filter(t => getDaysSinceUpdate(t) > 7).length
})

const averageCompletion = computed(() => {
    if (props.trackers.length === 0) return 0
    const total = props.trackers.reduce((sum, t) => sum + t.completion_percentage, 0)
    return total / props.trackers.length
})

// Methods
const getZoneQualifiers = (tracker) => {
    return tracker.tracked_metrics?.zone_qualifiers || []
}

const getDaysSinceUpdate = (tracker) => {
    const lastUpdate = new Date(tracker.last_updated_at)
    const now = new Date()
    const diffTime = Math.abs(now - lastUpdate)
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString()
}

const getProgressBarClass = (percentage) => {
    if (percentage >= 80) return 'progress-bar-success'
    if (percentage >= 50) return 'progress-bar-warning'
    return 'progress-bar-danger'
}

const addSymbol = async () => {
    if (!newSymbol.value) {
        toast.add({ severity: 'warn', summary: 'Warning', detail: 'Please select a symbol', life: 3000 })
        return
    }

    // Check if symbol already exists
    if (props.trackers.some(t => t.symbol === newSymbol.value)) {
        toast.add({ severity: 'warn', summary: 'Warning', detail: 'Symbol already being tracked', life: 3000 })
        return
    }

    router.post(route('analysis-tracker.store'), {
        symbol: newSymbol.value,
        metric_type: 'zone_qualifiers',
        metric_key: 'Fresh', // For zone qualifiers, this becomes the value
        metric_value: 'Fresh' // Default initial value
    }, {
        onSuccess: () => {
            showAddSymbolDialog.value = false
            newSymbol.value = ''
            toast.add({ severity: 'success', summary: 'Success', detail: 'Symbol added to tracker', life: 3000 })
        },
        onError: (errors) => {
            console.error('Error adding symbol:', errors)

            let errorMessage = 'Failed to add symbol'
            if (errors.symbol) {
                errorMessage = errors.symbol
            } else if (errors.metric_type) {
                errorMessage = errors.metric_type
            } else if (errors.metric_key) {
                errorMessage = errors.metric_key
            } else if (errors.metric_value) {
                errorMessage = errors.metric_value
            } else if (typeof errors === 'string') {
                errorMessage = errors
            }

            toast.add({ severity: 'error', summary: 'Error', detail: errorMessage, life: 5000 })
        }
    })
}

const editTracker = (tracker) => {
    selectedTracker.value = tracker
    showEditDialog.value = true
}

const startChecklist = (symbol) => {
    router.get(route('analysis-tracker.start-checklist', symbol))
}

const confirmDelete = (tracker) => {
    confirm.require({
        message: `Are you sure you want to delete tracking for ${tracker.symbol}?`,
        header: 'Delete Confirmation',
        icon: 'pi pi-info-circle',
        rejectLabel: 'Cancel',
        acceptLabel: 'Delete',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('analysis-tracker.destroy', tracker.id), {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Confirmed', detail: 'Tracker deleted', life: 3000 })
                }
            })
        }
    })
}

const onTrackerUpdated = () => {
    showEditDialog.value = false
    router.reload({ only: ['trackers'] })
    toast.add({ severity: 'success', summary: 'Success', detail: 'Analysis updated', life: 3000 })
}
</script>

<style scoped>
.progress-bar-success :deep(.p-progressbar-value) {
    background: #10b981;
}

.progress-bar-warning :deep(.p-progressbar-value) {
    background: #f59e0b;
}

.progress-bar-danger :deep(.p-progressbar-value) {
    background: #ef4444;
}
</style>