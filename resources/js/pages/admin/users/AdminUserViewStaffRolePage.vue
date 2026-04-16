<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">

            <LoadingSpinner v-if="loading" />

            <template v-else>
                <!-- Header -->
                <div class="mb-8 flex items-center gap-3">
                    <RouterLink :to="route.query.back || `/admin/users/${route.params.id}/staff-roles`"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900">Staff Role</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Role assignment #{{ staffRole.id }}</p>
                    </div>
                    <RouterLink v-if="can('edit')" :to="{ path: `/admin/users/${route.params.id}/staff-roles/${route.params.roleId}/edit`, query: { back: route.query.back } }"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        Edit
                    </RouterLink>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">ID</span>
                        <span class="text-sm font-mono text-gray-600">{{ staffRole.id }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Position</span>
                        <span class="text-sm font-medium text-gray-900">{{ staffRole.position?.name ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Site</span>
                        <span class="text-sm text-gray-700">{{ staffRole.site?.name ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Salary</span>
                        <span class="text-sm text-gray-700">{{ staffRole.salary?.toLocaleString() ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Start Date</span>
                        <span class="text-sm text-gray-700">{{ staffRole.start_date ? formatDate(staffRole.start_date) : '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">End Date</span>
                        <span v-if="!staffRole.end_date"
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600">
                            Active
                        </span>
                        <span v-else class="text-sm text-gray-700">{{ formatDate(staffRole.end_date) }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Created By</span>
                        <span class="text-sm text-gray-700">{{ staffRole.created_by?.name ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Created At</span>
                        <span class="text-sm text-gray-600">{{ formatDate(staffRole.created_at, true) }}</span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import { useAdminGuard } from '../../../composables/useAdminGuard'
import { useFormatDate } from '../../../composables/useFormatDate'

const route = useRoute()
const router = useRouter()
const { requireAdmin } = useAdminGuard()
const { formatDate } = useFormatDate()
const loading = ref(true)
const staffRole = ref({})
const myPermissions = ref([])

function can(permission) {
    return myPermissions.value.includes('super') || myPermissions.value.includes(permission)
}

onMounted(async () => {
    const me = await requireAdmin()
    if (!me) return

    myPermissions.value = me.permissions?.map(p => p.name) ?? []

    try {
        const { data } = await axios.get(`/api/admin/users/${route.params.id}/staff-roles/${route.params.roleId}`)
        staffRole.value = data
    } catch {
        router.push(`/admin/users/${route.params.id}/staff-roles`)
    } finally {
        loading.value = false
    }
})
</script>
