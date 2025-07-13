<template>
    <div class="max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Checklist Details</h1>
        <Link href="/checklists" method="get" as="button"
            class="px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors hover:cursor-pointer">
        Back
        </Link>
        <div class="bg-white p-6 rounded-lg shadow-md mt-6">
            <h2 class="text-xl font-semibold text-blue-900 mb-4">Trade Setup Details</h2>
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Asset</h3>
                    <p class="text-sm text-gray-600">{{ checklist.asset || 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Score</h3>
                    <p :class="['text-xl font-bold px-4 py-2 rounded-md inline-block',
                        checklist.score < 50 ? 'text-red-600 bg-red-100' :
                            checklist.score <= 80 ? 'text-yellow-600 bg-yellow-100' :
                                'text-emerald-600 bg-emerald-100']">
                        {{ checklist.score }}/100
                    </p>
                    <div v-if="checklist.score === 100" class="text-yellow-500 mt-2 font-bold text-lg text-center">★ All
                        Stars Aligned ★</div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Created</h3>
                    <p class="text-sm text-gray-600">{{ new Date(checklist.created_at).toLocaleDateString() }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Technicals</h3>
                    <p class="text-sm text-gray-600">Location: {{ checklist.technicals.location || 'Not selected' }}</p>
                    <p class="text-sm text-gray-600">Direction: {{ checklist.technicals.direction || 'Not selected' }}
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Fundamentals</h3>
                    <p class="text-sm text-gray-600">Valuation: {{ checklist.fundamentals.valuation || 'Not selected' }}
                    </p>
                    <p class="text-sm text-gray-600">Seasonal Confluence: {{ checklist.fundamentals.seasonalConfluence
                        || 'Not selected' }}</p>
                    <p class="text-sm text-gray-600">Non-Commercials: {{ checklist.fundamentals.nonCommercials || 'Not selected' }}</p>
                    <p class="text-sm text-gray-600">CoT Index: {{ checklist.fundamentals.cotIndex || 'Not selected' }}
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Zone Qualifiers ({{ checklist.zone_qualifiers.length
                    }})</h3>
                    <ul class="list-disc pl-5 text-sm text-gray-600">
                        <li v-for="qualifier in checklist.zone_qualifiers" :key="qualifier">
                            {{ qualifier }}
                        </li>
                        <li v-if="checklist.zone_qualifiers.length === 0" class="text-gray-500">None selected</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700">Notes</h3>
                    <p class="text-sm text-gray-600">{{ checklist.notes || 'No notes provided' }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    checklist: {
        type: Object,
        required: true
    }
});
</script>