<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                    <i class="pi pi-chart-line text-2xl text-blue-600"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Reset your password
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Enter your new password below
                </p>
            </div>

            <Card class="p-6">
                <template #content>
                    <form @submit.prevent="submit" class="space-y-6" method="post" action="/reset-password">
                        <!-- Hidden fields for password managers -->
                        <input type="hidden" name="username" :value="form.email" autocomplete="username" />

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <InputText id="email" name="email" v-model="form.email" type="email" autocomplete="username"
                                readonly class="w-full bg-gray-50" />
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password
                            </label>
                            <Password v-model="form.password" toggleMask class="w-full"
                                :class="{ 'p-invalid': form.errors.password }" placeholder="Enter your new password"
                                @input="form.clearErrors('password')" autocomplete="new-password" inputId="password"
                                name="password">
                                <template #header>
                                    <div class="font-semibold text-sm mb-4">Password Requirements</div>
                                </template>
                                <template #footer>
                                    <Divider />
                                    <ul class="pl-2 my-0 leading-normal text-sm">
                                        <li>Minimum 8 characters</li>
                                        <li>At least one letter</li>
                                        <li>At least one number</li>
                                        <li>Mixed case letters (A-z)</li>
                                    </ul>
                                </template>
                            </Password>
                            <small v-if="form.errors.password" class="p-error block mt-1">{{ form.errors.password
                            }}</small>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password
                            </label>
                            <Password v-model="form.password_confirmation" :feedback="false" toggleMask class="w-full"
                                :class="{ 'p-invalid': form.errors.password_confirmation }"
                                placeholder="Confirm your new password"
                                @input="form.clearErrors('password_confirmation')" autocomplete="new-password"
                                inputId="password_confirmation" name="password_confirmation" />
                            <small v-if="form.errors.password_confirmation" class="p-error block mt-1">{{
                                form.errors.password_confirmation }}</small>
                        </div>

                        <div>
                            <Button type="submit" :loading="form.processing" label="Reset Password" class="w-full"
                                size="large" />
                        </div>

                        <div class="text-center">
                            <Link :href="route('login')" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                            Back to Sign In
                            </Link>
                        </div>
                    </form>
                </template>
            </Card>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

// Receive props from backend
const props = defineProps({
    email: String,
    token: String,
})

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('password.update'))
}
</script>

<style scoped>
:deep(.p-card-content) {
    padding: 0;
}

/* Enhanced error styling */
:deep(.p-inputtext.p-invalid) {
    border-color: #ef4444;
    box-shadow: 0 0 0 0.2rem rgba(239, 68, 68, 0.2);
}

:deep(.p-password.p-invalid .p-inputtext) {
    border-color: #ef4444;
    box-shadow: 0 0 0 0.2rem rgba(239, 68, 68, 0.2);
}

/* Make password component full width */
:deep(.p-password) {
    width: 100%;
}

:deep(.p-password .p-inputtext) {
    width: 100%;
}

/* Style password requirements */
:deep(.p-password .p-password-panel) {
    margin-top: 0.5rem;
}

:deep(.p-password .p-password-panel ul) {
    margin: 0;
    padding-left: 1rem;
}

:deep(.p-password .p-password-panel li) {
    color: #6b7280;
    font-size: 0.875rem;
}

.p-error {
    color: #ef4444;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Focus states for better UX */
:deep(.p-inputtext:focus) {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.2);
}

:deep(.p-password .p-inputtext:focus) {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.2);
}
</style>
