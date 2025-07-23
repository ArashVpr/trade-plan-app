<template>
    <div class="max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Checklist History</h1>

        <Button label="Create New Checklist" icon="pi pi-plus" @click="$inertia.get('/')" class="mb-4" />

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
                        class="p-datatable-sm">
                        <Column field="asset" header="Asset">
                            <template #body="slotProps">
                                {{ slotProps.data.asset || 'N/A' }}
                            </template>
                        </Column>

                        <Column header="Score">
                            <template #body="slotProps">
                                <Tag :value="`${slotProps.data.score}/100`"
                                    :severity="getScoreSeverity(slotProps.data.score)" />
                            </template>
                        </Column>

                        <Column header="Created">
                            <template #body="slotProps">
                                {{ new Date(slotProps.data.created_at).toLocaleDateString() }}
                            </template>
                        </Column>

                        <Column header="Actions">
                            <template #body="slotProps">
                                <Button label="View Details" icon="pi pi-eye" size="small"
                                    @click="$inertia.get(`/checklists/${slotProps.data.id}`)" />
                            </template>
                        </Column>
                    </DataTable>
                </div>

                <!-- Custom Pagination -->
                <div class="mt-6 flex justify-center" v-if="checklists.data.length > 0">
                    <Paginator :first="(checklists.current_page - 1) * checklists.per_page" :rows="checklists.per_page"
                        :totalRecords="checklists.total" @page="onPageChange"
                        template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink" />
                </div>
            </template>
        </Card>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    checklists: {
        type: Object,
        default: () => ({})
    }
})

// Helper function to determine score severity for Tag component
const getScoreSeverity = (score) => {
    if (score < 50) return 'danger'
    if (score <= 80) return 'warning'
    return 'success'
}

// Handle pagination
const onPageChange = (event) => {
    const page = (event.first / event.rows) + 1
    // Assuming your Laravel pagination uses ?page= parameter
    const url = new URL(window.location.href)
    url.searchParams.set('page', page)
    window.$inertia.get(url.toString())
}
</script>
