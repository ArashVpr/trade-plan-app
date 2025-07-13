<template>
    <div class="max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Checklist History</h1>
        <button>
            <Link href="/"
                class="px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors">
                Create New Checklist
            </Link>
        </button>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-blue-900 mb-4">Saved Checklists</h2>
            <div v-if="checklists.length === 0" class="text-gray-600">
                No checklists found.
            </div>
            <div v-else class="space-y-4">
                <div v-for="checklist in checklists" :key="checklist.id"
                    class="flex justify-between items-center p-4 border-b border-gray-200">
                    <div>
                        <p class="text-sm font-medium text-gray-700">
                            Asset: {{ checklist.asset || 'N/A' }}
                            Length: {{ checklists.length }}
                        </p>
                        <p class="text-sm text-gray-600">
                            Score: <span :class="[
                                'font-bold',
                                checklist.score < 50 ? 'text-red-600' :
                                    checklist.score <= 80 ? 'text-yellow-600' :
                                        'text-emerald-600'
                            ]">{{ checklist.score }}/100</span>
                        </p>
                        <p class="text-sm text-gray-600">
                            Created: {{ new Date(checklist.created_at).toLocaleDateString() }}
                        </p>
                    </div>
                    <a :href="'/checklists/' + checklist.id"
                        class="px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors">
                        View Details
                    </a>
                </div>
            </div>
            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                <button v-for="link in checklists.links" :key="link.label" v-html="link.label" :disabled="!link.url"
                    :class="[
                        'px-4 py-2 mx-1 rounded-md',
                        link.active ? 'bg-blue-900 text-white' :
                            link.url ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' :
                                'bg-gray-100 text-gray-400 cursor-not-allowed'
                    ]" @click="link.url && $inertia.get(link.url)">
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

    defineProps({
        checklists: {
            type: Array,
            default: () => ([])
        }
    })
    
</script>
