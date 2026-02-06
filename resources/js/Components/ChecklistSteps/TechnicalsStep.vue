<template>
    <div class="space-y-8 animate-fade-in-up">
        <h2
            class="text-2xl font-bold bg-gradient-to-r from-blue-900 to-blue-700 bg-clip-text text-transparent mb-6 flex items-center gap-3">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg shadow-sm">
                <i class="pi pi-chart-line text-blue-700 dark:text-blue-300 text-xl"></i>
            </div>
            Technical Analysis
        </h2>

        <!-- Direction Section -->
        <section>
            <div class="flex items-center gap-2 mb-4 px-1">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Market Nature</h3>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">Direction</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="option in directionMetadata" :key="option.value"
                    @click="updateData('direction', option.value)"
                    class="cursor-pointer relative overflow-hidden rounded-xl border-2 transition-all duration-300 p-6 group hover:shadow-lg"
                    :class="[
                        modelValue.direction === option.value
                            ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-900/20 shadow-md ring-2 ring-blue-200 dark:ring-blue-800 transform scale-[1.01]'
                            : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-slate-50 dark:hover:bg-slate-700'
                    ]">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center transition-colors duration-300"
                            :class="modelValue.direction === option.value ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 group-hover:bg-white dark:group-hover:bg-slate-600 group-hover:text-blue-600 dark:group-hover:text-blue-400'">
                            <i :class="option.icon" class="text-xl"></i>
                        </div>
                        <div>
                            <h4
                                class="font-bold text-lg text-slate-900 dark:text-white group-hover:text-blue-700 dark:group-hover:text-blue-400 transition-colors">
                                {{ option.value }}
                            </h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ option.desc }}</p>
                        </div>

                        <!-- Checkmark for selected -->
                        <div v-if="modelValue.direction === option.value" class="absolute top-4 right-4 text-blue-600">
                            <i class="pi pi-check-circle text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Location Section -->
        <section>
            <div class="flex items-center gap-2 mb-4 px-1">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Price Structure</h3>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">Location</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <div v-for="(option, index) in locationMetadata" :key="option.value"
                    @click="updateData('location', option.value)"
                    class="cursor-pointer relative rounded-xl border-2 transition-all duration-200 p-4 flex flex-col items-center justify-center text-center gap-2 group hover:shadow-md"
                    :class="[
                        modelValue.location === option.value ?
                            option.activeClass :
                            'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-blue-300 dark:hover:border-blue-500'
                    ]">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center mb-1 transition-colors"
                        :class="modelValue.location === option.value ? option.iconClass : 'bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-400 group-hover:bg-white dark:group-hover:bg-slate-600'">
                        <i :class="option.icon"></i>
                    </div>
                    <span class="font-bold text-sm"
                        :class="modelValue.location === option.value ? option.textClass : 'text-slate-700 dark:text-slate-300 group-hover:text-blue-600 dark:group-hover:text-blue-400'">
                        {{ option.value }}
                    </span>
                    <div class="h-1 w-8 rounded-full mt-1" :class="option.barClass"></div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['update:modelValue', 'progress-updated'])

const directionMetadata = [
    {
        value: 'Impulsion',
        icon: 'pi pi-bolt',
        desc: 'Strong trending move with momentum.'
    },
    {
        value: 'Correction',
        icon: 'pi pi-replay',
        desc: 'Pullback or consolidation phase.'
    }
]

const locationMetadata = [
    {
        value: 'Very Cheap',
        icon: 'pi pi-angle-double-down',
        activeClass: 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 ring-1 ring-emerald-200 dark:ring-emerald-800/50',
        iconClass: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
        textClass: 'text-emerald-700 dark:text-emerald-300',
        barClass: 'bg-emerald-400 dark:bg-emerald-600'
    },
    {
        value: 'Cheap',
        icon: 'pi pi-angle-down',
        activeClass: 'border-green-500 bg-green-50 dark:bg-green-900/20 ring-1 ring-green-200 dark:ring-green-800/50',
        iconClass: 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
        textClass: 'text-green-700 dark:text-green-300',
        barClass: 'bg-green-400 dark:bg-green-600'
    },
    {
        value: 'EQ',
        icon: 'pi pi-minus',
        activeClass: 'border-slate-500 bg-slate-50 dark:bg-slate-800/50 ring-1 ring-slate-200 dark:ring-slate-700',
        iconClass: 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
        textClass: 'text-slate-700 dark:text-slate-200',
        barClass: 'bg-slate-400 dark:bg-slate-500'
    },
    {
        value: 'Expensive',
        icon: 'pi pi-angle-up',
        activeClass: 'border-orange-500 bg-orange-50 dark:bg-orange-900/20 ring-1 ring-orange-200 dark:ring-orange-800/50',
        iconClass: 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
        textClass: 'text-orange-700 dark:text-orange-300',
        barClass: 'bg-orange-400 dark:bg-orange-600'
    },
    {
        value: 'Very Expensive',
        icon: 'pi pi-angle-double-up',
        activeClass: 'border-red-500 bg-red-50 dark:bg-red-900/20 ring-1 ring-red-200 dark:ring-red-800/50',
        iconClass: 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
        textClass: 'text-red-700 dark:text-red-300',
        barClass: 'bg-red-400 dark:bg-red-600'
    },
]

const updateData = (key, value) => {
    const updatedData = { ...props.modelValue, [key]: value }
    emit('update:modelValue', updatedData)
    emit('progress-updated')
}
</script>

<style scoped>
/* Ensure dynamic colors work if not safelisted by purge */
/* Just in case, using standard colors usually works with JIT/v4 */
</style>
