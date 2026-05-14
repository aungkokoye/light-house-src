<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-2xl mx-auto">
            <LoadingSpinner v-if="loading" />

            <template v-else>
                <div class="mb-6 flex items-center gap-3">
                    <RouterLink :to="`/order/products/${route.params.id}`" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900">Price History</h1>
                        <p class="text-sm text-gray-500 mt-0.5">{{ productName }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-900">All Prices</h2>
                        <span class="text-xs text-gray-400">{{ prices.length }} total</span>
                    </div>

                    <div v-if="prices.length === 0" class="px-6 py-12 text-center text-sm text-gray-400">No prices found.</div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-50">
                                    <th class="px-6 py-3 font-medium">#</th>
                                    <th class="px-6 py-3 font-medium">Price</th>
                                    <th class="px-6 py-3 font-medium">Created At</th>
                                    <th class="px-6 py-3 font-medium">Updated At</th>
                                    <th v-if="can.delete" class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="(price, i) in prices" :key="price.id"
                                    class="hover:bg-gray-50/50 transition-colors"
                                    :class="i === 0 ? 'bg-emerald-50/40' : ''">
                                    <td class="px-6 py-3.5 text-xs font-mono text-gray-400">{{ price.id }}</td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900">
                                        {{ price.per_price.toLocaleString() }}
                                        <span v-if="i === 0" class="ml-2 text-xs text-emerald-600 font-normal">current</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-gray-400">{{ formatDate(price.created_at, true) }}</td>
                                    <td class="px-6 py-3.5 text-xs text-gray-400">{{ formatDate(price.updated_at, true) }}</td>
                                    <td v-if="can.delete" class="px-6 py-3.5">
                                        <button v-if="prices.length > 1" @click="confirmDelete(price)"
                                            class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <DeleteModal :show="!!deleteTarget" @confirm="deletePrice" @cancel="deleteTarget = null"
        title="Delete Price" message="Are you sure you want to delete this price?" />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import DeleteModal from '../../../components/DeleteModal.vue'
import { useFormatDate } from '../../../composables/useFormatDate'

const router = useRouter()
const route = useRoute()
const { formatDate } = useFormatDate()

const loading = ref(true)
const prices = ref([])
const productName = ref('')
const can = ref({})
const deleteTarget = ref(null)

function confirmDelete(price) { deleteTarget.value = price }

async function deletePrice() {
    try {
        await axios.delete(`/api/order/products/${route.params.id}/prices/${deleteTarget.value.id}`)
        prices.value = prices.value.filter(p => p.id !== deleteTarget.value.id)
        deleteTarget.value = null
    } catch (e) { console.error(e?.response?.status) }
}

onMounted(async () => {
    if (!localStorage.getItem('token')) { router.push('/login'); return }
    try {
        const { data } = await axios.get(`/api/order/products/${route.params.id}`)
        can.value = data.can ?? {}
        productName.value = data.name
        prices.value = data.prices ?? []
        loading.value = false
    } catch (e) {
        if (!e?.response) router.push('/order/products')
    }
})
</script>
