<template>
    <div>
        <div class="max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
            <h1 class="text-3xl font text-blue-900 mb-6 text-center">Checklist History</h1>

            <Button label="Create New Checklist" icon="pi pi-plus" @click="router.get(route('home'))" class="mb-4" />

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

                            <Column field="asset" header="Instrument" :style="{ width: '100px' }">
                                <template #body="slotProps">
                                    <Chip :label="slotProps.data.asset || 'N/A'" size="small" />
                                </template>
                            </Column>

                            <Column header="Score" :style="{ width: '80px' }">
                                <template #body="slotProps">
                                    <Tag :value="`${slotProps.data.score}/100`"
                                        :severity="getScoreSeverity(slotProps.data.score)" />
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

                            <Column header="Trade Status" :style="{ width: '100px' }">
                                <template #body="slotProps">
                                    <Tag :value="getTradeStatus(slotProps.data)"
                                        :severity="getTradeStatusSeverity(slotProps.data)" />
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
    </div>
    <ConfirmDialog></ConfirmDialog>
    <Toast />
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import { route } from 'ziggy-js'

const confirm = useConfirm()
const toast = useToast()

const props = defineProps({
    checklists: Object
})

// Helper function to determine score severity for Tag component
const getScoreSeverity = (score) => {
    if (score < 50) return 'danger'
    if (score <= 80) return 'warning'
    return 'success'
}

// Helper function to determine outcome severity
const getOutcomeSeverity = (outcome) => {
    switch (outcome) {
        case 'win': return 'success'
        case 'loss': return 'danger'
        case 'breakeven': return 'warning'
        default: return 'secondary'
    }
}

// Helper function to get trade status
const getTradeStatus = (checklist) => {
    if (!checklist.trade_entry) {
        return 'Analysis Only'
    }
    if (checklist.trade_entry.outcome) {
        // Show the outcome (Win/Loss/Breakeven) if trade is completed
        return checklist.trade_entry.outcome.charAt(0).toUpperCase() + checklist.trade_entry.outcome.slice(1)
    }
    // Show position type if trade is taken but not completed
    return checklist.trade_entry.position_type || 'Trade Pending'
}

// Helper function to get trade status severity
const getTradeStatusSeverity = (checklist) => {
    if (!checklist.trade_entry) {
        return 'info'  // Blue for analysis only
    }
    if (checklist.trade_entry.outcome) {
        // Use outcome severity for completed trades
        return getOutcomeSeverity(checklist.trade_entry.outcome)
    }
    // Use position severity for pending trades
    return checklist.trade_entry.position_type === 'Long' ? 'success' : 'danger'
}

const confirmDelete = (checklistId) => {
    // confirm.require({
    //     group: 'headless',
    //     header: 'Delete Checklist',
    //     message: 'Are you sure you want to delete this checklist? This action cannot be undone.',
    //     accept: () => {

    //         router.delete(route('checklists.destroy', checklistId), {}, {
    //             onSuccess: () => {
    //                 toast.add({ severity: 'success', summary: 'Success', detail: 'Checklist deleted', life: 3000 })
    //             },
    //             onError: (errors) => {
    //                 console.error('Delete error:', errors)
    //                 toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete checklist.', life: 3000 })
    //             }
    //         }
    //         )
    //     }
    // })
    confirm.require({
        message: 'Do you want to delete this record?',
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
