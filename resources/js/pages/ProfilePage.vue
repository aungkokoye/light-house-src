<template>
    <!-- Nav -->
    <header class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <!-- Logo -->
            <RouterLink to="/" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 1 1 0 10A5 5 0 0 1 12 7z" />
                    </svg>
                </div>
                <span class="font-semibold text-gray-900 text-lg">Light House</span>
            </RouterLink>

            <!-- Menu -->
            <nav class="flex items-center gap-6 text-sm">
                <RouterLink to="/profile" class="text-indigo-600 font-medium">Profile</RouterLink>
                <button @click="logout" class="text-gray-500 hover:text-gray-900 transition-colors">Log out</button>
            </nav>
        </div>
    </header>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-2xl mx-auto">

            <!-- Loading -->
            <div v-if="loading" class="flex items-center justify-center py-24">
                <svg class="w-6 h-6 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                </svg>
            </div>

            <template v-else-if="user">
                <!-- Avatar + Name -->
                <div class="flex items-center gap-5 mb-8">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-2xl font-bold shrink-0">
                        {{ initials }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ user.name }}</h1>
                        <p class="text-sm text-gray-500">{{ user.email }}</p>
                    </div>
                </div>

                <!-- Details Card -->
                <div class="bg-white rounded-2xl shadow-xl shadow-gray-100 border border-gray-100 divide-y divide-gray-50">

                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-gray-500">Name</span>
                        <span class="text-sm font-medium text-gray-900">{{ user.name }}</span>
                    </div>

                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-gray-500">Email</span>
                        <span class="text-sm font-medium text-gray-900">{{ user.email }}</span>
                    </div>

                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-gray-500">Email verified</span>
                        <span
                            class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full"
                            :class="user.email_verified_at ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700'"
                        >
                            <span class="w-1.5 h-1.5 rounded-full" :class="user.email_verified_at ? 'bg-green-500' : 'bg-yellow-500'"></span>
                            {{ user.email_verified_at ? 'Verified' : 'Not verified' }}
                        </span>
                    </div>

                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-gray-500">Sign-in method</span>
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-900">
                            <template v-if="user.google_id">
                                <svg class="w-4 h-4" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                Google
                            </template>
                            <template v-else>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0zm0 0v1.5a2.5 2.5 0 0 0 5 0V12a9 9 0 1 0-9 9m4.5-1.206a8.959 8.959 0 0 1-4.5 1.207" />
                                </svg>
                                Email & password
                            </template>
                        </span>
                    </div>

                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-sm text-gray-500">Member since</span>
                        <span class="text-sm font-medium text-gray-900">{{ joinedDate }}</span>
                    </div>

                </div>

                <!-- Change Password -->
                <div class="mt-6 bg-white rounded-2xl shadow-xl shadow-gray-100 border border-gray-100 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-1">Change password</h2>

                    <!-- Google account notice -->
                    <template v-if="user.google_id">
                        <p class="text-sm text-gray-400">Password cannot be changed for Google-linked accounts.</p>
                    </template>

                    <template v-else>
                        <p class="text-sm text-gray-500 mb-5">Choose a strong password of at least 8 characters.</p>

                        <form @submit.prevent="submitPassword" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Current password</label>
                                <input
                                    v-model="pwForm.current_password"
                                    type="password"
                                    placeholder="••••••••"
                                    required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                />
                                <p v-if="pwErrors.current_password" class="text-xs text-red-500 mt-1">{{ pwErrors.current_password[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">New password</label>
                                <input
                                    v-model="pwForm.password"
                                    type="password"
                                    placeholder="Min. 8 characters"
                                    required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                />
                                <p v-if="pwErrors.password" class="text-xs text-red-500 mt-1">{{ pwErrors.password[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm new password</label>
                                <input
                                    v-model="pwForm.password_confirmation"
                                    type="password"
                                    placeholder="Repeat new password"
                                    required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                />
                            </div>

                            <p v-if="pwSuccess" class="text-sm text-green-600 bg-green-50 rounded-xl px-4 py-2.5">Password updated successfully.</p>
                            <p v-if="pwErrors.message" class="text-sm text-red-500 bg-red-50 rounded-xl px-4 py-2.5">{{ pwErrors.message }}</p>

                            <button
                                type="submit"
                                :disabled="pwLoading"
                                class="w-full bg-indigo-600 text-white font-medium py-2.5 rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="pwLoading" class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                                    </svg>
                                    Updating…
                                </span>
                                <span v-else>Update password</span>
                            </button>
                        </form>
                    </template>
                </div>

            </template>

            <!-- Error -->
            <div v-else class="text-center py-24 text-gray-500 text-sm">
                Could not load profile. <RouterLink to="/" class="text-indigo-600 font-medium">Go home</RouterLink>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const loading = ref(true)
const user = ref(null)

async function logout() {
    await axios.post('/api/logout')
    localStorage.removeItem('token')
    router.push('/login')
}

const pwForm = reactive({ current_password: '', password: '', password_confirmation: '' })
const pwLoading = ref(false)
const pwSuccess = ref(false)
const pwErrors = ref({})

async function submitPassword() {
    pwLoading.value = true
    pwSuccess.value = false
    pwErrors.value = {}
    try {
        await axios.put('/api/password', pwForm)
        pwSuccess.value = true
        pwForm.current_password = ''
        pwForm.password = ''
        pwForm.password_confirmation = ''
    } catch (e) {
        pwErrors.value = e.response?.data?.errors ?? {}
        if (!Object.keys(pwErrors.value).length) {
            pwErrors.value = { message: e.response?.data?.message ?? 'Something went wrong.' }
        }
    } finally {
        pwLoading.value = false
    }
}

const initials = computed(() => {
    if (!user.value?.name) return '?'
    return user.value.name
        .split(' ')
        .map(w => w[0])
        .slice(0, 2)
        .join('')
        .toUpperCase()
})

const joinedDate = computed(() => {
    if (!user.value?.created_at) return '—'
    return new Date(user.value.created_at).toLocaleDateString('en-GB', {
        day: 'numeric', month: 'long', year: 'numeric',
    })
})

onMounted(async () => {
    if (!localStorage.getItem('token')) {
        router.push('/login')
        return
    }
    try {
        const { data } = await axios.get('/api/me')
        user.value = data
    } catch {
        router.push('/login')
    } finally {
        loading.value = false
    }
})
</script>
