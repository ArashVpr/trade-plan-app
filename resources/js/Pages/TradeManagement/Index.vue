<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto p-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-blue-900">Trade Management</h1>
                <p class="text-gray-600 mt-2">Monitor and manage your open trades with systematic discipline</p>
            </div>

            <!-- Tabs Navigation -->
            <TabView>
                <TabPanel header="Open Trades">
                    <!-- Open Trades Table -->
                    <Card>
                        <template #title>
                            Open Trades ({{ openTrades.length }})
                        </template>
                        <template #content>
                            <DataTable :value="openTrades" :paginator="true" :rows="10" responsiveLayout="scroll"
                                class="p-datatable-sm">
                                <Column field="checklist.symbol" header="Symbol" sortable>
                                    <template #body="{ data }">
                                        <div class="flex items-center">
                                            <Tag :value="data.checklist.symbol" severity="info" class="mr-2" />
                                            <EvaluationScore :score="data.checklist.score" size="small" />
                                        </div>
                                    </template>
                                </Column>

                                <Column field="entry_date" header="Entry Date" sortable>
                                    <template #body="{ data }">
                                        {{ formatDate(data.entry_date) }}
                                    </template>
                                </Column>

                                <Column field="position_type" header="Position" sortable>
                                    <template #body="{ data }">
                                        <Tag :value="data.position_type"
                                            :severity="data.position_type === 'long' ? 'success' : 'danger'" />
                                    </template>
                                </Column>

                                <Column field="entry_price" header="Entry" sortable />
                                <Column field="stop_price" header="Stop" sortable />
                                <Column field="target_price" header="Target" sortable />

                                <Column field="rrr" header="R:R" sortable>
                                    <template #body="{ data }">
                                        <Badge :value="`1:${data.rrr}`" severity="info" />
                                    </template>
                                </Column>

                                <Column field="trade_status" header="Status" sortable>
                                    <template #body="{ data }">
                                        <Tag :value="data.trade_status"
                                            :severity="getStatusSeverity(data.trade_status)" />
                                    </template>
                                </Column>

                                <Column field="management_items" header="Management Items">
                                    <template #body="{ data }">
                                        <div class="flex items-center space-x-2">
                                            <Badge :value="data.management_items.length" severity="secondary" />
                                            <Button icon="pi pi-plus" size="small" severity="secondary"
                                                @click="openManagementDialog(data)"
                                                v-tooltip="'Add Management Items'" />
                                            <Button icon="pi pi-eye" size="small" severity="info"
                                                @click="viewManagementItems(data)"
                                                v-tooltip="'View Management Items'" />
                                        </div>
                                    </template>
                                </Column>

                                <Column header="Actions">
                                    <template #body="{ data }">
                                        <div class="flex space-x-2">
                                            <Button icon="pi pi-pencil" size="small" severity="secondary"
                                                @click="editTrade(data)" v-tooltip="'Edit Trade'" />
                                        </div>
                                    </template>
                                </Column>
                            </DataTable>
                        </template>
                    </Card>
                </TabPanel>

                <TabPanel :header="`Alerts & Reminders (${alertItems.length})`">
                    <!-- Alerts & Reminders Content -->
                    <div v-if="alertItems.length > 0" class="space-y-4">
                        <div v-for="alert in alertItems" :key="alert.id" class="border rounded-lg p-4"
                            :class="getPriorityClasses(alert.priority)">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-3">
                                        <Badge :value="alert.priority.toUpperCase()"
                                            :severity="getPrioritySeverity(alert.priority)" class="mr-3" />
                                        <Tag :value="alert.type.toUpperCase()" severity="info" class="mr-3" />
                                        <Tag :value="alert.trade_entry.checklist.symbol" severity="secondary" />
                                    </div>

                                    <h3 class="font-semibold text-lg mb-2">{{ alert.title }}</h3>

                                    <p class="text-gray-700 mb-3">{{ alert.description }}</p>

                                    <div class="text-sm text-gray-600 space-y-1">
                                        <div v-if="alert.due_date">
                                            <strong>Due:</strong> {{ formatDateTime(alert.due_date) }}
                                            <span v-if="isOverdue(alert)"
                                                class="text-red-600 ml-2 font-medium">(Overdue)</span>
                                            <span v-else-if="isDueSoon(alert)"
                                                class="text-orange-600 ml-2 font-medium">(Due
                                                Soon)</span>
                                        </div>
                                        <div>
                                            <strong>Trade:</strong>
                                            {{ alert.trade_entry.position_type?.toUpperCase() }}
                                            {{ alert.trade_entry.checklist.symbol }}
                                            @ {{ alert.trade_entry.entry_price }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col space-y-2 ml-4">
                                    <Button label="Complete" severity="success"
                                        @click="updateStatus(alert.id, 'completed')" />
                                    <Button label="Ignore" severity="secondary"
                                        @click="updateStatus(alert.id, 'ignored')" />
                                    <Button label="View Trade" severity="info" @click="viewTrade(alert.trade_entry)" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-12 text-gray-500">
                        <i class="pi pi-check-circle text-6xl mb-4 text-green-500"></i>
                        <h3 class="text-xl font-semibold mb-2">All Clear!</h3>
                        <p>No urgent alerts or reminders at the moment.</p>
                        <p class="text-sm">Keep up the systematic trading!</p>
                    </div>
                </TabPanel>
            </TabView>

            <!-- Management Items Dialog -->
            <ManagementItemsDialog v-model:visible="managementDialogVisible" :trade="selectedTrade"
                :predefined-items="predefinedItems" @items-added="refreshData" />

            <!-- View Management Items Dialog -->
            <ViewManagementItemsDialog v-model:visible="viewDialogVisible" :trade="selectedTrade"
                @item-updated="refreshData" />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useToast } from 'primevue/usetoast'
import AppLayout from '@/Layouts/AppLayout.vue'
import EvaluationScore from '@/Components/UI/EvaluationScore.vue'
import ManagementItemsDialog from './Components/ManagementItemsDialog.vue'
import ViewManagementItemsDialog from './Components/ViewManagementItemsDialog.vue'

const toast = useToast()

const props = defineProps({
    openTrades: Array,
    alertItems: Array,
    predefinedItems: Object,
})

// State
const managementDialogVisible = ref(false)
const viewDialogVisible = ref(false)
const selectedTrade = ref(null)

// Methods
const formatDate = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString()
}

const formatDateTime = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleString()
}

const isOverdue = (alert) => {
    return alert.due_date && new Date(alert.due_date) < new Date() && alert.status === 'pending'
}

const isDueSoon = (alert) => {
    if (!alert.due_date) return false
    const dueDate = new Date(alert.due_date)
    const now = new Date()
    const diffHours = (dueDate - now) / (1000 * 60 * 60)
    return diffHours > 0 && diffHours <= 24 // Due within 24 hours
}

const getPriorityClasses = (priority) => {
    const classes = {
        'low': 'bg-blue-50 border-blue-200',
        'medium': 'bg-yellow-50 border-yellow-200',
        'high': 'bg-orange-50 border-orange-200',
        'critical': 'bg-red-50 border-red-200'
    }
    return classes[priority] || classes.medium
}

const getPrioritySeverity = (priority) => {
    const severities = {
        'low': 'info',
        'medium': 'warning',
        'high': 'warn',
        'critical': 'danger'
    }
    return severities[priority] || 'info'
}

const getStatusSeverity = (status) => {
    const severities = {
        'pending': 'warning',
        'active': 'info',
        'win': 'success',
        'loss': 'danger',
        'breakeven': 'secondary'
    }
    return severities[status] || 'info'
}

const updateStatus = (alertId, status) => {
    router.put(route('trade-management.update-status', alertId), {
        status,
    }, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Alert status updated successfully',
                life: 3000
            })
        }
    })
}

const openManagementDialog = (trade) => {
    selectedTrade.value = trade
    managementDialogVisible.value = true
}

const viewManagementItems = (trade) => {
    selectedTrade.value = trade
    viewDialogVisible.value = true
}

const editTrade = (trade) => {
    router.visit(route('checklists.edit', trade.checklist.id))
}

const viewTrade = (tradeEntry) => {
    router.visit(route('checklists.edit', tradeEntry.checklist.id))
}

const refreshData = () => {
    router.reload({ only: ['openTrades', 'alertItems'] })
}
</script>
