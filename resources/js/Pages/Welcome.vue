<template>
    <div class="checklist-container">
        <h1 class="text-2xl font-bold mb-4">Checklist</h1>
        <div class="grid grid-cols-3 gap-4">
            <!-- Left Section: Inputs -->
            <div class="col-span-2">
                <!-- Technicals Section -->
                <div>
                    <h2 class="text-xl font-semibold mb-2">Technicals</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Location</label>
                            <select v-model="technicals.location" class="form-select w-full">
                                <option value="Very Expensive">Very Expensive</option>
                                <option value="Expensive">Expensive</option>
                                <option value="EQ">EQ</option>
                                <option value="Cheap">Cheap</option>
                                <option value="Very Cheap">Very Cheap</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Direction</label>
                            <select v-model="technicals.direction" class="form-select w-full">
                                <option value="Correction">Correction</option>
                                <option value="Impulsion">Impulsion</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Fundamentals Section -->
                <div>
                    <h2 class="text-xl font-semibold mb-2">Fundamentals</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Valuation</label>
                            <select v-model="fundamentals.valuation" class="form-select w-full">
                                <option value="Overvalued">Overvalued</option>
                                <option value="Neutral">Neutral</option>
                                <option value="Undervalued">Undervalued</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Seasonal Confluence</label>
                            <select v-model="fundamentals.seasonalConfluence" class="form-select w-full">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Non-Commercials</label>
                            <select v-model="fundamentals.nonCommercials" class="form-select w-full">
                                <option value="Divergence">Divergence</option>
                                <option value="No-Divergence">No-Divergence</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">CoT Index</label>
                            <select v-model="fundamentals.cotIndex" class="form-select w-full">
                                <option value="Bullish">Bullish</option>
                                <option value="Neutral">Neutral</option>
                                <option value="Bearish">Bearish</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Zone Qualifiers Section -->
                <div>
                    <h2 class="text-xl font-semibold mb-2">Zone Qualifiers</h2>
                    <ul class="space-y-2">
                        <li v-for="(qualifier, index) in zoneQualifiers" :key="index" class="flex items-center">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" v-model="checkedZoneQualifiers[index]"
                                    class="form-checkbox h-5 w-5 text-blue-600" />
                                <span>{{ qualifier }}</span>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Section: Summary -->
            <div>
                <h2 class="text-xl font-semibold mb-2">Summary</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium">Location: {{ technicals.location }}</p>
                        <p class="text-sm font-medium">Direction: {{ technicals.direction }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Valuation: {{ fundamentals.valuation }}</p>
                        <p class="text-sm font-medium">Seasonal Confluence: {{ fundamentals.seasonalConfluence }}</p>
                        <p class="text-sm font-medium">Non-Commercials: {{ fundamentals.nonCommercials }}</p>
                        <p class="text-sm font-medium">CoT Index: {{ fundamentals.cotIndex }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Zone Qualifiers ({{ selectedZoneQualifiersCount }}):</p>
                        <ul class="list-disc pl-5">
                            <li v-for="(qualifier, index) in zoneQualifiers" :key="index"
                                v-if="checkedZoneQualifiers[index]">
                                {{ qualifier }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    name: String,
});
</script>

<script>
export default {
    data() {
        return {
            technicals: {
                location: "",
                direction: ""
            },
            fundamentals: {
                valuation: "",
                seasonalConfluence: "",
                nonCommercials: "",
                cotIndex: ""
            },
            zoneQualifiers: [
                "Fresh",
                "Original",
                "Flip",
                "LOL",
                "Minimum 1:2 Profit Margin",
                "Big Brother Coverage"
            ],
            checkedZoneQualifiers: Array(6).fill(false)
        };
    },
    computed: {
        selectedZoneQualifiersCount() {
            return this.checkedZoneQualifiers.filter(Boolean).length;
        }
    }
};
</script>

<style>
.checklist-container {
    max-width: 800px;
    margin: auto;
    text-align: center;
}

.grid {
    display: grid;
    gap: 1rem;
}

.form-select {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 0.5rem;
    width: 100%;
}

.list-disc {
    list-style-type: disc;
}
</style>