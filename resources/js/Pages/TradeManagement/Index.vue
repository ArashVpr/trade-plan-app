<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto p-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-blue-900 flex items-center">
                            <i class="pi pi-cog mr-3"></i>
                            Trade Management
                        </h1>
                        <p class="text-gray-600 mt-2">Monitor and manage your open trades with systematic discipline</p>
                    </div>
                    <div class="flex space-x-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ openTrades.length }}</div>
                            <div class="text-sm text-gray-500">Open Trades</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ alertItems.length }}</div>
                            <div class="text-sm text-gray-500">Active Alerts</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">
                                {{alertItems.filter(a => isOverdue(a)).length}}
                            </div>
                            <div class="text-sm text-gray-500">Overdue</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <Tabs value="0">
                <TabList>
                    <Tab value="0">📈 Open Trades</Tab>
                    <Tab value="1">🔔 Alerts & Reminders ({{ alertItems.length }})</Tab>
                </TabList>
                <TabPanels>
                    <TabPanel value="0">
                        <!-- Open Trades Table -->
                        <Card class="shadow-sm">
                            <template #title>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="pi pi-chart-line text-blue-600 mr-3 text-xl"></i>
                                        <span>Open Trades ({{ openTrades.length }})</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <Badge
                                            :value="`${openTrades.filter(t => t.trade_status === 'active').length} Active`"
                                            severity="success" />
                                        <Badge
                                            :value="`${openTrades.filter(t => t.trade_status === 'pending').length} Pending`"
                                            severity="warning" />
                                    </div>
                                </div>
                            </template>
                            <template #content>
                                <DataTable :value="openTrades" :paginator="true" :rows="10" responsiveLayout="scroll"
                                    class="p-datatable-sm">
                                    <Column field="checklist.symbol" header="Symbol" sortable>
                                        <template #body="{ data }">
                                            <Tag :value="data.checklist.symbol" severity="info" />
                                        </template>
                                    </Column>

                                    <Column field="checklist.score" header="Score" sortable>
                                        <template #body="{ data }">
                                            <EvaluationScore :score="data.checklist.score" size="small" title="" />
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

                                    <Column header="Actions">
                                        <template #body="{ data }">
                                            <div class="flex items-center space-x-2">
                                                <Badge :value="data.management_items.length" severity="secondary" />
                                                <Button icon="pi pi-plus" size="small" severity="secondary"
                                                    @click="openManagementDialog(data)"
                                                    v-tooltip="'Add Management Items'" />
                                                <Button icon="pi pi-eye" size="small" severity="info"
                                                    @click="viewManagementItems(data)"
                                                    v-tooltip="'View Management Items'" />
                                                <Button icon="pi pi-pencil" size="small" severity="secondary"
                                                    @click="editTrade(data)" v-tooltip="'Edit Trade'" />
                                            </div>
                                        </template>
                                    </Column>
                                </DataTable>
                            </template>
                        </Card>
                    </TabPanel>

                    <TabPanel value="1">
                        <!-- Alerts & Reminders Content -->
                        <div v-if="alertItems.length > 0" class="grid gap-4">
                            <Card v-for="alert in alertItems" :key="alert.id"
                                class="shadow-sm hover:shadow-md transition-shadow duration-200"
                                :class="getPriorityCardClasses(alert.priority)">
                                <template #content>
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-3">
                                                <Badge :value="alert.priority.toUpperCase()" size="large"
                                                    :severity="getPrioritySeverity(alert.priority)" class="mr-3" />
                                                <Tag :value="alert.type.toUpperCase()" severity="info" class="mr-3" />
                                                <Tag :value="alert.trade_entry.checklist.symbol" severity="secondary"
                                                    class="font-semibold" />
                                            </div>

                                            <h3 class="font-bold text-xl mb-2 text-gray-800">{{ alert.title }}</h3>

                                            <p class="text-gray-600 mb-4 leading-relaxed">{{ alert.description }}</p>

                                            <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                                                <div v-if="alert.due_date" class="flex items-center">
                                                    <i class="pi pi-clock mr-2 text-gray-500"></i>
                                                    <span class="text-sm">
                                                        <strong>Due:</strong> {{ formatDateTime(alert.due_date) }}
                                                        <span v-if="isOverdue(alert)"
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                                                            <i class="pi pi-exclamation-triangle mr-1"></i>
                                                            Overdue
                                                        </span>
                                                        <span v-else-if="isDueSoon(alert)"
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 ml-2">
                                                            <i class="pi pi-clock mr-1"></i>
                                                            Due Soon
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="pi pi-chart-line mr-2 text-gray-500"></i>
                                                    <span class="text-sm">
                                                        <strong>Trade:</strong>
                                                        <Tag :value="alert.trade_entry.position_type?.toUpperCase()"
                                                            :severity="alert.trade_entry.position_type === 'long' ? 'success' : 'danger'"
                                                            size="small" class="mx-1" />
                                                        {{ alert.trade_entry.checklist.symbol }}
                                                        @ <span class="font-mono">{{ alert.trade_entry.entry_price
                                                            }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col gap-2 ml-6">
                                            <Button label="✅ Complete" severity="success" size="small"
                                                class="font-medium" @click="updateStatus(alert.id, 'completed')" />
                                            <Button label="👁️ View Trade" severity="info" size="small" outlined
                                                @click="viewTrade(alert.trade_entry)" />
                                            <Button label="❌ Ignore" severity="secondary" size="small" outlined
                                                @click="updateStatus(alert.id, 'ignored')" />
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <Card v-else class="text-center">
                            <template #content>
                                <div class="py-16">
                                    <div
                                        class="w-24 h-24 mx-auto mb-6 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="pi pi-check-circle text-4xl text-green-600"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-800 mb-3">🎉 All Clear!</h3>
                                    <p class="text-gray-600 mb-2 text-lg">No urgent alerts or reminders at the moment.
                                    </p>
                                    <p class="text-gray-500">Keep up the systematic trading discipline!</p>
                                    <div class="mt-8">
                                        <Button label="📊 View Open Trades" severity="info" outlined
                                            @click="$emit('switch-tab', '0')" />
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </TabPanel>
                </TabPanels>
            </Tabs>

            <!-- Management Items Dialog -->
            <ManagementItemsDialog v-model:visible="managementDialogVisible" :trade="selectedTrade"
                :predefined-items="predefinedItems" @items-added="refreshData" />

            <!-- View Management Items Dialog -->
            <ViewManagementItemsDialog v-model:visible="viewDialogVisible" :trade="selectedTrade"
                :key="selectedTrade?.id + '-' + (selectedTrade?.management_items?.length || 0)"
                @item-updated="refreshData" />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
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

// Computed properties for better performance
const activeTrades = computed(() => openTrades.value?.filter(t => t.trade_status === 'active') || [])
const pendingTrades = computed(() => openTrades.value?.filter(t => t.trade_status === 'pending') || [])
const overdueTrades = computed(() => alertItems.value?.filter(a => isOverdue(a)) || [])

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

const getPriorityCardClasses = (priority) => {
    const classes = {
        'low': 'border-l-4 border-l-blue-500',
        'medium': 'border-l-4 border-l-yellow-500',
        'high': 'border-l-4 border-l-orange-500',
        'critical': 'border-l-4 border-l-red-500'
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
    const currentTradeId = selectedTrade.value?.id
    router.reload({
        only: ['openTrades', 'alertItems'],
        onSuccess: (page) => {
            // Update selectedTrade with fresh data if it was set
            if (currentTradeId && page.props.openTrades) {
                const updatedTrade = page.props.openTrades.find(trade => trade.id === currentTradeId)
                if (updatedTrade) {
                    selectedTrade.value = updatedTrade
                }
            }
        }
    })
}
</script>
