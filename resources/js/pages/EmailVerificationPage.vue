<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 flex items-center justify-center px-4">
        <div class="w-full max-w-md text-center">

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
                <!-- Icon -->
                <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-2">Check your email</h1>

                <!-- Known email -->
                <template v-if="email">
                    <p class="text-sm text-gray-500 mb-1">We sent a verification link to</p>
                    <p class="text-sm font-medium text-gray-900 mb-6">{{ email }}</p>
                    <p class="text-sm text-gray-500 mb-6">
                        Click the link in the email to activate your account. The link will expire in 60 minutes.
                    </p>
                </template>

                <!-- No email — ask for it -->
                <template v-else>
                    <p class="text-sm text-gray-500 mb-6">Enter your email address to receive a new verification link.</p>
                    <input
                        v-model="emailInput"
                        type="email"
                        placeholder="you@example.com"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition mb-4"
                    />
                </template>

                <!-- Success message -->
                <p v-if="resent" class="text-sm text-green-600 bg-green-50 rounded-xl px-4 py-2.5 mb-4">
                    Verification email resent successfully.
                </p>

                <!-- Error message -->
                <p v-if="error" class="text-sm text-red-500 bg-red-50 rounded-xl px-4 py-2.5 mb-4">
                    {{ error }}
                </p>

                <button
                    @click="resend"
                    :disabled="loading || cooldown > 0 || (!email && !emailInput)"
                    class="w-full bg-indigo-600 text-white font-medium py-2.5 rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="cooldown > 0">Resend in {{ cooldown }}s</span>
                    <span v-else-if="loading">Sending…</span>
                    <span v-else>Resend verification email</span>
                </button>
            </div>

            <p class="text-center text-sm text-gray-500 mt-6">
                <RouterLink to="/login" class="text-indigo-600 font-medium hover:text-indigo-700">Back to sign in</RouterLink>
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const email = route.query.email ?? ''
const emailInput = ref('')
const loading = ref(false)
const resent = ref(false)
const error = ref('')
const cooldown = ref(0)

let timer = null

async function resend() {
    loading.value = true
    resent.value = false
    error.value = ''
    try {
        await axios.post('/api/email/resend', { email: email || emailInput.value })
        resent.value = true
        cooldown.value = 60
        timer = setInterval(() => {
            cooldown.value--
            if (cooldown.value <= 0) clearInterval(timer)
        }, 1000)
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Something went wrong.'
    } finally {
        loading.value = false
    }
}

onUnmounted(() => clearInterval(timer))
</script>
