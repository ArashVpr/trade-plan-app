<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                    <i class="pi pi-chart-line text-2xl text-blue-600"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Create your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Join Trade Plan App and start improving your trading
                </p>
            </div>

            <Card class="p-6">
                <template #content>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name
                            </label>
                            <InputText id="name" v-model="form.name" type="text" autocomplete="name" required
                                class="w-full" :class="{ 'p-invalid': form.errors.name }"
                                placeholder="Enter your full name" @input="form.clearErrors('name')" />
                            <small v-if="form.errors.name" class="p-error block mt-1">{{ form.errors.name }}</small>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <InputText id="email" v-model="form.email" type="email" autocomplete="email" required
                                class="w-full" :class="{ 'p-invalid': form.errors.email }"
                                placeholder="Enter your email" @input="form.clearErrors('email')" />
                            <small v-if="form.errors.email" class="p-error block mt-1">{{ form.errors.email }}</small>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <Password id="password" v-model="form.password" toggleMask class="w-full"
                                :class="{ 'p-invalid': form.errors.password }" placeholder="Choose a strong password"
                                :pt="{ input: { class: 'w-full' } }" @input="form.clearErrors('password')" />
                            <small v-if="form.errors.password" class="p-error block mt-1">{{ form.errors.password
                                }}</small>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password
                            </label>
                            <Password id="password_confirmation" v-model="form.password_confirmation" :feedback="false"
                                toggleMask class="w-full" :class="{ 'p-invalid': form.errors.password_confirmation }"
                                placeholder="Confirm your password" :pt="{ input: { class: 'w-full' } }"
                                @input="form.clearErrors('password_confirmation')" />
                            <small v-if="form.errors.password_confirmation" class="p-error block mt-1">{{
                                form.errors.password_confirmation }}</small>
                        </div>

                        <div>
                            <Button type="submit" :loading="form.processing" label="Create Account" class="w-full"
                                size="large" />
                        </div>

                        <div class="text-center">
                            <span class="text-sm text-gray-600">
                                Already have an account?
                                <Link :href="route('login')" class="font-medium text-blue-600 hover:text-blue-500">
                                Sign in
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
import { useForm, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('register'))
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