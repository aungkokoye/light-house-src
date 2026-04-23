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
                    <!-- Audit Logs — admin only -->
                    <RouterLink v-if="isAdmin" to="/admin/audit-logs"
                        class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-violet-100 transition-all">
                        <div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center group-hover:bg-violet-100 transition-colors">
                            <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-gray-900">Audit Logs</p>
                            <p class="text-xs text-gray-400 mt-0.5">Track all system activity</p>
                        </div>
                    </RouterLink>

                    <!-- Chat Knowledge — super admin only -->
                    <RouterLink v-if="isSuper" to="/admin/chat-knowledge"
                        class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-teal-100 transition-all">
                        <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center group-hover:bg-teal-100 transition-colors">
                            <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-gray-900">Chat Knowledge</p>
                            <p class="text-xs text-gray-400 mt-0.5">Manage AI chatbot content</p>
                        </div>
                    </RouterLink>

                    <!-- Chat Knowledge Categories — super admin only -->
                    <RouterLink v-if="isSuper" to="/admin/chat-knowledge-categories"
                        class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-teal-100 transition-all">
                        <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center group-hover:bg-teal-100 transition-colors">
                            <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-gray-900">Chat Knowledge Categories</p>
                            <p class="text-xs text-gray-400 mt-0.5">Manage knowledge categories</p>
                        </div>
                    </RouterLink>

                    <!-- Permissions — admin only -->
                    <RouterLink v-if="isAdmin" to="/admin/permissions"
                        class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-rose-100 transition-all">
                        <div class="w-14 h-14 rounded-2xl bg-rose-50 flex items-center justify-center group-hover:bg-rose-100 transition-colors">
                            <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-gray-900">Permissions</p>
                            <p class="text-xs text-gray-400 mt-0.5">Manage permissions</p>
                        </div>
                    </RouterLink>

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

                    <!-- Staff Positions — admin only -->
                    <RouterLink v-if="isAdmin" to="/admin/staff-positions"
                        class="group bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-sky-100 transition-all">
                        <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center group-hover:bg-sky-100 transition-colors">
                            <svg class="w-7 h-7 text-sky-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-gray-900">Staff Positions</p>
                            <p class="text-xs text-gray-400 mt-0.5">Manage staff positions</p>
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
const isSuper = computed(() => isAdmin.value && user.value?.permissions?.some(p => p.name === 'super'))

onMounted(async () => {
    await requireAuth(user, loading)
})
</script>
