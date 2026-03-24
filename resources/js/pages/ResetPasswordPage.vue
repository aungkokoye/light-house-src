<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 flex items-center justify-center px-4">
        <div class="w-full max-w-md">

            <!-- Logo -->
            <div class="flex items-center justify-center gap-2 mb-8">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 1 1 0 10A5 5 0 0 1 12 7z" />
                    </svg>
                </div>
                <span class="font-semibold text-gray-900 text-xl">Light House</span>
            </div>

            <div class="bg-white rounded-2xl shadow-xl shadow-gray-100 border border-gray-100 p-8">

                <!-- Invalid link -->
                <template v-if="!token || !email">
                    <p class="text-sm text-red-500 text-center">Invalid or missing reset link. Please request a new one.</p>
                    <div class="mt-4 text-center">
                        <RouterLink to="/forgot-password" class="text-indigo-600 text-sm font-medium hover:text-indigo-700">Request new link</RouterLink>
                    </div>
                </template>

                <template v-else-if="!success">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Set new password</h1>
                    <p class="text-sm text-gray-500 mb-6">Choose a strong password of at least 8 characters.</p>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">New password</label>
                            <input
                                v-model="form.password"
                                type="password"
                                placeholder="Min. 8 characters"
                                required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            <p v-if="errors.password" class="text-xs text-red-500 mt-1">{{ errors.password[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm new password</label>
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                placeholder="Repeat new password"
                                required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                        </div>

                        <p v-if="errors.email" class="text-sm text-red-500 bg-red-50 rounded-xl px-4 py-2.5">{{ errors.email[0] }}</p>
                        <p v-if="errors.message" class="text-sm text-red-500 bg-red-50 rounded-xl px-4 py-2.5">{{ errors.message }}</p>

                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full bg-indigo-600 text-white font-medium py-2.5 rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="loading" class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                                </svg>
                                Resetting…
                            </span>
                            <span v-else>Reset password</span>
                        </button>
                    </form>
                </template>

                <!-- Success -->
                <template v-else>
                    <div class="text-center">
                        <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">Password reset!</h1>
                        <p class="text-sm text-gray-500 mb-6">Your password has been updated. You can now sign in.</p>
                        <RouterLink to="/login" class="inline-block w-full bg-indigo-600 text-white font-medium py-2.5 rounded-xl hover:bg-indigo-700 transition-colors text-sm">
                            Sign in
                        </RouterLink>
                    </div>
                </template>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const token = route.query.token ?? ''
const email = route.query.email ?? ''

const form = reactive({ password: '', password_confirmation: '' })
const loading = ref(false)
const success = ref(false)
const errors = ref({})

async function submit() {
    loading.value = true
    errors.value = {}
    try {
        await axios.post('/api/reset-password', {
            token,
            email,
            password: form.password,
            password_confirmation: form.password_confirmation,
        })
        success.value = true
    } catch (e) {
        errors.value = e.response?.data?.errors ?? {}
        if (!Object.keys(errors.value).length) {
            errors.value = { message: e.response?.data?.message ?? 'Something went wrong.' }
        }
    } finally {
        loading.value = false
    }
}
</script>