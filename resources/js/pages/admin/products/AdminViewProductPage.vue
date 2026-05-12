<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">
            <LoadingSpinner v-if="loading" />

            <template v-else-if="product">
                <div class="mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <RouterLink to="/admin/products" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </RouterLink>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Product Details</h1>
                            <p class="text-sm text-gray-500 mt-0.5">{{ product.name }}</p>
                        </div>
                    </div>
                    <RouterLink v-if="canEdit" :to="`/admin/products/${product.id}/edit`"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931z" />
                        </svg>
                        Edit
                    </RouterLink>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50 mb-4">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">ID</span>
                        <span class="text-sm font-mono text-gray-600">{{ product.id }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Name</span>
                        <span class="text-sm font-medium text-gray-900">{{ product.name }}</span>
                    </div>
                    <div class="px-6 py-4">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide block mb-2">Description</span>
                        <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ product.description ?? '—' }}</p>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Created At</span>
                        <span class="text-sm text-gray-600">{{ formatDate(product.created_at, true) }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Updated At</span>
                        <span class="text-sm text-gray-600">{{ formatDate(product.updated_at, true) }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Created By</span>
                        <p class="text-sm text-gray-700 font-medium">{{ product.created_by?.name ?? '—' }}</p>
                    </div>
                </div>

                <!-- Current price + price history link -->
                <RouterLink :to="`/admin/products/${product.id}/prices`"
                    class="group bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4 flex items-center justify-between hover:shadow-md hover:border-indigo-100 transition-all">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-0.5">Per Price</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ product.prices?.[0]?.per_price?.toLocaleString() ?? '—' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-indigo-500 group-hover:text-indigo-700 transition-colors">
                        <span>Price History</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </RouterLink>
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
import { useFormatDate } from '../../../composables/useFormatDate'

const router = useRouter()
const route = useRoute()
const { formatDate } = useFormatDate()

const loading = ref(true)
const product = ref(null)
const canEdit = ref(false)

onMounted(async () => {
    if (!localStorage.getItem('token')) { router.push('/login'); return }
    try {
        const { data: me } = await axios.get('/api/me')
        const roles = me.roles?.map(r => r.name) ?? []
        if (!roles.includes('admin') && !roles.includes('sale')) {
            router.replace('/403'); return
        }
        const perms = me.permissions?.map(p => p.name) ?? []
        canEdit.value = perms.includes('super') || (roles.includes('admin') && perms.includes('edit'))
        const { data } = await axios.get(`/api/order/products/${route.params.id}`)
        product.value = data
    } catch {
        router.push('/admin/products')
    } finally {
        loading.value = false
    }
})
</script>
