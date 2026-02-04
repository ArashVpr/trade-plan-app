<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <h1 class="text-3xl font text-blue-900 dark:text-blue-300 mb-6 text-center">Checklist History</h1>
            <Card>
                <template #title>
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-blue-900 dark:text-blue-300">Saved Checklists</h2>
                        <div class="flex gap-3 flex-wrap items-center">
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 rounded-full border border-blue-200 dark:border-blue-800">
                                <i class="pi pi-clock text-blue-600 text-sm"></i>
                                <span class="text-sm font-semibold text-blue-700">{{ statistics.pending }}</span>
                                <span class="text-xs text-blue-600">Pending</span>
                            </div>
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 dark:bg-amber-900/20 rounded-full border border-amber-200 dark:border-amber-800">
                                <i class="pi pi-chart-line text-amber-600 dark:text-amber-200 text-sm"></i>
                                <span class="text-sm font-semibold text-amber-700 dark:text-amber-100">{{
                                    statistics.active }}</span>
                                <span class="text-xs text-amber-600 dark:text-amber-200">Active</span>
                            </div>
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 rounded-full border border-green-200 dark:border-green-800">
                                <i class="pi pi-check-circle text-green-600 dark:text-green-200 text-sm"></i>
                                <span class="text-sm font-semibold text-green-700 dark:text-green-100">{{
                                    statistics.wins }}</span>
                                <span class="text-xs text-green-600 dark:text-green-200">Wins</span>
                            </div>
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 rounded-full border border-red-200 dark:border-red-800">
                                <i class="pi pi-times-circle text-red-600 dark:text-red-200 text-sm"></i>
                                <span class="text-sm font-semibold text-red-700 dark:text-red-100">{{ statistics.losses
                                    }}</span>
                                <span class="text-xs text-red-600 dark:text-red-200">Losses</span>
                            </div>
                        </div>
                    </div>
                </template>
                <template #content>
                    <!-- Mobile Card View (visible on small screens only) -->
                    <div class="md:hidden">
                        <!-- Mobile Search & Filters -->
                        <div class="mb-4 space-y-3">
                            <IconField class="w-full">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" placeholder="Search checklists..."
                                    class="w-full" />
                            </IconField>
                            <div v-if="hasActiveFilters" class="flex justify-end">
                                <Button label="Clear Filters" icon="pi pi-filter-slash" severity="danger" text
                                    size="small" @click="clearAllFilters" />
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div v-if="loading" class="space-y-4">
                            <div v-for="i in 3" :key="i"
                                class="bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <Skeleton width="6rem" height="1.5rem" />
                                    <Skeleton width="4rem" height="1.5rem" borderRadius="9999px" />
                                </div>
                                <div class="flex gap-2 mb-3">
                                    <Skeleton width="4rem" height="1.25rem" borderRadius="9999px" />
                                    <Skeleton width="3rem" height="1.25rem" borderRadius="9999px" />
                                </div>
                                <Skeleton width="100%" height="2rem" class="mb-3" />
                                <div class="flex justify-end gap-2">
                                    <Skeleton shape="circle" size="2rem" />
                                    <Skeleton shape="circle" size="2rem" />
                                    <Skeleton shape="circle" size="2rem" />
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <EmptyState v-else-if="checklists.data.length === 0 && !hasActiveFilters"
                            title="No checklists yet"
                            description="Start your trading journey by creating your first checklist to analyze trade setups."
                            icon="pi pi-list-check" action-label="Create First Checklist" action-icon="pi pi-plus"
                            @action="router.visit(route('home'))" />

                        <!-- Filtered Empty State -->
                        <EmptyState v-else-if="checklists.data.length === 0 && hasActiveFilters" variant="search"
                            title="No matches found"
                            description="Try adjusting your search or filters to find what you're looking for."
                            action-label="Clear Filters" action-icon="pi pi-filter-slash" action-severity="secondary"
                            @action="clearAllFilters" />

                        <!-- Mobile Cards -->
                        <ChecklistMobileCards v-else :items="checklists.data" :get-directional-bias="getDirectionalBias"
                            :get-trade-status="getTradeStatus" :get-trade-status-severity="getTradeStatusSeverity"
                            @delete="confirmDelete" />

                        <!-- Mobile Pagination -->
                        <div v-if="checklists.data.length > 0" class="mt-4 flex justify-center">
                            <Paginator :rows="checklists.per_page" :totalRecords="checklists.total"
                                :first="(checklists.current_page - 1) * checklists.per_page" @page="onPageChange"
                                template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="{first}-{last} of {totalRecords}" />
                        </div>
                    </div>

                    <!-- Desktop DataTable (hidden on small screens, visible on md+) -->
                    <div class="hidden md:block">
                        <DataTable :value="checklists.data" lazy :paginator="true" :rows="checklists.per_page"
                            :totalRecords="checklists.total" :result-count="checklists.total" dataKey="id"
                            :loading="loading" @page="onPageChange" @sort="onSort" @filter="onFilter"
                            class="p-datatable-sm text-sm" scrollable scrollHeight="600px" :sortField="sortField"
                            :sortOrder="sortOrder" removableSort v-model:filters="filters" filterDisplay="menu"
                            :globalFilterFields="['symbol', 'score']" rowHover>
                            <template #header>
                                <div class="flex justify-between items-center h-10">
                                    <!-- Dynamic Header Title / Clear Button Area -->
                                    <div class="flex items-center">
                                        <div v-if="hasActiveFilters" class="animate-fade-in">
                                            <Button label="Clear Filters" icon="pi pi-filter-slash" severity="danger"
                                                text
                                                class="!px-0 hover:bg-transparent text-red-600 hover:text-red-700 font-semibold"
                                                @click="clearAllFilters" />
                                        </div>
                                        <div v-else class="text-lg font-semibold text-slate-700 dark:text-slate-300">
                                            Checklists
                                        </div>
                                    </div>

                                    <!-- Search Area -->
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Keyword Search..."
                                            class="w-64" />
                                    </IconField>
                                </div>
                            </template>
                            <template #empty>
                                <EmptyState v-if="!hasActiveFilters" title="No checklists yet"
                                    description="Start your trading journey by creating your first checklist to analyze trade setups."
                                    icon="pi pi-list-check" action-label="Create First Checklist"
                                    action-icon="pi pi-plus" @action="router.visit(route('home'))" />
                                <EmptyState v-else variant="search" title="No matches found"
                                    description="We couldn't find any results matching your search or filters."
                                    action-label="Clear All Filters" action-icon="pi pi-filter-slash"
                                    action-severity="secondary" @action="clearAllFilters" />
                            </template>
                            <template #loading>
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    Loading data...
                                </div>
                            </template>

                            <Column field="created_at" header="Date" sortable :style="{ width: '110px' }">
                                <template #body="slotProps">
                                    <span class="font-medium text-slate-700 dark:text-slate-300">
                                        {{ new Date(slotProps.data.created_at).toLocaleDateString() }}
                                    </span>
                                </template>
                            </Column>

                            <Column field="symbol" header="Symbol" sortable :style="{ width: '110px' }">
                                <template #body="slotProps">
                                    <div class="font-bold text-blue-900 dark:text-blue-300">{{ slotProps.data.symbol ||
                                        'N/A' }}</div>
                                </template>
                            </Column>

                            <Column field="score" header="Score" sortable :style="{ width: '100px' }">
                                <template #body="slotProps">
                                    <Tag :value="`${slotProps.data.score}/100`"
                                        :severity="getScoreSeverity(slotProps.data.score)" rounded />
                                </template>
                            </Column>

                            <Column field="bias" header="Bias" :showFilterMatchModes="false"
                                :style="{ width: '140px' }">
                                <template #filter="{ filterModel, filterCallback }">
                                    <Select v-model="filterModel.value" @change="filterCallback()"
                                        :options="biasOptions" optionLabel="label" optionValue="value"
                                        placeholder="Select Bias" class="p-column-filter min-w-[12rem]"
                                        :showClear="true">
                                        <template #option="slotProps">
                                            <Tag :value="slotProps.option.label"
                                                :severity="getBiasSeverity(slotProps.option.value)" />
                                        </template>
                                    </Select>
                                </template>
                                <template #body="slotProps">
                                    <div v-if="getDirectionalBias(slotProps.data).hasEnoughData">
                                        <Tag :value="getDirectionalBias(slotProps.data).biasDisplay"
                                            :severity="getDirectionalBias(slotProps.data).severity"
                                            class="text-xs font-bold" />
                                    </div>
                                    <span v-else class="text-slate-400 dark:text-slate-500 text-xs italic">No
                                        Signals</span>
                                </template>
                            </Column>

                            <Column field="position_type" header="Position" :showFilterMatchModes="false"
                                :style="{ width: '130px' }">
                                <template #filter="{ filterModel, filterCallback }">
                                    <Select v-model="filterModel.value" @change="filterCallback()"
                                        :options="positionOptions" optionLabel="label" optionValue="value"
                                        placeholder="Select Position" class="p-column-filter min-w-[12rem]"
                                        :showClear="true">
                                        <template #option="slotProps">
                                            <Tag :value="slotProps.option.label"
                                                :severity="slotProps.option.value === 'Long' ? 'success' : 'danger'" />
                                        </template>
                                    </Select>
                                </template>
                                <template #body="slotProps">
                                    <Tag :value="slotProps.data.trade_entry?.position_type || 'N/A'"
                                        :severity="slotProps.data.trade_entry?.position_type === 'Long' ? 'success' : 'danger'"
                                        v-if="slotProps.data.trade_entry?.position_type" />
                                    <span v-else class="text-slate-400 dark:text-slate-500 text-sm">-</span>
                                </template>
                            </Column>

                            <Column field="entry_date" header="Entry&#160;Date" sortable
                                :style="{ width: '130px', whiteSpace: 'nowrap' }">
                                <template #body="slotProps">
                                    <span class="text-sm border-b border-dotted border-slate-300 dark:border-slate-600">
                                        {{ slotProps.data.trade_entry?.entry_date ?
                                            new Date(slotProps.data.trade_entry.entry_date).toLocaleDateString() : '-'
                                        }}
                                    </span>
                                </template>
                            </Column>

                            <Column field="trade_status" header="Status" :showFilterMatchModes="false"
                                :style="{ width: '150px' }">
                                <template #filter="{ filterModel, filterCallback }">
                                    <Select v-model="filterModel.value" @change="filterCallback()"
                                        :options="tradeStatusOptions" optionLabel="label" optionValue="value"
                                        placeholder="Select Status" class="p-column-filter min-w-[12rem]"
                                        :showClear="true">
                                        <template #option="slotProps">
                                            <Tag :value="slotProps.option.label"
                                                :severity="getTradeStatusSeverity({ trade_entry: { trade_status: slotProps.option.value } })" />
                                        </template>
                                    </Select>
                                </template>
                                <template #body="slotProps">
                                    <Tag :value="getTradeStatus(slotProps.data)"
                                        :severity="getTradeStatusSeverity(slotProps.data)" class="!whitespace-nowrap" />
                                </template>
                            </Column>

                            <Column field="rrr" header="R:R" sortable :style="{ width: '80px' }">
                                <template #body="slotProps">
                                    <span class="font-mono font-semibold text-slate-600 dark:text-slate-300">
                                        {{ slotProps.data.trade_entry?.rrr ?
                                            Number(slotProps.data.trade_entry.rrr).toFixed(2) : '-' }}
                                    </span>
                                </template>
                            </Column>

                            <Column header="Img" :style="{ width: '60px' }">
                                <template #body="slotProps">
                                    <div v-if="slotProps.data.trade_entry?.screenshot_paths && slotProps.data.trade_entry.screenshot_paths.length > 0"
                                        class="flex items-center justify-center">
                                        <i class="pi pi-image text-blue-500"
                                            v-tooltip="`${slotProps.data.trade_entry.screenshot_paths.length} images`"></i>
                                    </div>
                                </template>
                            </Column>

                            <Column header="Actions" :style="{ width: '120px' }" Frozen alignFrozen="right">
                                <template #body="slotProps">
                                    <div class="flex gap-2 justify-end">
                                        <Link :href="route('checklists.show', slotProps.data.id)">
                                            <Button icon="pi pi-eye" size="small" severity="secondary" text rounded
                                                aria-label="View" />
                                        </Link>
                                        <Link :href="route('checklists.edit', slotProps.data.id)">
                                            <Button icon="pi pi-pencil" size="small" severity="info" text rounded
                                                aria-label="Edit" />
                                        </Link>
                                        <Button icon="pi pi-trash" size="small" severity="danger" text rounded
                                            aria-label="Delete" @click="confirmDelete(slotProps.data.id)" />
                                    </div>
                                </template>
                            </Column>
                        </DataTable>
                    </div>

                    <!-- Custom Pagination - Removed in favor of built-in DataTable paginator for cleaner integration -->

                </template>
            </Card>

            <!-- PrimeVue ConfirmDialog -->
            <!-- <ConfirmDialog group="headless">
                <template #container="{ message, acceptCallback, rejectCallback }">
                    <div class="flex flex-col items-center p-8 bg-surface-0 dark:bg-surface-900 rounded">
                        <div
                            class="rounded-full bg-red-500 text-white inline-flex justify-center items-center h-24 w-24 -mt-20">
                            <i class="pi pi-trash !text-4xl"></i>
                        </div>
                        <span class="font-bold text-2xl block mb-2 mt-6">{{ message.header }}</span>
                        <p class="mb-0 text-center">{{ message.message }}</p>
                        <div class="flex items-center gap-2 mt-6">
                            <Button label="Delete" @click="acceptCallback" severity="danger" class="w-32"></Button>
                            <Button label="Cancel" outlined @click="rejectCallback" class="w-32"></Button>
                        </div>
                    </div>
                </template>
            </ConfirmDialog> -->
        </div>
        <ConfirmDialog></ConfirmDialog>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import EmptyState from '@/Components/UI/EmptyState.vue'
import ChecklistMobileCards from '@/Components/UI/ChecklistMobileCards.vue'
import { onMounted, computed, ref, watch } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import { route } from 'ziggy-js'
import { useDirectionalBias } from '@/composables/useDirectionalBias.js'
import { FilterMatchMode } from '@primevue/core/api'

const confirm = useConfirm()
const toast = useToast()
const page = usePage()
const loading = ref(false)

// Sorting state initialized from URL or defaults
const sortField = ref(route().params.sortField || 'created_at')
const sortOrder = ref(route().params.sortOrder === 'asc' ? 1 : -1)

// Initialize filters from URL parameters to persist state on reload/navigation
const filters = ref({
    global: { value: route().params.search || null, matchMode: FilterMatchMode.CONTAINS },
    bias: { value: route().params.bias?.[0] || null, matchMode: FilterMatchMode.EQUALS },
    position_type: { value: route().params.position?.[0] || null, matchMode: FilterMatchMode.EQUALS },
    trade_status: { value: route().params.tradeStatus?.[0] || null, matchMode: FilterMatchMode.EQUALS }
})

// Debounce Utility
const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

// Filter options

const biasOptions = ref([
    { label: 'BUY', value: 'buy' },
    { label: 'LEAN BUY', value: 'lean_buy' },
    { label: 'NEUTRAL', value: 'neutral' },
    { label: 'LEAN SELL', value: 'lean_sell' },
    { label: 'SELL', value: 'sell' }
])

const positionOptions = ref([
    { label: 'Long', value: 'Long' },
    { label: 'Short', value: 'Short' }
])

const tradeStatusOptions = ref([
    { label: 'Pending', value: 'pending' },
    { label: 'Open', value: 'active' },
    { label: 'Win', value: 'win' },
    { label: 'Loss', value: 'loss' },
    { label: 'Breakeven', value: 'breakeven' },
    { label: 'Cancelled', value: 'cancelled' },
    { label: 'Analysis Only', value: 'analysis_only' }
])

// Computed property to check if any filters are active
const hasActiveFilters = computed(() => {
    return !!(
        filters.value.global.value ||
        filters.value.bias.value ||
        filters.value.position_type.value ||
        filters.value.trade_status.value
    )
})

onMounted(() => {
    if (page.props.flash?.success) {
        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: page.props.flash.success,
            life: 3000
        })
    }
})

const props = defineProps({
    checklists: Object,
    settings: Object,
    statistics: Object,
})

// Helper function to calculate directional bias for a specific checklist
const getDirectionalBias = (checklist) => {
    const { directionalBias } = useDirectionalBias(
        computed(() => checklist.technicals),
        computed(() => checklist.fundamentals),
        computed(() => props.settings)
    )
    return directionalBias.value
}

// Helper to get severity for Bias filter dropdown options
const getBiasSeverity = (bias) => {
    switch (bias) {
        case 'buy':
            return 'success';
        case 'lean_buy':
            return 'info';
        case 'sell':
            return 'danger';
        case 'lean_sell':
            return 'warn';
        case 'neutral':
        default:
            return 'secondary';
    }
}

const clearAllFilters = () => {
    filters.value.global.value = null;
    filters.value.bias.value = null;
    filters.value.position_type.value = null;
    filters.value.trade_status.value = null;

    // Reset sort to default
    sortField.value = 'created_at';
    sortOrder.value = -1;

    // Trigger update
    updateTableData(getParams(1));
}

// Helpers for params construction
const getParams = (pageOverride = null) => {
    return {
        search: filters.value.global?.value || null,
        bias: filters.value.bias?.value ? [filters.value.bias.value] : null,
        position: filters.value.position_type?.value ? [filters.value.position_type.value] : null,
        tradeStatus: filters.value.trade_status?.value ? [filters.value.trade_status.value] : null,
        sortField: sortField.value,
        sortOrder: sortOrder.value === 1 ? 'asc' : 'desc',
        page: pageOverride || route().params.page || 1
    };
}

// Watch for global search changes (Instant Search)
watch(() => filters.value.global.value, debounce((newValue) => {
    updateTableData(getParams(1)); // Reset to page 1 on search
}, 300));

// Helper function to determine score severity for Tag component
const getScoreSeverity = (score) => {

    if (score < 50) return 'danger'
    if (score <= 80) return 'warn'
    return 'success'
}

// Helper function to get trade status - matches Dashboard and Show.vue
const getTradeStatus = (checklist) => {
    const tradeEntry = checklist.trade_entry

    if (!tradeEntry || !tradeEntry.trade_status) {
        return 'Analysis Only'
    }

    // Check if we have the new trade_status field (after migration)
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

// Helper function to get trade status severity - matches Dashboard and Show.vue
const getTradeStatusSeverity = (checklist) => {
    const tradeEntry = checklist.trade_entry

    if (!tradeEntry || !tradeEntry.trade_status) {
        return 'info'  // Blue for analysis only
    }

    // Check if we have the new trade_status field
    switch (tradeEntry.trade_status) {
        case 'pending': return 'warn'   // Changed to Warn (Yellow) for visibility
        case 'active': return 'info'    // Changed to Info (Blue)
        case 'win': return 'success' // Green
        case 'loss': return 'danger'  // Red
        case 'breakeven': return 'secondary'
        case 'cancelled': return 'contrast'
        default: return 'secondary'
    }
}

const confirmDelete = (checklistId) => {
    confirm.require({
        message: 'Do you want to delete this checklist?',
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
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
            router.post(route('checklists.destroy', checklistId))
            toast.add({ severity: 'success', summary: 'Success', detail: 'Checklist deleted', life: 3000 });
        }
    })
}

// Handle Server-Side Filtering
const onFilter = (event) => {
    updateTableData(getParams(1));
}

const onGlobalSearch = () => {
    // Handled by watch, but kept for @keydown.enter if needed (though debounce handles it)
}

// Handle pagination
const onPageChange = (event) => {
    const page = event.page + 1; // event.page is 0-indexed
    updateTableData(getParams(page));
}

// Handle sorting
const onSort = (event) => {
    sortField.value = event.sortField
    sortOrder.value = event.sortOrder

    // Changing sort keeps current page? Usually better to reset to 1, but user preference varies.
    // Let's keep current page unless it feels wrong.
    // Actually standard behavior is usually keeping page if possible, but sorting might shift rows significantly.
    // Let's reset to page 1 to be safe and consistent with previous code.
    updateTableData(getParams(1));
}

// Centralized data update function
const updateTableData = (params) => {
    router.get(route('checklists.index'), params, {
        preserveState: true,
        replace: true,
        onStart: () => loading.value = true,
        onFinish: () => loading.value = false
    });
}


</script>
