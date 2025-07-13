<template>
    <div class="checklist-container max-w-5xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Trade Setup Checklist</h1>

        <!-- Step Indicator -->
        <div class="flex justify-center mb-8">
            <div class="flex space-x-4">
                <div v-for="(step, index) in steps" :key="index"
                    :class="['px-4 py-2 rounded-full text-sm font-medium cursor-pointer transition-all duration-300',
                        currentStep === index + 1 ? 'bg-blue-900 text-white' : 'bg-gray-200 text-gray-600 hover:bg-gray-300']" @click="currentStep = index + 1">
                    Step {{ index + 1 }}: {{ step }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Wizard Steps -->
            <div class="col-span-2 bg-white p-6 rounded-lg shadow-md">
                <!-- Step 1: Zone Qualifiers -->
                <div v-if="currentStep === 1" class="space-y-4">
                    <h2 class="text-2xl font-semibold text-blue-900">Zone Qualifiers</h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asset</label>
                        <select v-model="asset"
                            class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900">
                            <option value="" disabled>Select Asset</option>
                            <option value="EUR/USD">EUR/USD</option>
                            <option value="GBP/USD">GBP/USD</option>
                            <option value="USD/JPY">USD/JPY</option>
                            <option value="AUD/USD">AUD/USD</option>
                        </select>
                    </div>
                    <ul class="space-y-3">
                        <li v-for="(qualifier, index) in zoneQualifiers" :key="index" class="flex items-center">
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" v-model="checkedZoneQualifiers[index]"
                                    class="form-checkbox h-5 w-5 text-blue-900 rounded focus:ring-blue-900"
                                    @change="updateProgress" />
                                <span class="text-gray-700">{{ qualifier }}</span>
                            </label>
                        </li>
                    </ul>

                </div>

                <!-- Step 2: Technicals -->
                <div v-if="currentStep === 2" class="space-y-6">
                    <h2 class="text-2xl font-semibold text-blue-900">Technicals</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <select v-model="technicals.location"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                @change="updateProgress">
                                <option value="" disabled>Select Location</option>
                                <option value="Very Expensive">Very Expensive</option>
                                <option value="Expensive">Expensive</option>
                                <option value="EQ">EQ</option>
                                <option value="Cheap">Cheap</option>
                                <option value="Very Cheap">Very Cheap</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                            <select v-model="technicals.direction"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                @change="updateProgress">
                                <option value="" disabled>Select Direction</option>
                                <option value="Correction">Correction</option>
                                <option value="Impulsion">Impulsion</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Fundamentals -->
                <div v-if="currentStep === 3" class="space-y-6">
                    <h2 class="text-2xl font-semibold text-blue-900">Fundamentals</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valuation</label>
                            <select v-model="fundamentals.valuation"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                @change="updateProgress">
                                <option value="" disabled>Select Valuation</option>
                                <option value="Overvalued">Overvalued</option>
                                <option value="Neutral">Neutral</option>
                                <option value="Undervalued">Undervalued</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Seasonal Confluence</label>
                            <select v-model="fundamentals.seasonalConfluence"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                @change="updateProgress">
                                <option value="" disabled>Select Seasonal Confluence</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Non-Commercials</label>
                            <select v-model="fundamentals.nonCommercials"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                @change="updateProgress">
                                <option value="" disabled>Select Non-Commercials</option>
                                <option value="Divergence">Divergence</option>
                                <option value="No-Divergence">No-Divergence</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CoT Index</label>
                            <select v-model="fundamentals.cotIndex"
                                class="form-select w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                @change="updateProgress">
                                <option value="" disabled>Select CoT Index</option>
                                <option value="Bullish">Bullish</option>
                                <option value="Neutral">Neutral</option>
                                <option value="Bearish">Bearish</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea v-model="notes"
                                class="form-textarea w-full rounded-md border-gray-300 focus:ring-blue-900 focus:border-blue-900"
                                rows="4" placeholder="Add any notes about this trade setup"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-6 flex justify-between">
                    <button @click="resetWizard"
                        class="px-4 py-2 bg-gray-400 text-white rounded-md hover:bg-red-500 transition-colors">
                        Reset
                    </button>
                    <span>
                        <button v-if="currentStep > 1" @click="currentStep--"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Previous
                        </button>
                        <button v-if="currentStep < 3" @click="currentStep++"
                            class="px-4 py-2 ml-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors">
                            Next
                        </button>
                        <button v-if="currentStep === 3" @click="submitChecklist" :disabled="!canSubmit"
                            class="px-4 py-2 ml-2 bg-blue-900 text-white rounded-md transition-colors">
                            Submit
                        </button>
                    </span>
                </div>

                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="progress-bar-container bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="progress-bar h-full transition-all duration-300"
                            :style="{ width: evaluationScore + '%' }" :class="{
                                'bg-red-500': evaluationScore < 50,
                                'bg-yellow-500': evaluationScore >= 50 && evaluationScore <= 80,
                                'bg-emerald-500': evaluationScore > 80
                            }">
                        </div>
                    </div>
                    <!-- <p class="text-sm text-gray-600 mt-2">{{ evaluationScore }} Score</p> -->
                </div>
            </div>

            <!-- Summary Section -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-blue-900 mb-4">Trade Setup Summary</h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Technicals</h3>
                        <p class="text-sm text-gray-600">Location: {{ technicals.location || 'Not selected' }}</p>
                        <p class="text-sm text-gray-600">Direction: {{ technicals.direction || 'Not selected' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Fundamentals</h3>
                        <p class="text-sm text-gray-600">Valuation: {{ fundamentals.valuation || 'Not selected' }}</p>
                        <p class="text-sm text-gray-600">Seasonal Confluence: {{ fundamentals.seasonalConfluence || "Not selected" }} </p>
                        <p class="text-sm text-gray-600">Non-Commercials: {{ fundamentals.nonCommercials || 'Not selected' }} </p>
                        <p class="text-sm text-gray-600">CoT Index: {{ fundamentals.cotIndex || 'Not selected' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Zone Qualifiers ({{ selectedZoneQualifiersCount
                        }})</h3>
                        <ul class="list-disc pl-5 text-sm text-gray-600">
                            <li v-for="qualifier in filteredZoneQualifiers" :key="qualifier">
                                {{ qualifier }}
                            </li>
                            <li v-if="filteredZoneQualifiers.length === 0" class="text-gray-500">None selected</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-blue-900">Evaluation Score</h3>
                        <p :class="['text-2xl font-bold px-4 py-2 rounded-md inline-block',
                            evaluationScore < 50 ? 'text-red-600 bg-red-100' :
                                evaluationScore <= 80 ? 'text-yellow-600 bg-yellow-100' :
                                    'text-emerald-600 bg-emerald-100']">
                            {{ evaluationScore }}/100
                        </p>
                        <div v-if="evaluationScore === 100" class="text-yellow-500 mt-2 font-bold text-lg text-center">★
                            All Stars Aligned ★</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';

export default {
    data() {
        return {
            currentStep: 1,
            steps: ['Zone Qualifiers', 'Technicals', 'Fundamentals'],
            technicals: {
                location: '',
                direction: ''
            },
            fundamentals: {
                valuation: '',
                seasonalConfluence: '',
                nonCommercials: '',
                cotIndex: ''
            },
            zoneQualifiers: [
                'Fresh',
                'Original',
                'Flip',
                'LOL',
                'Minimum 1:2 Profit Margin',
                'Big Brother Coverage'
            ],
            asset: '',
            notes: '',
            checkedZoneQualifiers: Array(6).fill(false),
            progressCount: 0,
            totalInputs: 12 // Total number of inputs across all steps
        };
    },
    computed: {
        progressPercentage() {
            return Math.round((this.progressCount / this.totalInputs) * 100);
        },
        selectedZoneQualifiersCount() {
            return this.checkedZoneQualifiers.filter(Boolean).length;
        },
        filteredZoneQualifiers() {
            return this.zoneQualifiers.filter((_, index) => this.checkedZoneQualifiers[index]);
        },
        evaluationScore() {
            let score = 0;
            score += this.checkedZoneQualifiers.filter(Boolean).length * 5; // Each checked zone qualifier contributes 5 points
            score += ['Very Cheap', 'Very Expensive'].includes(this.technicals.location) ? 12 : // 12 points for 'Very Cheap' and 'Very Expensive'
                ['Cheap', 'Expensive'].includes(this.technicals.location) ? 7 : 0;
            score += this.technicals.direction === 'Correction' ? 6 :
                this.technicals.direction === 'Impulsion' ? 12 : 0;
            score += ['Undervalued', 'Overvalued'].includes(this.fundamentals.valuation) ? 13 : 0;
            score += this.fundamentals.seasonalConfluence === 'Yes' ? 6 : 0;
            score += this.fundamentals.nonCommercials === 'Divergence' ? 15 : 0;
            score += ['Bullish', 'Bearish'].includes(this.fundamentals.cotIndex) ? 12 : 0;
            return score;
        },
        canProceed() {
            if (this.currentStep === 1) {
                return this.selectedZoneQualifiersCount > 0;
            }
            if (this.currentStep === 2) {
                return this.technicals.location && this.technicals.direction;
            }
            return true;
        },
        canSubmit() {
            return this.technicals.location &&
                this.technicals.direction &&
                this.fundamentals.valuation &&
                this.fundamentals.seasonalConfluence &&
                this.fundamentals.nonCommercials &&
                this.fundamentals.cotIndex &&
                this.selectedZoneQualifiersCount > 0;
        }
    },
    methods: {
        updateProgress() {
            this.progressCount = this.checkedZoneQualifiers.filter(Boolean).length +
                (this.technicals.location ? 1 : 0) +
                (this.technicals.direction ? 1 : 0) +
                (this.fundamentals.valuation ? 1 : 0) +
                (this.fundamentals.seasonalConfluence ? 1 : 0) +
                (this.fundamentals.nonCommercials ? 1 : 0) +
                (this.fundamentals.cotIndex ? 1 : 0);
        },
        resetWizard() {
            this.currentStep = 1;
            this.technicals = { location: '', direction: '' };
            this.fundamentals = { valuation: '', seasonalConfluence: '', nonCommercials: '', cotIndex: '' };
            this.checkedZoneQualifiers = Array(6).fill(false);
            this.asset = '';
            this.notes = '';
            this.progressCount = 0;
        },
        submitChecklist() {

            Inertia.post('/checklists', {
                zone_qualifiers: this.zoneQualifiers.filter((_, index) => this.checkedZoneQualifiers[index]),
                technicals: this.technicals,
                fundamentals: this.fundamentals,
                score: this.evaluationScore,
                asset: this.asset,
                notes: this.notes
            }, {
                onSuccess: () => {
                    this.resetForm();
                }
            });
        },
    }
};
</script>

<style scoped>
/* Scoped styles for the component */
.form-select {
    transition: all 0.3s ease;
}

.form-select:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.2);
}
</style>