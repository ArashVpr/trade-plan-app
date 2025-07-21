<template>
    <div class="max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Checklist History</h1>
        <Link href="/" method="get" as="button"
            class="px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors mb-4 text-center hover:cursor-pointer">
        Create New Checklist
        </Link>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-blue-900 mb-4">Saved Checklists</h2>
            <div v-if="checklists.data.length === 0" class="text-gray-600">
                No checklists found.
            </div>
            <div v-else class="space-y-4">
                <div v-for="checklist in checklists.data" :key="checklist.id"
                    class="flex justify-between items-center p-4 border-b border-gray-200">
                    <div>
                        <p class="text-sm font-medium text-gray-700">
                            Asset: {{ checklist.asset || 'N/A' }}
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
                    <Link :href="'/checklists/' + checklist.id" method="get" as="button"
                        class="px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors hover:cursor-pointer">
                    View Details
                    </Link>
                </div>
            </div>
            <!-- Pagination -->
            <div class="mt-6 flex justify-center" v-if="checklists.data.length > 0">
                <template v-for="link in checklists.links">
                    <Link v-if="link.url" :key="link.label + '-link'" :href="link.url" method="get" as="button" :class="[
                        'px-4 py-2 mx-1 rounded-md',
                        link.active ? 'bg-blue-900 text-white  hover:cursor-pointer' :
                            'bg-gray-200 text-gray-700 hover:bg-gray-300 hover:cursor-pointer'
                    ]" @click="$inertia.get(link.url)">
                    {{ link.label === '&laquo;' ? '«' : link.label === '&raquo;' ? '»' : link.label }}
                    </Link>
                    <button v-else :key="link.label"
                        class="px-4 py-2 mx-1 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed" disabled>
                        {{ link.label === '&laquo;' ? '«' : link.label === '&raquo;' ? '»' : link.label }} <!-- FIX THIS -->
                    </button>
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    checklists: {
        type: Object,
        default: () => ({})
    }
})

</script>
