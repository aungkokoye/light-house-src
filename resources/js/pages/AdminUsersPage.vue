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
                <RouterLink to="/dashboard" class="text-gray-500 hover:text-gray-900 transition-colors">Dashboard</RouterLink>
                <RouterLink to="/profile" class="text-gray-500 hover:text-gray-900 transition-colors">Profile</RouterLink>
                <button @click="logout" class="text-gray-500 hover:text-gray-900 transition-colors">Log out</button>
            </nav>
        </div>
    </header>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-6xl mx-auto">

            <div v-if="loading" class="flex items-center justify-center py-24">
                <svg class="w-6 h-6 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                </svg>
            </div>

            <template v-else>
                <!-- Header -->
                <div class="mb-6 flex items-center gap-3">
                    <RouterLink to="/dashboard" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900">Users</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manage all registered users.</p>
                    </div>
                    <RouterLink to="/admin/users/create"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New User
                    </RouterLink>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <!-- Search -->
                        <div class="relative lg:col-span-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                            </svg>
                            <input v-model="filters.search" @input="debouncedFetch"
                                type="text" placeholder="Search name or email…"
                                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50" />
                        </div>

                        <!-- Role -->
                        <select v-model="filters.role" @change="fetchUsers(1)"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 text-gray-600">
                            <option value="">All roles</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>

                        <!-- Activated -->
                        <select v-model="filters.activated" @change="fetchUsers(1)"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 text-gray-600">
                            <option value="">All status</option>
                            <option value="true">Activated</option>
                            <option value="false">Deactivated</option>
                        </select>

                        <!-- Email verified -->
                        <select v-model="filters.email_verified" @change="fetchUsers(1)"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 text-gray-600">
                            <option value="">All verification</option>
                            <option value="true">Verified</option>
                            <option value="false">Unverified</option>
                        </select>

                        <!-- Updated from -->
                        <input v-model="filters.updated_from" @change="fetchUsers(1)"
                            type="date"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 text-gray-600" />

                        <!-- Updated to -->
                        <input v-model="filters.updated_to" @change="fetchUsers(1)"
                            type="date"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 text-gray-600" />
                    </div>

                    <!-- Active filters / reset -->
                    <div v-if="hasActiveFilters" class="mt-3 flex items-center justify-between">
                        <p class="text-xs text-gray-400">Filters applied</p>
                        <button @click="resetFilters" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
                            Clear all
                        </button>
                    </div>
                </div>

                <!-- Table card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-900">All Users</h2>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400">{{ meta.total ?? 0 }} total</span>
                            <!-- Per page -->
                            <select v-model="perPage" @change="fetchUsers(1)"
                                class="text-xs border border-gray-300 rounded-lg px-2 py-1 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 text-gray-600">
                                <option :value="10">10 / page</option>
                                <option :value="20">20 / page</option>
                                <option :value="30">30 / page</option>
                                <option :value="40">40 / page</option>
                                <option :value="50">50 / page</option>
                            </select>
                        </div>
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
                                    <th v-for="col in columns" :key="col.key"
                                        class="px-6 py-3 font-medium"
                                        :class="col.sortable ? 'cursor-pointer select-none hover:text-gray-600' : ''"
                                        @click="col.sortable && toggleSort(col.key)">
                                        <span class="inline-flex items-center gap-1">
                                            {{ col.label }}
                                            <span v-if="col.sortable" class="inline-flex flex-col leading-none">
                                                <svg class="w-2.5 h-2.5 -mb-0.5 transition-colors"
                                                    :class="sortBy === col.key && sortDir === 'asc' ? 'text-indigo-500' : 'text-gray-200'"
                                                    viewBox="0 0 10 6" fill="currentColor">
                                                    <path d="M5 0L10 6H0L5 0Z"/>
                                                </svg>
                                                <svg class="w-2.5 h-2.5 transition-colors"
                                                    :class="sortBy === col.key && sortDir === 'desc' ? 'text-indigo-500' : 'text-gray-200'"
                                                    viewBox="0 0 10 6" fill="currentColor">
                                                    <path d="M5 6L0 0H10L5 6Z"/>
                                                </svg>
                                            </span>
                                        </span>
                                    </th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="u in users" :key="u.id" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-3.5 text-gray-400 text-xs font-mono">{{ u.id }}</td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ u.name }}</td>
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
                                            {{ u.email_verified_at ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex items-center gap-1 text-xs font-medium"
                                            :class="u.activated ? 'text-green-600' : 'text-red-500'">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="u.activated ? 'bg-green-500' : 'bg-red-400'"></span>
                                            {{ u.activated ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-gray-400">
                                        <span>{{ formatDate(u.created_at) }}</span>
                                        <span v-if="u.updated_at !== u.created_at" class="block text-gray-300">
                                            {{ formatDate(u.updated_at) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-1">
                                            <RouterLink :to="`/admin/users/${u.id}`"
                                                class="p-1.5 text-indigo-600 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                                </svg>
                                            </RouterLink>
                                            <RouterLink :to="`/admin/users/${u.id}/edit`"
                                                class="p-1.5 text-indigo-600 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                                </svg>
                                            </RouterLink>
                                            <button @click="confirmDelete(u)"
                                                class="p-1.5 text-red-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="meta.last_page > 1" class="px-6 py-4 border-t border-gray-50 flex items-center justify-between">
                        <p class="text-xs text-gray-400">
                            Showing {{ meta.from }}–{{ meta.to }} of {{ meta.total }}
                        </p>
                        <div class="flex items-center gap-1">
                            <button @click="fetchUsers(currentPage - 1)" :disabled="currentPage === 1"
                                class="px-3 py-1.5 text-xs rounded-lg border border-gray-100 text-gray-500 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                Prev
                            </button>
                            <template v-for="page in visiblePages" :key="page">
                                <span v-if="page === '...'" class="px-2 text-xs text-gray-300">…</span>
                                <button v-else @click="fetchUsers(page)"
                                    class="px-3 py-1.5 text-xs rounded-lg border transition-colors"
                                    :class="page === currentPage ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-100 text-gray-500 hover:bg-gray-50'">
                                    {{ page }}
                                </button>
                            </template>
                            <button @click="fetchUsers(currentPage + 1)" :disabled="currentPage === meta.last_page"
                                class="px-3 py-1.5 text-xs rounded-lg border border-gray-100 text-gray-500 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Delete confirmation modal -->
    <Teleport to="body">
        <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="deleteTarget = null"></div>

            <!-- Dialog -->
            <div class="relative bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Delete User</h3>
                        <p class="text-xs text-gray-400 mt-0.5">This action cannot be undone.</p>
                    </div>
                </div>

                <p class="text-sm text-gray-600 mb-6">
                    You are about to delete <span class="font-semibold text-gray-900">{{ deleteTarget.name }}</span>
                    (<span class="text-gray-500">{{ deleteTarget.email }}</span>).
                    Are you sure you want to continue?
                </p>

                <div class="flex items-center justify-end gap-3">
                    <button @click="deleteTarget = null"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button @click="deleteUser" :disabled="deleting"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        {{ deleting ? 'Deleting…' : 'Yes, delete' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const loading = ref(true)
const usersLoading = ref(false)
const users = ref([])
const currentPage = ref(1)
const meta = ref({})
const perPage = ref(20)
const deleteTarget = ref(null)
const deleting = ref(false)
const sortBy = ref('id')
const sortDir = ref('asc')

const columns = [
    { key: 'id',               label: 'ID',             sortable: true  },
    { key: 'name',             label: 'Name',           sortable: true  },
    { key: 'email',            label: 'Email',          sortable: true  },
    { key: 'role',             label: 'Role',           sortable: false },
    { key: 'email_verified_at',label: 'Email Verified', sortable: true  },
    { key: 'activated',        label: 'Activated',      sortable: true  },
    { key: 'updated_at',       label: 'Created / Updated', sortable: true },
]

const filters = ref({
    search: '',
    role: '',
    activated: '',
    email_verified: '',
    updated_from: '',
    updated_to: '',
})

const hasActiveFilters = computed(() =>
    Object.values(filters.value).some(v => v !== '')
)

const visiblePages = computed(() => {
    const total = meta.value.last_page ?? 1
    const current = currentPage.value
    const pages = []
    if (total <= 7) {
        for (let i = 1; i <= total; i++) pages.push(i)
        return pages
    }
    pages.push(1)
    if (current > 3) pages.push('...')
    for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) pages.push(i)
    if (current < total - 2) pages.push('...')
    pages.push(total)
    return pages
})

function formatDate(date) {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}

async function logout() {
    try { await axios.post('/api/logout') } finally {
        localStorage.removeItem('token')
        router.push('/login')
    }
}

function resetFilters() {
    filters.value = { search: '', role: '', activated: '', email_verified: '', updated_from: '', updated_to: '' }
    fetchUsers(1)
}

let searchTimer = null
function debouncedFetch() {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => fetchUsers(1), 350)
}

function confirmDelete(user) {
    deleteTarget.value = user
}

async function deleteUser() {
    if (!deleteTarget.value) return
    deleting.value = true
    try {
        await axios.delete(`/api/admin/users/${deleteTarget.value.id}`)
        deleteTarget.value = null
        fetchUsers(currentPage.value)
    } catch (e) {
        console.error('delete error', e?.response?.status)
    } finally {
        deleting.value = false
    }
}

function toggleSort(column) {
    if (sortBy.value === column) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = column
        sortDir.value = 'asc'
    }
    fetchUsers(1)
}

async function fetchUsers(page = 1) {
    usersLoading.value = true
    try {
        const params = { page, per_page: perPage.value, sort_by: sortBy.value, sort_dir: sortDir.value, ...filters.value }
        // strip empty strings
        Object.keys(params).forEach(k => params[k] === '' && delete params[k])
        const { data } = await axios.get('/api/admin/users', { params })
        users.value = data.data
        meta.value = { total: data.total, from: data.from, to: data.to, last_page: data.last_page }
        currentPage.value = data.current_page
    } catch (e) {
        console.error('fetchUsers error', e?.response?.status, e?.response?.data)
    } finally {
        usersLoading.value = false
    }
}

onMounted(async () => {
    if (!localStorage.getItem('token')) { router.push('/login'); return }
    try {
        const { data: me } = await axios.get('/api/me')
        if (!me.roles?.some(r => r.name === 'admin')) { router.replace('/unauthorized'); return }
    } catch {
        router.push('/login'); return
    } finally {
        loading.value = false
    }
    fetchUsers()
})
</script>