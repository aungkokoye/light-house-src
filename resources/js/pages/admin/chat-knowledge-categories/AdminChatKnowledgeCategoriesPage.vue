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
                        <h1 class="text-3xl font-bold text-gray-900">Chat Knowledge Categories</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manage AI chatbot knowledge categories.</p>
                    </div>
                    <RouterLink to="/admin/chat-knowledge-categories/create"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New Category
                    </RouterLink>
                </div>

                <!-- Search -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
                    <div class="relative max-w-sm">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                        </svg>
                        <input v-model="search" @input="debouncedFetch" type="text" placeholder="Search categories…"
                            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50" />
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-900">All Categories</h2>
                        <span class="text-xs text-gray-400">{{ meta.total ?? 0 }} total</span>
                    </div>

                    <div v-if="tableLoading" class="flex items-center justify-center py-12">
                        <svg class="w-5 h-5 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                        </svg>
                    </div>

                    <div v-else-if="fetchError" class="px-6 py-12 text-center text-sm text-red-400">{{ fetchError }}</div>
                    <div v-else-if="rows.length === 0" class="px-6 py-12 text-center text-sm text-gray-400">No categories found.</div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-50">
                                    <th class="px-6 py-3 font-medium cursor-pointer select-none hover:text-gray-600" @click="toggleSort('id')">
                                        <span class="inline-flex items-center gap-1">ID
                                            <span class="inline-flex flex-col leading-none">
                                                <svg class="w-2.5 h-2.5 -mb-0.5" :class="sortBy === 'id' && sortDir === 'asc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 0L10 6H0L5 0Z"/></svg>
                                                <svg class="w-2.5 h-2.5" :class="sortBy === 'id' && sortDir === 'desc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 6L0 0H10L5 6Z"/></svg>
                                            </span>
                                        </span>
                                    </th>
                                    <th class="px-6 py-3 font-medium cursor-pointer select-none hover:text-gray-600" @click="toggleSort('name')">
                                        <span class="inline-flex items-center gap-1">Name
                                            <span class="inline-flex flex-col leading-none">
                                                <svg class="w-2.5 h-2.5 -mb-0.5" :class="sortBy === 'name' && sortDir === 'asc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 0L10 6H0L5 0Z"/></svg>
                                                <svg class="w-2.5 h-2.5" :class="sortBy === 'name' && sortDir === 'desc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 6L0 0H10L5 6Z"/></svg>
                                            </span>
                                        </span>
                                    </th>
                                    <th class="px-6 py-3 font-medium">Description</th>
                                    <th class="px-6 py-3 font-medium cursor-pointer select-none hover:text-gray-600" @click="toggleSort('sort_order')">
                                        <span class="inline-flex items-center gap-1">Order
                                            <span class="inline-flex flex-col leading-none">
                                                <svg class="w-2.5 h-2.5 -mb-0.5" :class="sortBy === 'sort_order' && sortDir === 'asc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 0L10 6H0L5 0Z"/></svg>
                                                <svg class="w-2.5 h-2.5" :class="sortBy === 'sort_order' && sortDir === 'desc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 6L0 0H10L5 6Z"/></svg>
                                            </span>
                                        </span>
                                    </th>
                                    <th class="px-6 py-3 font-medium cursor-pointer select-none hover:text-gray-600" @click="toggleSort('created_at')">
                                        <span class="inline-flex items-center gap-1">Created
                                            <span class="inline-flex flex-col leading-none">
                                                <svg class="w-2.5 h-2.5 -mb-0.5" :class="sortBy === 'created_at' && sortDir === 'asc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 0L10 6H0L5 0Z"/></svg>
                                                <svg class="w-2.5 h-2.5" :class="sortBy === 'created_at' && sortDir === 'desc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 6L0 0H10L5 6Z"/></svg>
                                            </span>
                                        </span>
                                    </th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="row in rows" :key="row.id" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-3.5 text-xs font-mono text-gray-400">{{ row.id }}</td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ row.name }}</td>
                                    <td class="px-6 py-3.5 text-sm text-gray-500 max-w-xs truncate">{{ row.description ?? '—' }}</td>
                                    <td class="px-6 py-3.5 text-xs text-gray-400">{{ row.sort_order }}</td>
                                    <td class="px-6 py-3.5 text-xs text-gray-400">{{ formatDate(row.created_at) }}</td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-1">
                                            <RouterLink :to="`/admin/chat-knowledge-categories/${row.id}/edit`"
                                                class="p-1.5 text-indigo-600 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931z" />
                                                </svg>
                                            </RouterLink>
                                            <button @click="confirmDelete(row)"
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

                    <div v-if="meta.last_page > 1" class="px-6 py-4 border-t border-gray-50 flex items-center justify-between">
                        <p class="text-xs text-gray-400">Showing {{ meta.from }}–{{ meta.to }} of {{ meta.total }}</p>
                        <div class="flex items-center gap-1">
                            <button @click="fetch(currentPage - 1)" :disabled="currentPage === 1"
                                class="px-3 py-1.5 text-xs rounded-lg border border-gray-100 text-gray-500 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Prev</button>
                            <button @click="fetch(currentPage + 1)" :disabled="currentPage === meta.last_page"
                                class="px-3 py-1.5 text-xs rounded-lg border border-gray-100 text-gray-500 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">Next</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <DeleteModal :show="!!deleteTarget" @confirm="deleteRow" @cancel="deleteTarget = null"
        title="Delete Category" message="Are you sure you want to delete this category? All knowledge entries in this category will also be deleted." />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import DeleteModal from '../../../components/DeleteModal.vue'
import { useAdminGuard } from '../../../composables/useAdminGuard'
import { useFormatDate } from '../../../composables/useFormatDate'

const router = useRouter()
const { requireAdmin } = useAdminGuard()
const { formatDate } = useFormatDate()
const loading = ref(true)
const tableLoading = ref(false)
const fetchError = ref('')
const rows = ref([])
const meta = ref({})
const currentPage = ref(1)
const sortBy = ref('name')
const sortDir = ref('asc')
const search = ref('')
const deleteTarget = ref(null)

let searchTimer = null
function debouncedFetch() {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => fetch(1), 350)
}

function toggleSort(col) {
    sortDir.value = sortBy.value === col ? (sortDir.value === 'asc' ? 'desc' : 'asc') : 'asc'
    sortBy.value = col
    fetch(1)
}

function confirmDelete(row) {
    deleteTarget.value = row
}

async function deleteRow() {
    try {
        await axios.delete(`/api/admin/chat-knowledge-categories/${deleteTarget.value.id}`)
        deleteTarget.value = null
        fetch(currentPage.value)
    } catch (e) {
        console.error(e?.response?.status)
    }
}

async function fetch(page = 1) {
    tableLoading.value = true
    currentPage.value = page
    fetchError.value = ''
    try {
        const params = { page, sort_by: sortBy.value, sort_dir: sortDir.value }
        if (search.value) params.search = search.value
        const { data } = await axios.get('/api/admin/chat-knowledge-categories', { params })
        rows.value = data.data ?? []
        meta.value = { total: data.total ?? 0, from: data.from ?? 0, to: data.to ?? 0, last_page: data.last_page ?? 1 }
        currentPage.value = data.current_page ?? 1
    } catch (e) {
        fetchError.value = e?.response?.status === 403
            ? 'You do not have permission to manage categories.'
            : 'Failed to load categories. Please try again.'
    } finally {
        tableLoading.value = false
    }
}

onMounted(async () => {
    const me = await requireAdmin()
    if (!me) return
    if (!me.permissions?.some(p => p.name === 'super')) {
        router.replace('/403')
        return
    }
    loading.value = false
    fetch(1)
})
</script>
