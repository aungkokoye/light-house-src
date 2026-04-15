<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">
            <LoadingSpinner v-if="loading" />

            <template v-else-if="site">
                <div class="mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <RouterLink to="/admin/sites" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </RouterLink>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Site Details</h1>
                            <p class="text-sm text-gray-500 mt-0.5">{{ site.name }}</p>
                        </div>
                    </div>
                    <RouterLink :to="`/admin/sites/${site.id}/edit`"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931z" />
                        </svg>
                        Edit
                    </RouterLink>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">ID</span>
                        <span class="text-sm font-mono text-gray-600">{{ site.id }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Name</span>
                        <span class="text-sm font-medium text-gray-900">{{ site.name }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-start justify-between gap-4">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide shrink-0">Description</span>
                        <span class="text-sm text-gray-600 text-right">{{ site.description ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-start justify-between gap-4">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide shrink-0">Address</span>
                        <span class="text-sm text-gray-600 text-right">{{ site.address ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Phone</span>
                        <span class="text-sm text-gray-600">{{ site.phone ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Created At</span>
                        <span class="text-sm text-gray-600">{{ formatDate(site.created_at, true) }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Updated At</span>
                        <span class="text-sm text-gray-600">{{ formatDate(site.updated_at, true) }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Created By</span>
                        <div class="text-right">
                            <p class="text-sm text-gray-700 font-medium">{{ site.created_by?.name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ site.created_by?.email }}</p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import { useAdminGuard } from '../../../composables/useAdminGuard'
import { useFormatDate } from '../../../composables/useFormatDate'

const router = useRouter()
const route = useRoute()
const { requireAdmin } = useAdminGuard()
const { formatDate } = useFormatDate()
const loading = ref(true)
const site = ref(null)

onMounted(async () => {
    const me = await requireAdmin()
    if (!me) return
    try {
        const { data } = await axios.get(`/api/admin/sites/${route.params.id}`)
        site.value = data
    } catch {
        router.push('/admin/sites')
    } finally {
        loading.value = false
    }
})
</script>