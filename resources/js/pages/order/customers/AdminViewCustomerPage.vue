<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">
            <LoadingSpinner v-if="loading" />

            <template v-else-if="customer">
                <div class="mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <RouterLink to="/order/customers" @click.prevent="goBack('/order/customers', 'customer-list-back')" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </RouterLink>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Customer Details</h1>
                            <p class="text-sm text-gray-500 mt-0.5">{{ customer.name }}</p>
                        </div>
                    </div>
                    <span class="relative group">
                        <RouterLink :to="customer.invoices_count ? `/order/invoices?customer_id=${customer.id}` : ''"
                            :class="customer.invoices_count ? 'text-indigo-600 border-indigo-200 hover:bg-indigo-50' : 'opacity-40 cursor-not-allowed pointer-events-none text-gray-400 border-gray-200'"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z" />
                            </svg>
                            Invoices{{ customer.invoices_count ? ` (${customer.invoices_count})` : '' }}
                        </RouterLink>
                        <span v-if="!customer.invoices_count"
                            class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 whitespace-nowrap rounded bg-gray-800 px-2 py-1 text-xs text-white opacity-0 group-hover:opacity-100 transition-opacity">
                            No invoices
                        </span>
                    </span>
                    <RouterLink v-if="can.edit" :to="`/order/customers/${customer.id}/edit`"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931z" />
                        </svg>
                        Edit
                    </RouterLink>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Name</span>
                        <span class="text-sm font-medium text-gray-900">{{ customer.name }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Phone</span>
                        <span class="text-sm text-gray-900">{{ customer.phone }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Email</span>
                        <span class="text-sm text-gray-600">{{ customer.email ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Company</span>
                        <span class="text-sm text-gray-600">{{ customer.company_name ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Title</span>
                        <span class="text-sm text-gray-600">{{ customer.title ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-start justify-between gap-4">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide shrink-0">Address</span>
                        <span class="text-sm text-gray-600 text-right">{{ customer.address ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Created At</span>
                        <span class="text-sm text-gray-600">{{ formatDate(customer.created_at, true) }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Created By</span>
                        <span class="text-sm text-gray-700 font-medium">{{ customer.created_by?.name ?? '—' }}</span>
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
import { useFormatDate } from '../../../composables/useFormatDate'
import { useGoBack } from '../../../composables/useGoBack'

const router = useRouter()
const route  = useRoute()
const { goBack } = useGoBack()
const { formatDate } = useFormatDate()

const loading  = ref(true)
const customer = ref(null)
const can      = ref({})

onMounted(async () => {
    if (!localStorage.getItem('token')) { router.push('/login'); return }
    try {
        const { data } = await axios.get(`/api/order/customers/${route.params.id}`)
        customer.value = data
        can.value      = data.can ?? {}
    } catch {
        router.push('/order/customers')
    } finally {
        loading.value = false
    }
})
</script>
