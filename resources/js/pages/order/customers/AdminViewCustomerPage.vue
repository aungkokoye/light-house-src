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
