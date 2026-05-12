<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-4xl mx-auto">
            <LoadingSpinner v-if="loading" />

            <template v-else>
                <div class="mb-6 flex items-center gap-3">
                    <RouterLink to="/admin/invoices" @click.prevent="goBack('/admin/invoices', 'invoice-list-back')" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 font-mono">{{ invoice.invoice_no }}</h1>
                        <p class="text-xs text-gray-400 mt-0.5">Created {{ formatDate(invoice.created_at) }} by {{ invoice.created_by?.name ?? '—' }}</p>
                    </div>
                    <RouterLink v-if="can('edit')" :to="`/admin/invoices/${invoice.id}/edit`"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931z" /></svg>
                        Edit
                    </RouterLink>
                </div>

                <!-- Info card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Customer</p>
                            <p class="text-sm font-medium text-gray-900">{{ invoice.customer?.name ?? '—' }}</p>
                            <p v-if="invoice.customer?.company_profile?.name" class="text-xs text-gray-500">{{ invoice.customer.company_profile.name }}</p>
                            <p class="text-xs text-gray-400">{{ invoice.customer?.email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Invoice No</p>
                            <p class="text-sm font-mono font-semibold text-indigo-700">{{ invoice.invoice_no }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Discount</p>
                            <p class="text-sm text-gray-900">{{ invoice.discount.toLocaleString() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Total</p>
                            <p class="text-base font-semibold text-gray-900">{{ invoice.total.toLocaleString() }}</p>
                        </div>
                        <div v-if="invoice.note" class="sm:col-span-2">
                            <p class="text-xs text-gray-400 mb-0.5">Note</p>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ invoice.note }}</p>
                        </div>
                    </div>
                </div>

                <!-- Jobs table -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-50">
                        <h2 class="font-semibold text-gray-900">Invoice Items</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-50">
                                    <th class="px-6 py-3 font-medium">#</th>
                                    <th class="px-6 py-3 font-medium">Product</th>
                                    <th class="px-6 py-3 font-medium">Service</th>
                                    <th class="px-6 py-3 font-medium">Qty</th>
                                    <th class="px-6 py-3 font-medium">Unit Price</th>
                                    <th class="px-6 py-3 font-medium">Delivery Date</th>
                                    <th class="px-6 py-3 font-medium">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="(job, i) in invoice.jobs" :key="job.id" class="hover:bg-gray-50/50">
                                    <td class="px-6 py-3.5 text-xs font-mono text-gray-400">{{ i + 1 }}</td>
                                    <td class="px-6 py-3.5 text-gray-700">{{ job.product?.name ?? '—' }}</td>
                                    <td class="px-6 py-3.5 text-gray-700">{{ job.service?.name ?? '—' }}</td>
                                    <td class="px-6 py-3.5 text-gray-900">{{ job.quantity }}</td>
                                    <td class="px-6 py-3.5 text-gray-900">{{ job.unit_price.toLocaleString() }}</td>
                                    <td class="px-6 py-3.5 text-xs text-gray-500">{{ formatDate(job.delivery_date) }}</td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ job.total.toLocaleString() }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="border-t border-gray-100 bg-gray-50/50">
                                    <td colspan="6" class="px-6 py-3 text-right text-xs text-gray-400">Subtotal</td>
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ subtotal.toLocaleString() }}</td>
                                </tr>
                                <tr v-if="invoice.discount > 0" class="bg-gray-50/50">
                                    <td colspan="6" class="px-6 py-2 text-right text-xs text-gray-400">Discount</td>
                                    <td class="px-6 py-2 text-gray-700">- {{ invoice.discount.toLocaleString() }}</td>
                                </tr>
                                <tr class="bg-indigo-50/50">
                                    <td colspan="6" class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total</td>
                                    <td class="px-6 py-3 text-base font-bold text-indigo-700">{{ invoice.total.toLocaleString() }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Payments table -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-900">Payments</h2>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400">Paid: {{ totalPaid.toLocaleString() }} / {{ invoice.total.toLocaleString() }}</span>
                            <button v-if="can('add-payment') && !hasFinalPayment" type="button" @click="openAddPayment"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Payment
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-50">
                                    <th class="px-6 py-3 font-medium">#</th>
                                    <th class="px-6 py-3 font-medium">Type</th>
                                    <th class="px-6 py-3 font-medium">Bank</th>
                                    <th class="px-6 py-3 font-medium">Stage</th>
                                    <th class="px-6 py-3 font-medium">Amount</th>
                                    <th class="px-6 py-3 font-medium">Date</th>
                                    <th class="px-6 py-3 font-medium">Note</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-if="!invoice.payments?.length">
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-400">No payments yet.</td>
                                </tr>
                                <tr v-else v-for="(pmt, i) in invoice.payments" :key="pmt.id" class="hover:bg-gray-50/50">
                                    <td class="px-6 py-3.5 text-xs font-mono text-gray-400">{{ i + 1 }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="TYPE_CLS[pmt.type_id]">
                                            {{ typeMap[pmt.type_id] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-gray-500">{{ pmt.bank?.name ?? '—' }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="STAGE_CLS[pmt.stage]">
                                            {{ stageMap[pmt.stage] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ pmt.amount.toLocaleString() }}</td>
                                    <td class="px-6 py-3.5 text-xs text-gray-500">{{ formatDate(pmt.payment_date) }}</td>
                                    <td class="px-6 py-3.5">
                                        <button v-if="pmt.note" @click="noteModal = pmt.note"
                                            class="inline-flex items-center gap-1.5 text-xs text-indigo-600 hover:text-indigo-800 transition-colors">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                            </svg>
                                            View
                                        </button>
                                        <span v-else class="text-gray-400">—</span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="border-t border-gray-100 bg-gray-50/50">
                                    <td colspan="6" class="px-6 py-3 text-right text-xs text-gray-400">Total Paid</td>
                                    <td class="px-6 py-3 font-medium text-gray-900 text-right">{{ totalPaid.toLocaleString() }}</td>
                                </tr>
                                <tr :class="balance === 0 ? 'bg-emerald-50/50' : 'bg-red-50/50'">
                                    <td colspan="6" class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Balance</td>
                                    <td class="px-6 py-3 text-base font-bold text-right" :class="balance === 0 ? 'text-emerald-600' : 'text-red-600'">
                                        {{ balance.toLocaleString() }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Add Payment modal -->
    <Teleport to="body">
        <div v-if="showAddPayment" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" @click.self="closeAddPayment">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Add Payment</h3>
                    <button @click="closeAddPayment" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Type <span class="text-red-400">*</span></label>
                        <select v-model.number="pmtForm.type_id"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                            :class="pmtErrors.type_id ? 'border-red-300' : 'border-gray-300'">
                            <option value="">Select type</option>
                            <option v-for="t in paymentMeta?.types" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>
                        <p v-if="pmtErrors.type_id" class="mt-1 text-xs text-red-500">{{ pmtErrors.type_id[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Stage <span class="text-red-400">*</span></label>
                        <select v-model.number="pmtForm.stage"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                            :class="pmtErrors.stage ? 'border-red-300' : 'border-gray-300'">
                            <option value="">Select stage</option>
                            <option v-for="s in paymentMeta?.stages" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                        <p v-if="pmtErrors.stage" class="mt-1 text-xs text-red-500">{{ pmtErrors.stage[0] }}</p>
                    </div>
                </div>

                <div v-if="pmtForm.type_id === paymentMeta?.type_bank">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Bank <span class="text-red-400">*</span></label>
                    <select v-model.number="pmtForm.bank_id"
                        class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                        :class="pmtErrors.bank_id ? 'border-red-300' : 'border-gray-300'">
                        <option value="">Select bank</option>
                        <option v-for="b in banks" :key="b.id" :value="b.id">{{ b.name }}</option>
                    </select>
                    <p v-if="pmtErrors.bank_id" class="mt-1 text-xs text-red-500">{{ pmtErrors.bank_id[0] }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Amount <span class="text-red-400">*</span></label>
                        <input v-model.number="pmtForm.amount" type="number" min="0" placeholder="0"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                            :class="pmtErrors.amount ? 'border-red-300' : 'border-gray-300'" />
                        <p v-if="pmtErrors.amount" class="mt-1 text-xs text-red-500">{{ pmtErrors.amount[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Payment Date <span class="text-red-400">*</span></label>
                        <input v-model="pmtForm.payment_date" type="date"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                            :class="pmtErrors.payment_date ? 'border-red-300' : 'border-gray-300'" />
                        <p v-if="pmtErrors.payment_date" class="mt-1 text-xs text-red-500">{{ pmtErrors.payment_date[0] }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Note</label>
                    <textarea v-model="pmtForm.note" rows="2" placeholder="Optional note…"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" @click="closeAddPayment" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</button>
                    <button type="button" @click="submitPayment" :disabled="addingPayment"
                        class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                        {{ addingPayment ? 'Saving…' : 'Save Payment' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>

    <!-- Note modal -->
    <Teleport to="body">
        <div v-if="noteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" @click.self="noteModal = null">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Payment Note</h3>
                    <button @click="noteModal = null" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ noteModal }}</p>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import { useFormatDate } from '../../../composables/useFormatDate'
import { useGoBack } from '../../../composables/useGoBack'

const router = useRouter()
const { goBack } = useGoBack()
const route = useRoute()
const { formatDate } = useFormatDate()
const loading = ref(true)
const invoice = ref(null)
const noteModal = ref(null)
const myPermissions = ref([])
const myRole = ref('')
const banks = ref([])
const paymentMeta = ref(null)

const TYPE_CLS  = { 1: 'bg-green-50 text-green-700', 2: 'bg-blue-50 text-blue-700', 3: 'bg-gray-50 text-gray-600' }
const STAGE_CLS = { 1: 'bg-amber-50 text-amber-700', 2: 'bg-indigo-50 text-indigo-700' }
const typeMap  = computed(() => Object.fromEntries((paymentMeta.value?.types  ?? []).map(t => [t.id, t.name])))
const stageMap = computed(() => Object.fromEntries((paymentMeta.value?.stages ?? []).map(s => [s.id, s.name])))

// add-payment modal state
const showAddPayment = ref(false)
const addingPayment = ref(false)
const pmtErrors = ref({})
const pmtForm = ref({ type_id: '', bank_id: '', stage: '', amount: 0, payment_date: '', note: '' })

function can(action) {
    if (myPermissions.value.includes('super')) return true
    const isAdminOrSale = ['admin', 'sale'].includes(myRole.value)
    if (action === 'edit') return isAdminOrSale && myPermissions.value.includes('edit')
    if (action === 'add-payment') return isAdminOrSale && myPermissions.value.includes('create')
    return false
}

function openAddPayment() {
    pmtForm.value = { type_id: '', bank_id: '', stage: '', amount: 0, payment_date: '', note: '' }
    pmtErrors.value = {}
    showAddPayment.value = true
}
function closeAddPayment() { showAddPayment.value = false }

async function submitPayment() {
    pmtErrors.value = {}
    addingPayment.value = true
    try {
        const { data: newPayment } = await axios.post('/api/order/payments', {
            invoice_id:   invoice.value.id,
            type_id:      pmtForm.value.type_id,
            bank_id:      pmtForm.value.type_id === paymentMeta.value?.type_bank ? (pmtForm.value.bank_id || null) : null,
            stage:        pmtForm.value.stage,
            amount:       pmtForm.value.amount,
            payment_date: pmtForm.value.payment_date,
            note:         pmtForm.value.note || null,
        })
        invoice.value.payments.push(newPayment)
        closeAddPayment()
    } catch (e) {
        if (e?.response?.status === 422) pmtErrors.value = e.response.data.errors ?? {}
    } finally {
        addingPayment.value = false
    }
}

const subtotal = computed(() =>
    (invoice.value?.jobs ?? []).reduce((sum, j) => sum + j.total, 0)
)

const totalPaid = computed(() =>
    (invoice.value?.payments ?? []).reduce((sum, p) => sum + p.amount, 0)
)

const hasFinalPayment = computed(() =>
    (invoice.value?.payments ?? []).some(p => p.stage === paymentMeta.value?.stage_final)
)

const balance = computed(() => (invoice.value?.total ?? 0) - totalPaid.value)

onMounted(async () => {
    if (!localStorage.getItem('token')) { router.push('/login'); return }
    try {
        const { data: me } = await axios.get('/api/me')
        const roles = me.roles?.map(r => r.name) ?? []
        if (!roles.includes('admin') && !roles.includes('sale')) { router.replace('/403'); return }
        myRole.value = roles.includes('admin') ? 'admin' : 'sale'
        myPermissions.value = me.permissions?.map(p => p.name) ?? []
        const [invoiceRes, bankRes, metaRes] = await Promise.all([
            axios.get(`/api/order/invoices/${route.params.id}`),
            axios.get('/api/order/banks'),
            axios.get('/api/order/payments/meta'),
        ])
        invoice.value = invoiceRes.data
        banks.value = bankRes.data
        paymentMeta.value = metaRes.data
    } catch { router.push('/login') }
    finally { loading.value = false }
})
</script>
