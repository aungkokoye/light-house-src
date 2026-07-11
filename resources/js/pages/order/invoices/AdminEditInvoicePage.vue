<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-6xl mx-auto">
            <LoadingSpinner v-if="loading" />

            <template v-else>
                <div class="mb-8 flex items-center gap-3">
                    <RouterLink to="/order/invoices" @click.prevent="goBack('/order/invoices', 'invoice-list-back')" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Invoice</h1>
                        <p class="text-sm text-gray-500 mt-0.5 font-mono">{{ invoice?.invoice_no }}</p>
                    </div>
                </div>

                <div v-if="generalError" class="mb-4 rounded-xl bg-red-50 border border-red-100 px-4 py-3">
                    <p class="text-sm text-red-600">{{ generalError }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Header fields -->
                    <div class="bg-white rounded-2xl border border-gray-300 shadow-sm p-6 space-y-5">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Customer <span class="text-red-400">*</span></label>
                            <SearchableSelect
                                v-model="form.customer_id"
                                :options="selectedCustomerOption"
                                :on-search="searchCustomers"
                                placeholder="Search customer…"
                                :has-error="!!errors.customer_id" />
                            <p v-if="errors.customer_id" class="mt-1 text-xs text-red-500">{{ errors.customer_id[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Discount</label>
                            <input v-model.number="form.discount" type="number" placeholder="0"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.discount ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.discount" class="mt-1 text-xs text-red-500">{{ errors.discount[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Note</label>
                            <textarea v-model="form.note" rows="3" placeholder="Optional note…"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50 resize-none"
                                :class="errors.note ? 'border-red-300' : 'border-gray-300'"></textarea>
                            <p v-if="errors.note" class="mt-1 text-xs text-red-500">{{ errors.note[0] }}</p>
                        </div>
                    </div>

                    <!-- Invoice jobs -->
                    <div class="bg-white rounded-2xl border border-gray-300 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-50">
                            <h2 class="font-semibold text-gray-900">Jobs</h2>
                        </div>

                        <p v-if="errors.jobs" class="px-6 pt-3 text-xs text-red-500">{{ errors.jobs[0] }}</p>

                        <div class="p-4 space-y-3">
                            <div v-for="(job, i) in form.jobs" :key="i"
                                class="border border-gray-100 rounded-xl p-4 space-y-4 bg-gray-50/50">

                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-gray-400">Item {{ i + 1 }}</span>
                                    <button type="button" @click="removeJob(i)" :disabled="form.jobs.length === 1"
                                        class="p-1 text-red-400 hover:text-red-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Product <span class="text-red-400">*</span></label>
                                        <select v-model="job.product_id"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="jobError(i,'product_id') ? 'border-red-300' : 'border-gray-300'">
                                            <option value="">— Select product —</option>
                                            <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}{{ p.current_price != null ? ` (${Number(p.current_price).toLocaleString()})` : '' }}</option>
                                        </select>
                                        <p v-if="jobError(i,'product_id')" class="mt-1 text-xs text-red-500">{{ jobError(i,'product_id')[0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Service <span class="text-red-400">*</span></label>
                                        <select v-model="job.service_id"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="jobError(i,'service_id') ? 'border-red-300' : 'border-gray-300'">
                                            <option value="">— Select service —</option>
                                            <option v-for="s in services" :key="s.id" :value="s.id">{{ s.name }}</option>
                                        </select>
                                        <p v-if="jobError(i,'service_id')" class="mt-1 text-xs text-red-500">{{ jobError(i,'service_id')[0] }}</p>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                                    <textarea v-model="job.note" rows="3" placeholder="Optional job description…"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white resize-none"></textarea>
                                </div>

                                <div class="grid grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Qty <span class="text-red-400">*</span></label>
                                        <input v-model.number="job.quantity" type="number" placeholder="1"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="jobError(i,'quantity') ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="jobError(i,'quantity')" class="mt-1 text-xs text-red-500">{{ jobError(i,'quantity')[0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Unit Price <span class="text-red-400">*</span></label>
                                        <input v-model.number="job.unit_price" type="number" placeholder="0"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="jobError(i,'unit_price') ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="jobError(i,'unit_price')" class="mt-1 text-xs text-red-500">{{ jobError(i,'unit_price')[0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Delivery Date <span class="text-red-400">*</span></label>
                                        <input v-model="job.delivery_date" type="date"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="jobError(i,'delivery_date') ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="jobError(i,'delivery_date')" class="mt-1 text-xs text-red-500">{{ jobError(i,'delivery_date')[0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Total</label>
                                        <div class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-100 text-gray-700">
                                            {{ ((job.quantity || 0) * (job.unit_price || 0)).toLocaleString() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-4">
                            <button type="button" @click="addJob"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Job
                            </button>
                        </div>
                    </div>

                    <!-- Payments -->
                    <div class="bg-white rounded-2xl border border-gray-300 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-50">
                            <h2 class="font-semibold text-gray-900">Payments</h2>
                        </div>

                        <div v-if="errors.payments" class="mx-4 mt-4 rounded-xl bg-red-50 border border-red-100 px-4 py-3">
                            <p class="text-xs font-medium text-red-600">{{ errors.payments[0] }}</p>
                        </div>

                        <div class="p-4 space-y-3">
                            <p v-if="!payments.length" class="text-center text-sm text-gray-400 py-4">No payments yet.</p>

                            <div v-for="(pmt, i) in payments" :key="pmt._key"
                                class="border border-gray-100 rounded-xl p-4 space-y-4 bg-gray-50/50">

                                <!-- Card header -->
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-gray-400">Payment {{ i + 1 }}</span>
                                    <button type="button" @click="removePayment(i)"
                                        class="p-1 text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                    </button>
                                </div>

                                <!-- Payment Type + Stage -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Payment Type <span class="text-red-400">*</span></label>
                                        <select v-model.number="pmt.payment_type_id"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="pmtError(i,'payment_type_id') ? 'border-red-300' : 'border-gray-300'">
                                            <option value="">Select payment type</option>
                                            <option v-for="b in paymentTypes" :key="b.id" :value="b.id">{{ b.name }}</option>
                                        </select>
                                        <p v-if="pmtError(i,'payment_type_id')" class="mt-1 text-xs text-red-500">{{ pmtError(i,'payment_type_id') }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Stage <span class="text-red-400">*</span></label>
                                        <select v-model.number="pmt.stage"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="pmtError(i,'stage') ? 'border-red-300' : 'border-gray-300'">
                                            <option value="">Select stage</option>
                                            <option v-for="s in paymentMeta?.stages" :key="s.id" :value="s.id">{{ s.name }}</option>
                                        </select>
                                        <p v-if="pmtError(i,'stage')" class="mt-1 text-xs text-red-500">{{ pmtError(i,'stage') }}</p>
                                    </div>
                                </div>

                                <!-- Amount + Date -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Amount <span class="text-red-400">*</span></label>
                                        <input v-model.number="pmt.amount" type="number"
                                            :placeholder="pmt.stage === (paymentMeta?.stage_refund ?? 3) ? 'e.g. -1000' : '0'"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="(pmtError(i,'amount') || anyAmountError) ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="pmt.stage === (paymentMeta?.stage_refund ?? 3) && !pmtError(i,'amount')" class="mt-1 text-xs text-amber-600">For refund, enter a negative value (e.g. -1000).</p>
                                        <p v-if="pmtError(i,'amount')" class="mt-1 text-xs text-red-500">{{ pmtError(i,'amount') }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Payment Date <span class="text-red-400">*</span></label>
                                        <input v-model="pmt.payment_date" type="date"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="pmtError(i,'payment_date') ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="pmtError(i,'payment_date')" class="mt-1 text-xs text-red-500">{{ pmtError(i,'payment_date') }}</p>
                                    </div>
                                </div>

                                <!-- Note -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Note</label>
                                    <textarea v-model="pmt.note" rows="2" placeholder="Optional note…"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white resize-none"></textarea>
                                </div>
                            </div>

                        </div>
                        <div class="px-4 pb-4">
                            <button v-if="!hasFinalPayment || can.add_refund" type="button" @click="addPayment"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Payment
                            </button>
                        </div>
                    </div>

                    <!-- Summary + Submit -->
                    <div class="bg-white rounded-2xl border border-gray-300 shadow-sm p-6">
                        <div class="flex items-end justify-between">
                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex gap-4">
                                    <span>Subtotal</span>
                                    <span class="font-medium text-gray-900">{{ subtotal.toLocaleString() }}</span>
                                </div>
                                <div class="flex gap-4">
                                    <span>Discount</span>
                                    <span class="font-medium text-gray-900">{{ (form.discount || 0).toLocaleString() }}</span>
                                </div>
                                <div class="flex gap-4 text-base font-semibold text-gray-900 border-t border-gray-100 pt-1 mt-1">
                                    <span>Total</span>
                                    <span>{{ grandTotal.toLocaleString() }}</span>
                                </div>
                                <div class="flex gap-4 border-t border-gray-100 pt-1 mt-1">
                                    <span>Paid</span>
                                    <span class="font-medium text-gray-900">{{ totalPaid.toLocaleString() }}</span>
                                </div>
                                <div class="flex gap-4">
                                    <span>Balance</span>
                                    <span class="font-semibold" :class="balance === 0 ? 'text-emerald-600' : balance < 0 ? 'text-red-600' : 'text-gray-900'">
                                        {{ balance.toLocaleString() }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="button" @click="goBack('/order/invoices', 'invoice-list-back')" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</button>
                                <button type="submit" :disabled="submitting"
                                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                                    {{ submitting ? 'Saving…' : 'Save Changes' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <DeleteModal
        :show="deleteJobModal.show"
        title="Remove Job"
        message="Are you sure you want to remove this job?"
        @confirm="confirmDeleteJob"
        @cancel="deleteJobModal.show = false"
    />

    <DeleteModal
        :show="deletePaymentModal.show"
        title="Delete Payment"
        message="Are you sure you want to delete this payment?"
        @confirm="confirmDeletePayment"
        @cancel="deletePaymentModal.show = false"
    />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import DeleteModal from '../../../components/DeleteModal.vue'
import { useGoBack } from '../../../composables/useGoBack'

const router = useRouter()
const route = useRoute()
const { goBack } = useGoBack()
const loading = ref(true)
const submitting = ref(false)
const errors = ref({})
const generalError = ref('')
const invoice = ref(null)
const can = ref({})
const paymentTypes = ref([])
const products = ref([])
const services = ref([])
const paymentMeta = ref(null)

const selectedCustomerOption = computed(() => {
    const c = invoice.value?.customer
    if (!c) return []
    return [{
        value: c.id,
        label: [c.name, c.company_name].filter(Boolean).join(' — '),
        sub:   c.phone,
    }]
})

async function searchCustomers(q) {
    if (!q || q.length < 2) return []
    const { data } = await axios.get('/api/order/customers/search', { params: { search: q } })
    return data.map(c => ({
        value: c.id,
        label: [c.name, c.company_name].filter(Boolean).join(' — '),
        sub:   c.phone,
    }))
}


const form = ref({ customer_id: '', discount: 0, note: '', jobs: [] })

// payments state
let _pmtKey = 0
const payments = ref([])
const deletePaymentModal = ref({ show: false, index: null })
const deleteJobModal = ref({ show: false, index: null })

function newJob() {
    return { id: null, service_id: '', product_id: '', quantity: 1, unit_price: 0, delivery_date: '', note: '' }
}
function addJob() { form.value.jobs.push(newJob()) }
function removeJob(i) { deleteJobModal.value = { show: true, index: i } }
async function confirmDeleteJob() {
    const i = deleteJobModal.value.index
    deleteJobModal.value = { show: false, index: null }
    const job = form.value.jobs[i]
    if (job.id) {
        try {
            await axios.delete(`/api/order/invoices/${route.params.id}/jobs/${job.id}`)
        } catch (e) {
            generalError.value = e?.response?.data?.message ?? 'Failed to delete job. Please try again.'
            return
        }
    }
    form.value.jobs.splice(i, 1)
}
function jobError(i, field) { return errors.value[`jobs.${i}.${field}`] }

function newPayment() {
    return { _key: ++_pmtKey, id: null, payment_type_id: '', stage: '', amount: 0, payment_date: '', note: '' }
}
function addPayment() { payments.value.push(newPayment()) }
function removePayment(i) {
    deletePaymentModal.value = { show: true, index: i }
}
async function confirmDeletePayment() {
    const i = deletePaymentModal.value.index
    deletePaymentModal.value = { show: false, index: null }
    const pmt = payments.value[i]
    if (pmt.id) {
        try {
            await axios.delete(`/api/order/payments/${pmt.id}`)
        } catch {
            generalError.value = 'Failed to delete payment. Please try again.'
            return
        }
    }
    payments.value.splice(i, 1)
}
function pmtError(i, field) {
    return errors.value[`payments.${i}.${field}`]?.[0] ?? null
}

const anyAmountError  = computed(() => !!errors.value.payments)
const hasFinalPayment = computed(() => payments.value.some(p => p.stage === (paymentMeta.value?.stage_final ?? 2) && p.stage !== (paymentMeta.value?.stage_refund ?? 3)))

const today = new Date().toISOString().slice(0, 10)

const subtotal = computed(() =>
    form.value.jobs.reduce((sum, j) => sum + (j.quantity || 0) * (j.unit_price || 0), 0)
)
const grandTotal  = computed(() => Math.max(0, subtotal.value - (form.value.discount || 0)))
const totalPaid   = computed(() => payments.value.reduce((sum, p) => sum + (p.amount || 0), 0))
const balance     = computed(() => grandTotal.value - totalPaid.value)

async function submit() {
    errors.value = {}
    generalError.value = ''
    submitting.value = true

    try {
        await axios.put(`/api/order/invoices/${route.params.id}`, {
            ...form.value,
            payments: payments.value.map(p => ({
                id:               p.id || undefined,
                payment_type_id:  p.payment_type_id || null,
                stage:        p.stage,
                amount:       p.amount,
                payment_date: p.payment_date,
                note:         p.note || null,
            })),
        })
        router.push(`/order/invoices/${route.params.id}`)
    } catch (e) {
        if (e?.response?.status === 422) errors.value = e.response.data.errors ?? {}
        else generalError.value = 'Something went wrong. Please try again.'
    } finally {
        submitting.value = false
    }
}

onMounted(async () => {
    if (!localStorage.getItem('token')) { router.push('/login'); return }
    try {
        const [invoiceRes, paymentTypeRes, metaRes, productRes, serviceRes] = await Promise.all([
            axios.get(`/api/order/invoices/${route.params.id}`),
            axios.get('/api/order/payment-types'),
            axios.get('/api/order/payments/meta'),
            axios.get('/api/order/products', { params: { per_page: 100, sort_by: 'name', sort_dir: 'asc' } }),
            axios.get('/api/order/services', { params: { per_page: 100, sort_by: 'name', sort_dir: 'asc' } }),
        ])
        invoice.value      = invoiceRes.data
        can.value          = invoiceRes.data.can ?? {}
        paymentTypes.value = paymentTypeRes.data.data ?? []
        products.value    = productRes.data.data ?? []
        services.value    = serviceRes.data.data ?? []
        paymentMeta.value = metaRes.data

        form.value.customer_id = invoice.value.customer_id
        form.value.discount    = invoice.value.discount
        form.value.note        = invoice.value.note ?? ''
        form.value.jobs        = invoice.value.jobs.map(j => ({
            id:            j.id,
            service_id:    j.service_id,
            product_id:    j.product_id,
            quantity:      j.quantity,
            unit_price:    j.unit_price,
            delivery_date: (j.delivery_date ?? '').slice(0, 10),
            note:          j.note ?? '',
        }))
        if (!form.value.jobs.length) {
            form.value.jobs.push(newJob())
        }


        payments.value = (invoice.value.payments ?? []).map(p => ({
            _key:             ++_pmtKey,
            id:               p.id,
            payment_type_id:  p.payment_type_id ?? '',
            stage:        p.stage,
            amount:       p.amount,
            payment_date: (p.payment_date ?? '').slice(0, 10),
            note:         p.note ?? '',
        }))
        loading.value = false
    } catch { generalError.value = 'Failed to load invoice.' }
})
</script>
