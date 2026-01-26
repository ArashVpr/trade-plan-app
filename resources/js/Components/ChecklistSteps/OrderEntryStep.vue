<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-blue-900 dark:text-blue-300 flex items-center gap-2">
                <i class="pi pi-money-bill text-blue-900 dark:text-blue-300"></i>
                Order Entry (Optional)
            </h2>
            <Chip :label="`Symbol: ${symbol || '—'}`" />
        </div>

        <Message severity="info" :closable="false" class="mb-4">
            <strong>Optional:</strong> You can submit your analysis without filling out the order
            entry details.
            Complete this section only if you've actually placed or plan to place a trade.
        </Message>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="field">
                <label class="block text-sm font-medium mb-2">Entry Date</label>
                <DatePicker :model-value="modelValue.entryDate" @update:model-value="updateData('entryDate', $event)"
                    dateFormat="yy-mm-dd" class="w-full" showIcon fluid iconDisplay="input" />
            </div>

            <div class="field">
                <label class="block text-sm font-medium mb-2">Position Type</label>
                <Select :model-value="modelValue.positionType" @update:model-value="updateData('positionType', $event)"
                    :options="positionOptions" placeholder="Select Position Type" class="w-full" />
            </div>

            <div class="field">
                <label class="block text-sm font-medium mb-2">Entry Price</label>
                <InputText :model-value="modelValue.entryPrice" @update:model-value="updateData('entryPrice', $event)"
                    type="number" step="0.0001" placeholder="0.0000" class="w-full" />
            </div>

            <div class="field">
                <label class="block text-sm font-medium mb-2">Stop Price</label>
                <InputText :model-value="modelValue.stopPrice" @update:model-value="updateData('stopPrice', $event)"
                    type="number" step="0.0001" placeholder="0.0000" class="w-full" />
            </div>

            <div class="field">
                <label class="block text-sm font-medium mb-2">Target Price</label>
                <InputText :model-value="modelValue.targetPrice" @update:model-value="updateData('targetPrice', $event)"
                    type="number" step="0.0001" placeholder="0.0000" class="w-full" />
            </div>

            <div class="field">
                <label class="block text-sm font-medium mb-2">R:R Ratio</label>
                <InputText :model-value="modelValue.rrr" @update:model-value="updateData('rrr', $event)" type="number"
                    step="0.01" placeholder="1.50" class="w-full" />
            </div>
        </div>

        <div class="field">
            <label class="block text-sm font-medium mb-2">Trade Status</label>
            <Select :model-value="modelValue.trade_status" @update:model-value="updateData('trade_status', $event)"
                :options="tradeStatusOptions" optionLabel="label" optionValue="value" placeholder="Select Trade Status"
                class="w-full" />
        </div>

        <div class="field">
            <label class="block text-sm font-medium mb-2">Chart Screenshots (Max 5)</label>
            <FileUpload ref="fileupload" name="screenshots[]" :multiple="true" accept="image/*" :maxFileSize="5000000"
                :fileLimit="5" customUpload @select="onFilesSelect" @remove="onFileRemove" class="w-full">
                <template #header="{ chooseCallback, clearCallback, files }">
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <div class="flex gap-2">
                            <Button @click="chooseCallback()" icon="pi pi-images" label="Choose" outlined
                                severity="secondary" size="small" />
                            <Button @click="clearCallback()" icon="pi pi-times" label="Clear" outlined severity="danger"
                                size="small" :disabled="!files || files.length === 0" />
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ files?.length || 0 }}/5 files</span>
                    </div>
                </template>
                <template #content="{ files, removeFileCallback }">
                    <div v-if="files.length > 0" class="pt-4">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div v-for="(file, index) of files" :key="file.name + file.type + file.size"
                                class="relative border border-gray-200 dark:border-gray-700 rounded-lg p-2">
                                <img role="presentation" :alt="file.name" :src="file.objectURL"
                                    class="w-full h-32 object-cover rounded" />
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 truncate">{{ file.name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ formatSize(file.size) }}</div>
                                <Button icon="pi pi-times" @click="removeFileCallback(index)" rounded text
                                    severity="danger" class="absolute top-1 right-1" size="small" />
                            </div>
                        </div>
                    </div>
                </template>
                <template #empty>
                    <div class="flex items-center justify-center flex-col py-8">
                        <i class="pi pi-image text-gray-400 dark:text-gray-500 text-4xl mb-3"></i>
                        <p class="text-gray-600 dark:text-gray-400">Drag and drop images here or click Choose</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max 5 images, 5MB each</p>
                    </div>
                </template>
            </FileUpload>
        </div>

        <div class="field">
            <label class="block text-sm font-medium mb-2">Notes</label>
            <Textarea :model-value="modelValue.notes" @update:model-value="updateData('notes', $event)" rows="3"
                placeholder="Add any notes about this trade" class="w-full" />
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    },
    symbol: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue', 'progress-updated'])

const positionOptions = ['Long', 'Short']
const tradeStatusOptions = [
    { label: 'Pending', value: 'pending' },
    { label: 'Active', value: 'active' },
    { label: 'Win', value: 'win' },
    { label: 'Loss', value: 'loss' },
    { label: 'Breakeven', value: 'breakeven' },
    { label: 'Cancelled', value: 'cancelled' }
]

const updateData = (key, value) => {
    const updatedData = { ...props.modelValue, [key]: value }
    emit('update:modelValue', updatedData)
    emit('progress-updated')
}

const onFilesSelect = (event) => {
    updateData('screenshots', event.files)
}

const onFileRemove = (event) => {
    updateData('screenshots', event.files)
}

const formatSize = (bytes) => {
    if (bytes === 0) return '0 B'
    const k = 1024
    const sizes = ['B', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}
</script>
