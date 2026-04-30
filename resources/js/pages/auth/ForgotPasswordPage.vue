<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 flex items-center justify-center px-4">
        <div class="w-full max-w-md">

            <!-- Logo -->
            <div class="flex items-center justify-center mb-8">
                <RouterLink to="/" class="flex items-center gap-2.5">
                    <img :src="'/images/logo.png'" alt="Lighthouse Printing Solutions" class="h-16 w-auto" />
                    <div class="flex flex-col leading-tight">
                        <span class="text-base font-bold text-red-600 tracking-tight">LightHouse</span>
                        <span class="text-sm font-medium text-gray-400 tracking-wide">Printing Solutions</span>
                    </div>
                </RouterLink>
            </div>

            <div class="bg-white rounded-2xl shadow-xl shadow-gray-100 border border-gray-400 p-8">
                <!-- Icon -->
                <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z" />
                    </svg>
                </div>

                <template v-if="!sent">
                    <h1 class="text-2xl font-bold text-gray-900 text-center mb-2">Forgot your password?</h1>
                    <p class="text-sm text-gray-500 text-center mb-6">Enter your email and we'll send you a link to reset your password.</p>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                            <input
                                v-model="email"
                                type="email"
                                placeholder="you@example.com"
                                required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-400 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            <p v-if="error" class="text-xs text-red-500 mt-1">{{ error }}</p>
                        </div>

                        <div>
                            <div id="recaptcha-forgot"></div>
                            <p v-if="captchaError" class="text-xs text-red-500 mt-1">{{ captchaError }}</p>
                        </div>

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
                                Sending…
                            </span>
                            <span v-else>Send reset link</span>
                        </button>
                    </form>
                </template>

                <!-- Sent state -->
                <template v-else>
                    <h1 class="text-2xl font-bold text-gray-900 text-center mb-2">Check your email</h1>
                    <p class="text-sm text-gray-500 text-center mb-1">We sent a password reset link to</p>
                    <p class="text-sm font-medium text-gray-900 text-center mb-6">{{ email }}</p>
                    <p class="text-sm text-green-600 bg-green-50 rounded-xl px-4 py-2.5 text-center">
                        Password reset link sent successfully.
                    </p>
                </template>
            </div>

            <p class="text-center text-sm text-gray-500 mt-6">
                <RouterLink to="/login" class="text-indigo-600 font-medium hover:text-indigo-700">Back to sign in</RouterLink>
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRecaptcha } from '../../composables/useRecaptcha'

const email = ref('')
const loading = ref(false)
const error = ref('')
const sent = ref(false)
const { getToken, reset, captchaError } = useRecaptcha('recaptcha-forgot')

async function submit() {
    loading.value = true
    error.value = ''
    captchaError.value = ''
    try {
        await axios.post('/api/forgot-password', { email: email.value, recaptcha_token: getToken() })
        sent.value = true
    } catch (e) {
        if (e.response?.status === 429) {
            error.value = 'Too many attempts. Please wait a minute and try again.'
        } else {
            const errors = e.response?.data?.errors ?? {}
            if (errors.recaptcha_token) {
                reset()
                captchaError.value = errors.recaptcha_token[0]
            } else {
                error.value = errors.email?.[0] ?? 'Something went wrong.'
            }
        }
    } finally {
        loading.value = false
    }
}
</script>