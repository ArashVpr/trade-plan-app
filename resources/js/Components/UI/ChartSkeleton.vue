<template>
    <div class="w-full" :class="containerClass">
        <div v-if="loading" class="flex flex-col items-center justify-center" :style="{ height: height }">
            <!-- Line Chart Skeleton -->
            <div v-if="type === 'line'" class="w-full h-full flex items-end justify-around gap-1 px-4">
                <div v-for="i in 7" :key="i" class="flex-1">
                    <Skeleton :height="`${20 + Math.random() * 60}%`" borderRadius="4px" />
                </div>
            </div>

            <!-- Bar Chart Skeleton -->
            <div v-else-if="type === 'bar'" class="w-full h-full flex items-end justify-around gap-2 px-4">
                <div v-for="i in 5" :key="i" class="flex-1">
                    <Skeleton :height="`${30 + Math.random() * 50}%`" borderRadius="4px" />
                </div>
            </div>

            <!-- Doughnut/Pie Chart Skeleton -->
            <div v-else-if="['doughnut', 'pie'].includes(type)" class="flex items-center justify-center h-full">
                <Skeleton shape="circle" size="12rem" />
            </div>

            <!-- Radar/Polar Chart Skeleton -->
            <div v-else-if="['radar', 'polarArea'].includes(type)" class="flex items-center justify-center h-full">
                <Skeleton shape="circle" size="12rem" />
            </div>

            <!-- Generic skeleton fallback -->
            <div v-else class="w-full h-full flex items-center justify-center">
                <Skeleton width="80%" height="80%" borderRadius="8px" />
            </div>
        </div>

        <!-- Actual chart slot when loaded -->
        <div v-else>
            <slot />
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    /** Whether the chart is loading */
    loading: {
        type: Boolean,
        default: true
    },
    /** Chart type for appropriate skeleton shape */
    type: {
        type: String,
        default: 'line',
        validator: (value) => ['line', 'bar', 'doughnut', 'pie', 'radar', 'polarArea'].includes(value)
    },
    /** Height of the skeleton container */
    height: {
        type: String,
        default: '16rem'
    },
    /** Additional container classes */
    containerClass: {
        type: String,
        default: ''
    }
})
</script>
