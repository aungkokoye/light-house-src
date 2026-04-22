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
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Audit Logs</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Track all system activity.</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                            </svg>
                            <input v-model="search" @input="debouncedFetch"
                                type="text" placeholder="Search by user email…"
                                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50" />
                        </div>
                        <select v-model="eventFilter" @change="fetchLogs(1)"
                            class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 text-gray-600">
                            <option value="">All Events</option>
                            <option v-for="e in availableEvents" :key="e" :value="e">{{ eventLabel(e) }}</option>
                        </select>
                    </div>
                    <div v-if="hasActiveFilters" class="mt-3 flex items-center justify-between">
                        <p class="text-xs text-gray-400">Filters applied</p>
                        <button @click="resetFilter" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium transition-colors">Clear all</button>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-900">All Logs</h2>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400">{{ meta.total ?? 0 }} total</span>
                            <select v-model="perPage" @change="fetchLogs(1)"
                                class="text-xs border border-gray-300 rounded-lg px-2 py-1 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200 text-gray-600">
                                <option :value="10">10 / page</option>
                                <option :value="20">20 / page</option>
                                <option :value="30">30 / page</option>
                                <option :value="40">40 / page</option>
                                <option :value="50">50 / page</option>
                            </select>
                        </div>
                    </div>

                    <div v-if="tableLoading" class="flex items-center justify-center py-12">
                        <svg class="w-5 h-5 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                        </svg>
                    </div>

                    <div v-else-if="logs.length === 0" class="px-6 py-12 text-center text-sm text-gray-400">
                        No audit logs found.
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
                                <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-3.5 text-xs font-mono text-gray-400">{{ log.id }}</td>
                                    <td class="px-6 py-3.5 text-gray-600">{{ log.user_email ?? '—' }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="eventClass(log.event)">
                                            {{ log.event }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-gray-400 font-mono">{{ shortType(log.auditable_type) }}</td>
                                    <td class="px-6 py-3.5 text-xs font-mono text-gray-400">{{ log.auditable_id ?? '—' }}</td>
                                    <td class="px-6 py-3.5 text-xs font-mono text-gray-400">{{ log.ip_address ?? '—' }}</td>
                                    <td class="px-6 py-3.5 text-xs text-gray-400">{{ formatDate(log.created_at, true) }}</td>
                                    <td class="px-6 py-3.5">
                                        <RouterLink :to="`/admin/audit-logs/${log.id}`"
                                            class="p-1.5 text-indigo-600 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors inline-flex">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                            </svg>
                                        </RouterLink>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="meta.last_page > 1" class="px-6 py-4 border-t border-gray-50 flex items-center justify-between">
                        <p class="text-xs text-gray-400">Showing {{ meta.from }}–{{ meta.to }} of {{ meta.total }}</p>
                        <div class="flex items-center gap-1">
                            <button @click="fetchLogs(currentPage - 1)" :disabled="currentPage === 1"
                                class="px-3 py-1.5 text-xs rounded-lg border border-gray-100 text-gray-500 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                Prev
                            </button>
                            <template v-for="page in visiblePages" :key="page">
                                <span v-if="page === '...'" class="px-2 text-xs text-gray-300">…</span>
                                <button v-else @click="fetchLogs(page)"
                                    class="px-3 py-1.5 text-xs rounded-lg border transition-colors"
                                    :class="page === currentPage ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-100 text-gray-500 hover:bg-gray-50'">
                                    {{ page }}
                                </button>
                            </template>
                            <button @click="fetchLogs(currentPage + 1)" :disabled="currentPage === meta.last_page"
                                class="px-3 py-1.5 text-xs rounded-lg border border-gray-100 text-gray-500 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import { useAdminGuard } from '../../../composables/useAdminGuard'
import { useFormatDate } from '../../../composables/useFormatDate'

const { requireAdmin } = useAdminGuard()
const { formatDate } = useFormatDate()
const loading = ref(true)
const tableLoading = ref(false)
const logs = ref([])
const meta = ref({})
const currentPage = ref(1)
const perPage = ref(20)
const sortBy = ref('created_at')
const sortDir = ref('desc')
const search = ref('')
const eventFilter = ref('')
const availableEvents = ref([])

const STORAGE_KEY = 'admin_audit_logs_state'

const hasActiveFilters = computed(() => search.value !== '' || eventFilter.value !== '')

function saveState() {
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
        search: search.value, event: eventFilter.value, page: currentPage.value,
        perPage: perPage.value, sortBy: sortBy.value, sortDir: sortDir.value,
    }))
}

function restoreState() {
    const saved = sessionStorage.getItem(STORAGE_KEY)
    if (!saved) return false
    const s = JSON.parse(saved)
    search.value      = s.search   ?? ''
    eventFilter.value = s.event    ?? ''
    currentPage.value = s.page     ?? 1
    perPage.value     = s.perPage  ?? 20
    sortBy.value      = s.sortBy   ?? 'created_at'
    sortDir.value     = s.sortDir  ?? 'desc'
    return true
}

const columns = [
    { key: 'id',           label: 'ID',         sortable: true  },
    { key: 'user_email',   label: 'User Email',  sortable: true  },
    { key: 'event',        label: 'Event',       sortable: false },
    { key: 'type',         label: 'Model',       sortable: false },
    { key: 'auditable_id', label: 'Model ID',    sortable: false },
    { key: 'ip_address',   label: 'IP Address',  sortable: true  },
    { key: 'created_at',   label: 'Date',        sortable: true  },
]

const visiblePages = computed(() => {
    const total = meta.value.last_page ?? 1
    const cur = currentPage.value
    const pages = []
    if (total <= 7) { for (let i = 1; i <= total; i++) pages.push(i); return pages }
    pages.push(1)
    if (cur > 3) pages.push('...')
    for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) pages.push(i)
    if (cur < total - 2) pages.push('...')
    pages.push(total)
    return pages
})

function eventLabel(event) {
    return event.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

function eventClass(event) {
    const map = {
        created:          'bg-emerald-50 text-emerald-700',
        updated:          'bg-sky-50 text-sky-700',
        deleted:          'bg-red-50 text-red-600',
        login:            'bg-indigo-50 text-indigo-700',
        logout:           'bg-gray-100 text-gray-600',
        password_changed: 'bg-amber-50 text-amber-700',
    }
    return map[event] ?? 'bg-gray-100 text-gray-600'
}

function shortType(type) {
    return type ? type.split('\\').pop() : '—'
}

function resetFilter() {
    search.value = ''
    eventFilter.value = ''
    sessionStorage.removeItem(STORAGE_KEY)
    fetchLogs(1)
}

let searchTimer = null
function debouncedFetch() {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => fetchLogs(1), 350)
}

function toggleSort(column) {
    if (sortBy.value === column) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = column
        sortDir.value = 'desc'
    }
    fetchLogs(1)
}

async function fetchLogs(page = 1) {
    tableLoading.value = true
    currentPage.value = page
    saveState()
    try {
        const params = { page, per_page: perPage.value, sort_by: sortBy.value, sort_dir: sortDir.value }
        if (search.value) params.search = search.value
        if (eventFilter.value) params.event = eventFilter.value
        const { data } = await axios.get('/api/admin/audit-logs', { params })
        logs.value = data.data
        meta.value = { total: data.total, from: data.from, to: data.to, last_page: data.last_page }
        currentPage.value = data.current_page
    } catch (e) {
        console.error('fetchLogs error', e?.response?.status)
    } finally {
        tableLoading.value = false
    }
}

onMounted(async () => {
    const me = await requireAdmin()
    if (!me) return
    loading.value = false
    const [, restored] = await Promise.all([
        axios.get('/api/admin/audit-logs/events').then(({ data }) => { availableEvents.value = data }),
        Promise.resolve(restoreState()),
    ])
    fetchLogs(restored ? currentPage.value : 1)
})
</script>