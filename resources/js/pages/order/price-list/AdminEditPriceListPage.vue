<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">
            <LoadingSpinner v-if="loading" />

            <template v-else-if="item">
                <div class="mb-8 flex items-center gap-3">
                    <RouterLink :to="`/order/price-list/${item.id}`" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Price List</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Update this price list.</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Service <span class="text-red-400">*</span></label>
                            <select v-model="form.job_service_id"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.job_service_id ? 'border-red-300' : 'border-gray-300'">
                                <option value="">Select a service…</option>
                                <option v-for="s in services" :key="s.id" :value="s.id">{{ s.name }}</option>
                            </select>
                            <p v-if="errors.job_service_id" class="mt-1 text-xs text-red-500">{{ errors.job_service_id[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Product Description <span class="text-red-400">*</span></label>
                            <textarea v-model="form.product_description" rows="3" placeholder="Describe the product…"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50 resize-none"
                                :class="errors.product_description ? 'border-red-300' : 'border-gray-300'"></textarea>
                            <p v-if="errors.product_description" class="mt-1 text-xs text-red-500">{{ errors.product_description[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Dimension</label>
                            <input v-model="form.dimension" type="text" placeholder="e.g. 210x297mm"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.dimension ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.dimension" class="mt-1 text-xs text-red-500">{{ errors.dimension[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Price <span class="text-red-400">*</span></label>
                            <input v-model.number="form.price" type="number" min="0" placeholder="0"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.price ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.price" class="mt-1 text-xs text-red-500">{{ errors.price[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Remark</label>
                            <textarea v-model="form.remark" rows="2" placeholder="Optional remark…"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50 resize-none"
                                :class="errors.remark ? 'border-red-300' : 'border-gray-300'"></textarea>
                            <p v-if="errors.remark" class="mt-1 text-xs text-red-500">{{ errors.remark[0] }}</p>
                        </div>

                        <p v-if="generalError" class="text-xs text-red-500">{{ generalError }}</p>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <RouterLink :to="`/order/price-list/${item.id}`" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</RouterLink>
                            <button type="submit" :disabled="submitting"
                                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                                {{ submitting ? 'Saving…' : 'Save Changes' }}
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
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import { useAdminGuard } from '../../../composables/useAdminGuard'

const router = useRouter()
const route = useRoute()
const { requireAdmin } = useAdminGuard()
const loading = ref(true)
const submitting = ref(false)
const errors = ref({})
const generalError = ref('')
const services = ref([])
const item = ref(null)
const form = ref({ job_service_id: '', product_description: '', dimension: '', price: 0, remark: '' })

async function submit() {
    errors.value = {}
    generalError.value = ''
    submitting.value = true
    try {
        await axios.put(`/api/order/price-list/${item.value.id}`, form.value)
        router.push(`/order/price-list/${item.value.id}`)
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
    const [itemRes, servicesRes] = await Promise.all([
        axios.get(`/api/order/price-list/${route.params.id}`),
        axios.get('/api/order/services', { params: { per_page: 100 } }),
    ])
    item.value = itemRes.data
    services.value = servicesRes.data.data ?? []
    form.value = {
        job_service_id:      item.value.job_service?.id ?? '',
        product_description: item.value.product_description,
        dimension:           item.value.dimension ?? '',
        price:               item.value.price,
        remark:              item.value.remark ?? '',
    }
    loading.value = false
})
</script>
