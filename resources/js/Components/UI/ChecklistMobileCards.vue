<template>
    <div class="space-y-4">
        <div
            v-for="item in items"
            :key="item.id"
            class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow"
        >
            <!-- Header Row: Symbol & Score -->
            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="font-bold text-lg text-blue-900 dark:text-blue-300">
                        {{ item.symbol || 'N/A' }}
                    </span>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ formatDate(item.created_at) }}
                    </div>
                </div>
                <Tag
                    :value="`${item.score}/100`"
                    :severity="getScoreSeverity(item.score)"
                    rounded
                    class="text-sm"
                />
            </div>

            <!-- Status Row -->
            <div class="flex flex-wrap gap-2 mb-3">
                <!-- Directional Bias -->
                <div v-if="getDirectionalBias(item).hasEnoughData">
                    <Tag
                        :value="getDirectionalBias(item).biasDisplay"
                        :severity="getDirectionalBias(item).severity"
                        class="text-xs font-bold"
                    />
                </div>
                <span v-else class="text-slate-400 dark:text-slate-500 text-xs italic">No Signals</span>

                <!-- Position Type -->
                <Tag
                    v-if="item.trade_entry?.position_type"
                    :value="item.trade_entry.position_type"
                    :severity="item.trade_entry.position_type === 'Long' ? 'success' : 'danger'"
                    class="text-xs"
                />

                <!-- Trade Status -->
                <Tag
                    :value="getTradeStatus(item)"
                    :severity="getTradeStatusSeverity(item)"
                    class="text-xs"
                />
            </div>

            <!-- Details Row -->
            <div class="grid grid-cols-2 gap-2 text-sm mb-3 bg-slate-50 dark:bg-slate-700/50 p-2 rounded">
                <div>
                    <span class="text-slate-500 dark:text-slate-400 text-xs">Entry:</span>
                    <span class="ml-1 font-medium text-slate-700 dark:text-slate-200 block">
                        {{ item.trade_entry?.entry_date ? formatDate(item.trade_entry.entry_date) : '-' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 dark:text-slate-400 text-xs">R:R:</span>
                    <span class="ml-1 font-mono font-semibold text-slate-600 dark:text-slate-300 block">
                        {{ item.trade_entry?.rrr ? Number(item.trade_entry.rrr).toFixed(2) : '-' }}
                    </span>
                </div>
            </div>

            <!-- Actions Row -->
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-700">
                <Link :href="route('checklists.show', item.id)">
                    <Button icon="pi pi-eye" size="small" severity="secondary" text rounded aria-label="View" />
                </Link>
                <Link :href="route('checklists.edit', item.id)">
                    <Button icon="pi pi-pencil" size="small" severity="info" text rounded aria-label="Edit" />
                </Link>
                <Button
                    icon="pi pi-trash"
                    size="small"
                    severity="danger"
                    text
                    rounded
                    aria-label="Delete"
                    @click="$emit('delete', item.id)"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

defineProps({
    items: {
        type: Array,
        required: true
    },
    getDirectionalBias: {
        type: Function,
        required: true
    },
    getTradeStatus: {
        type: Function,
        required: true
    },
    getTradeStatusSeverity: {
        type: Function,
        required: true
    }
})

defineEmits(['delete'])

const getScoreSeverity = (score) => {
    if (score < 50) return 'danger'
    if (score <= 80) return 'warn'
    return 'success'
}

const formatDate = (dateString) => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString()
}
</script>
