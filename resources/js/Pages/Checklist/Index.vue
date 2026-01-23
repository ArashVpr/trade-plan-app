<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <h1 class="text-3xl font text-blue-900 mb-6 text-center">Checklist History</h1>
            <Card>
                <template #title>
                    <h2 class="text-xl font-semibold text-blue-900">Saved Checklists</h2>
                </template>
                <template #content>
                    <div v-if="checklists.data.length === 0">
                        <Message severity="info" :closable="false">
                            No checklists found.
                        </Message>
                    </div>
                    <div v-else>
                        <DataTable :value="checklists.data" :paginator="false" :rows="10" stripedRows
                            class="p-datatable-sm" scrollable scrollHeight="600px">
                            <Column header="Date" :style="{ width: '100px' }">
                                <template #body="slotProps">
                                    <span class="text-sm">
                                        {{ new Date(slotProps.data.created_at).toLocaleDateString() }}
                                    </span>
                                </template>
                            </Column>

                            <Column field="symbol" header="Symbol" :style="{ width: '100px' }">
                                <template #body="slotProps">
                                    <Chip :label="slotProps.data.symbol || 'N/A'" size="small" />
                                </template>
                            </Column>

                            <Column header="Score" :style="{ width: '80px' }">
                                <template #body="slotProps">
                                    <Tag :value="`${slotProps.data.score}/100`"
                                        :severity="getScoreSeverity(slotProps.data.score)" />
                                </template>
                            </Column>

                            <Column header="Bias" :style="{ width: '120px' }">
                                <template #body="slotProps">
                                    <div v-if="getDirectionalBias(slotProps.data).hasEnoughData">
                                        <Tag :value="getDirectionalBias(slotProps.data).biasDisplay"
                                            :severity="getDirectionalBias(slotProps.data).severity"
                                            class="text-xs font-bold" />
                                    </div>
                                    <span v-else class="text-gray-400 text-xs">No Signals</span>
                                </template>
                            </Column>

                            <Column header="Position" :style="{ width: '80px' }">
                                <template #body="slotProps">
                                    <Tag :value="slotProps.data.trade_entry?.position_type || 'N/A'"
                                        :severity="slotProps.data.trade_entry?.position_type === 'Long' ? 'success' : 'danger'"
                                        v-if="slotProps.data.trade_entry?.position_type" />
                                    <span v-else class="text-gray-400 text-sm">N/A</span>
                                </template>
                            </Column>

                            <Column header="Entry Date" :style="{ width: '100px' }">
                                <template #body="slotProps">
                                    <span class="text-sm">
                                        {{ slotProps.data.trade_entry?.entry_date ?
                                            new Date(slotProps.data.trade_entry.entry_date).toLocaleDateString() : 'N/A'
                                        }}
                                    </span>
                                </template>
                            </Column>

                            <Column header="Trade Status" :style="{ width: '120px' }">
                                <template #body="slotProps">
                                    <Tag :value="getTradeStatus(slotProps.data)"
                                        :severity="getTradeStatusSeverity(slotProps.data)"
                                        class="whitespace-nowrap" />
                                </template>
                            </Column>

                            <Column header="R:R" :style="{ width: '70px' }">
                                <template #body="slotProps">
                                    <span class="text-sm font-mono">
                                        {{ slotProps.data.trade_entry?.rrr ?
                                            Number(slotProps.data.trade_entry.rrr).toFixed(2) : 'N/A' }}
                                    </span>
                                </template>
                            </Column>

                            <Column header="Images" :style="{ width: '80px' }">
                                <template #body="slotProps">
                                    <div v-if="slotProps.data.trade_entry?.screenshot_paths && slotProps.data.trade_entry.screenshot_paths.length > 0" class="flex items-center justify-center">
                                        <Tag :value="slotProps.data.trade_entry.screenshot_paths.length" icon="pi pi-image" severity="info" />
                                    </div>
                                    <span v-else class="text-gray-400 text-sm">—</span>
                                </template>
                            </Column>

                            <Column header="Actions" :style="{ width: '140px' }">
                                <template #body="slotProps">
                                    <div class="flex gap-1">
                                        <Link :href="route('checklists.show', slotProps.data.id)">
                                        <Button icon="pi pi-eye" size="small" severity="info" text
                                            v-tooltip="'View Details'" />
                                        </Link>
                                        <Link :href="route('checklists.edit', slotProps.data.id)">
                                        <Button icon="pi pi-pencil" size="small" severity="success" text
                                            v-tooltip="'Edit'" />
                                        </Link>
                                        <Button icon="pi pi-trash" size="small" severity="danger" text
                                            @click="confirmDelete(slotProps.data.id)" v-tooltip="'Delete'" />
                                    </div>
                                </template>
                            </Column>
                        </DataTable>
                    </div>

                    <!-- Custom Pagination -->
                    <div class="mt-6 flex justify-center" v-if="checklists.data.length > 0">
                        <Paginator :first="(checklists.current_page - 1) * checklists.per_page"
                            :rows="checklists.per_page" :totalRecords="checklists.total" @page="onPageChange"
                            template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink" />
                    </div>
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
        <Toast />
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { onMounted, computed } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import { route } from 'ziggy-js'
import { useDirectionalBias } from '@/composables/useDirectionalBias.js'

const confirm = useConfirm()
const toast = useToast()
const page = usePage()

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

// Helper function to determine score severity for Tag component
const getScoreSeverity = (score) => {
    if (score < 50) return 'danger'
    if (score <= 80) return 'warn'
    return 'success'
}

// Helper function to get trade status - matches Dashboard and Show.vue
const getTradeStatus = (checklist) => {
    const tradeEntry = checklist.trade_entry

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

// Helper function to get trade status severity - matches Dashboard and Show.vue
const getTradeStatusSeverity = (checklist) => {
    const tradeEntry = checklist.trade_entry

    if (!tradeEntry) {
        return 'info'  // Blue for analysis only
    }

    // Check if we have the new trade_status field
    if (tradeEntry.trade_status) {
        switch (tradeEntry.trade_status) {
            case 'pending': return 'info'   // Blue
            case 'active': return 'warn'    // Yellow
            case 'win': return 'success' // Green
            case 'loss': return 'danger'  // Red
            case 'breakeven': return 'info' // Yellow
            case 'cancelled': return 'secondary' // Gray
            default: return 'secondary'
        }
    }
    return 'secondary'  // Default for unknown status
}

const confirmDelete = (checklistId) => {
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
            router.post(route('checklists.destroy', checklistId))
            toast.add({ severity: 'success', summary: 'Success', detail: 'Checklist deleted', life: 3000 });
        },
        reject: () => {
            toast.add({ severity: 'info', summary: 'Cancelled', detail: 'Delete cancelled', life: 3000 });
        }
    })
}

// Handle pagination
const onPageChange = (event) => {
    const page = (event.first / event.rows) + 1;
    router.get(window.location.pathname, { page: page }, {
        preserveState: true,
        replace: true,
    });
}
</script>
