<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-6xl mx-auto">

            <LoadingSpinner v-if="loading" />

            <template v-else>
                <div class="mb-6 flex items-center gap-3">
                    <RouterLink to="/dashboard" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900">Staff Positions</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manage all staff positions.</p>
                    </div>
                    <RouterLink v-if="can('create')" to="/admin/staff-positions/create"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New Position
                    </RouterLink>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- Search -->
                        <div class="relative lg:col-span-2">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                            </svg>
                            <input v-model="filters.search" @input="debouncedFetch"
                                type="text" placeholder="Search by name…"
                                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50" />
                        </div>

                        <!-- Created from -->
                        <input v-model="filters.created_from" @change="fetchPositions(1)"
                            type="date"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 text-gray-600" />

                        <!-- Created to -->
                        <input v-model="filters.created_to" @change="fetchPositions(1)"
                            type="date"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 text-gray-600" />
                    </div>

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
                        <h2 class="font-semibold text-gray-900">All Staff Positions</h2>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400">{{ meta.total ?? 0 }} total</span>
                            <select v-model="perPage" @change="fetchPositions(1)"
                                class="text-xs border border-gray-300 rounded-lg px-2 py-1 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 text-gray-600">
                                <option :value="10">10 / page</option>
                                <option :value="20">20 / page</option>
                                <option :value="30">30 / page</option>
                                <option :value="40">40 / page</option>
                                <option :value="50">50 / page</option>
                            </select>
                        </div>
                    </div>

                    <div v-if="listLoading" class="flex items-center justify-center py-12">
                        <svg class="w-5 h-5 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                        </svg>
                    </div>

                    <div v-else-if="positions.length === 0" class="px-6 py-12 text-center text-sm text-gray-400">
                        No staff positions found.
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
                                                    viewBox="0 0 10 6" fill="currentColor"><path d="M5 0L10 6H0L5 0Z"/></svg>
                                                <svg class="w-2.5 h-2.5 transition-colors"
                                                    :class="sortBy === col.key && sortDir === 'desc' ? 'text-indigo-500' : 'text-gray-200'"
                                                    viewBox="0 0 10 6" fill="currentColor"><path d="M5 6L0 0H10L5 6Z"/></svg>
                                            </span>
                                        </span>
                                    </th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="position in positions" :key="position.id" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-3.5 text-xs font-mono text-gray-400">{{ position.id }}</td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ position.name }}</td>
                                    <td class="px-6 py-3.5 text-xs text-gray-400">{{ formatDate(position.created_at) }}</td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-1">
                                            <RouterLink v-if="can('view')" :to="`/admin/staff-positions/${position.id}`"
                                                class="p-1.5 text-indigo-600 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                                </svg>
                                            </RouterLink>
                                            <RouterLink v-if="can('edit')" :to="`/admin/staff-positions/${position.id}/edit`"
                                                class="p-1.5 text-indigo-600 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931z" />
                                                </svg>
                                            </RouterLink>
                                            <button v-if="can('delete')" @click="confirmDelete(position)"
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
                            <button @click="fetchPositions(currentPage - 1)" :disabled="currentPage === 1"
                                class="px-3 py-1.5 text-xs rounded-lg border border-gray-100 text-gray-500 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                Prev
                            </button>
                            <template v-for="page in visiblePages" :key="page">
                                <span v-if="page === '...'" class="px-2 text-xs text-gray-300">…</span>
                                <button v-else @click="fetchPositions(page)"
                                    class="px-3 py-1.5 text-xs rounded-lg border transition-colors"
                                    :class="page === currentPage ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-100 text-gray-500 hover:bg-gray-50'">
                                    {{ page }}
                                </button>
                            </template>
                            <button @click="fetchPositions(currentPage + 1)" :disabled="currentPage === meta.last_page"
                                class="px-3 py-1.5 text-xs rounded-lg border border-gray-100 text-gray-500 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <DeleteModal :show="!!deleteTarget" @confirm="deletePosition" @cancel="deleteTarget = null"
        title="Delete Staff Position" message="Are you sure you want to delete this staff position?" />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import DeleteModal from '../../../components/DeleteModal.vue'
import { useAdminGuard } from '../../../composables/useAdminGuard'
import { useFormatDate } from '../../../composables/useFormatDate'

const { requireAdmin } = useAdminGuard()
const { formatDate } = useFormatDate()
const loading = ref(true)
const listLoading = ref(false)
const positions = ref([])
const meta = ref({})
const currentPage = ref(1)
const perPage = ref(20)
const sortBy = ref('name')
const sortDir = ref('asc')
const deleteTarget = ref(null)
const deleting = ref(false)
const myPermissions = ref([])

function can(permission) {
    return myPermissions.value.includes('super') || myPermissions.value.includes(permission)
}

const filters = ref({ search: '', created_from: '', created_to: '' })

const STORAGE_KEY = 'admin_staff_positions_state'

function saveState() {
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
        filters: filters.value,
        page: currentPage.value,
        perPage: perPage.value,
        sortBy: sortBy.value,
        sortDir: sortDir.value,
    }))
}

function restoreState() {
    const saved = sessionStorage.getItem(STORAGE_KEY)
    if (!saved) return false
    const state = JSON.parse(saved)
    filters.value  = state.filters  ?? filters.value
    currentPage.value = state.page  ?? 1
    perPage.value  = state.perPage  ?? 20
    sortBy.value   = state.sortBy   ?? 'name'
    sortDir.value  = state.sortDir  ?? 'asc'
    return true
}

const columns = [
    { key: 'id',         label: 'ID',         sortable: true },
    { key: 'name',       label: 'Name',        sortable: true },
    { key: 'created_at', label: 'Created At',  sortable: true },
]

const hasActiveFilters = computed(() => Object.values(filters.value).some(v => v !== ''))

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

function resetFilters() {
    filters.value = { search: '', created_from: '', created_to: '' }
    sessionStorage.removeItem(STORAGE_KEY)
    fetchPositions(1)
}

let searchTimer = null
function debouncedFetch() {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => fetchPositions(1), 350)
}

function toggleSort(column) {
    if (sortBy.value === column) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = column
        sortDir.value = 'asc'
    }
    fetchPositions(1)
}

function confirmDelete(position) {
    deleteTarget.value = position
}

async function deletePosition() {
    deleting.value = true
    try {
        await axios.delete(`/api/admin/staff-positions/${deleteTarget.value.id}`)
        deleteTarget.value = null
        fetchPositions(currentPage.value)
    } catch (e) {
        console.error('delete error', e?.response?.status)
    } finally {
        deleting.value = false
    }
}

async function fetchPositions(page = 1) {
    listLoading.value = true
    currentPage.value = page
    saveState()
    try {
        const params = { page, per_page: perPage.value, sort_by: sortBy.value, sort_dir: sortDir.value, ...filters.value }
        Object.keys(params).forEach(k => params[k] === '' && delete params[k])
        const { data } = await axios.get('/api/admin/staff-positions', { params })
        positions.value = data.data
        meta.value = { total: data.total, from: data.from, to: data.to, last_page: data.last_page }
        currentPage.value = data.current_page
    } catch (e) {
        console.error('fetchPositions error', e?.response?.status)
    } finally {
        listLoading.value = false
    }
}

onMounted(async () => {
    const me = await requireAdmin()
    if (!me) return
    myPermissions.value = me.permissions?.map(p => p.name) ?? []
    loading.value = false
    const restored = restoreState()
    fetchPositions(restored ? currentPage.value : 1)
})
</script>
