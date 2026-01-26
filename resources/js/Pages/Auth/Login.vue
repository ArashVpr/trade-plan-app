<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-950 py-12 px-4 sm:px-6 lg:px-8">

        <div class="max-w-md w-full space-y-8">
            <div>
                <div
                    class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40">
                    <i class="pi pi-chart-line text-2xl text-blue-600"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                    Sign in to your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                    Welcome back to Trade Plan App
                </p>
            </div>

            <Card class="p-6">
                <template #content>
                    <form @submit.prevent="submit" class="space-y-6" method="post" action="/login">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Address
                            </label>
                            <InputText id="email" name="email" v-model="form.email" type="email" autocomplete="username"
                                required class="w-full" :class="{ 'p-invalid': form.errors.email }"
                                placeholder="Enter your email" @input="form.clearErrors('email')" />
                            <small v-if="form.errors.email" class="p-error block mt-1">{{ form.errors.email }}</small>
                        </div>

                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Password
                            </label>
                            <Password v-model="form.password" :feedback="false" toggleMask class="w-full"
                                :class="{ 'p-invalid': form.errors.password }" placeholder="Enter your password"
                                @input="form.clearErrors('password')" autocomplete="current-password" inputId="password"
                                name="password" />
                            <small v-if="form.errors.password" class="p-error block mt-1">{{ form.errors.password
                            }}</small>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <Checkbox id="remember" v-model="form.remember" :binary="true" />
                                <label for="remember" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                    Remember me
                                </label>
                            </div>

                            <div class="text-sm">
                                <Link :href="route('password.request')"
                                    class="font-medium text-blue-600 hover:text-blue-500">
                                    Forgot your password?
                                </Link>
                            </div>
                        </div>

                        <div>
                            <Button type="submit" :loading="form.processing" label="Sign in" class="w-full"
                                size="large" />
                        </div>

                        <div class="text-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                Don't have an account?
                                <Link :href="route('register')" class="font-medium text-blue-600 hover:text-blue-500">
                                    Sign up
                                </Link>
                            </span>
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

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'))
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
