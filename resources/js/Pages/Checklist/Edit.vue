<template>
    <div class="max-w-6xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Edit Checklist</h1>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center mb-6">
            <Button label="Back" icon="pi pi-arrow-left" severity="secondary"
                @click="router.get(route('checklists.show', checklist.id))" />
            <div class="flex gap-3">
                <Button label="Cancel" icon="pi pi-times" severity="secondary" outlined
                    @click="router.get(route('checklists.show', checklist.id))" />
                <Button label="Save Changes" icon="pi pi-save" severity="success" @click="submitForm"
                    :disabled="!canSubmit" />
            </div>
        </div>

        <!-- Main Content -->
        <Card class="w-full">
            <template #title>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="pi pi-edit text-blue-900"></i>
                        <span class="text-blue-900">Edit Trade Setup</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <Tag :value="`Score: ${form.score}/100`" :severity="getScoreSeverity(form.score)"
                            class="text-lg font-bold px-4 py-2" />
                        <div v-if="directionalBias?.hasEnoughData" class="flex items-center gap-2">
                            <Tag :value="directionalBias.biasDisplay" :severity="directionalBias.severity"
                                class="text-sm font-bold px-3 py-1" />
                        </div>
                        <div v-if="form.score === 100" class="text-yellow-500 font-bold text-sm">
                            ★ All Stars Aligned ★
                        </div>
                    </div>
                </div>
            </template>
            <template #content>
                <form @submit.prevent="submitForm" class="space-y-8">
                    <!-- Basic Information Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-gray-50 rounded-lg">
                        <div class="field">
                            <label class="flex items-center gap-2 text-sm font-medium mb-2">
                                <span>Symbol</span>
                                <i class="pi pi-lock text-gray-400 text-xs"></i>
                            </label>
                            <InputText :value="props.checklist.symbol" readonly
                                class="w-full bg-gray-50 text-gray-600 cursor-not-allowed border-gray-200" />
                        </div>
                        <div class="field">
                            <label class="block text-sm font-medium mb-2">Entry Date</label>
                            <DatePicker v-model="form.entry_date" dateFormat="yy-mm-dd" class="w-full" showIcon fluid
                                iconDisplay="input" :invalid="!!$errors.entry_date" />
                            <Message v-if="$errors.entry_date" severity="error" :closable="false">{{ $errors.entry_date
                            }}</Message>
                        </div>
                        <div class="field">
                            <label class="block text-sm font-medium mb-2">Created</label>
                            <InputText :value="new Date(checklist.created_at).toLocaleDateString()" readonly
                                class="w-full" />
                        </div>
                    </div>

                    <!-- Analysis Sections -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column: Technical & Fundamental Analysis -->
                        <div class="space-y-6">
                            <!-- Technical Analysis -->
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="pi pi-chart-line text-blue-900"></i>
                                    Technical Analysis
                                </h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Location</label>
                                        <Select v-model="form.technicals.location"
                                            :options="['Very Expensive', 'Expensive', 'EQ', 'Cheap', 'Very Cheap']"
                                            placeholder="Select Location" class="w-full"
                                            :invalid="!!$errors['technicals.location']" />
                                        <Message v-if="$errors['technicals.location']" severity="error"
                                            :closable="false">{{ $errors['technicals.location'] }}</Message>
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Direction</label>
                                        <Select v-model="form.technicals.direction"
                                            :options="['Correction', 'Impulsion']" placeholder="Select Direction"
                                            class="w-full" :invalid="!!$errors['technicals.direction']" />
                                        <Message v-if="$errors['technicals.direction']" severity="error"
                                            :closable="false">{{ $errors['technicals.direction'] }}</Message>
                                    </div>
                                </div>
                            </div>

                            <!-- Fundamental Analysis -->
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="pi pi-globe text-blue-900"></i>
                                    Fundamental Analysis
                                </h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Valuation</label>
                                        <Select v-model="form.fundamentals.valuation"
                                            :options="['Overvalued', 'Neutral', 'Undervalued']"
                                            placeholder="Select Valuation" class="w-full"
                                            :invalid="!!$errors['fundamentals.valuation']" />
                                        <Message v-if="$errors['fundamentals.valuation']" severity="error"
                                            :closable="false">{{ $errors['fundamentals.valuation'] }}</Message>
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Seasonality</label>
                                        <Select v-model="form.fundamentals.seasonalConfluence"
                                            :options="['Bullish', 'Neutral', 'Bearish']"
                                            placeholder="Select Seasonality" class="w-full"
                                            :invalid="!!$errors['fundamentals.seasonalConfluence']" />
                                        <Message v-if="$errors['fundamentals.seasonalConfluence']" severity="error"
                                            :closable="false">{{ $errors['fundamentals.seasonalConfluence'] }}</Message>
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Non-Commercials</label>
                                        <Select v-model="form.fundamentals.nonCommercials"
                                            :options="['Bullish Divergence', 'Neutral', 'Bearish Divergence']"
                                            placeholder="Select Non-Commercials" class="w-full"
                                            :invalid="!!$errors['fundamentals.nonCommercials']" />
                                        <Message v-if="$errors['fundamentals.nonCommercials']" severity="error"
                                            :closable="false">{{ $errors['fundamentals.nonCommercials'] }}</Message>
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">CoT Index</label>
                                        <Select v-model="form.fundamentals.cotIndex"
                                            :options="['Bullish', 'Neutral', 'Bearish']" placeholder="Select CoT Index"
                                            class="w-full" :invalid="!!$errors['fundamentals.cotIndex']" />
                                        <Message v-if="$errors['fundamentals.cotIndex']" severity="error"
                                            :closable="false">{{ $errors['fundamentals.cotIndex'] }}</Message>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Zone Qualifiers & Order Details -->
                        <div class="space-y-6">
                            <!-- Zone Qualifiers -->
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="pi pi-map-marker text-blue-900"></i>
                                    Zone Qualifiers ({{ form.zone_qualifiers.length }})
                                </h3>
                                <div class="grid grid-cols-1 gap-2">
                                    <div v-for="(qualifier, index) in zoneQualifiers" :key="index"
                                        class="flex items-center">
                                        <Checkbox v-model="form.zone_qualifiers" :value="qualifier"
                                            :inputId="`qualifier-${index}`" class="mr-3" />
                                        <label :for="`qualifier-${index}`" class="text-gray-700 text-sm">
                                            {{ qualifier }}
                                        </label>
                                    </div>
                                    <Message v-if="$errors.zone_qualifiers" severity="error" :closable="false">{{
                                        $errors.zone_qualifiers }}</Message>
                                </div>
                            </div>

                            <!-- Order Entry Details -->
                            <div ref="orderEntryRef"
                                class="p-4 border border-gray-200 rounded-lg transition-all duration-500"
                                :class="{ 'border-blue-500 bg-blue-50': shouldHighlightOrderEntry }">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="pi pi-money-bill text-blue-900"></i>
                                    Order Entry Details
                                    <Tag v-if="shouldHighlightOrderEntry" value="Focus" severity="info" class="ml-2" />
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Position Type</label>
                                        <Select v-model="form.position_type" :options="['Long', 'Short']"
                                            placeholder="Select Position Type" class="w-full"
                                            :invalid="!!$errors.position_type" />
                                        <Message v-if="$errors.position_type" severity="error" :closable="false">{{
                                            $errors.position_type }}</Message>
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Trade Status</label>
                                        <Select v-model="form.trade_status" :options="[
                                            { label: 'Order Pending', value: 'pending' },
                                            { label: 'Position Open', value: 'active' },
                                            { label: 'Win', value: 'win' },
                                            { label: 'Loss', value: 'loss' },
                                            { label: 'Breakeven', value: 'breakeven' },
                                            { label: 'Cancelled', value: 'cancelled' }
                                        ]" option-label="label" option-value="value" placeholder="Select Status"
                                            class="w-full" :invalid="!!$errors.trade_status" />
                                        <Message v-if="$errors.trade_status" severity="error" :closable="false">{{
                                            $errors.trade_status }}</Message>
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Entry Price</label>
                                        <InputText v-model="form.entry_price" type="number" step="0.0001"
                                            placeholder="0.0000" class="w-full" :invalid="!!$errors.entry_price" />
                                        <Message v-if="$errors.entry_price" severity="error" :closable="false">{{
                                            $errors.entry_price }}</Message>
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Stop Loss</label>
                                        <InputText v-model="form.stop_price" type="number" step="0.0001"
                                            placeholder="0.0000" class="w-full" :invalid="!!$errors.stop_price" />
                                        <Message v-if="$errors.stop_price" severity="error" :closable="false">{{
                                            $errors.stop_price }}</Message>
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">Take Profit</label>
                                        <InputText v-model="form.target_price" type="number" step="0.0001"
                                            placeholder="0.0000" class="w-full" :invalid="!!$errors.target_price" />
                                        <Message v-if="$errors.target_price" severity="error" :closable="false">{{
                                            $errors.target_price }}</Message>
                                    </div>
                                    <div class="field">
                                        <label class="block text-sm font-medium mb-1">R:R Ratio</label>
                                        <InputText v-model="form.rrr" type="number" step="0.01" placeholder="1.50"
                                            class="w-full" :invalid="!!$errors.rrr" />
                                        <Message v-if="$errors.rrr" severity="error" :closable="false">{{ $errors.rrr }}
                                        </Message>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes & Screenshot Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Notes -->
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="pi pi-file-edit text-blue-900"></i>
                                Trading Notes
                            </h3>
                            <div class="field">
                                <Textarea v-model="form.notes" rows="6" class="w-full"
                                    placeholder="Document your analysis, reasoning, and any additional context for this trade..."
                                    :invalid="!!$errors.notes" />
                                <Message v-if="$errors.notes" severity="error" :closable="false">{{ $errors.notes }}
                                </Message>
                            </div>
                        </div>

                        <!-- Screenshot Upload -->
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="pi pi-image text-blue-900"></i>
                                Chart Screenshots (Max 5)
                            </h3>

                            <!-- Existing Screenshots -->
                            <div v-if="existingImages.length > 0" class="mb-4">
                                <label class="block text-sm font-medium mb-2">Current Images</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div v-for="(image, index) in existingImages" :key="image"
                                        class="relative border border-gray-200 rounded-lg p-2">
                                        <img :src="`/storage/${image}`" :alt="`Screenshot ${index + 1}`"
                                            class="w-full h-32 object-cover rounded cursor-pointer"
                                            @click="viewImage(`/storage/${image}`)" />
                                        <Button icon="pi pi-times" @click="removeExistingImage(index)" rounded text
                                            severity="danger" class="absolute top-1 right-1" size="small" />
                                    </div>
                                </div>
                            </div>

                            <!-- New Screenshots Upload -->
                            <div class="field">
                                <label v-if="existingImages.length > 0" class="block text-sm font-medium mb-2">Add New
                                    Images</label>
                                <FileUpload ref="fileupload" name="screenshots[]" :multiple="true" accept="image/*"
                                    :maxFileSize="5000000" :fileLimit="Math.max(0, 5 - existingImages.length)"
                                    customUpload @select="onFilesSelect" @remove="onFileRemove"
                                    :disabled="existingImages.length >= 5" class="w-full">
                                    <template #header="{ chooseCallback, clearCallback, files }">
                                        <div class="flex flex-wrap justify-between items-center gap-4">
                                            <div class="flex gap-2">
                                                <Button @click="chooseCallback()" icon="pi pi-images" label="Choose"
                                                    outlined severity="secondary" size="small"
                                                    :disabled="existingImages.length >= 5" />
                                                <Button @click="clearCallback()" icon="pi pi-times" label="Clear"
                                                    outlined severity="danger" size="small"
                                                    :disabled="!files || files.length === 0" />
                                            </div>
                                            <span class="text-sm text-gray-600">{{ existingImages.length +
                                                (files?.length || 0) }}/5 total</span>
                                        </div>
                                    </template>
                                    <template #content="{ files, removeFileCallback }">
                                        <div v-if="files.length > 0" class="pt-4">
                                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                                <div v-for="(file, index) of files"
                                                    :key="file.name + file.type + file.size"
                                                    class="relative border border-gray-200 rounded-lg p-2">
                                                    <img role="presentation" :alt="file.name" :src="file.objectURL"
                                                        class="w-full h-32 object-cover rounded" />
                                                    <div class="mt-2 text-xs text-gray-600 truncate">{{ file.name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ formatSize(file.size) }}</div>
                                                    <Button icon="pi pi-times" @click="removeFileCallback(index)"
                                                        rounded text severity="danger" class="absolute top-1 right-1"
                                                        size="small" />
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template #empty>
                                        <div class="flex items-center justify-center flex-col py-8">
                                            <i class="pi pi-image text-gray-400 text-4xl mb-3"></i>
                                            <p class="text-gray-600">Drag and drop images here or click Choose</p>
                                            <p class="text-xs text-gray-500 mt-1">Max {{ Math.max(0, 5 -
                                                existingImages.length) }} more images, 5MB each</p>
                                        </div>
                                    </template>
                                </FileUpload>
                                <Message v-if="$errors.screenshots" severity="error" :closable="false">{{
                                    $errors.screenshots }}</Message>
                            </div>
                        </div>
                    </div>
                </form>
            </template>
        </Card>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, watch, onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useToast } from 'primevue/usetoast'
import { useDirectionalBias } from '@/composables/useDirectionalBias'

const toast = useToast()
const orderEntryRef = ref(null)

const props = defineProps({
    checklist: Object,
    settings: Object,
    tradeEntry: Object,
    errors: Object,
    instruments: {
        type: Array,
        default: () => []
    }
})

// Computed property to access errors in template
const $errors = computed(() => props.errors || {})

// Computed property for symbol options
const symbolOptions = computed(() => {
    if (props.instruments && props.instruments.length > 0) {
        return props.instruments.map(inst => inst.symbol)
    }
    // Fallback to original hardcoded list
    return ['EURUSD', 'GBPUSD', 'USDJPY', 'AUDUSD', 'USDCAD', 'NZDUSD', 'EURGBP', 'EURJPY', 'GBPJPY', 'AUDJPY']
})

// Check if we should focus on order entry section
const shouldHighlightOrderEntry = ref(false)

// Handle focus on order entry when coming from "Add Trade Details"
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search)
    const focusParam = urlParams.get('focus')

    if (focusParam === 'order-entry') {
        shouldHighlightOrderEntry.value = true

        // Scroll to order entry section after a brief delay
        setTimeout(() => {
            if (orderEntryRef.value) {
                orderEntryRef.value.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                })
            }
        }, 300)

        // Remove highlight after 3 seconds
        setTimeout(() => {
            shouldHighlightOrderEntry.value = false
        }, 3000)
    }
})

// Helper functions for PrimeVue components
const getScoreSeverity = (score) => {
    if (score < 50) return 'danger'
    if (score <= 80) return 'warning'
    return 'success'
}

// Date formatting helper
const formatDate = (date) => {
    if (!date) return ''
    if (typeof date === 'string') return date
    if (date instanceof Date) {
        return date.toISOString().split('T')[0] // Convert to YYYY-MM-DD format
    }
    return date
}

// File upload handler
const existingImages = ref([...(props.tradeEntry?.screenshot_paths || [])])

const onFilesSelect = (event) => {
    // Validate total doesn't exceed 5
    const totalImages = existingImages.value.length + event.files.length
    if (totalImages > 5) {
        toast.add({
            severity: 'warn',
            summary: 'Upload Limit',
            detail: `Cannot add ${event.files.length} images. You can only have 5 total. Currently have ${existingImages.value.length} existing image(s).`,
            life: 3000
        })
        // Clear the selected files
        event.files = []
        return
    }
    form.screenshots = event.files
}

const onFileRemove = (event) => {
    form.screenshots = event.files
}

const removeExistingImage = (index) => {
    existingImages.value.splice(index, 1)
    form.existing_screenshots = existingImages.value
}

const viewImage = (src) => {
    window.open(src, '_blank')
}

const formatSize = (bytes) => {
    if (bytes === 0) return '0 B'
    const k = 1024
    const sizes = ['B', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

// Form submission handler
const submitForm = (event) => {
    // Prevent default form submission
    event.preventDefault()

    // Format the entry_date before submission
    if (form.entry_date) {
        form.entry_date = formatDate(form.entry_date)
    }

    // Create FormData manually to properly handle files + nested objects
    const formData = new FormData()

    // Add all form fields
    formData.append('zone_qualifiers', JSON.stringify(form.zone_qualifiers))
    formData.append('technicals', JSON.stringify(form.technicals))
    formData.append('fundamentals', JSON.stringify(form.fundamentals))
    formData.append('score', form.score)
    formData.append('notes', form.notes)
    formData.append('entry_date', form.entry_date)
    formData.append('position_type', form.position_type)
    formData.append('entry_price', form.entry_price)
    formData.append('stop_price', form.stop_price)
    formData.append('target_price', form.target_price)
    formData.append('trade_status', form.trade_status)
    formData.append('rrr', form.rrr)
    formData.append('existing_screenshots', JSON.stringify(existingImages.value))

    // Add new screenshot files
    if (form.screenshots && form.screenshots.length > 0) {
        form.screenshots.forEach((file, index) => {
            formData.append(`screenshots[${index}]`, file)
        })
    }

    // Add method spoofing for PUT request
    formData.append('_method', 'PUT')

    // Use router.post with FormData directly
    router.post(route('checklists.update', props.checklist.id), formData, {
        onSuccess: () => {
            // Backend already sends success message via redirect
            // Clear the new screenshots array after successful submission
            form.screenshots = [];
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Please check the form for errors',
                life: 3000
            });
        }
    })
}

const zoneQualifiers = [
    'Fresh', 'Original', 'Flip', 'LOL', 'Minimum 1:2 Profit Margin', 'Big Brother Coverage'
]

const form = useForm({
    zone_qualifiers: [...props.checklist.zone_qualifiers],
    technicals: { ...props.checklist.technicals },
    fundamentals: { ...props.checklist.fundamentals },
    score: props.checklist.score,
    notes: props.tradeEntry?.notes || '',
    entry_date: props.tradeEntry?.entry_date || '',
    position_type: props.tradeEntry?.position_type || '',
    entry_price: props.tradeEntry?.entry_price || '',
    stop_price: props.tradeEntry?.stop_price || '',
    target_price: props.tradeEntry?.target_price || '',
    trade_status: props.tradeEntry?.trade_status || '',
    rrr: props.tradeEntry?.rrr || '',
    screenshots: [],
    existing_screenshots: [...(props.tradeEntry?.screenshot_paths || [])]
})

const canSubmit = computed(() => {
    // First check if core checklist fields are valid
    const isValid = form.technicals.location &&
        form.technicals.direction &&
        form.fundamentals.valuation &&
        form.fundamentals.seasonalConfluence &&
        form.fundamentals.nonCommercials &&
        form.fundamentals.cotIndex &&
        form.zone_qualifiers.length > 0

    if (!isValid) return false

    // Check total image count (existing + new) doesn't exceed 5
    const totalImages = existingImages.value.length + (form.screenshots?.length || 0)
    if (totalImages > 5) return false

    // Then check if there are any changes
    const hasChanges = hasFormChanges.value

    return hasChanges
})

// Detect if form has changes compared to original data
const hasFormChanges = computed(() => {
    // Compare checklist fields
    const originalChecklist = {
        zone_qualifiers: props.checklist.zone_qualifiers,
        technicals: props.checklist.technicals,
        fundamentals: props.checklist.fundamentals,
    }

    const currentChecklist = {
        zone_qualifiers: form.zone_qualifiers,
        technicals: form.technicals,
        fundamentals: form.fundamentals,
    }

    // Compare trade entry fields
    const originalTradeEntry = {
        notes: props.tradeEntry?.notes || '',
        entry_date: props.tradeEntry?.entry_date || '',
        position_type: props.tradeEntry?.position_type || '',
        entry_price: props.tradeEntry?.entry_price || '',
        stop_price: props.tradeEntry?.stop_price || '',
        target_price: props.tradeEntry?.target_price || '',
        trade_status: props.tradeEntry?.trade_status || '',
        rrr: props.tradeEntry?.rrr || '',
    }

    const currentTradeEntry = {
        notes: form.notes,
        entry_date: form.entry_date,
        position_type: form.position_type,
        entry_price: form.entry_price,
        stop_price: form.stop_price,
        target_price: form.target_price,
        trade_status: form.trade_status,
        rrr: form.rrr,
    }

    // Deep comparison function
    const isEqual = (obj1, obj2) => {
        return JSON.stringify(obj1) === JSON.stringify(obj2)
    }

    // Check for screenshot changes
    const hasScreenshotChanges = form.screenshots.length > 0 ||
        !isEqual(existingImages.value, props.tradeEntry?.screenshot_paths || [])

    return !isEqual(originalChecklist, currentChecklist) ||
        !isEqual(originalTradeEntry, currentTradeEntry) ||
        hasScreenshotChanges
})

// Calculate directional bias in real-time
const { directionalBias } = useDirectionalBias(
    computed(() => form.technicals),
    computed(() => form.fundamentals),
    computed(() => props.settings)
)

const evaluationScore = () => {
    // 1. Raw weighted score
    let raw = 0;
    const zoneKeys = ['fresh', 'original', 'flip', 'lol', 'min_profit_margin', 'big_brother'];
    zoneKeys.forEach((key, idx) => {
        if (form.zone_qualifiers.includes(zoneQualifiers[idx])) {
            raw += Number(props.settings[`zone_${key}_weight`] || 0);
        }
    });
    // Technicals: Location
    if (['Very Cheap', 'Very Expensive'].includes(form.technicals.location)) {
        raw += Number(props.settings.technical_very_exp_chp_weight || 0);
    } else if (['Cheap', 'Expensive'].includes(form.technicals.location)) {
        raw += Number(props.settings.technical_exp_chp_weight || 0);
    }
    // Technicals: Direction
    if (form.technicals.direction === 'Impulsion') {
        raw += Number(props.settings.technical_direction_impulsive_weight || 0);
    } else if (form.technicals.direction === 'Correction') {
        raw += Number(props.settings.technical_direction_correction_weight || 0);
    }
    // Fundamentals: Valuation
    if (['Undervalued', 'Overvalued'].includes(form.fundamentals.valuation)) {
        raw += Number(props.settings.fundamental_valuation_weight || 0);
    }
    // Fundamentals: Seasonal
    if (['Bullish', 'Bearish'].includes(form.fundamentals.seasonalConfluence)) {
        raw += Number(props.settings.fundamental_seasonal_weight || 0);
    }
    // Fundamentals: Non-Commercial
    if (['Bullish Divergence', 'Bearish Divergence'].includes(form.fundamentals.nonCommercials)) {
        raw += Number(props.settings.fundamental_noncommercial_divergence_weight || 0);
    }
    // Fundamentals: CoT Index
    if (['Bullish', 'Bearish'].includes(form.fundamentals.cotIndex)) {
        raw += Number(props.settings.fundamental_cot_index_weight || 0);
    }
    // 2. Max possible score based on one selection per category
    const zoneMax = zoneKeys.reduce((sum, key) => sum + Number(props.settings[`zone_${key}_weight`] || 0), 0);
    const locHigh = Math.max(
        Number(props.settings.technical_very_exp_chp_weight || 0),
        Number(props.settings.technical_exp_chp_weight || 0)
    );
    const dirHigh = Math.max(
        Number(props.settings.technical_direction_impulsive_weight || 0),
        Number(props.settings.technical_direction_correction_weight || 0)
    );
    const fundMax =
        Number(props.settings.fundamental_valuation_weight || 0) +
        Number(props.settings.fundamental_seasonal_weight || 0) +
        Number(props.settings.fundamental_noncommercial_divergence_weight || 0) +
        Number(props.settings.fundamental_cot_index_weight || 0);
    const max = zoneMax + locHigh + dirHigh + fundMax;
    // 3. Normalize to 0-100
    form.score = max > 0 ? Math.round((raw / max) * 100) : 0;
};

// Watch for changes in form fields and update score
watch(() => form, evaluationScore, { deep: true })
</script>
