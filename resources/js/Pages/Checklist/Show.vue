<template>
    <div class="max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Checklist Details</h1>
        <div class="flex justify-between">
            <div>
                <Link href="/checklists" method="get" as="button"
                    class="px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors hover:cursor-pointer">
                Back
                </Link>
            </div>
            <div>
                <Link :href="`/checklists/${checklist.id}/edit`" method="get" as="button"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-md mr-2 hover:bg-emerald-700 transition-colors hover:cursor-pointer">
                Edit
                </Link>
                <Link :href="`/checklists/${checklist.id}`" as="button" @click="confirmDelete"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors hover:cursor-pointer">
                Delete
                </Link>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md mt-6">
            <div class="flex flex-wrap">
                <div class="w-full md:w-1/2">
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
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-700">Created</h3>
                            <p class="text-sm text-gray-600">{{ new Date(checklist.created_at).toLocaleDateString() }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-700">Zone Qualifiers ({{
                                checklist.zone_qualifiers.length
                                }})</h3>
                            <ul class="list-disc pl-5 text-sm text-gray-600">
                                <li v-for="qualifier in checklist.zone_qualifiers" :key="qualifier">
                                    {{ qualifier }}
                                </li>
                                <li v-if="checklist.zone_qualifiers.length === 0" class="text-gray-500">None selected
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-700">Technicals</h3>
                            <p class="text-sm text-gray-600">Location: {{ checklist.technicals.location || 'Not selected' }}</p>
                            <p class="text-sm text-gray-600">Direction: {{ checklist.technicals.direction || 'Not selected' }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-700">Fundamentals</h3>
                            <p class="text-sm text-gray-600">Valuation: {{ checklist.fundamentals.valuation || 'Not selected' }}</p>
                            <p class="text-sm text-gray-600">Seasonal Confluence: {{ checklist.fundamentals.seasonalConfluence || 'Not selected' }}</p>
                            <p class="text-sm text-gray-600">Non-Commercials: {{ checklist.fundamentals.nonCommercials || 'Not selected' }}</p>
                            <p class="text-sm text-gray-600">CoT Index: {{ checklist.fundamentals.cotIndex || 'Not selected' }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-700">Notes</h3>
                            <p class="text-sm text-gray-600">{{ checklist.notes || 'No notes provided' }}</p>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Score Breakdown</h3>
                    <div class="w-full h-80">
                        <Radar :data="chartData" :options="chartOptions" />
                    </div>
                    <div v-if="checklist.score === 100" class="text-yellow-500 mt-2 font-bold text-lg text-center">
                        ★ All Stars Aligned ★
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Radar } from 'vue-chartjs'
import { Chart, registerables } from 'chart.js'
import { computed } from 'vue'

Chart.register(...registerables)

const props = defineProps({
    checklist: {
        type: Object,
        required: true
    }
})

const confirmDelete = () => {
    const result = confirm('Are you sure you want to delete this checklist? This action cannot be undone.');
    if (result) {
        $inertia.delete(route('checklists.destroy', props.checklist.id));
    }
}

// Calculate individual category scores with normalization
const calculateCategoryScores = (checklist) => {
    const maxValues = { ZoneQualifiers: 30, Technicals: 24, Fundamentals: 46 };
    let scores = {
        ZoneQualifiers: Math.min(checklist.zone_qualifiers.length * 5, maxValues.ZoneQualifiers),
        Technicals: 0,
        Fundamentals: 0
    };

    // Technicals score
    scores.Technicals += ['Very Cheap', 'Very Expensive'].includes(checklist.technicals.location) ? 12 :
        ['Cheap', 'Expensive'].includes(checklist.technicals.location) ? 7 : 0;
    scores.Technicals += checklist.technicals.direction === 'Correction' ? 6 :
        checklist.technicals.direction === 'Impulsion' ? 12 : 0;
    scores.Technicals = Math.min(scores.Technicals, maxValues.Technicals);

    // Fundamentals score
    scores.Fundamentals += ['Undervalued', 'Overvalued'].includes(checklist.fundamentals.valuation) ? 13 : 0;
    scores.Fundamentals += checklist.fundamentals.seasonalConfluence === 'Yes' ? 6 : 0;
    scores.Fundamentals += checklist.fundamentals.nonCommercials === 'Divergence' ? 15 : 0;
    scores.Fundamentals += ['Bullish', 'Bearish'].includes(checklist.fundamentals.cotIndex) ? 12 : 0;
    scores.Fundamentals = Math.min(scores.Fundamentals, maxValues.Fundamentals);

    return scores;
};

const chartData = computed(() => {
    const scores = calculateCategoryScores(props.checklist);
    const maxValues = { ZoneQualifiers: 30, Technicals: 24, Fundamentals: 46 };
    return {
        labels: ['Zone Qualifiers', 'Technicals', 'Fundamentals'],
        datasets: [{
            label: 'Score Contribution',
            data: [
                (scores.ZoneQualifiers / maxValues.ZoneQualifiers) * 100,
                (scores.Technicals / maxValues.Technicals) * 100,
                (scores.Fundamentals / maxValues.Fundamentals) * 100
            ],
            backgroundColor: (context) => {
                const index = context.dataIndex;
                const labels = ['ZoneQualifiers', 'Technicals', 'Fundamentals'];
                const value = scores[labels[index]];
                const max = maxValues[labels[index]];
                return value === max ? 'rgba(16, 185, 129, 0.2)' : 'rgba(16, 185, 129, 0.2)';
            },
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 2,
            pointBackgroundColor: 'rgba(16, 185, 129, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(16, 185, 129, 1)'
        }]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        r: {
            beginAtZero: true,
            max: 100,
            ticks: {
                stepSize: 25,
                callback: (value) => `${value}%`
            },
            pointLabels: {
                font: {
                    size: 14
                }
            }
        }
    },
    plugins: {
        legend: {
            display: true,
            position: 'top',
            labels: {
                font: {
                    size: 18
                },
                generateLabels: (chart) => {
                    const data = chart.data;
                    const scores = calculateCategoryScores(props.checklist); // Calculate scores for display
                    const maxValues = { ZoneQualifiers: 30, Technicals: 24, Fundamentals: 46 };
                    return data.labels.map((label, i) => {
                        const key = label.replace(' ', ''); // Ensure label matches scores key
                        const value = scores[key]; // Use corrected key to access scores
                        const max = maxValues[key];
                        const percentage = (value / max) * 100;
                        return {
                            text: `${label} (${value}/${max}, ${percentage.toFixed(1)}%)`,
                            fillStyle: value === max ? 'gold' : 'rgba(16, 185, 129, 1)',
                            font: { size: 18 }
                        };
                    });
                }
            }
        },
        tooltip: {
            callbacks: {
                label: (context) => {
                    const label = context.label || ''
                    const value = context.raw || 0
                    const scores = calculateCategoryScores(props.checklist)
                    const maxValues = { ZoneQualifiers: 30, Technicals: 24, Fundamentals: 46 }
                    const max = maxValues[label]
                    const rawScore = scores[label]
                    return `${label}: ${rawScore}/${max} (${value.toFixed(1)}%)`
                }
            }
        }
    }
}
</script>