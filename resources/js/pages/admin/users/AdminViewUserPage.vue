<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">

            <LoadingSpinner v-if="loading" />

            <template v-else-if="user">
                <!-- Header -->
                <div class="mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button @click="router.back()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
                            <p class="text-sm text-gray-500 mt-0.5">Viewing account for <span class="font-medium text-gray-700">{{ user.name }}</span>.</p>
                        </div>
                    </div>
                    <RouterLink :to="`/admin/users/${user.id}/edit`"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931z" />
                        </svg>
                        Edit
                    </RouterLink>
                </div>

                <!-- Avatar + name -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600 text-2xl font-bold shrink-0">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-lg">{{ user.name }}</p>
                        <p class="text-sm text-gray-400">{{ user.email }}</p>
                    </div>
                    <span class="ml-auto inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full"
                        :class="user.roles?.[0]?.name === 'admin' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600'">
                        {{ user.roles?.[0]?.name ?? 'user' }}
                    </span>
                </div>

                <!-- Details -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">ID</span>
                        <span class="text-sm font-mono text-gray-600">{{ user.id }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Email Verified</span>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 text-sm font-medium"
                                :class="user.email_verified_at ? 'text-green-600' : 'text-yellow-600'">
                                <span class="w-1.5 h-1.5 rounded-full" :class="user.email_verified_at ? 'bg-green-500' : 'bg-yellow-500'"></span>
                                {{ user.email_verified_at ? 'Verified' : 'Not verified' }}
                            </span>
                            <button v-if="!user.email_verified_at" @click="resendVerification"
                                :disabled="resending"
                                class="text-xs font-medium text-indigo-600 hover:text-indigo-700 disabled:opacity-50 transition-colors">
                                {{ resending ? 'Sending…' : 'Resend email' }}
                            </button>
                        </div>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Activated</span>
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium"
                            :class="user.activated ? 'text-green-600' : 'text-red-500'">
                            <span class="w-1.5 h-1.5 rounded-full" :class="user.activated ? 'bg-green-500' : 'bg-red-400'"></span>
                            {{ user.activated ? 'Active' : 'Deactivated' }}
                        </span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Sign-in Method</span>
                        <span class="text-sm text-gray-600">{{ user.google_id ? 'Google' : 'Email' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Created At</span>
                        <span class="text-sm text-gray-600">{{ formatDate(user.created_at, true) }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Updated At</span>
                        <span class="text-sm text-gray-600">{{ formatDate(user.updated_at, true) }}</span>
                    </div>
                    <div v-if="user.email_verified_at" class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Verified At</span>
                        <span class="text-sm text-gray-600">{{ formatDate(user.email_verified_at, true) }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Created By</span>
                        <div class="text-right">
                            <p class="text-sm text-gray-700 font-medium">{{ user.created_by_name }}</p>
                            <p class="text-xs text-gray-400">{{ user.created_by_email }}</p>
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
const user = ref(null)
const resending = ref(false)

async function resendVerification() {
    resending.value = true
    try {
        await axios.post(`/api/admin/users/${user.value.id}/resend-verification`)
    } finally {
        resending.value = false
    }
}

onMounted(async () => {
    const me = await requireAdmin()
    if (!me) return

    try {
        const { data } = await axios.get(`/api/admin/users/${route.params.id}`)
        user.value = data
    } catch {
        router.push('/admin/users')
    } finally {
        loading.value = false
    }
})
</script>