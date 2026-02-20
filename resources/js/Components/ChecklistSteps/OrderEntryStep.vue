<template>
    <div class="space-y-8 animate-fade-in-up">
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-surface-200 dark:border-surface-700 pb-4">
            <h2
                class="text-2xl font-bold bg-gradient-to-r from-blue-900 to-blue-700 bg-clip-text text-transparent flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg shadow-sm">
                    <i class="pi pi-money-bill text-blue-700 dark:text-blue-300 text-xl"></i>
                </div>
                Order Entry
                <Badge value="Optional" severity="secondary" class="ml-2 font-normal"></Badge>
            </h2>
            <div
                class="px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center gap-2 border border-slate-200 dark:border-slate-700">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Symbol</span>
                <div class="flex items-center gap-2">
                    <SymbolLogo :symbol="symbol" size="small" />
                    <span class="font-mono font-bold text-lg text-slate-800 dark:text-slate-200">{{ symbol || '---'
                        }}</span>
                </div>
            </div>
        </div>

        <Message severity="info" :closable="false" class="shadow-sm">
            <template #icon>
                <i class="pi pi-info-circle text-lg"></i>
            </template>
            <div class="ml-2">
                <strong>Execution Details:</strong> Fill this section only if you are placing a live order.
            </div>
        </Message>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Position Type -->
                <section>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Position
                        Type</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div v-for="type in positionOptions" :key="type" @click="updateData('positionType', type)"
                            class="cursor-pointer relative overflow-hidden rounded-xl border-2 transition-all duration-200 p-4 flex flex-col items-center gap-2 group hover:shadow-md"
                            :class="[
                                modelValue.positionType === type
                                    ? (type === 'Long' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-rose-500 bg-rose-50 dark:bg-rose-900/20') + ' shadow-md ring-1'
                                    : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700'
                            ]">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                :class="modelValue.positionType === type
                                    ? (type === 'Long' ? 'bg-emerald-200 text-emerald-700' : 'bg-rose-200 text-rose-700')
                                    : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-600'">
                                <i :class="type === 'Long' ? 'pi pi-arrow-up' : 'pi pi-arrow-down'"
                                    class="font-bold"></i>
                            </div>
                            <span class="font-bold"
                                :class="modelValue.positionType === type ? (type === 'Long' ? 'text-emerald-700' : 'text-rose-700') : 'text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-slate-100'">{{
                                    type }}</span>
                        </div>
                    </div>
                </section>

                <!-- Entry Date -->
                <section>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Entry
                        Date</label>
                    <DatePicker :model-value="modelValue.entryDate"
                        @update:model-value="updateData('entryDate', $event)" dateFormat="yy-mm-dd" showIcon fluid
                        iconDisplay="input" placeholder="Select date" />
                </section>

                <!-- Trade Status -->
                <section>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Status</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <button v-for="opt in tradeStatusOptions" :key="opt.value"
                            @click="updateData('trade_status', opt.value)"
                            class="py-2 px-3 rounded-lg border text-sm font-medium transition-all cursor-pointer"
                            :class="modelValue.trade_status === opt.value
                                ? getStatusClass(opt.value)
                                : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-slate-50 dark:hover:bg-slate-700 dark:hover:text-slate-200'">
                            {{ opt.label }}
                        </button>
                    </div>
                </section>
            </div>

            <!-- Right Column - Pricing -->
            <div
                class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-5">
                <h3 class="font-semibold text-lg text-slate-800 dark:text-slate-200 mb-2 flex items-center gap-2">
                    <i class="pi pi-calculator text-blue-500"></i> Parameters
                </h3>

                <div>
                    <label class="block text-xs uppercase text-slate-500 font-bold mb-1.5 ml-1">Entry Price</label>
                    <IconField class="w-full">
                        <InputIcon class="pi pi-at text-slate-400 dark:text-slate-500" />
                        <InputText :model-value="modelValue.entryPrice"
                            @update:model-value="updateData('entryPrice', $event)" type="number" step="0.0001"
                            placeholder="0.0000"
                            class="w-full dark:bg-slate-900 dark:text-white dark:border-slate-700" />
                    </IconField>
                </div>

                <div>
                    <label class="block text-xs uppercase text-slate-500 font-bold mb-1.5 ml-1">Stop Loss</label>
                    <IconField class="w-full">
                        <InputIcon class="pi pi-minus-circle text-rose-500 dark:text-rose-400" />
                        <InputText :model-value="modelValue.stopPrice"
                            @update:model-value="updateData('stopPrice', $event)" type="number" step="0.0001"
                            placeholder="0.0000"
                            class="w-full dark:bg-slate-900 dark:text-white dark:border-slate-700" />
                    </IconField>
                </div>

                <div>
                    <label class="block text-xs uppercase text-slate-500 font-bold mb-1.5 ml-1">Take Profit</label>
                    <IconField class="w-full">
                        <InputIcon class="pi pi-bullseye text-emerald-500 dark:text-emerald-400" />
                        <InputText :model-value="modelValue.targetPrice"
                            @update:model-value="updateData('targetPrice', $event)" type="number" step="0.0001"
                            placeholder="0.0000"
                            class="w-full dark:bg-slate-900 dark:text-white dark:border-slate-700" />
                    </IconField>
                </div>

                <div>
                    <label class="block text-xs uppercase text-slate-500 font-bold mb-1.5 ml-1">Risk : Reward</label>
                    <IconField class="w-full">
                        <InputIcon>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 font-mono">R:R</span>
                        </InputIcon>
                        <InputText :model-value="modelValue.rrr" @update:model-value="updateData('rrr', $event)"
                            type="number" step="0.01" placeholder="1.50"
                            class="w-full dark:bg-slate-900 dark:text-white dark:border-slate-700" />
                    </IconField>
                </div>
            </div>
        </div>

        <!-- Screenshots -->
        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                <i class="pi pi-image text-blue-900 dark:text-blue-300"></i>
                Chart Screenshots (Max 5)
            </h3>
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
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ files?.length || 0 }}/5 total</span>
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
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 flex items-center gap-1">
                            <i class="pi pi-clone text-[10px]"></i>
                            Or press <kbd
                                class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-[10px] font-mono mx-1">Ctrl+V</kbd>
                            to paste
                        </p>
                    </div>
                </template>
            </FileUpload>
        </div>

        <div class="field">
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Trade Notes</label>
            <Textarea :model-value="modelValue.notes" @update:model-value="updateData('notes', $event)" rows="3"
                placeholder="Execution context, emotions, or market conditions..." class="w-full" />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import SymbolLogo from '@/Components/SymbolLogo.vue'

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

const fileupload = ref(null)

const positionOptions = ['Long', 'Short']
const tradeStatusOptions = [
    { label: 'Pending', value: 'pending' },
    { label: 'Active', value: 'active' },
    { label: 'Win', value: 'win' },
    { label: 'Loss', value: 'loss' },
    { label: 'Breakeven', value: 'breakeven' },
    { label: 'Cancelled', value: 'cancelled' }
]

const getStatusClass = (status) => {
    switch (status) {
        case 'active': return 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-200'
        case 'win': return 'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-200'
        case 'loss': return 'bg-rose-600 text-white border-rose-600 shadow-md shadow-rose-200'
        case 'pending': return 'bg-amber-500 text-white border-amber-500'
        case 'breakeven': return 'bg-slate-500 text-white border-slate-500'
        case 'cancelled': return 'bg-slate-400 text-white border-slate-400'
        default: return 'bg-slate-200'
    }
}

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

// Paste functionality
const handlePaste = async (event) => {
    const items = event.clipboardData?.items
    if (!items) return

    // Check if we've reached the file limit
    const currentFiles = fileupload.value?.files || []
    if (currentFiles.length >= 5) {
        console.warn('Maximum file limit (5) reached')
        return
    }

    for (let i = 0; i < items.length; i++) {
        const item = items[i]

        // Check if the clipboard item is an image
        if (item.type.indexOf('image') !== -1) {
            event.preventDefault()

            const blob = item.getAsFile()
            if (!blob) continue

            // Check file size (5MB limit)
            if (blob.size > 5000000) {
                console.warn('Pasted image exceeds 5MB limit')
                continue
            }

            // Create a File object with a meaningful name
            const timestamp = new Date().getTime()
            const extension = blob.type.split('/')[1]
            const file = new File([blob], `pasted-image-${timestamp}.${extension}`, { type: blob.type })

            // Add object URL for preview
            file.objectURL = URL.createObjectURL(file)

            // Add the file to the FileUpload component
            const updatedFiles = [...currentFiles, file]

            // Update the FileUpload component's files
            if (fileupload.value) {
                fileupload.value.files = updatedFiles
                updateData('screenshots', updatedFiles)
            }

            // Only handle the first image if multiple items in clipboard
            break
        }
    }
}

onMounted(() => {
    // Add paste event listener to the entire document
    document.addEventListener('paste', handlePaste)
})

onUnmounted(() => {
    // Clean up the event listener
    document.removeEventListener('paste', handlePaste)
})
</script>
