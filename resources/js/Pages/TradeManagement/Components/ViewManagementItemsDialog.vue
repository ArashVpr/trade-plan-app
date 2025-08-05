<template>
    <Dialog :visible="visible" modal :header="`Management Items - ${trade?.checklist?.symbol}`"
        :style="{ width: '50rem' }" :dismissableMask="true" @update:visible="$emit('update:visible', $event)">
        <div v-if="trade?.management_items?.length > 0" class="space-y-4">
            <div v-for="item in trade.management_items" :key="item.id" class="border rounded-lg p-4"
                :class="getItemStatusClasses(item.status)">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <Badge :value="item.priority.toUpperCase()" :severity="getPrioritySeverity(item.priority)"
                                class="mr-2" />
                            <Badge :value="item.type.toUpperCase()" severity="info" class="mr-2" />
                            <Tag :value="item.status.toUpperCase()" :severity="getStatusSeverity(item.status)" />
                        </div>

                        <h4 class="font-semibold text-lg mb-1">{{ item.title }}</h4>

                        <p v-if="item.description" class="text-gray-600 mb-2">
                            {{ item.description }}
                        </p>

                        <div class="text-sm text-gray-500 space-y-1">
                            <div v-if="item.due_date">
                                <strong>Due:</strong> {{ formatDateTime(item.due_date) }}
                                <span v-if="isOverdue(item)" class="text-red-600 ml-2">(Overdue)</span>
                            </div>
                            <div v-if="item.triggered_at">
                                <strong>{{ item.status === 'completed' ? 'Completed' : 'Triggered' }}:</strong>
                                {{ formatDateTime(item.triggered_at) }}
                            </div>
                            <div v-if="item.notes">
                                <strong>Notes:</strong> {{ item.notes }}
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 ml-4">
                        <template v-if="item.status === 'pending'">
                            <Button label="Complete" size="small" severity="success"
                                @click="updateItemStatus(item.id, 'completed')" />
                            <Button label="Trigger" size="small" severity="warning"
                                @click="updateItemStatus(item.id, 'triggered')" />
                            <Button label="Ignore" size="small" severity="secondary"
                                @click="updateItemStatus(item.id, 'ignored')" />
                        </template>

                        <template v-else>
                            <Button label="Reset" size="small" severity="secondary"
                                @click="updateItemStatus(item.id, 'pending')" />
                        </template>

                        <Button icon="pi pi-pencil" size="small" severity="info" @click="editItem(item)"
                            v-tooltip="'Edit'" />

                        <Button icon="pi pi-trash" size="small" severity="danger" @click="deleteItem(item.id)"
                            v-tooltip="'Delete'" />
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-8 text-gray-500">
            <i class="pi pi-info-circle text-4xl mb-4"></i>
            <p>No management items found for this trade.</p>
            <p class="text-sm">Add management items to track important events and reminders.</p>
        </div>

        <template #footer>
            <div class="flex justify-end">
                <Button label="Close" @click="$emit('update:visible', false)" />
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

const emit = defineEmits(['update:visible', 'item-updated'])

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
