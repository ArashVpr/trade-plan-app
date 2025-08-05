<template>
    <Dialog :visible="visible" modal :style="{ width: '65rem', maxHeight: '90vh' }"
        @update:visible="$emit('update:visible', $event)" class="add-management-dialog">

        <!-- Enhanced Header -->
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <i class="pi pi-plus text-green-600"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Add Management Items</h2>
                        <p class="text-sm text-gray-600">{{ trade?.checklist?.symbol }}</p>
                    </div>
                </div>
                <Badge v-if="selectedItems.length > 0" :value="`${selectedItems.length} selected`" severity="success" />
            </div>
        </template>

        <div class="max-h-96 overflow-y-auto space-y-6">
            <!-- Quick Actions -->
            <Card class="bg-blue-50 border-blue-200">
                <template #content>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="pi pi-lightbulb text-blue-600 text-xl mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-blue-900">Quick Add Common Items</h3>
                                <p class="text-sm text-blue-700">Add frequently used management items with one click</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Button label="⚡ Risk Management" size="small" severity="warning" outlined
                                @click="addQuickItem('risk')" />
                            <Button label="📈 Profit Taking" size="small" severity="success" outlined
                                @click="addQuickItem('profit')" />
                            <Button label="⏰ Time Check" size="small" severity="info" outlined
                                @click="addQuickItem('time')" />
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Predefined Items Section -->
            <Card>
                <template #title>
                    <div class="flex items-center">
                        <i class="pi pi-bookmark text-indigo-600 mr-2"></i>
                        Predefined Templates
                    </div>
                </template>
                <template #content>
                    <div v-if="predefinedItems && Object.keys(predefinedItems).length > 0">
                        <Accordion value="0">
                            <AccordionPanel v-for="(items, category) in predefinedItems" :key="category"
                                :value="category">
                                <AccordionHeader>
                                    <div class="flex items-center">
                                        <Badge :value="items.length" severity="secondary" class="mr-2" />
                                        <span class="font-medium">{{ formatCategoryName(category) }}</span>
                                    </div>
                                </AccordionHeader>
                                <AccordionContent>
                                    <div class="grid gap-3">
                                        <Card v-for="(item, index) in items" :key="index"
                                            class="border hover:shadow-md transition-shadow cursor-pointer"
                                            :class="isItemSelected(category, item) ? 'ring-2 ring-blue-500 bg-blue-50' : ''"
                                            @click="togglePredefinedItem(category, item)">
                                            <template #content>
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center mb-2">
                                                            <Badge :value="(item.priority || 'medium').toUpperCase()"
                                                                size="small"
                                                                :severity="getPrioritySeverity(item.priority)"
                                                                class="mr-2" />
                                                            <Badge :value="(item.type || category).toUpperCase()"
                                                                severity="info" size="small" />
                                                        </div>
                                                        <h4 class="font-semibold text-gray-800 mb-1">{{ item.title ||
                                                            'Untitled' }}</h4>
                                                        <p class="text-sm text-gray-600">{{ item.description || 'No description' }}</p>
                                                    </div>
                                                    <div class="ml-4">
                                                        <Button
                                                            :icon="isItemSelected(category, item) ? 'pi pi-check' : 'pi pi-plus'"
                                                            :severity="isItemSelected(category, item) ? 'success' : 'secondary'"
                                                            size="small" :outlined="!isItemSelected(category, item)"
                                                            @click.stop="togglePredefinedItem(category, item)" />
                                                    </div>
                                                </div>
                                            </template>
                                        </Card>
                                    </div>
                                </AccordionContent>
                            </AccordionPanel>
                        </Accordion>
                    </div>
                    <div v-else class="text-center py-8">
                        <i class="pi pi-info-circle text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">No predefined templates available</p>
                    </div>
                </template>
            </Card>

            <!-- Custom Item Form -->
            <Card>
                <template #title>
                    <div class="flex items-center">
                        <i class="pi pi-pencil text-purple-600 mr-2"></i>
                        Create Custom Item
                    </div>
                </template>
                <template #content>
                    <form @submit.prevent="addCustomItem" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2 text-gray-700">
                                    <i class="pi pi-tag mr-1"></i>Type
                                </label>
                                <Select v-model="customForm.type" :options="typeOptions" option-label="label"
                                    option-value="value" placeholder="Select type" class="w-full" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2 text-gray-700">
                                    <i class="pi pi-flag mr-1"></i>Priority
                                </label>
                                <Select v-model="customForm.priority" :options="priorityOptions" option-label="label"
                                    option-value="value" placeholder="Select priority" class="w-full" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 text-gray-700">
                                <i class="pi pi-file mr-1"></i>Title
                            </label>
                            <InputText v-model="customForm.title" placeholder="Enter management item title"
                                class="w-full" required />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 text-gray-700">
                                <i class="pi pi-align-left mr-1"></i>Description
                            </label>
                            <Textarea v-model="customForm.description" placeholder="Enter description (optional)"
                                rows="3" class="w-full" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 text-gray-700">
                                <i class="pi pi-calendar mr-1"></i>Due Date (Optional)
                            </label>
                            <DatePicker v-model="customForm.due_date" show-time hour-format="24" class="w-full"
                                placeholder="Select due date" />
                        </div>

                        <Button type="submit" label="🎯 Add Custom Item" severity="success" class="w-full"
                            :disabled="!customForm.title || !customForm.type || !customForm.priority" />
                    </form>
                </template>
            </Card>

            <!-- Selected Items Preview -->
            <div v-if="selectedItems.length > 0">
                <Divider />
                <h3 class="text-lg font-semibold mb-4">Selected Items ({{ selectedItems.length }})</h3>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    <div v-for="(item, index) in selectedItems" :key="index"
                        class="flex items-center justify-between p-2 bg-blue-50 border border-blue-200 rounded">
                        <div class="flex items-center">
                            <Badge :value="(item.priority || 'medium').toUpperCase()"
                                :severity="getPrioritySeverity(item.priority)" class="mr-2" />
                            <span class="font-medium">{{ item.title || 'Untitled' }}</span>
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
    return severities[priority] || 'warning'
}

const isItemSelected = (category, item) => {
    if (!item || !selectedItems.value) return false
    return selectedItems.value.some(selected =>
        selected.title === item.title && selected.type === category
    )
}

const togglePredefinedItem = (category, item) => {
    if (!item || !selectedItems.value || !item.title) return

    const existingIndex = selectedItems.value.findIndex(selected =>
        selected.title === item.title && selected.type === category
    )

    if (existingIndex > -1) {
        selectedItems.value.splice(existingIndex, 1)
    } else {
        const newItem = {
            type: category,
            title: item.title || 'Untitled',
            description: item.description || '',
            priority: item.priority || 'medium',
            due_date: null,
        }
        selectedItems.value.push(newItem)
    }
}

const addQuickItem = (type) => {
    const quickItems = {
        risk: {
            title: 'Move Stop to Breakeven',
            description: 'Consider moving stop loss to breakeven after 1R profit',
            priority: 'high',
            type: 'risk'
        },
        profit: {
            title: 'Take Partial Profits',
            description: 'Take 50% profits at 1.5R and let rest run',
            priority: 'medium',
            type: 'technical'
        },
        time: {
            title: 'Check Market Session',
            description: 'Review if major market session changes affect trade',
            priority: 'low',
            type: 'time'
        }
    }

    const item = quickItems[type]
    if (item && !selectedItems.value.some(s => s.title === item.title)) {
        selectedItems.value.push(item)
    }
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
