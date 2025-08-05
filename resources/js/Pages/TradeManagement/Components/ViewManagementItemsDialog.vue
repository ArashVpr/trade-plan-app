<template>
    <Dialog :visible="visible" modal :style="{ width: '60rem', height: modalHeight }" :dismissableMask="true"
        @update:visible="$emit('update:visible', $event)" class="management-items-dialog">

        <!-- Enhanced Header -->
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <i class="pi pi-list text-blue-600"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Management Items</h2>
                        <p class="text-sm text-gray-600">{{ trade?.checklist?.symbol }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <Badge :value="`${trade?.management_items?.length || 0} items`" severity="info" />
                    <Badge v-if="pendingCount > 0" :value="`${pendingCount} pending`" severity="warning" />
                </div>
            </div>
        </template>

        <div class="flex-1 overflow-y-auto pr-2">
            <div v-if="trade?.management_items?.length > 0" class="space-y-4">
                <Card v-for="item in trade.management_items" :key="item.id"
                    class="shadow-sm hover:shadow-md transition-all duration-200"
                    :class="getEnhancedItemClasses(item.status)">
                    <template #content>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-3">
                                    <Badge :value="item.priority.toUpperCase()" size="large"
                                        :severity="getPrioritySeverity(item.priority)" class="mr-3" />
                                    <Badge :value="item.type.toUpperCase()" severity="info" class="mr-3" />
                                    <Tag :value="item.status.toUpperCase()" :severity="getStatusSeverity(item.status)"
                                        :icon="getStatusIcon(item.status)" />
                                </div>

                                <h4 class="font-bold text-lg mb-2 text-gray-800">{{ item.title }}</h4>

                                <p v-if="item.description" class="text-gray-600 mb-3 leading-relaxed">
                                    {{ item.description }}
                                </p>

                                <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                                    <div v-if="item.due_date" class="flex items-center">
                                        <i class="pi pi-clock mr-2 text-gray-500"></i>
                                        <span class="text-sm">
                                            <strong>Due:</strong> {{ formatDateTime(item.due_date) }}
                                            <span v-if="isOverdue(item)"
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                                                <i class="pi pi-exclamation-triangle mr-1"></i>
                                                Overdue
                                            </span>
                                        </span>
                                    </div>
                                    <div v-if="item.triggered_at" class="flex items-center">
                                        <i class="pi pi-check-circle mr-2 text-gray-500"></i>
                                        <span class="text-sm">
                                            <strong>{{ item.status === 'completed' ? 'Completed' : 'Triggered'
                                            }}:</strong>
                                            {{ formatDateTime(item.triggered_at) }}
                                        </span>
                                    </div>
                                    <div v-if="item.notes" class="flex items-start">
                                        <i class="pi pi-comment mr-2 text-gray-500 mt-0.5"></i>
                                        <span class="text-sm">
                                            <strong>Notes:</strong> {{ item.notes }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 ml-6">
                                <template v-if="item.status === 'pending'">
                                    <Button label="✅ Complete" size="small" severity="success" class="font-medium"
                                        @click="updateItemStatus(item.id, 'completed')" />
                                    <Button label="⚡ Trigger" size="small" severity="warning"
                                        @click="updateItemStatus(item.id, 'triggered')" />
                                    <Button label="⏸️ Ignore" size="small" severity="secondary" outlined
                                        @click="updateItemStatus(item.id, 'ignored')" />
                                </template>

                                <template v-else>
                                    <Button label="🔄 Reset" size="small" severity="secondary" outlined
                                        @click="updateItemStatus(item.id, 'pending')" />
                                </template>

                                <Button icon="pi pi-pencil" size="small" severity="info" outlined
                                    @click="editItem(item)" v-tooltip="'Edit Item'" />

                                <Button icon="pi pi-trash" size="small" severity="danger" outlined
                                    @click="deleteItem(item.id)" v-tooltip="'Delete Item'" />
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <Card v-else class="text-center">
                <template #content>
                    <div class="py-12">
                        <div class="w-20 h-20 mx-auto mb-6 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="pi pi-list text-3xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">No Management Items</h3>
                        <p class="text-gray-600 mb-6">This trade doesn't have any management items yet.</p>
                    </div>
                </template>
            </Card>
        </div>

        <template #footer>
            <div class="flex justify-end">
                <Button label="Close" severity="secondary" @click="$emit('update:visible', false)" />
            </div>
        </template>
    </Dialog>

    <!-- Edit Item Dialog -->
    <Dialog v-model:visible="editDialogVisible" modal header="Edit Management Item" :style="{ width: '30rem' }"
        :dismissableMask="true">
        <form @submit.prevent="saveEdit" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2">Title</label>
                <InputText v-model="editForm.title" class="w-full" required />
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Description</label>
                <Textarea v-model="editForm.description" rows="3" class="w-full" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Priority</label>
                    <Dropdown v-model="editForm.priority" :options="priorityOptions" option-label="label"
                        option-value="value" class="w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Due Date</label>
                    <Calendar v-model="editForm.due_date" show-time hour-format="24" class="w-full" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Notes</label>
                <Textarea v-model="editForm.notes" rows="2" class="w-full"
                    placeholder="Add notes about this management item" />
            </div>
        </form>

        <template #footer>
            <div class="flex justify-end space-x-2">
                <Button label="Cancel" severity="secondary" @click="editDialogVisible = false" />
                <Button label="Save" @click="saveEdit" />
            </div>
        </template>
    </Dialog>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:visible="deleteDialogVisible" modal header="Confirm Deletion" :style="{ width: '400px' }"
        :dismissableMask="true">
        <div class="space-y-4">
            <div class="flex items-center">
                <i class="pi pi-exclamation-triangle text-orange-500 text-2xl mr-3"></i>
                <div>
                    <p class="font-medium">Are you sure you want to delete this management item?</p>
                    <p class="text-sm text-gray-600 mt-1">This action cannot be undone.</p>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2">
                <Button label="Cancel" severity="secondary" outlined @click="cancelDelete" />
                <Button label="Delete" severity="danger" @click="confirmDelete" />
            </div>
        </template>
    </Dialog>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useToast } from 'primevue/usetoast'

const toast = useToast()

const props = defineProps({
    visible: Boolean,
    trade: Object,
})

const emit = defineEmits(['update:visible', 'item-updated', 'open-add-dialog'])

// Computed properties
const pendingCount = computed(() => {
    return props.trade?.management_items?.filter(item => item.status === 'pending').length || 0
})

const modalHeight = computed(() => {
    const itemCount = props.trade?.management_items?.length || 0

    // Base height for header and footer
    const baseHeight = 200

    // Height per item (approximately 250px per item for card, spacing, etc.)
    const itemHeight = 250

    // Calculate total height
    const calculatedHeight = baseHeight + (itemCount * itemHeight)

    // Minimum height of 300px, maximum of 90vh
    const minHeight = 300
    const maxHeightVh = window.innerHeight * 0.9

    const finalHeight = Math.min(Math.max(calculatedHeight, minHeight), maxHeightVh)

    return `${finalHeight}px`
})

// State
const editDialogVisible = ref(false)
const deleteDialogVisible = ref(false)
const itemToDelete = ref(null)
const editForm = ref({
    id: null,
    title: '',
    description: '',
    priority: '',
    due_date: null,
    notes: '',
})

const priorityOptions = [
    { label: 'Low', value: 'low' },
    { label: 'Medium', value: 'medium' },
    { label: 'High', value: 'high' },
    { label: 'Critical', value: 'critical' },
]

// Methods
const formatDateTime = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleString()
}

const isOverdue = (item) => {
    return item.due_date && new Date(item.due_date) < new Date() && item.status === 'pending'
}

const getItemStatusClasses = (status) => {
    const classes = {
        'pending': 'border-yellow-200 bg-yellow-50',
        'completed': 'border-green-200 bg-green-50',
        'triggered': 'border-orange-200 bg-orange-50',
        'ignored': 'border-gray-200 bg-gray-50'
    }
    return classes[status] || classes.pending
}

const getEnhancedItemClasses = (status) => {
    const classes = {
        'pending': 'border-l-4 border-l-yellow-500',
        'completed': 'border-l-4 border-l-green-500',
        'triggered': 'border-l-4 border-l-orange-500',
        'ignored': 'border-l-4 border-l-gray-400'
    }
    return classes[status] || classes.pending
}

const getStatusIcon = (status) => {
    const icons = {
        'pending': 'pi-clock',
        'completed': 'pi-check-circle',
        'triggered': 'pi-play-circle',
        'ignored': 'pi-times-circle'
    }
    return icons[status] || 'pi-clock'
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
        'completed': 'success',
        'triggered': 'warn',
        'ignored': 'secondary'
    }
    return severities[status] || 'info'
}

const updateItemStatus = (itemId, status) => {
    const notes = status === 'completed' ? 'Marked as completed' :
        status === 'triggered' ? 'Triggered by user' :
            status === 'ignored' ? 'Ignored by user' : null

    router.put(route('trade-management.update-status', itemId), {
        status,
        notes,
    }, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Management item updated successfully',
                life: 3000
            })
            emit('item-updated')
        }
    })
}

const editItem = (item) => {
    editForm.value = {
        id: item.id,
        title: item.title,
        description: item.description || '',
        priority: item.priority,
        due_date: item.due_date ? new Date(item.due_date) : null,
        notes: item.notes || '',
    }
    editDialogVisible.value = true
}

const saveEdit = () => {
    router.put(route('trade-management.update', editForm.value.id), {
        title: editForm.value.title,
        description: editForm.value.description,
        priority: editForm.value.priority,
        due_date: editForm.value.due_date ? new Date(editForm.value.due_date).toISOString() : null,
        notes: editForm.value.notes,
    }, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Management item updated successfully',
                life: 3000
            })
            editDialogVisible.value = false
            emit('item-updated')
        }
    })
}

const deleteItem = (itemId) => {
    itemToDelete.value = itemId
    deleteDialogVisible.value = true
}

const confirmDelete = () => {
    if (!itemToDelete.value) return

    router.delete(route('trade-management.destroy', itemToDelete.value), {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Management item deleted successfully',
                life: 3000
            })
            deleteDialogVisible.value = false
            itemToDelete.value = null
            emit('item-updated')
        }
    })
}

const cancelDelete = () => {
    deleteDialogVisible.value = false
    itemToDelete.value = null
}
</script>
