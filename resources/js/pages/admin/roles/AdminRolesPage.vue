<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-6xl mx-auto">

            <LoadingSpinner v-if="loading" />

            <template v-else>
                <div class="mb-8 flex items-center gap-3">
                    <RouterLink to="/dashboard" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900">Access Control</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manage roles and permissions.</p>
                    </div>
                    <RouterLink v-if="hasSuper" to="/admin/roles/create"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New Role
                    </RouterLink>
                </div>

                <!-- Toggle -->
                <div class="mb-6 inline-flex rounded-xl border border-gray-200 bg-gray-100 p-1 gap-1">
                    <span class="px-4 py-1.5 text-sm font-medium rounded-lg bg-white text-gray-900 shadow-sm">Roles</span>
                    <RouterLink to="/admin/permissions"
                        class="px-4 py-1.5 text-sm font-medium rounded-lg text-gray-500 hover:text-gray-700 transition-colors">
                        Permissions
                    </RouterLink>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-900">All Roles</h2>
                        <span class="text-xs text-gray-400">{{ roles.length }} total</span>
                    </div>

                    <div v-if="roles.length === 0" class="px-6 py-12 text-center text-sm text-gray-400">
                        No roles found.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-50">
                                    <th v-for="col in columns" :key="col.key" class="px-6 py-3 font-medium">
                                        <button v-if="col.sortable" @click="toggleSort(col.key)"
                                            class="flex items-center gap-1 hover:text-gray-600 transition-colors">
                                            {{ col.label }}
                                            <span class="flex flex-col leading-none">
                                                <svg class="w-2.5 h-2.5" :class="sortBy === col.key && sortDir === 'asc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 10" fill="currentColor"><path d="M5 2l4 6H1z"/></svg>
                                                <svg class="w-2.5 h-2.5" :class="sortBy === col.key && sortDir === 'desc' ? 'text-indigo-500' : 'text-gray-200'" viewBox="0 0 10 10" fill="currentColor"><path d="M5 8 1 2h8z"/></svg>
                                            </span>
                                        </button>
                                        <span v-else>{{ col.label }}</span>
                                    </th>
                                    <th class="px-6 py-3 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-3.5 text-xs font-mono text-gray-400">{{ role.id }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded-full"
                                            :class="role.name === 'admin' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600'">
                                            {{ role.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-gray-500 text-xs">{{ role.users_count }}</td>
                                    <td class="px-6 py-3.5 text-gray-400 text-xs">{{ formatDate(role.created_at) }}</td>
                                    <td class="px-6 py-3.5 text-gray-400 text-xs">{{ formatDate(role.updated_at) }}</td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-1">
                                            <RouterLink :to="`/admin/roles/${role.id}`"
                                                class="p-1.5 text-indigo-600 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                                </svg>
                                            </RouterLink>
                                            <RouterLink v-if="hasSuper" :to="`/admin/roles/${role.id}/edit`"
                                                class="p-1.5 text-indigo-600 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931z" />
                                                </svg>
                                            </RouterLink>
                                            <button v-if="hasSuper" @click="confirmDelete(role)"
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

    <DeleteModal :show="!!deleteTarget" @confirm="deleteRole" @cancel="deleteTarget = null"
        title="Delete Role" message="Are you sure you want to delete this role?" />
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
const roles = ref([])
const deleteTarget = ref(null)
const deleting = ref(false)
const sortBy = ref('updated_at')
const sortDir = ref('desc')
const hasSuper = ref(false)

const columns = [
    { key: 'id',         label: 'ID',         sortable: true },
    { key: 'name',       label: 'Name',        sortable: true },
    { key: 'users',      label: 'Users',       sortable: false },
    { key: 'created_at', label: 'Created At',  sortable: true },
    { key: 'updated_at', label: 'Updated At',  sortable: true },
]

async function fetchRoles() {
    const { data } = await axios.get('/api/admin/roles', {
        params: { sort_by: sortBy.value, sort_dir: sortDir.value }
    })
    roles.value = data
}

function toggleSort(column) {
    if (sortBy.value === column) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = column
        sortDir.value = 'asc'
    }
    fetchRoles()
}

function confirmDelete(role) {
    deleteTarget.value = role
}

async function deleteRole() {
    deleting.value = true
    try {
        await axios.delete(`/api/admin/roles/${deleteTarget.value.id}`)
        roles.value = roles.value.filter(r => r.id !== deleteTarget.value.id)
        deleteTarget.value = null
    } catch (e) {
        console.error('delete error', e?.response?.status)
    } finally {
        deleting.value = false
    }
}

onMounted(async () => {
    const me = await requireAdmin()
    if (!me) return
    hasSuper.value = me.permissions?.some(p => p.name === 'super') ?? false
    try {
        await fetchRoles()
    } finally {
        loading.value = false
    }
})
</script>