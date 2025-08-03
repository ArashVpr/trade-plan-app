<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-blue-900 mb-2">User Profile</h1>
                    <p class="text-gray-600">Manage your account settings and preferences</p>
                </div>

                <!-- Tab Menu -->
                <TabMenu v-model:activeIndex="activeTab" :model="tabItems" class="mb-6" />

                <!-- Profile Information Tab -->
                <Card v-if="activeTab === 0" class="mb-6">
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-user"></i>
                            Profile Information
                        </div>
                    </template>
                    <template #content>
                        <form @submit.prevent="updateProfile" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div class="flex flex-col gap-2">
                                    <label for="name" class="font-medium">Full Name</label>
                                    <InputText id="name" v-model="profileForm.name"
                                        :class="{ 'p-invalid': profileForm.errors.name }"
                                        placeholder="Enter your full name" />
                                    <small v-if="profileForm.errors.name" class="p-error">
                                        {{ profileForm.errors.name }}
                                    </small>
                                </div>

                                <!-- Email -->
                                <div class="flex flex-col gap-2">
                                    <label for="email" class="font-medium">Email Address</label>
                                    <InputText id="email" v-model="profileForm.email"
                                        :class="{ 'p-invalid': profileForm.errors.email }"
                                        placeholder="Enter your email" type="email" />
                                    <small v-if="profileForm.errors.email" class="p-error">
                                        {{ profileForm.errors.email }}
                                    </small>
                                </div>

                                <!-- Timezone -->
                                <div class="flex flex-col gap-2">
                                    <label for="timezone" class="font-medium">Timezone</label>
                                    <Select id="timezone" v-model="profileForm.timezone" :options="timezones"
                                        option-label="label" option-value="value" placeholder="Select your timezone"
                                        :class="{ 'p-invalid': profileForm.errors.timezone }" filter showClear />
                                    <small v-if="profileForm.errors.timezone" class="p-error">
                                        {{ profileForm.errors.timezone }}
                                    </small>
                                </div>
                            </div>

                            <!-- Bio -->
                            <div class="flex flex-col gap-2">
                                <label for="bio" class="font-medium">Bio</label>
                                <Textarea id="bio" v-model="profileForm.bio"
                                    :class="{ 'p-invalid': profileForm.errors.bio }"
                                    placeholder="Tell us about yourself..." rows="4" :maxlength="500" />
                                <small v-if="profileForm.errors.bio" class="p-error">
                                    {{ profileForm.errors.bio }}
                                </small>
                                <small class="text-gray-500">
                                    {{ profileForm.bio?.length || 0 }}/500 characters
                                </small>
                            </div>

                            <!-- Avatar Upload -->
                            <div class="flex flex-col gap-2">
                                <label class="font-medium">Profile Picture</label>
                                <div class="flex items-center gap-4">
                                    <Avatar :image="user.avatar_path ? `/storage/${user.avatar_path}` : null"
                                        :label="user.name.charAt(0).toUpperCase()" size="xlarge" shape="circle"
                                        class="border" />
                                    <div>
                                        <FileUpload mode="basic" accept="image/*" :maxFileSize="2000000"
                                            @upload="onAvatarUpload" :auto="true" chooseLabel="Change Picture"
                                            class="p-button-outlined p-button-sm" />
                                        <small class="block text-gray-500 mt-1">
                                            Max file size: 2MB. Accepted formats: JPG, PNG, GIF
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end">
                                <Button type="submit" :loading="profileForm.processing" icon="pi pi-save"
                                    label="Save Changes" />
                            </div>
                        </form>
                    </template>
                </Card>

                <!-- Security Tab -->
                <Card v-if="activeTab === 1" class="mb-6">
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-shield"></i>
                            Security Settings
                        </div>
                    </template>
                    <template #content>
                        <form @submit.prevent="updatePassword" class="space-y-6">
                            <div class="max-w-md space-y-6">
                                <!-- Current Password -->
                                <div class="flex flex-col gap-2">
                                    <label for="current_password" class="font-medium">Current Password</label>
                                    <Password id="current_password" v-model="passwordForm.current_password"
                                        :class="{ 'p-invalid': passwordForm.errors.current_password }"
                                        placeholder="Enter current password" :feedback="false" toggleMask fluid />
                                    <small v-if="passwordForm.errors.current_password" class="p-error">
                                        {{ passwordForm.errors.current_password }}
                                    </small>
                                </div>

                                <!-- New Password -->
                                <div class="flex flex-col gap-2">
                                    <label for="password" class="font-medium">New Password</label>
                                    <Password id="password" v-model="passwordForm.password"
                                        :class="{ 'p-invalid': passwordForm.errors.password }"
                                        placeholder="Enter new password" :feedback="false" toggleMask fluid />
                                    <small v-if="passwordForm.errors.password" class="p-error">
                                        {{ passwordForm.errors.password }}
                                    </small>
                                </div>

                                <!-- Confirm Password -->
                                <div class="flex flex-col gap-2">
                                    <label for="password_confirmation" class="font-medium">Confirm New Password</label>
                                    <Password id="password_confirmation" v-model="passwordForm.password_confirmation"
                                        :class="{ 'p-invalid': passwordForm.errors.password_confirmation }"
                                        placeholder="Confirm new password" :feedback="false" toggleMask fluid />
                                    <small v-if="passwordForm.errors.password_confirmation" class="p-error">
                                        {{ passwordForm.errors.password_confirmation }}
                                    </small>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end">
                                <Button type="submit" :loading="passwordForm.processing" icon="pi pi-key"
                                    label="Update Password" severity="secondary" />
                            </div>
                        </form>

                        <!-- Last Login Info -->
                        <Divider />
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Last login:</span>
                            <span>{{ formatDate(user.last_login_at) || 'Never' }}</span>
                        </div>
                    </template>
                </Card>

                <!-- Checklist Weights Tab -->
                <Card v-if="activeTab === 2" class="mb-6">
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-sliders-h"></i>
                            Checklist Weights
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-6">
                            <!-- Description -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <i class="pi pi-info-circle text-blue-600 mt-1"></i>
                                    <div>
                                        <h3 class="font-medium text-blue-900 mb-1">About Checklist Weights</h3>
                                        <p class="text-blue-800 text-sm">
                                            Adjust the importance of each factor in your trade analysis. Higher weights
                                            mean that factor has more influence on your overall checklist score.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <form @submit.prevent="updateChecklistWeights" class="space-y-8">
                                <!-- Zone Qualifiers Section -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                        <i class="pi pi-map-marker text-blue-600"></i>
                                        Zone Qualifiers
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Fresh Zone</label>
                                            <InputNumber v-model="weightsForm.zone_fresh_weight" :min="0" :max="100"
                                                suffix=" pts" class="w-full" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Original Zone</label>
                                            <InputNumber v-model="weightsForm.zone_original_weight" :min="0" :max="100"
                                                suffix=" pts" class="w-full" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Flip Zone</label>
                                            <InputNumber v-model="weightsForm.zone_flip_weight" :min="0" :max="100"
                                                suffix=" pts" class="w-full" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Liquidity Zone</label>
                                            <InputNumber v-model="weightsForm.zone_lol_weight" :min="0" :max="100"
                                                suffix=" pts" class="w-full" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Min Profit Margin</label>
                                            <InputNumber v-model="weightsForm.zone_min_profit_margin_weight" :min="0"
                                                :max="100" suffix=" pts" class="w-full" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Big Brother</label>
                                            <InputNumber v-model="weightsForm.zone_big_brother_weight" :min="0"
                                                :max="100" suffix=" pts" class="w-full" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Technical Analysis Section -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                        <i class="pi pi-chart-line text-green-600"></i>
                                        Technical Analysis
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Very Expensive CHP</label>
                                            <InputNumber v-model="weightsForm.technical_very_exp_chp_weight" :min="0"
                                                :max="100" suffix=" pts" class="w-full" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Expensive CHP</label>
                                            <InputNumber v-model="weightsForm.technical_exp_chp_weight" :min="0"
                                                :max="100" suffix=" pts" class="w-full" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Impulsive Direction</label>
                                            <InputNumber v-model="weightsForm.technical_direction_impulsive_weight"
                                                :min="0" :max="100" suffix=" pts" class="w-full" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Correction Direction</label>
                                            <InputNumber v-model="weightsForm.technical_direction_correction_weight"
                                                :min="0" :max="100" suffix=" pts" class="w-full" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Fundamental Analysis Section -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                        <i class="pi pi-globe text-purple-600"></i>
                                        Fundamental Analysis
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Valuation</label>
                                            <InputNumber v-model="weightsForm.fundamental_valuation_weight" :min="0"
                                                :max="100" suffix=" pts" class="w-full" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Seasonal</label>
                                            <InputNumber v-model="weightsForm.fundamental_seasonal_weight" :min="0"
                                                :max="100" suffix=" pts" class="w-full" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">COT Index</label>
                                            <InputNumber v-model="weightsForm.fundamental_cot_index_weight" :min="0"
                                                :max="100" suffix=" pts" class="w-full" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium">Non-Commercial Divergence</label>
                                            <InputNumber
                                                v-model="weightsForm.fundamental_noncommercial_divergence_weight"
                                                :min="0" :max="100" suffix=" pts" class="w-full" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex justify-between items-center pt-4 border-t">
                                    <Button type="button" label="Reset to Defaults" severity="secondary" outlined
                                        icon="pi pi-refresh" @click="resetToDefaults" />
                                    <Button type="submit" :loading="weightsForm.processing" icon="pi pi-save"
                                        label="Save Weights" />
                                </div>
                            </form>
                        </div>
                    </template>
                </Card>

                <!-- Account Management Tab -->
                <Card v-if="activeTab === 3" class="mb-6">
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-exclamation-triangle text-red-500"></i>
                            <span class="text-red-500">Danger Zone</span>
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-6">
                            <!-- Account Deletion -->
                            <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                                <h3 class="text-lg font-medium text-red-800 mb-2">Delete Account</h3>
                                <p class="text-red-700 mb-4">
                                    Once you delete your account, all of your checklists, trade entries, and personal
                                    data will be permanently removed. This action cannot be undone.
                                </p>
                                <Button label="Delete Account" icon="pi pi-trash" severity="danger"
                                    @click="confirmDelete = true" />
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:visible="confirmDelete" modal header="Confirm Account Deletion" :style="{ width: '450px' }">
            <div class="space-y-4">
                <p>Are you absolutely sure you want to delete your account?</p>
                <p class="text-red-600 font-medium">This action cannot be undone and will permanently delete:</p>
                <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                    <li>All your checklists and analysis</li>
                    <li>All your trade entries and history</li>
                    <li>Your profile and settings</li>
                    <li>Any uploaded files</li>
                </ul>
                <div class="flex flex-col gap-2">
                    <label for="delete_confirmation" class="font-medium">
                        Type "DELETE" to confirm:
                    </label>
                    <InputText id="delete_confirmation" v-model="deleteConfirmation" placeholder="Type DELETE"
                        :class="{ 'p-invalid': deleteForm.errors.confirmation }" />
                    <small v-if="deleteForm.errors.confirmation" class="p-error">
                        {{ deleteForm.errors.confirmation }}
                    </small>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-2">
                    <Button label="Cancel" severity="secondary" outlined @click="confirmDelete = false" />
                    <Button label="Delete Account" severity="danger" :loading="deleteForm.processing"
                        :disabled="deleteConfirmation !== 'DELETE'" @click="deleteAccount" />
                </div>
            </template>
        </Dialog>

        <!-- Toast for notifications -->
        <Toast />
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { useToast } from 'primevue/usetoast'
import AppLayout from '@/Layouts/AppLayout.vue'

// Define props
const props = defineProps({
    user: Object,
    checklistWeights: Object,
    checklistDefaults: Object,
})

// Toast
const toast = useToast()

// Tab management
const activeTab = ref(0)
const tabItems = [
    { label: 'Profile', icon: 'pi pi-user' },
    { label: 'Security', icon: 'pi pi-shield' },
    { label: 'Checklist Weights', icon: 'pi pi-sliders-h' },
    { label: 'Account', icon: 'pi pi-exclamation-triangle' },
]

// Profile form
const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    timezone: props.user.timezone,
    bio: props.user.bio,
})

// Password form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

// Checklist weights form
const weightsForm = useForm({
    zone_fresh_weight: props.checklistWeights?.zone_fresh_weight || props.checklistDefaults?.zone_fresh_weight || 4,
    zone_original_weight: props.checklistWeights?.zone_original_weight || props.checklistDefaults?.zone_original_weight || 4,
    zone_flip_weight: props.checklistWeights?.zone_flip_weight || props.checklistDefaults?.zone_flip_weight || 4,
    zone_lol_weight: props.checklistWeights?.zone_lol_weight || props.checklistDefaults?.zone_lol_weight || 4,
    zone_min_profit_margin_weight: props.checklistWeights?.zone_min_profit_margin_weight || props.checklistDefaults?.zone_min_profit_margin_weight || 4,
    zone_big_brother_weight: props.checklistWeights?.zone_big_brother_weight || props.checklistDefaults?.zone_big_brother_weight || 4,
    technical_very_exp_chp_weight: props.checklistWeights?.technical_very_exp_chp_weight || props.checklistDefaults?.technical_very_exp_chp_weight || 12,
    technical_exp_chp_weight: props.checklistWeights?.technical_exp_chp_weight || props.checklistDefaults?.technical_exp_chp_weight || 6,
    technical_direction_impulsive_weight: props.checklistWeights?.technical_direction_impulsive_weight || props.checklistDefaults?.technical_direction_impulsive_weight || 12,
    technical_direction_correction_weight: props.checklistWeights?.technical_direction_correction_weight || props.checklistDefaults?.technical_direction_correction_weight || 6,
    fundamental_valuation_weight: props.checklistWeights?.fundamental_valuation_weight || props.checklistDefaults?.fundamental_valuation_weight || 10,
    fundamental_seasonal_weight: props.checklistWeights?.fundamental_seasonal_weight || props.checklistDefaults?.fundamental_seasonal_weight || 6,
    fundamental_cot_index_weight: props.checklistWeights?.fundamental_cot_index_weight || props.checklistDefaults?.fundamental_cot_index_weight || 12,
    fundamental_noncommercial_divergence_weight: props.checklistWeights?.fundamental_noncommercial_divergence_weight || props.checklistDefaults?.fundamental_noncommercial_divergence_weight || 12,
})

// Delete form
const deleteForm = useForm({
    confirmation: '',
})

// State
const confirmDelete = ref(false)
const deleteConfirmation = ref('')

// Timezone options
const timezones = [
    { label: 'UTC', value: 'UTC' },
    { label: 'Eastern Time (ET)', value: 'America/New_York' },
    { label: 'Central Time (CT)', value: 'America/Chicago' },
    { label: 'Mountain Time (MT)', value: 'America/Denver' },
    { label: 'Pacific Time (PT)', value: 'America/Los_Angeles' },
    { label: 'London (GMT)', value: 'Europe/London' },
    { label: 'Tokyo (JST)', value: 'Asia/Tokyo' },
    { label: 'Sydney (AEST)', value: 'Australia/Sydney' },
]

// Methods
const updateProfile = () => {
    profileForm.put(route('profile.update'), {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Profile updated successfully',
                life: 3000,
            })
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Failed to update profile',
                life: 3000,
            })
        },
    })
}

const updatePassword = () => {
    passwordForm.put(route('profile.password'), {
        onSuccess: () => {
            passwordForm.reset()
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Password updated successfully',
                life: 3000,
            })
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Failed to update password',
                life: 3000,
            })
        },
    })
}

const updateChecklistWeights = () => {
    weightsForm.put(route('profile.checklist-weights'), {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Checklist weights updated successfully',
                life: 3000,
            })
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Failed to update checklist weights',
                life: 3000,
            })
        },
    })
}

const resetToDefaults = () => {
    Object.keys(props.checklistDefaults).forEach(key => {
        if (weightsForm.hasOwnProperty(key)) {
            weightsForm[key] = props.checklistDefaults[key]
        }
    })

    toast.add({
        severity: 'info',
        summary: 'Reset',
        detail: 'Weights reset to default values',
        life: 3000,
    })
}

const deleteAccount = () => {
    deleteForm.confirmation = deleteConfirmation.value
    deleteForm.delete(route('profile.delete'), {
        onSuccess: () => {
            // User will be redirected to login after deletion
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Failed to delete account',
                life: 3000,
            })
        },
    })
}

const onAvatarUpload = (event) => {
    // This would handle avatar upload
    console.log('Avatar upload:', event)
    toast.add({
        severity: 'info',
        summary: 'Info',
        detail: 'Avatar upload functionality to be implemented',
        life: 3000,
    })
}

const formatDate = (date) => {
    if (!date) return null
    return new Date(date).toLocaleString()
}
</script>
