<template>
    <Dialog :visible="visible" modal :header="`Add Management Items - ${trade?.checklist?.symbol}`"
        :style="{ width: '50rem' }" @update:visible="$emit('update:visible', $event)">
        <div class="space-y-6">
            <!-- Predefined Items Section -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Add Predefined Items</h3>
                <Accordion>
                    <AccordionTab v-for="(items, category) in predefinedItems" :key="category"
                        :header="formatCategoryName(category)">
                        <div class="space-y-3">
                            <div v-for="(item, index) in items" :key="index"
                                class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <Badge :value="item.priority.toUpperCase()"
                                            :severity="getPrioritySeverity(item.priority)" class="mr-3" />
                                        <div>
                                            <h4 class="font-medium">{{ item.title }}</h4>
                                            <p class="text-sm text-gray-600">{{ item.description }}</p>
                                        </div>
                                    </div>
                                </div>
                                <Button label="Add" size="small" @click="addPredefinedItem(category, item)" />
                            </div>
                        </div>
                    </AccordionTab>
                </Accordion>
            </div>

            <Divider />

            <!-- Custom Item Form -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Add Custom Management Item</h3>
                <form @submit.prevent="addCustomItem" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Type</label>
                            <Dropdown v-model="customForm.type" :options="typeOptions" option-label="label"
                                option-value="value" placeholder="Select type" class="w-full" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Priority</label>
                            <Dropdown v-model="customForm.priority" :options="priorityOptions" option-label="label"
                                option-value="value" placeholder="Select priority" class="w-full" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Title</label>
                        <InputText v-model="customForm.title" placeholder="Enter management item title" class="w-full"
                            required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Description</label>
                        <Textarea v-model="customForm.description" placeholder="Enter description (optional)" rows="3"
                            class="w-full" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Due Date (Optional)</label>
                        <Calendar v-model="customForm.due_date" show-time hour-format="24" class="w-full"
                            placeholder="Select due date" />
                    </div>

                    <Button type="submit" label="Add Custom Item" class="w-full"
                        :disabled="!customForm.title || !customForm.type || !customForm.priority" />
                </form>
            </div>

            <!-- Selected Items Preview -->
            <div v-if="selectedItems.length > 0">
                <Divider />
                <h3 class="text-lg font-semibold mb-4">Selected Items ({{ selectedItems.length }})</h3>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    <div v-for="(item, index) in selectedItems" :key="index"
                        class="flex items-center justify-between p-2 bg-blue-50 border border-blue-200 rounded">
                        <div class="flex items-center">
                            <Badge :value="item.priority.toUpperCase()" :severity="getPrioritySeverity(item.priority)"
                                class="mr-2" />
                            <span class="font-medium">{{ item.title }}</span>
                        </div>
                        <Button icon="pi pi-times" size="small" severity="danger" text
                            @click="removeSelectedItem(index)" />
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-between">
                <Button label="Cancel" severity="secondary" @click="closeDialog" />
                <Button label="Add All Items" @click="submitItems" :disabled="selectedItems.length === 0" />
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
    predefinedItems: Object,
})

const emit = defineEmits(['update:visible', 'items-added'])

// State
const selectedItems = ref([])
const customForm = ref({
    type: '',
    title: '',
    description: '',
    priority: '',
    due_date: null,
})

// Options
const typeOptions = [
    { label: 'Technical', value: 'technical' },
    { label: 'Fundamental', value: 'fundamental' },
    { label: 'Risk Management', value: 'risk' },
    { label: 'Time-based', value: 'time' },
    { label: 'Custom', value: 'custom' },
]

const priorityOptions = [
    { label: 'Low', value: 'low' },
    { label: 'Medium', value: 'medium' },
    { label: 'High', value: 'high' },
    { label: 'Critical', value: 'critical' },
]

// Methods
const formatCategoryName = (category) => {
    return category.charAt(0).toUpperCase() + category.slice(1).replace(/([A-Z])/g, ' $1')
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

const addPredefinedItem = (category, item) => {
    const newItem = {
        type: category,
        title: item.title,
        description: item.description,
        priority: item.priority,
        due_date: null,
    }

    // Check if item already exists
    const exists = selectedItems.value.some(selected =>
        selected.title === item.title && selected.type === category
    )

    if (!exists) {
        selectedItems.value.push(newItem)
        toast.add({
            severity: 'success',
            summary: 'Added',
            detail: `${item.title} added to selection`,
            life: 2000
        })
    } else {
        toast.add({
            severity: 'warn',
            summary: 'Already Added',
            detail: `${item.title} is already in selection`,
            life: 2000
        })
    }
}

const addCustomItem = () => {
    if (!customForm.value.title || !customForm.value.type || !customForm.value.priority) {
        return
    }

    selectedItems.value.push({ ...customForm.value })

    // Reset form
    customForm.value = {
        type: '',
        title: '',
        description: '',
        priority: '',
        due_date: null,
    }

    toast.add({
        severity: 'success',
        summary: 'Added',
        detail: 'Custom item added to selection',
        life: 2000
    })
}

const removeSelectedItem = (index) => {
    selectedItems.value.splice(index, 1)
}

const submitItems = () => {
    if (selectedItems.value.length === 0) return

    router.post(route('trade-management.add-predefined'), {
        trade_entry_id: props.trade.id,
        items: selectedItems.value.map(item => ({
            type: item.type,
            title: item.title,
            description: item.description,
            priority: item.priority,
            due_date: item.due_date ? new Date(item.due_date).toISOString() : null,
        }))
    }, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: `${selectedItems.value.length} management items added`,
                life: 3000
            })
            closeDialog()
            emit('items-added')
        }
    })
}

const closeDialog = () => {
    selectedItems.value = []
    customForm.value = {
        type: '',
        title: '',
        description: '',
        priority: '',
        due_date: null,
    }
    emit('update:visible', false)
}
</script>
