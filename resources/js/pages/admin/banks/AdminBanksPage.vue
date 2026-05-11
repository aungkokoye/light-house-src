<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-4xl mx-auto">

            <LoadingSpinner v-if="loading" />

            <template v-else>
                <div class="mb-6 flex items-center gap-3">
                    <RouterLink to="/dashboard" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900">Banks</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manage payment banks.</p>
                    </div>
                    <RouterLink v-if="can('create')" to="/admin/banks/create"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New Bank
                    </RouterLink>
                </div>

                <!-- Search -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                        </svg>
                        <input v-model="search" type="text" placeholder="Search bank name…"
                            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50" />
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-900">All Banks</h2>
                        <span class="text-xs text-gray-400">{{ filtered.length }} total</span>
                    </div>

                    <div v-if="filtered.length === 0" class="px-6 py-12 text-center text-sm text-gray-400">
                        No banks found.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-50">
                                    <th class="px-6 py-3 font-medium cursor-pointer select-none hover:text-gray-600" @click="toggleSort('id')">
                                        <span class="inline-flex items-center gap-1">
                                            ID
                                            <span class="inline-flex flex-col leading-none">
                                                <svg class="w-2.5 h-2.5 -mb-0.5 transition-colors" :class="sortBy === 'id' && sortDir === 'asc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 0L10 6H0L5 0Z"/></svg>
                                                <svg class="w-2.5 h-2.5 transition-colors" :class="sortBy === 'id' && sortDir === 'desc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 6L0 0H10L5 6Z"/></svg>
                                            </span>
                                        </span>
                                    </th>
                                    <th class="px-6 py-3 font-medium cursor-pointer select-none hover:text-gray-600" @click="toggleSort('name')">
                                        <span class="inline-flex items-center gap-1">
                                            Name
                                            <span class="inline-flex flex-col leading-none">
                                                <svg class="w-2.5 h-2.5 -mb-0.5 transition-colors" :class="sortBy === 'name' && sortDir === 'asc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 0L10 6H0L5 0Z"/></svg>
                                                <svg class="w-2.5 h-2.5 transition-colors" :class="sortBy === 'name' && sortDir === 'desc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 6L0 0H10L5 6Z"/></svg>
                                            </span>
                                        </span>
                                    </th>
                                    <th class="px-6 py-3 font-medium cursor-pointer select-none hover:text-gray-600" @click="toggleSort('created_at')">
                                        <span class="inline-flex items-center gap-1">
                                            Created At
                                            <span class="inline-flex flex-col leading-none">
                                                <svg class="w-2.5 h-2.5 -mb-0.5 transition-colors" :class="sortBy === 'created_at' && sortDir === 'asc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 0L10 6H0L5 0Z"/></svg>
                                                <svg class="w-2.5 h-2.5 transition-colors" :class="sortBy === 'created_at' && sortDir === 'desc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 6" fill="currentColor"><path d="M5 6L0 0H10L5 6Z"/></svg>
                                            </span>
                                        </span>
                                    </th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="bank in filtered" :key="bank.id" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-3.5 text-xs font-mono text-gray-400">{{ bank.id }}</td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ bank.name }}</td>
                                    <td class="px-6 py-3.5 text-xs text-gray-400">{{ formatDate(bank.created_at) }}</td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-1">
                                            <RouterLink v-if="can('view')" :to="`/admin/banks/${bank.id}`"
                                                class="p-1.5 text-indigo-600 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                                </svg>
                                            </RouterLink>
                                            <RouterLink v-if="can('edit')" :to="`/admin/banks/${bank.id}/edit`"
                                                class="p-1.5 text-indigo-600 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931z" />
                                                </svg>
                                            </RouterLink>
                                            <button v-if="can('delete')" @click="confirmDelete(bank)"
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
                </div>
            </template>
        </div>
    </div>

    <DeleteModal :show="!!deleteTarget" @confirm="deleteBank" @cancel="deleteTarget = null"
        title="Delete Bank" message="Are you sure you want to delete this bank?" />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import DeleteModal from '../../../components/DeleteModal.vue'
import { useFormatDate } from '../../../composables/useFormatDate'

const router = useRouter()
const { formatDate } = useFormatDate()

const loading = ref(true)
const banks = ref([])
const search = ref('')
const sortBy = ref('name')
const sortDir = ref('asc')
const deleteTarget = ref(null)
const myPermissions = ref([])
const myRole = ref('')

function can(action) {
    if (myPermissions.value.includes('super')) return true
    const isAdmin = myRole.value === 'admin'
    if (action === 'view')   return myPermissions.value.includes('view')
    if (action === 'create') return isAdmin && myPermissions.value.includes('create')
    if (action === 'edit')   return isAdmin && myPermissions.value.includes('edit')
    if (action === 'delete') return isAdmin && myPermissions.value.includes('delete')
    return false
}

function toggleSort(col) {
    if (sortBy.value === col) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = col
        sortDir.value = 'asc'
    }
}

const filtered = computed(() => {
    let list = banks.value
    if (search.value.trim()) {
        const q = search.value.trim().toLowerCase()
        list = list.filter(b => b.name.toLowerCase().includes(q))
    }
    return [...list].sort((a, b) => {
        const av = a[sortBy.value] ?? ''
        const bv = b[sortBy.value] ?? ''
        const cmp = String(av).localeCompare(String(bv), undefined, { numeric: true })
        return sortDir.value === 'asc' ? cmp : -cmp
    })
})

function confirmDelete(bank) {
    deleteTarget.value = bank
}

async function deleteBank() {
    try {
        await axios.delete(`/api/order/banks/${deleteTarget.value.id}`)
        banks.value = banks.value.filter(b => b.id !== deleteTarget.value.id)
        deleteTarget.value = null
    } catch (e) {
        console.error('delete error', e?.response?.status)
    }
}

onMounted(async () => {
    if (!localStorage.getItem('token')) { router.push('/login'); return }
    try {
        const { data: me } = await axios.get('/api/me')
        const roles = me.roles?.map(r => r.name) ?? []
        if (!roles.includes('admin') && !roles.includes('sale')) {
            router.replace('/403'); return
        }
        myRole.value = roles.includes('admin') ? 'admin' : 'sale'
        myPermissions.value = me.permissions?.map(p => p.name) ?? []
        const { data } = await axios.get('/api/order/banks')
        banks.value = data
    } catch {
        router.push('/login')
    } finally {
        loading.value = false
    }
})
</script>
