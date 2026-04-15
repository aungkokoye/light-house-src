<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-6xl mx-auto">

            <!-- Loading -->
            <LoadingSpinner v-if="loading" />

            <template v-else>
                <!-- Header -->
                <div class="mb-10">
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-sm text-gray-500 mt-1">Welcome back, {{ user?.name }}.</p>
                </div>

                <!-- Icon grid -->
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Profile — all users -->
                    <RouterLink to="/profile"
                        class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-indigo-100 transition-all">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-gray-900">Profile</p>
                            <p class="text-xs text-gray-400 mt-0.5">Manage your account</p>
                        </div>
                    </RouterLink>

                    <!-- Roles — admin only -->
                    <RouterLink v-if="isAdmin" to="/admin/roles"
                        class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-indigo-100 transition-all">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-gray-900">Roles</p>
                            <p class="text-xs text-gray-400 mt-0.5">View roles & permissions</p>
                        </div>
                    </RouterLink>

                    <!-- Sites — admin only -->
                    <RouterLink v-if="isAdmin" to="/admin/sites"
                        class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-indigo-100 transition-all">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                            <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-gray-900">Sites</p>
                            <p class="text-xs text-gray-400 mt-0.5">Manage printing sites</p>
                        </div>
                    </RouterLink>

                    <!-- Users — admin only -->
                    <RouterLink v-if="isAdmin" to="/admin/users"
                        class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-indigo-100 transition-all">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-gray-900">Users</p>
                            <p class="text-xs text-gray-400 mt-0.5">Manage all users</p>
                        </div>
                    </RouterLink>
                </div>
            </template>

        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AppHeader from '../components/AppHeader.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import { useAuth } from '../composables/useAuth'

const { requireAuth } = useAuth()
const loading = ref(true)
const user = ref(null)

const isAdmin = computed(() => user.value?.roles?.some(r => r.name === 'admin'))

onMounted(async () => {
    await requireAuth(user, loading)
})
</script>