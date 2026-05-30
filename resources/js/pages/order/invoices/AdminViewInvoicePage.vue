<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-4xl mx-auto">
            <LoadingSpinner v-if="loading" />

            <template v-else>
                <div class="mb-6 flex items-center gap-3">
                    <RouterLink to="/order/invoices" @click.prevent="goBack('/order/invoices', 'invoice-list-back')" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 font-mono">{{ invoice.invoice_no }}</h1>
                        <p class="text-xs text-gray-400 mt-0.5">Created {{ formatDate(invoice.created_at) }} by {{ invoice.created_by?.name ?? '—' }}</p>
                    </div>
                    <span class="relative group">
                        <a href="#" @click.prevent="sendInvoice"
                            :class="[sendingInvoice || !invoice.customer?.email ? 'opacity-50 pointer-events-none' : '']"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                            {{ sendingInvoice ? 'Sending…' : 'Send Invoice' }}
                        </a>
                        <span v-if="!invoice.customer?.email"
                            class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 whitespace-nowrap rounded bg-gray-800 px-2 py-1 text-xs text-white opacity-0 group-hover:opacity-100 transition-opacity">
                            Customer has no email address
                        </span>
                    </span>
                    <a href="#" @click.prevent="printInvoice"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" /></svg>
                        Print
                    </a>
                    <RouterLink v-if="can.edit" :to="`/order/invoices/${invoice.id}/edit`"
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
                        <h2 class="font-semibold text-gray-900">Jobs</h2>
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
                                    <th class="px-6 py-3"></th>
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
                                    <td class="px-6 py-3.5">
                                        <span class="relative group">
                                            <button type="button" @click="job.note ? noteModal = job.note : null"
                                                :class="job.note ? 'text-gray-400 hover:text-indigo-600 hover:bg-gray-100' : 'opacity-30 cursor-not-allowed'"
                                                class="p-1 rounded transition-colors">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                                </svg>
                                            </button>
                                            <span v-if="!job.note"
                                                class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 whitespace-nowrap rounded bg-gray-800 px-2 py-1 text-xs text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                                No note
                                            </span>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="border-t border-gray-100 bg-gray-50/50">
                                    <td colspan="7" class="px-6 py-3 text-right text-xs text-gray-400">Subtotal</td>
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ subtotal.toLocaleString() }}</td>
                                </tr>
                                <tr v-if="invoice.discount > 0" class="bg-gray-50/50">
                                    <td colspan="7" class="px-6 py-2 text-right text-xs text-gray-400">Discount</td>
                                    <td class="px-6 py-2 text-gray-700">- {{ invoice.discount.toLocaleString() }}</td>
                                </tr>
                                <tr class="bg-indigo-50/50">
                                    <td colspan="7" class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total</td>
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
                            <button v-if="can.add_payment && !hasFinalPayment" type="button" @click="openAddPayment"
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
                                    <th class="px-3 py-2 font-medium">#</th>
                                    <th class="px-3 py-2 font-medium">Type</th>
                                    <th class="px-3 py-2 font-medium">Bank</th>
                                    <th class="px-3 py-2 font-medium">Stage</th>
                                    <th class="px-3 py-2 font-medium">Amount</th>
                                    <th class="px-3 py-2 font-medium">Date</th>
                                    <th class="px-3 py-2 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-if="!invoice.payments?.length">
                                    <td colspan="7" class="px-3 py-6 text-center text-sm text-gray-400">No payments yet.</td>
                                </tr>
                                <tr v-else v-for="(pmt, i) in invoice.payments" :key="pmt.id" class="hover:bg-gray-50/50">
                                    <td class="px-3 py-2.5 text-xs font-mono text-gray-400">{{ i + 1 }}</td>
                                    <td class="px-3 py-2.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="TYPE_CLS[pmt.type_id]">
                                            {{ typeMap[pmt.type_id] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 text-gray-500">{{ pmt.bank?.name ?? '—' }}</td>
                                    <td class="px-3 py-2.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="STAGE_CLS[pmt.stage]">
                                            {{ stageMap[pmt.stage] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 font-medium text-gray-900">{{ pmt.amount.toLocaleString() }}</td>
                                    <td class="px-3 py-2.5 text-xs text-gray-500">{{ formatDate(pmt.payment_date) }}</td>
                                    <td class="px-3 py-2.5">
                                        <div class="flex items-center gap-1">
                                            <span class="relative group">
                                                <button @click="sendReceipt(pmt)" type="button"
                                                    :class="[sendingReceiptId === pmt.id || !invoice.customer?.email ? 'opacity-50 pointer-events-none' : '']"
                                                    class="p-1 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                                                </button>
                                                <span v-if="!invoice.customer?.email"
                                                    class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 whitespace-nowrap rounded bg-gray-800 px-2 py-1 text-xs text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                                    Customer has no email address
                                                </span>
                                            </span>
                                            <button @click="printReceipt(pmt)" type="button"
                                                class="p-1 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" /></svg>
                                            </button>
                                            <span class="relative group">
                                                <button type="button" @click="pmt.note ? noteModal = pmt.note : null"
                                                    :class="pmt.note ? 'text-gray-400 hover:text-indigo-600 hover:bg-gray-100' : 'opacity-30 cursor-not-allowed'"
                                                    class="p-1 rounded transition-colors">
                                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                                    </svg>
                                                </button>
                                                <span v-if="!pmt.note"
                                                    class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 whitespace-nowrap rounded bg-gray-800 px-2 py-1 text-xs text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                                    No note
                                                </span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="border-t border-gray-100 bg-gray-50/50">
                                    <td colspan="6" class="px-3 py-2 text-right text-xs text-gray-400">Total Paid</td>
                                    <td class="px-3 py-2 font-medium text-gray-900 text-right">{{ totalPaid.toLocaleString() }}</td>
                                </tr>
                                <tr :class="balance === 0 ? 'bg-emerald-50/50' : 'bg-red-50/50'">
                                    <td colspan="6" class="px-3 py-2 text-right text-sm font-semibold text-gray-700">Balance</td>
                                    <td class="px-3 py-2 text-base font-bold text-right" :class="balance === 0 ? 'text-emerald-600' : 'text-red-600'">
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

    <!-- Print layout (hidden on screen, visible when printing) -->
    <div id="invoice-print" class="hidden">
        <div class="print-page">
            <!-- Header -->
            <div class="print-header">
                <div>
                    <div class="print-logo-row">
                        <img :src="'/images/logo.png'" alt="Light House" class="print-logo" />
                        <div class="print-company-name">{{ COMPANY_NAME }}</div>
                    </div>
                    <div class="print-company-info">
                        <div class="print-contact-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            {{ COMPANY_ADDRESS }}
                        </div>
                        <div class="print-contact-row">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            {{ COMPANY_FACEBOOK }}
                        </div>
                        <div class="print-contact-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                            {{ COMPANY_PHONE }}
                        </div>
                        <div class="print-contact-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                            {{ COMPANY_EMAIL }}
                        </div>
                    </div>
                </div>
                <div class="print-header-right">
                    <div class="print-invoice-label">INVOICE</div>
                    <div class="print-invoice-no">{{ invoice?.invoice_no }}</div>
                    <div class="print-meta">Date: {{ formatDate(invoice?.created_at) }}</div>
                    <div class="print-meta">Created by: {{ invoice?.created_by?.name || invoice?.createdBy?.name || '—' }}</div>
                </div>
            </div>

            <hr class="print-divider" />

            <!-- Customer & Invoice info -->
            <div class="print-info-grid">
                <div>
                    <div class="print-section-title">Bill To</div>
                    <div class="print-value-lg">{{ invoice?.customer?.name }}</div>
                    <div v-if="invoice?.customer?.company_profile?.name" class="print-value">{{ invoice.customer.company_profile.name }}</div>
                    <div class="print-value-muted">{{ invoice?.customer?.email }}</div>
                </div>
                <div>
                    <div class="print-section-title">Summary</div>
                    <table class="print-summary-table">
                        <tr><td class="print-summary-label">Subtotal</td><td class="print-summary-value">{{ subtotal.toLocaleString() }}</td></tr>
                        <tr v-if="invoice?.discount > 0"><td class="print-summary-label">Discount</td><td class="print-summary-value">- {{ invoice.discount.toLocaleString() }}</td></tr>
                        <tr class="print-total-row"><td class="print-summary-label">Total</td><td class="print-summary-value print-total-value">{{ invoice?.total.toLocaleString() }}</td></tr>
                    </table>
                </div>
            </div>

            <!-- Jobs -->
            <div class="print-section-title" style="margin-top:24px;">Jobs</div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th>#</th><th>Product</th><th>Service</th><th>Qty</th><th>Unit Price</th><th>Delivery Date</th><th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(job, i) in invoice?.jobs" :key="job.id">
                        <td>{{ i + 1 }}</td>
                        <td>{{ job.product?.name ?? '—' }}</td>
                        <td>{{ job.service?.name ?? '—' }}</td>
                        <td>{{ job.quantity }}</td>
                        <td>{{ job.unit_price.toLocaleString() }}</td>
                        <td>{{ formatDate(job.delivery_date) }}</td>
                        <td>{{ job.total.toLocaleString() }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Note -->
            <div v-if="invoice?.note" class="print-note">
                <span class="print-note-label">Note:</span> {{ invoice.note }}
            </div>

        </div>
    </div>

    <!-- Receipt print layout -->
    <div id="receipt-print" v-show="receiptPayment" class="hidden">
        <div class="print-page">
            <div class="print-header">
                <div>
                    <div class="print-logo-row">
                        <img :src="'/images/logo.png'" :alt="COMPANY_NAME" class="print-logo" />
                        <div class="print-company-name">{{ COMPANY_NAME }}</div>
                    </div>
                    <div class="print-company-info">
                        <div class="print-contact-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            {{ COMPANY_ADDRESS }}
                        </div>
                        <div class="print-contact-row">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            {{ COMPANY_FACEBOOK }}
                        </div>
                        <div class="print-contact-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                            {{ COMPANY_PHONE }}
                        </div>
                        <div class="print-contact-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                            {{ COMPANY_EMAIL }}
                        </div>
                    </div>
                </div>
                <div class="print-header-right">
                    <div class="print-receipt-label">RECEIPT</div>
                    <div class="print-invoice-no">{{ invoice?.invoice_no }}</div>
                    <div class="print-meta">Date: {{ formatDate(receiptPayment?.payment_date) }}</div>
                    <div class="print-meta">Created by: {{ receiptPayment?.created_by?.name || receiptPayment?.createdBy?.name || '—' }}</div>
                </div>
            </div>

            <hr class="print-divider" />

            <div class="print-info-grid">
                <div>
                    <div class="print-section-title">Bill To</div>
                    <div class="print-value-lg">{{ invoice?.customer?.name }}</div>
                    <div v-if="invoice?.customer?.company_profile?.name" class="print-value">{{ invoice.customer.company_profile.name }}</div>
                    <div class="print-value-muted">{{ invoice?.customer?.email }}</div>
                </div>
                <div>
                    <div class="print-section-title">Invoice Reference</div>
                    <table class="print-summary-table">
                        <tr><td class="print-summary-label">Invoice No</td><td class="print-summary-value" style="font-family:monospace;font-weight:600;">{{ invoice?.invoice_no }}</td></tr>
                        <tr class="print-receipt-total-row">
                            <td class="print-summary-label">Amount Paid</td>
                            <td class="print-summary-value print-receipt-amount">{{ receiptPayment?.amount?.toLocaleString() }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="print-section-title" style="margin-top:24px;">Payment Details</div>
            <table class="print-table">
                <thead>
                    <tr><th>Type</th><th>Bank</th><th>Stage</th><th>Amount</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ typeMap[receiptPayment?.type_id] }}</td>
                        <td>{{ receiptPayment?.bank?.name ?? '—' }}</td>
                        <td>{{ stageMap[receiptPayment?.stage] }}</td>
                        <td>{{ receiptPayment?.amount?.toLocaleString() }}</td>
                        <td>{{ formatDate(receiptPayment?.payment_date) }}</td>
                    </tr>
                </tbody>
            </table>

            <div v-if="receiptPayment?.note" class="print-note">
                <span class="print-note-label">Note:</span> {{ receiptPayment.note }}
            </div>
        </div>
    </div>

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
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import { useFormatDate } from '../../../composables/useFormatDate'
import { useGoBack } from '../../../composables/useGoBack'

const COMPANY_NAME     = import.meta.env.VITE_COMPANY_NAME     ?? ''
const COMPANY_ADDRESS  = import.meta.env.VITE_COMPANY_ADDRESS  ?? ''
const COMPANY_PHONE    = import.meta.env.VITE_COMPANY_PHONE    ?? ''
const COMPANY_EMAIL    = import.meta.env.VITE_COMPANY_EMAIL    ?? ''
const COMPANY_FACEBOOK = import.meta.env.VITE_COMPANY_FACEBOOK ?? ''

const router = useRouter()
const { goBack } = useGoBack()
const route = useRoute()
const { formatDate } = useFormatDate()
const loading = ref(true)
const invoice = ref(null)
const noteModal = ref(null)
const can = ref({})
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

function printInvoice() {
    document.body.dataset.print = 'invoice'
    window.print()
}

const sendingInvoice = ref(false)
const receiptPayment = ref(null)
const sendingReceiptId = ref(null)

async function printReceipt(pmt) {
    receiptPayment.value = pmt
    document.body.dataset.print = 'receipt'
    await nextTick()
    window.print()
}

async function sendReceipt(pmt) {
    sendingReceiptId.value = pmt.id
    try {
        await axios.post(`/api/order/payments/${pmt.id}/send-receipt`)
    } catch (e) {
        console.error('Failed to send receipt', e)
    } finally {
        sendingReceiptId.value = null
    }
}

async function sendInvoice() {
    sendingInvoice.value = true
    try {
        await axios.post(`/api/order/invoices/${invoice.value.id}/send`)
    } catch (e) {
        console.error('Failed to send invoice', e)
    } finally {
        sendingInvoice.value = false
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
        const [invoiceRes, bankRes, metaRes] = await Promise.all([
            axios.get(`/api/order/invoices/${route.params.id}`),
            axios.get('/api/order/banks'),
            axios.get('/api/order/payments/meta'),
        ])
        invoice.value = invoiceRes.data
        can.value     = invoiceRes.data.can ?? {}
        banks.value   = bankRes.data.data ?? []
        paymentMeta.value = metaRes.data
    } catch { router.push('/login') }
    finally { loading.value = false }

    window.addEventListener('afterprint', () => {
        receiptPayment.value = null
        delete document.body.dataset.print
    })
})
</script>

<style>
@media print {
    body > * { display: none !important; }
    body[data-print] #app { display: block !important; }
    body[data-print] #app > *:not(#invoice-print):not(#receipt-print) { display: none !important; }
    body[data-print="invoice"] #invoice-print { display: block !important; }
    body[data-print="receipt"] #receipt-print { display: block !important; }

    .print-page { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #111827; padding: 32px; }
    .print-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .print-logo { height: 48px; }
    .print-header-right { text-align: right; }
    .print-invoice-label { font-size: 22px; font-weight: 700; color: #4f46e5; letter-spacing: 2px; }
    .print-invoice-no { font-size: 14px; font-weight: 600; font-family: monospace; color: #374151; margin-top: 2px; }
    .print-meta { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .print-company-info { margin-top: 8px; font-size: 11px; color: #6b7280; }
    .print-logo-row { display: flex; align-items: center; gap: 10px; }
    .print-company-name { font-size: 15px; font-weight: 800; color: #111827; }
    .print-contact-row { display: flex; align-items: center; gap: 5px; margin-top: 4px; }
    .print-contact-row svg { width: 11px; height: 11px; flex-shrink: 0; color: #9ca3af; }
    .print-divider { border: none; border-top: 1px solid #e5e7eb; margin: 16px 0; }
    .print-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 8px; }
    .print-section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #9ca3af; margin-bottom: 6px; }
    .print-value-lg { font-size: 14px; font-weight: 600; color: #111827; }
    .print-value { font-size: 12px; color: #374151; margin-top: 2px; }
    .print-value-muted { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .print-summary-table { width: 100%; border-collapse: collapse; }
    .print-summary-label { padding: 3px 0; color: #6b7280; }
    .print-summary-value { padding: 3px 0; text-align: right; font-weight: 500; }
    .print-total-row td { border-top: 1px solid #e5e7eb; padding-top: 6px; font-weight: 700; font-size: 14px; color: #4f46e5; }
    .print-total-value { color: #4f46e5; }
    .print-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 11px; }
    .print-table th { background: #f9fafb; padding: 7px 10px; text-align: left; font-weight: 600; color: #6b7280; border-bottom: 1px solid #e5e7eb; font-size: 10px; text-transform: uppercase; letter-spacing: .05em; }
    .print-table td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; color: #374151; }
    .print-table tfoot td { border-top: 1px solid #e5e7eb; font-weight: 600; background: #f9fafb; }
    .print-balance-row td { font-weight: 700; font-size: 13px; color: #4f46e5; }
    .print-note { margin-top: 12px; font-size: 11px; color: #6b7280; background: #f9fafb; padding: 8px 12px; border-radius: 6px; }
    .print-note-label { font-weight: 600; color: #374151; }
    .print-receipt-label { font-size: 22px; font-weight: 700; color: #059669; letter-spacing: 2px; }
    .print-receipt-total-row td { border-top: 1px solid #e5e7eb; padding-top: 6px; font-weight: 700; font-size: 13px; color: #059669; }
    .print-receipt-amount { color: #059669; }
}
</style>
