<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 flex items-center justify-center px-4">
        <div class="w-full max-w-md">

            <!-- Logo -->
            <div class="flex items-center justify-center gap-2 mb-8">
                <RouterLink to="/" class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 1 1 0 10A5 5 0 0 1 12 7z" />
                        </svg>
                    </div>
                    <span class="font-semibold text-gray-900 text-xl">Light House</span>
                </RouterLink>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-100 border border-gray-400 p-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Create an account</h1>
                    <p class="text-sm text-gray-500 mt-1">Get started with Light House today</p>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Your name"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-400 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                        <p v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name[0] }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="you@example.com"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-400 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                        <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email[0] }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <input
                            v-model="form.password"
                            type="password"
                            placeholder="Min. 8 characters"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-400 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                        <p v-if="errors.password" class="text-xs text-red-500 mt-1">{{ errors.password[0] }}</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm password</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="Repeat your password"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-400 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                    </div>

                    <!-- Captcha -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Security code</label>
                        <div class="flex items-center gap-2 mb-2">
                            <img
                                :src="captchaUrl"
                                alt="captcha"
                                class="rounded-lg border border-gray-400 h-12"
                            />
                            <button
                                type="button"
                                @click="refreshCaptcha"
                                class="p-2 rounded-lg border border-gray-400 text-gray-500 hover:text-indigo-600 hover:border-indigo-300 transition-colors"
                                title="Refresh captcha"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>
                        <input
                            v-model="form.captcha"
                            type="text"
                            placeholder="Type the code above"
                            autocomplete="off"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-400 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            :class="{ 'border-red-400 focus:ring-red-400': errors.captcha }"
                        />
                        <p v-if="errors.captcha" class="text-xs text-red-500 mt-1">{{ errors.captcha[0] }}</p>
                    </div>

                    <!-- General error -->
                    <p v-if="errors.message" class="text-sm text-red-500">{{ errors.message }}</p>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full bg-indigo-600 text-white font-medium py-2.5 rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed mt-2"
                    >
                        <span v-if="loading" class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                            </svg>
                            Creating account…
                        </span>
                        <span v-else>Create account</span>
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-gray-500 mt-6">
                Already have an account?
                <RouterLink to="/login" class="text-indigo-600 font-medium hover:text-indigo-700">Sign in</RouterLink>
            </p>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const loading = ref(false)
const errors = ref({})

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    captcha: '',
})

function buildCaptchaUrl() {
    return `/captcha?t=${Date.now()}`
}
const captchaUrl = ref(buildCaptchaUrl())

function refreshCaptcha() {
    form.captcha = ''
    delete errors.value.captcha
    captchaUrl.value = buildCaptchaUrl()
}

async function handleSubmit() {
    loading.value = true
    errors.value = {}
    try {
        await axios.post('/api/register', form)
        router.push({ path: '/verify-email', query: { email: form.email } })
    } catch (e) {
        errors.value = e.response?.data?.errors ?? {}
        if (errors.value.captcha) {
            refreshCaptcha()
        }
        if (!Object.keys(errors.value).length) {
            errors.value = { message: 'We could not process your request. Please try again.' }
        }
    } finally {
        loading.value = false
    }
}
</script>
