<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <!-- Toast for notifications -->
        <Toast />

        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                    <i class="pi pi-chart-line text-2xl text-blue-600"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Forgot your password?
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Enter your email address and we'll send you a link to reset your password
                </p>
            </div>

            <Card class="p-6">
                <template #content>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <InputText id="email" v-model="form.email" type="email" autocomplete="email" required
                                class="w-full" :class="{ 'p-invalid': form.errors.email }"
                                placeholder="Enter your email address" @input="form.clearErrors('email')" />
                            <small v-if="form.errors.email" class="p-error block mt-1">{{ form.errors.email }}</small>
                        </div>

                        <div>
                            <Button type="submit" :loading="form.processing" label="Send Reset Link" class="w-full"
                                size="large" />
                        </div>

                        <div class="text-center space-y-2">
                            <div class="text-sm">
                                <Link :href="route('login')" class="font-medium text-blue-600 hover:text-blue-500">
                                Back to Sign In
                                </Link>
                            </div>
                            <div class="text-sm text-gray-600">
                                Don't have an account?
                                <Link :href="route('register')" class="font-medium text-blue-600 hover:text-blue-500">
                                Sign up
                                </Link>
                            </div>
                        </div>
                    </form>
                </template>
            </Card>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useForm, Link, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useToast } from 'primevue/usetoast'

// Toast setup
const toast = useToast()
const page = usePage()

// Show toast for flash messages
onMounted(() => {
    if (page.props.flash?.success) {
        toast.add({
            severity: 'success',
            summary: 'Email Sent',
            detail: page.props.flash.success,
            life: 6000
        })
    }
})

const form = useForm({
    email: '',
})

const submit = () => {
    form.post(route('password.email'))
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
</style>
