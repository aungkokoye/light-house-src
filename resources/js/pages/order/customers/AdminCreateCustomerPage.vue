<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">
            <LoadingSpinner v-if="loading" />

            <template v-else>
                <div class="mb-8 flex items-center gap-3">
                    <RouterLink to="/order/customers" @click.prevent="goBack('/order/customers', 'customer-list-back')" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">New Customer</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Add a new customer record.</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Name <span class="text-red-400">*</span></label>
                            <input v-model="form.name" type="text" placeholder="Full name"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.name ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone <span class="text-red-400">*</span></label>
                            <input v-model="form.phone" type="text" placeholder="+95 9 xxx xxx xxx"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.phone ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.phone" class="mt-1 text-xs text-red-500">{{ errors.phone[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email</label>
                            <input v-model="form.email" type="email" placeholder="email@example.com"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.email ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Company Name</label>
                            <input v-model="form.company_name" type="text" placeholder="Company or organisation"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.company_name ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.company_name" class="mt-1 text-xs text-red-500">{{ errors.company_name[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Title / Role</label>
                            <input v-model="form.title" type="text" placeholder="e.g. Manager"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.title ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.title" class="mt-1 text-xs text-red-500">{{ errors.title[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Address</label>
                            <textarea v-model="form.address" rows="3" placeholder="Customer address"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.address ? 'border-red-300' : 'border-gray-300'"></textarea>
                            <p v-if="errors.address" class="mt-1 text-xs text-red-500">{{ errors.address[0] }}</p>
                        </div>

                        <p v-if="generalError" class="text-xs text-red-500">{{ generalError }}</p>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="goBack('/order/customers', 'customer-list-back')" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</button>
                            <button type="submit" :disabled="submitting"
                                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                                {{ submitting ? 'Creating…' : 'Create Customer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import { useAdminGuard } from '../../../composables/useAdminGuard'
import { useGoBack } from '../../../composables/useGoBack'

const router = useRouter()
const { requireAdmin } = useAdminGuard()
const { goBack } = useGoBack()

const loading      = ref(true)
const submitting   = ref(false)
const errors       = ref({})
const generalError = ref('')
const form = ref({ name: '', phone: '', email: '', company_name: '', title: '', address: '' })

async function submit() {
    errors.value       = {}
    generalError.value = ''
    submitting.value   = true
    try {
        const { data } = await axios.post('/api/order/customers', form.value)
        router.push(`/order/customers/${data.id}`)
    } catch (e) {
        if (e?.response?.status === 422) errors.value = e.response.data.errors ?? {}
        else generalError.value = 'Something went wrong. Please try again.'
    } finally {
        submitting.value = false
    }
}

onMounted(async () => {
    const me = await requireAdmin()
    if (!me) return
    loading.value = false
})
</script>
