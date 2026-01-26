<template>
    <div class="chart-data">
        <!-- Chart container -->
        <div v-if="chartData && chartData.labels" class="chart-container">
            <component :is="chartComponent" :data="chartData" :options="chartOptions" :class="chartClass" />
        </div>

        <!-- Fallback for no data -->
        <div v-else class="no-data-message">
            <Message severity="info" :closable="false">
                {{ noDataMessage }}
            </Message>
        </div>

        <!-- Chart legend (optional) -->
        <div v-if="showLegend && chartData" class="chart-legend mt-4">
            <div class="flex flex-wrap gap-2">
                <div v-for="(dataset, index) in chartData.datasets" :key="index" class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded"
                        :style="{ backgroundColor: dataset.backgroundColor || dataset.borderColor }"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ dataset.label }}</span>
                </div>
            </div>
        </div>

        <!-- Chart statistics (optional) -->
        <div v-if="showStats && stats" class="chart-stats mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div v-for="(stat, key) in stats" :key="key" class="text-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="text-2xl font-bold text-blue-900 dark:text-blue-300">{{ stat.value }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ stat.label }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    chartData: {
        type: Object,
        required: true
    },
    chartType: {
        type: String,
        default: 'line',
        validator: (value) => ['line', 'bar', 'pie', 'doughnut', 'radar', 'polarArea'].includes(value)
    },
    chartOptions: {
        type: Object,
        default: () => ({
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        })
    },
    height: {
        type: String,
        default: '400px'
    },
    showLegend: {
        type: Boolean,
        default: false
    },
    showStats: {
        type: Boolean,
        default: false
    },
    stats: {
        type: Object,
        default: null
    },
    noDataMessage: {
        type: String,
        default: 'No chart data available'
    }
})

const chartComponent = computed(() => {
    const componentMap = {
        line: 'Chart',
        bar: 'Chart',
        pie: 'Chart',
        doughnut: 'Chart',
        radar: 'Chart',
        polarArea: 'Chart'
    }
    return componentMap[props.chartType] || 'Chart'
})

const chartClass = computed(() => {
    return `chart-${props.chartType}`
})

// Default chart options for different types
const defaultOptions = computed(() => {
    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: !props.showLegend,
                position: 'top'
            }
        }
    }

    if (props.chartType === 'line') {
        return {
            ...baseOptions,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    }

    if (props.chartType === 'bar') {
        return {
            ...baseOptions,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    }

    return baseOptions
})

// Merge default options with provided options
const mergedOptions = computed(() => {
    return { ...defaultOptions.value, ...props.chartOptions }
})
</script>

<style scoped>
.chart-container {
    position: relative;
    height: v-bind(height);
}

.chart-legend {
    border-top: 1px solid var(--p-content-border-color);
    padding-top: 1rem;
}

.no-data-message {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 200px;
}
</style>
