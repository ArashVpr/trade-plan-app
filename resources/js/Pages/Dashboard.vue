<template>
    <AppLayout>
        <!-- Toast for notifications -->
        <Toast />

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
            </div>

            <!-- Data Tables Row -->
            <div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-8">
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

            <!-- Pattern Analysis -->
            <div class="mb-8">
                <Card>
                    <template #header>
                        <div class="p-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">🔍 Winning Patterns Analysis</h3>
                            <p class="text-sm text-gray-600 mt-1">Discover what consistently makes your trades
                                successful</p>
                        </div>
                    </template>
                    <template #content>
                        <div class="p-4">
                            <div v-if="stats.pattern_analysis.total_wins === 0" class="text-center py-8 text-gray-500">
                                <i class="pi pi-chart-line text-3xl mb-3"></i>
                                <p>No winning trades yet to analyze patterns</p>
                            </div>

                            <div v-else class="space-y-6">
                                <!-- Recommendations -->
                                <div v-if="stats.pattern_analysis.recommendations.length > 0">
                                    <h4 class="text-md font-semibold text-blue-900 mb-3">💡 Key Insights &
                                        Recommendations</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-for="rec in stats.pattern_analysis.recommendations" :key="rec.title"
                                            class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <i :class="{
                                                        'pi pi-target text-green-600': rec.type === 'setup',
                                                        'pi pi-star text-yellow-600': rec.type === 'score',
                                                        'pi pi-chart-bar text-blue-600': rec.type === 'symbol',
                                                        'pi pi-map-marker text-purple-600': rec.type === 'zone'
                                                    }" class="text-lg"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <h5 class="font-semibold text-gray-900 text-sm">{{ rec.title }}</h5>
                                                    <p class="text-gray-700 text-xs mt-1">{{ rec.description }}</p>
                                                    <p class="text-blue-800 text-xs mt-2 font-medium">→ {{ rec.action }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pattern Analysis Tabs -->
                                <Tabs value="0">
                                    <TabList>
                                        <Tab value="0">🎯 Top Setups</Tab>
                                        <Tab value="1">📊 Score Patterns</Tab>
                                        <Tab value="2">💱 Symbol Patterns</Tab>
                                        <Tab value="3">🗺️ Zone Patterns</Tab>
                                        <Tab value="4">⚙️ Technical Patterns</Tab>
                                        <Tab value="5">📈 Fundamental Patterns</Tab>
                                    </TabList>
                                    <TabPanels>
                                        <!-- Top Performing Setups -->
                                        <TabPanel value="0">
                                            <DataTable :value="stats.pattern_analysis.most_profitable_setups"
                                                class="p-datatable-sm">
                                                <Column field="setup" header="Winning Setup">
                                                    <template #body="slotProps">
                                                        <div class="space-y-2">
                                                            <div class="font-semibold text-sm text-green-800">{{
                                                                slotProps.data.setup }}</div>
                                                            <div class="flex flex-wrap gap-1">
                                                                <template
                                                                    v-for="tag in parseSetupTags(slotProps.data.setup)"
                                                                    :key="tag.text">
                                                                    <small :class="tag.class">{{ tag.text }}</small>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </Column>
                                                <Column field="frequency" header="Wins">
                                                    <template #body="slotProps">
                                                        <Badge :value="slotProps.data.frequency" severity="success" />
                                                    </template>
                                                </Column>
                                                <Column field="success_rate" header="% of Total Wins">
                                                    <template #body="slotProps">
                                                        <div class="flex items-center gap-2">
                                                            <ProgressBar :value="slotProps.data.success_rate"
                                                                :showValue="false" class="w-16" />
                                                            <span class="text-sm">{{ slotProps.data.success_rate
                                                                }}%</span>
                                                        </div>
                                                    </template>
                                                </Column>
                                            </DataTable>
                                        </TabPanel>

                                        <!-- Score Patterns -->
                                        <TabPanel value="1">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                <div v-for="(count, range) in stats.pattern_analysis.score_patterns"
                                                    :key="range" class="text-center p-4 bg-gray-50 rounded-lg">
                                                    <div class="text-2xl font-bold text-blue-900">{{ count }}</div>
                                                    <div class="text-sm text-gray-600">{{ range }} Score</div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ Math.round((count / stats.pattern_analysis.total_wins) * 100)
                                                        }}% of wins
                                                    </div>
                                                </div>
                                            </div>
                                        </TabPanel>

                                        <!-- Symbol Patterns -->
                                        <TabPanel value="2">
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                <div v-for="(count, symbol) in stats.pattern_analysis.symbol_patterns"
                                                    :key="symbol"
                                                    class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                                    <span class="font-mono font-bold text-blue-900">{{ symbol }}</span>
                                                    <div class="text-right">
                                                        <div class="font-semibold">{{ count }} wins</div>
                                                        <div class="text-xs text-gray-600">
                                                            {{ Math.round((count / stats.pattern_analysis.total_wins) *
                                                                100) }}%
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </TabPanel>

                                        <!-- Zone Patterns -->
                                        <TabPanel value="3">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                <div v-for="(count, pattern) in stats.pattern_analysis.zone_patterns"
                                                    :key="pattern" class="text-center p-4 bg-green-50 rounded-lg">
                                                    <div class="text-2xl font-bold text-green-900">{{ count }}</div>
                                                    <div class="text-sm text-gray-600">{{ pattern }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ Math.round((count / stats.pattern_analysis.total_wins) * 100)
                                                        }}% of wins
                                                    </div>
                                                </div>
                                            </div>
                                        </TabPanel>

                                        <!-- Technical Patterns -->
                                        <TabPanel value="4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div v-for="(data, pattern) in stats.pattern_analysis.technical_patterns"
                                                    :key="pattern"
                                                    class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                                                    <div class="flex items-center gap-3">
                                                        <i :class="{
                                                            'pi pi-map-marker text-blue-600': pattern.includes('Location'),
                                                            'pi pi-arrow-right text-indigo-600': pattern.includes('Direction')
                                                        }" class="text-lg"></i>
                                                        <span class="font-semibold text-blue-900">{{ pattern }}</span>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-xl font-bold text-blue-900">{{ data.count }}
                                                        </div>
                                                        <div class="text-xs text-gray-600">
                                                            {{ data.percentage }}% of wins
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </TabPanel>

                                        <!-- Fundamental Patterns -->
                                        <TabPanel value="5">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div v-for="(data, pattern) in stats.pattern_analysis.fundamental_patterns"
                                                    :key="pattern"
                                                    class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border border-purple-200">
                                                    <div class="flex items-center gap-3">
                                                        <i :class="{
                                                            'pi pi-dollar text-purple-600': pattern.includes('Valuation'),
                                                            'pi pi-calendar text-yellow-600': pattern.includes('Seasonal'),
                                                            'pi pi-chart-line text-pink-600': pattern.includes('COT Divergence'),
                                                            'pi pi-trending-up text-orange-600': pattern.includes('COT Index')
                                                        }" class="text-lg"></i>
                                                        <span class="font-semibold text-purple-900">{{ pattern }}</span>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-xl font-bold text-purple-900">{{ data.count }}
                                                        </div>
                                                        <div class="text-xs text-gray-600">
                                                            {{ data.percentage }}% of wins
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </TabPanel>
                                    </TabPanels>
                                </Tabs>
                            </div>
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
import { computed, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useToast } from 'primevue/usetoast'
import AppLayout from '@/Layouts/AppLayout.vue'

// Toast setup
const toast = useToast()
const page = usePage()

// Show toast for flash messages
onMounted(() => {
    if (page.props.flash?.success) {
        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: page.props.flash.success,
            life: 5000
        })
    }

    if (page.props.flash?.error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: page.props.flash.error,
            life: 5000
        })
    }
})

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

// Parse setup string into individual tags with appropriate styling
const parseSetupTags = (setupString) => {
    const tags = []

    if (!setupString) return tags

    // Split by + and & to get individual components
    const parts = setupString.split(/\s*[\+&]\s*/)

    parts.forEach(part => {
        const trimmed = part.trim()
        if (!trimmed) return

        // Technical Location Tags
        if (trimmed.includes('Very Expensive') || trimmed.includes('Very Cheap') ||
            trimmed.includes('Expensive') || trimmed.includes('Cheap')) {
            tags.push({
                text: trimmed,
                class: 'bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs'
            })
        }
        // Technical Direction Tags
        else if (trimmed.includes('Impulsion') || trimmed.includes('Correction')) {
            tags.push({
                text: trimmed,
                class: 'bg-indigo-100 text-indigo-800 px-2 py-1 rounded text-xs'
            })
        }
        // Fundamental Valuation Tags
        else if (trimmed.includes('Overvalued') || trimmed.includes('Undervalued')) {
            tags.push({
                text: trimmed,
                class: 'bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs'
            })
        }
        // Seasonal Tags
        else if (trimmed.includes('Seasonal')) {
            tags.push({
                text: trimmed,
                class: 'bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs'
            })
        }
        // COT Divergence Tags
        else if (trimmed.includes('COT Divergence')) {
            tags.push({
                text: trimmed,
                class: 'bg-pink-100 text-pink-800 px-2 py-1 rounded text-xs'
            })
        }
        // COT Index Tags
        else if (trimmed.includes('COT')) {
            tags.push({
                text: trimmed,
                class: 'bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs'
            })
        }
        // Zone Qualifiers Tags
        else if (trimmed.includes('Zone')) {
            tags.push({
                text: trimmed,
                class: 'bg-green-100 text-green-800 px-2 py-1 rounded text-xs'
            })
        }
        // Default styling for unmatched parts
        else {
            tags.push({
                text: trimmed,
                class: 'bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs'
            })
        }
    })

    return tags
}
</script>

<style scoped>
/* Custom styling for dashboard cards */
:deep(.p-card-content) {
    padding: 0;
}
</style>