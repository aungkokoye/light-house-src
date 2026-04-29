<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 flex items-center justify-center px-4">
        <div class="w-full max-w-md text-center">

<div class="bg-white rounded-2xl shadow-xl shadow-gray-100 border border-gray-300 p-8">
                <!-- Icon -->
                <div class="flex items-center justify-center mx-auto mb-4">
                    <RouterLink to="/" class="flex items-center gap-2.5">
                        <img :src="'/images/logo.png'" alt="Lighthouse Printing Solutions" class="h-14 w-auto" />
                        <div class="flex flex-col leading-tight text-left">
                            <span class="text-base font-bold text-red-600 tracking-tight">LightHouse</span>
                            <span class="text-sm font-medium text-gray-400 tracking-wide">Printing Solutions</span>
                        </div>
                    </RouterLink>
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
