<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-blue-900">
                Tracking Analysis for {{ tracker.symbol }}
            </h3>
            <div class="text-sm text-gray-600">
                Completion: {{ tracker.completion_percentage }}%
            </div>
        </div>

        <!-- Zone Qualifiers Section -->
        <Card>
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="pi pi-map-marker text-blue-900"></i>
                    <span class="text-blue-900">Zone Qualifiers</span>
                </div>
            </template>
            <template #content>
                <div class="space-y-4">
                    <div class="flex flex-wrap gap-2">
                        <Chip v-for="qualifier in selectedZoneQualifiers" :key="qualifier" :label="qualifier" removable
                            @remove="removeZoneQualifier(qualifier)" />
                    </div>

                    <div class="flex gap-2">
                        <Select v-model="newZoneQualifier" :options="availableZoneQualifiers"
                            placeholder="Add zone qualifier" class="flex-1" />
                        <Button icon="pi pi-plus" @click="addZoneQualifier" :disabled="!newZoneQualifier" />
                    </div>
                </div>
            </template>
        </Card>

        <!-- Technical Analysis Section -->
        <Card>
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="pi pi-chart-line text-blue-900"></i>
                    <span class="text-blue-900">Technical Analysis</span>
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="field">
                        <label class="block text-sm font-medium mb-2">Price Location</label>
                        <Select v-model="technicals.location" :options="locationOptions"
                            placeholder="Select price location" class="w-full" />
                    </div>

                    <div class="field">
                        <label class="block text-sm font-medium mb-2">Market Direction</label>
                        <Select v-model="technicals.direction" :options="directionOptions"
                            placeholder="Select market direction" class="w-full" />
                    </div>
                </div>
            </template>
        </Card>

        <!-- Fundamental Analysis Section -->
        <Card>
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="pi pi-globe text-blue-900"></i>
                    <span class="text-blue-900">Fundamental Analysis</span>
                </div>
            </template>
            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="field">
                        <label class="block text-sm font-medium mb-2">Valuation</label>
                        <Select v-model="fundamentals.valuation" :options="valuationOptions"
                            placeholder="Select valuation" class="w-full" />
                    </div>

                    <div class="field">
                        <label class="block text-sm font-medium mb-2">Seasonal Confluence</label>
                        <Select v-model="fundamentals.seasonalConfluence" :options="seasonalOptions"
                            placeholder="Select seasonal confluence" class="w-full" />
                    </div>
                </div>
            </template>
        </Card>

        <!-- Actions -->
        <div class="flex justify-end gap-2">
            <Button label="Cancel" icon="pi pi-times" @click="$emit('cancelled')" outlined />
            <Button label="Update Analysis" icon="pi pi-check" @click="updateTracker" />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
    tracker: Object,
})

const emit = defineEmits(['updated', 'cancelled'])

// Reactive data
const selectedZoneQualifiers = ref([])
const newZoneQualifier = ref('')
const technicals = ref({
    location: '',
    direction: ''
})
const fundamentals = ref({
    valuation: '',
    seasonalConfluence: ''
})

// Options for dropdowns
const zoneQualifierOptions = [
    'Fresh', 'Original', 'Flip', 'LOL', 'Minimum 1:2 Profit Margin', 'Big Brother Coverage'
]

const locationOptions = [
    'Very Expensive', 'Expensive', 'Fair Value', 'Cheap', 'Very Cheap'
]

const directionOptions = [
    'Impulsive', 'Corrective'
]

const valuationOptions = [
    'Overvalued', 'Neutral', 'Undervalued'
]

const seasonalOptions = [
    'Bullish', 'Neutral', 'Bearish'
]

// Computed properties
const availableZoneQualifiers = computed(() => {
    return zoneQualifierOptions.filter(q => !selectedZoneQualifiers.value.includes(q))
})

// Methods
const addZoneQualifier = () => {
    if (newZoneQualifier.value && !selectedZoneQualifiers.value.includes(newZoneQualifier.value)) {
        selectedZoneQualifiers.value.push(newZoneQualifier.value)
        newZoneQualifier.value = ''
    }
}

const removeZoneQualifier = (qualifier) => {
    const index = selectedZoneQualifiers.value.indexOf(qualifier)
    if (index > -1) {
        selectedZoneQualifiers.value.splice(index, 1)
    }
}

const updateTracker = async () => {
    console.log('=== UPDATE TRACKER DEBUG ===')
    console.log('Tracker object:', props.tracker)
    console.log('Tracker ID:', props.tracker?.id)

    // Validate that we have a valid tracker with ID
    if (!props.tracker || !props.tracker.id) {
        console.error('ERROR: No tracker ID found!')
        console.error('Tracker object:', props.tracker)
        alert('Error: Invalid tracker data. Please refresh the page and try again.')
        return
    }

    const trackedMetrics = {
        zone_qualifiers: selectedZoneQualifiers.value,
        technicals: {
            location: technicals.value.location,
            direction: technicals.value.direction
        },
        fundamentals: {
            valuation: fundamentals.value.valuation,
            seasonalConfluence: fundamentals.value.seasonalConfluence
        }
    }

    try {
        const updateRoute = route('analysis-tracker.update', props.tracker.id)
        console.log('Generated route:', updateRoute)
        console.log('Data to send:', { tracked_metrics: trackedMetrics })
        console.log('=== END DEBUG ===')

        router.put(updateRoute, {
            tracked_metrics: trackedMetrics
        }, {
            onSuccess: () => {
                console.log('Update successful')
                emit('updated')
            },
            onError: (errors) => {
                console.error('Error updating tracker:', errors)
                alert('Error updating tracker: ' + JSON.stringify(errors))
            }
        })
    } catch (error) {
        console.error('Error in updateTracker:', error)
        alert('Error: ' + error.message)
    }
}

// Initialize form data
onMounted(() => {
    console.log('=== FORM MOUNTED DEBUG ===')
    console.log('Props tracker:', props.tracker)
    console.log('Tracker ID:', props.tracker?.id)
    console.log('Tracker symbol:', props.tracker?.symbol)
    console.log('=== END MOUNT DEBUG ===')

    const metrics = props.tracker.tracked_metrics || {}

    selectedZoneQualifiers.value = metrics.zone_qualifiers || []

    technicals.value = {
        location: metrics.technicals?.location || '',
        direction: metrics.technicals?.direction || ''
    }

    fundamentals.value = {
        valuation: metrics.fundamentals?.valuation || '',
        seasonalConfluence: metrics.fundamentals?.seasonalConfluence || ''
    }
})
</script>