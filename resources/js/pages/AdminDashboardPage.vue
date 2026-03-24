<template>
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
                <RouterLink to="/admin" class="text-indigo-600 font-medium">Admin</RouterLink>
                <RouterLink to="/profile" class="text-gray-500 hover:text-gray-900 transition-colors">Profile</RouterLink>
                <button @click="logout" class="text-gray-500 hover:text-gray-900 transition-colors">Log out</button>
            </nav>
        </div>
    </header>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-6xl mx-auto">

            <!-- Loading -->
            <div v-if="loading" class="flex items-center justify-center py-24">
                <svg class="w-6 h-6 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                </svg>
            </div>

            <template v-else>
                <!-- Header -->
                <div class="mb-8">
                    <span class="inline-block text-xs font-semibold tracking-widest text-indigo-600 uppercase bg-indigo-50 px-3 py-1 rounded-full mb-3">Admin</span>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-sm text-gray-500 mt-1">Welcome back, {{ user?.name }}.</p>
                </div>

                <!-- Stats -->
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div v-for="stat in stats" :key="stat.label" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <p class="text-xs text-gray-400 mb-1">{{ stat.label }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stat.value }}</p>
                        <p class="text-xs font-medium mt-1" :class="stat.positive ? 'text-green-500' : 'text-red-500'">{{ stat.change }}</p>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-900">Users</h2>
                        <span class="text-xs text-gray-400">{{ users.length }} total</span>
                    </div>

                    <div v-if="usersLoading" class="flex items-center justify-center py-12">
                        <svg class="w-5 h-5 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                        </svg>
                    </div>

                    <div v-else-if="users.length === 0" class="px-6 py-12 text-center text-sm text-gray-400">
                        No users found.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-50">
                                    <th class="px-6 py-3 font-medium">Name</th>
                                    <th class="px-6 py-3 font-medium">Email</th>
                                    <th class="px-6 py-3 font-medium">Role</th>
                                    <th class="px-6 py-3 font-medium">Verified</th>
                                    <th class="px-6 py-3 font-medium">Sign-in</th>
                                    <th class="px-6 py-3 font-medium">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="u in users" :key="u.id" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-3.5 font-medium text-gray-900">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-bold shrink-0">
                                                {{ u.name.charAt(0).toUpperCase() }}
                                            </div>
                                            {{ u.name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5 text-gray-500">{{ u.email }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded-full"
                                            :class="u.roles?.[0]?.name === 'admin' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600'">
                                            {{ u.roles?.[0]?.name ?? 'user' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex items-center gap-1 text-xs font-medium"
                                            :class="u.email_verified_at ? 'text-green-600' : 'text-yellow-600'">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="u.email_verified_at ? 'bg-green-500' : 'bg-yellow-500'"></span>
                                            {{ u.email_verified_at ? 'Verified' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-gray-500">
                                        <span v-if="u.google_id" class="inline-flex items-center gap-1 text-xs">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24">
                                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                            </svg>
                                            Google
                                        </span>
                                        <span v-else class="text-xs text-gray-400">Email</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-gray-400 text-xs">{{ formatDate(u.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const loading = ref(true)
const usersLoading = ref(true)
const user = ref(null)
const users = ref([])

const stats = [
    { label: 'Total users', value: '—', change: '', positive: true },
    { label: 'Verified', value: '—', change: '', positive: true },
    { label: 'Google sign-in', value: '—', change: '', positive: true },
    { label: 'Admins', value: '—', change: '', positive: true },
]

function formatDate(date) {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}

async function logout() {
    await axios.post('/api/logout')
    localStorage.removeItem('token')
    router.push('/login')
}

onMounted(async () => {
    if (!localStorage.getItem('token')) {
        router.push('/login')
        return
    }
    try {
        const { data } = await axios.get('/api/me')
        const isAdmin = data.roles?.some(r => r.name === 'admin')
        if (!isAdmin) {
            router.push('/')
            return
        }
        user.value = data
    } catch {
        router.push('/login')
        return
    } finally {
        loading.value = false
    }

    try {
        usersLoading.value = true
        const { data } = await axios.get('/api/admin/users')
        users.value = data

        stats[0].value = data.length
        stats[1].value = data.filter(u => u.email_verified_at).length
        stats[2].value = data.filter(u => u.google_id).length
        stats[3].value = data.filter(u => u.roles?.some(r => r.name === 'admin')).length
    } catch {
        // leave empty
    } finally {
        usersLoading.value = false
    }
})
</script>