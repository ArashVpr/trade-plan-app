<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-blue-900 mb-2">Trading Dashboard</h1>
                <p class="text-gray-600">Overview of your trading activity and performance</p>
            </div>

            <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <Card class="text-center">
                    <template #content>
                        <div class="p-4">
                            <i class="pi pi-list text-4xl text-blue-600 mb-3"></i>
                            <h3 class="text-2xl font-bold text-gray-900">{{ stats.overview.total_checklists }}</h3>
                            <p class="text-gray-600">Total Checklists</p>
                        </div>
                    </template>
                </Card>

                <Card class="text-center">
                    <template #content>
                        <div class="p-4">
                            <i class="pi pi-calendar text-4xl text-green-600 mb-3"></i>
                            <h3 class="text-2xl font-bold text-gray-900">{{ stats.overview.weekly_checklists }}</h3>
                            <p class="text-gray-600">This Week</p>
                        </div>
                    </template>
                </Card>

                <Card class="text-center">
                    <template #content>
                        <div class="p-4">
                            <i class="pi pi-chart-bar text-4xl text-purple-600 mb-3"></i>
                            <h3 class="text-2xl font-bold text-gray-900">{{ stats.overview.monthly_checklists }}</h3>
                            <p class="text-gray-600">This Month</p>
                        </div>
                    </template>
                </Card>

                <Card class="text-center">
                    <template #content>
                        <div class="p-4">
                            <i class="pi pi-star text-4xl text-yellow-600 mb-3"></i>
                            <h3 class="text-2xl font-bold text-gray-900">{{ stats.overview.avg_score }}</h3>
                            <p class="text-gray-600">Average Score</p>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Weekly Trend Chart -->
                <Card>
                    <template #header>
                        <div class="p-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Weekly Activity Trend</h3>
                        </div>
                    </template>
                    <template #content>
                        <div class="p-4">
                            <Chart type="line" :data="weeklyTrendData" :options="chartOptions" class="w-full h-64" />
                        </div>
                    </template>
                </Card>

                <!-- Score Distribution Chart -->
                <Card>
                    <template #header>
                        <div class="p-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Score Distribution</h3>
                        </div>
                    </template>
                    <template #content>
                        <div class="p-4">
                            <Chart type="doughnut" :data="scoreDistributionData" :options="doughnutOptions"
                                class="w-full h-64" />
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Data Tables Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Position Performance -->
                <Card>
                    <template #header>
                        <div class="p-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Performance by Position Type</h3>
                        </div>
                    </template>
                    <template #content>
                        <div class="p-4">
                            <DataTable :value="stats.position_type_performance" class="p-datatable-sm">
                                <Column field="position_type" header="Position Type">
                                    <template #body="slotProps">
                                        <Tag :severity="slotProps.data.position_type === 'Long' ? 'success' : 'danger'"
                                            :value="slotProps.data.position_type" />
                                    </template>
                                </Column>
                                <Column field="count" header="Count" />
                                <Column field="avg_score" header="Avg Score">
                                    <template #body="slotProps">
                                        <span class="font-semibold">{{ slotProps.data.avg_score }}/100</span>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </template>
                </Card>

                <!-- Top Symbols -->
                <Card>
                    <template #header>
                        <div class="p-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Top Performing Symbols</h3>
                        </div>
                    </template>
                    <template #content>
                        <div class="p-4">
                            <DataTable :value="stats.top_symbols" class="p-datatable-sm">
                                <Column field="symbol" header="Symbol">
                                    <template #body="slotProps">
                                        <span class="font-mono font-bold text-blue-900">{{ slotProps.data.symbol
                                        }}</span>
                                    </template>
                                </Column>
                                <Column field="count" header="Trades" />
                                <Column field="avg_score" header="Avg Score">
                                    <template #body="slotProps">
                                        <ProgressBar :value="slotProps.data.avg_score" :showValue="false"
                                            class="mb-1" />
                                        <small class="text-gray-600">{{ slotProps.data.avg_score }}/100</small>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Recent Activity -->
            <Card>
                <template #header>
                    <div class="p-4 border-b flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                        <Button label="View All" size="small" text @click="router.get(route('checklists.index'))" />
                    </div>
                </template>
                <template #content>
                    <div class="p-4">
                        <DataTable :value="stats.recent_activity" class="p-datatable-sm">
                            <Column field="symbol" header="Symbol">
                                <template #body="slotProps">
                                    <span class="font-mono font-bold">{{ slotProps.data.symbol }}</span>
                                </template>
                            </Column>
                            <Column field="position_type" header="Position">
                                <template #body="slotProps">
                                    <Tag :severity="slotProps.data.position_type === 'Long' ? 'success' : slotProps.data.position_type === 'Short' ? 'danger' : 'secondary'"
                                        :value="slotProps.data.position_type" />
                                </template>
                            </Column>
                            <Column field="overall_score" header="Score">
                                <template #body="slotProps">
                                    <div class="flex items-center gap-2">
                                        <ProgressBar :value="slotProps.data.overall_score" :showValue="false"
                                            class="w-16" />
                                        <span class="text-sm font-semibold">{{ slotProps.data.overall_score
                                        }}/100</span>
                                    </div>
                                </template>
                            </Column>
                            <Column field="created_at" header="Created">
                                <template #body="slotProps">
                                    <small class="text-gray-600">{{ slotProps.data.created_at }}</small>
                                </template>
                            </Column>
                            <Column field="has_trade_entry" header="Status">
                                <template #body="slotProps">
                                    <Tag :severity="slotProps.data.status_severity"
                                        :value="slotProps.data.trade_status" />
                                </template>
                            </Column>
                            <Column header="Actions">
                                <template #body="slotProps">
                                    <Button icon="pi pi-eye" size="small" text
                                        @click="router.get(route('checklists.show', slotProps.data.id))"
                                        v-tooltip="'View Details'" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </template>
            </Card>

            <!-- Quick Actions -->
            <div class="mt-8 text-center">
                <div class="inline-flex gap-4">
                    <Button label="New Checklist" icon="pi pi-plus" size="large" @click="router.get(route('home'))" />
                    <Button label="View History" icon="pi pi-history" severity="secondary" size="large"
                        @click="router.get(route('checklists.index'))" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    stats: Object
})

// Chart data for weekly trend
const weeklyTrendData = computed(() => ({
    labels: props.stats.weekly_trend.map(item => item.week),
    datasets: [
        {
            label: 'Checklists Created',
            data: props.stats.weekly_trend.map(item => item.count),
            fill: false,
            borderColor: '#3B82F6',
            backgroundColor: '#3B82F6',
            tension: 0.4,
            pointBackgroundColor: '#3B82F6',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2
        }
    ]
}))

// Chart data for score distribution
const scoreDistributionData = computed(() => {
    // Define color mapping for each score range    
    const colorMap = {
        'Excellent (80-100)': '#10B981', // Green
        'Good (60-79)': '#3B82F6',       // Blue
        'Average (40-59)': '#F59E0B',    // Yellow
        'Poor (0-39)': '#EF4444'         // Red
    }

    // Map colors to match the actual data order
    const colors = props.stats.score_distribution.map(item =>
        colorMap[item.range] || '#6B7280' // Default gray for unknown ranges
    )

    return {
        labels: props.stats.score_distribution.map(item => item.range),
        datasets: [
            {
                data: props.stats.score_distribution.map(item => item.count),
                backgroundColor: colors,
                borderWidth: 0
            }
        ]
    }
})

// Chart options
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1
            }
        }
    }
}

const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
        }
    }
}
</script>

<style scoped>
/* Custom styling for dashboard cards */
:deep(.p-card-content) {
    padding: 0;
}
</style>