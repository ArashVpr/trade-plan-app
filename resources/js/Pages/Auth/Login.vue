<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                    <i class="pi pi-chart-line text-2xl text-blue-600"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Sign in to your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Welcome back to Trade Plan App
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
                                placeholder="Enter your email" />
                            <small v-if="form.errors.email" class="p-error">{{ form.errors.email }}</small>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <Password id="password" v-model="form.password" :feedback="false" toggleMask class="w-full"
                                :class="{ 'p-invalid': form.errors.password }" placeholder="Enter your password"
                                :pt="{ input: { class: 'w-full' } }" />
                            <small v-if="form.errors.password" class="p-error">{{ form.errors.password }}</small>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <Checkbox id="remember" v-model="form.remember" :binary="true" />
                                <label for="remember" class="ml-2 block text-sm text-gray-900">
                                    Remember me
                                </label>
                            </div>

                            <div class="text-sm">
                                <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                                    Forgot your password?
                                </a>
                            </div>
                        </div>

                        <div>
                            <Button type="submit" :loading="form.processing" label="Sign in" class="w-full"
                                size="large" />
                        </div>

                        <div class="text-center">
                            <span class="text-sm text-gray-600">
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
import { useForm, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

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
</style>
