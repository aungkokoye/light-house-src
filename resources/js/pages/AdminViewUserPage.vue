m<template>
    <!-- Nav -->
    <header class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <RouterLink to="/" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 1 1 0 10A5 5 0 0 1 12 7z" />
                    </svg>
                </div>
                <span class="font-semibold text-gray-900 text-lg">Light House</span>
            </RouterLink>
            <nav class="flex items-center gap-6 text-sm">
                <RouterLink to="/" class="text-gray-500 hover:text-gray-900 transition-colors">Home</RouterLink>
                <RouterLink to="/dashboard" class="text-gray-500 hover:text-gray-900 transition-colors">Dashboard</RouterLink>
                <RouterLink to="/profile" class="text-gray-500 hover:text-gray-900 transition-colors">Profile</RouterLink>
                <button @click="logout" class="text-gray-500 hover:text-gray-900 transition-colors">Log out</button>
            </nav>
        </div>
    </header>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">

            <div v-if="loading" class="flex items-center justify-center py-24">
                <svg class="w-6 h-6 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                </svg>
            </div>

            <template v-else-if="user">
                <!-- Header -->
                <div class="mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <RouterLink to="/admin/users" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </RouterLink>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
                            <p class="text-sm text-gray-500 mt-0.5">Viewing account for <span class="font-medium text-gray-700">{{ user.name }}</span>.</p>
                        </div>
                    </div>
                    <RouterLink :to="`/admin/users/${user.id}/edit`"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931z" />
                        </svg>
                        Edit
                    </RouterLink>
                </div>

                <!-- Avatar + name -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600 text-2xl font-bold shrink-0">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-lg">{{ user.name }}</p>
                        <p class="text-sm text-gray-400">{{ user.email }}</p>
                    </div>
                    <span class="ml-auto inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full"
                        :class="user.roles?.[0]?.name === 'admin' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600'">
                        {{ user.roles?.[0]?.name ?? 'user' }}
                    </span>
                </div>

                <!-- Details -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">ID</span>
                        <span class="text-sm font-mono text-gray-600">{{ user.id }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Email Verified</span>
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium"
                            :class="user.email_verified_at ? 'text-green-600' : 'text-yellow-600'">
                            <span class="w-1.5 h-1.5 rounded-full" :class="user.email_verified_at ? 'bg-green-500' : 'bg-yellow-500'"></span>
                            {{ user.email_verified_at ? 'Verified' : 'Not verified' }}
                        </span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Activated</span>
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium"
                            :class="user.activated ? 'text-green-600' : 'text-red-500'">
                            <span class="w-1.5 h-1.5 rounded-full" :class="user.activated ? 'bg-green-500' : 'bg-red-400'"></span>
                            {{ user.activated ? 'Active' : 'Deactivated' }}
                        </span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Sign-in Method</span>
                        <span class="text-sm text-gray-600">{{ user.google_id ? 'Google' : 'Email' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Created At</span>
                        <span class="text-sm text-gray-600">{{ formatDate(user.created_at) }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Updated At</span>
                        <span class="text-sm text-gray-600">{{ formatDate(user.updated_at) }}</span>
                    </div>
                    <div v-if="user.email_verified_at" class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Verified At</span>
                        <span class="text-sm text-gray-600">{{ formatDate(user.email_verified_at) }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Created By</span>
                        <div class="text-right">
                            <p class="text-sm text-gray-700 font-medium">{{ user.created_by_name }}</p>
                            <p class="text-xs text-gray-400">{{ user.created_by_email }}</p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const route = useRoute()
const loading = ref(true)
const user = ref(null)

function formatDate(date) {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

async function logout() {
    try { await axios.post('/api/logout') } finally {
        localStorage.removeItem('token')
        router.push('/login')
    }
}

onMounted(async () => {
    if (!localStorage.getItem('token')) { router.push('/login'); return }

    try {
        const { data: me } = await axios.get('/api/me')
        if (!me.roles?.some(r => r.name === 'admin')) { router.replace('/unauthorized'); return }
    } catch {
        router.push('/login'); return
    }

    try {
        const { data } = await axios.get(`/api/admin/users/${route.params.id}`)
        user.value = data
    } catch {
        router.push('/admin/users')
    } finally {
        loading.value = false
    }
})
</script>