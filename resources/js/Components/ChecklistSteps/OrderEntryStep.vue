<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-blue-900 flex items-center gap-2">
                <i class="pi pi-money-bill text-blue-900"></i>
                Order Entry (Optional)
            </h2>
            <Chip :label="`Instrument: ${asset || '—'}`" />
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
                <Dropdown :model-value="modelValue.positionType"
                    @update:model-value="updateData('positionType', $event)" :options="positionOptions"
                    placeholder="Select Position Type" class="w-full" />
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
            <label class="block text-sm font-medium mb-2">Outcome</label>
            <Dropdown :model-value="modelValue.outcome" @update:model-value="updateData('outcome', $event)"
                :options="outcomeOptions" placeholder="Select Outcome" class="w-full" />
        </div>

        <div class="field">
            <label class="block text-sm font-medium mb-2">Chart Screenshot</label>
            <FileUpload mode="basic" name="screenshot" :auto="false" accept="image/*" :maxFileSize="10000000"
                @select="onFileSelect" chooseLabel="Choose File" class="w-full" />
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
    asset: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue', 'progress-updated'])

const positionOptions = ['Long', 'Short']
const outcomeOptions = ['win', 'loss', 'breakeven']

const updateData = (key, value) => {
    const updatedData = { ...props.modelValue, [key]: value }
    emit('update:modelValue', updatedData)
    emit('progress-updated')
}

const onFileSelect = (event) => {
    updateData('screenshot', event.files[0])
}
</script>
