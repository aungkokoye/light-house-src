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
                        <h1 class="text-3xl font-bold text-gray-900">New Invoice</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Create a new customer invoice.</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Header fields -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-medium text-gray-600">Customer <span class="text-red-400">*</span></label>
                                <button type="button" @click="showNewCustomer = true"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    New Customer
                                </button>
                            </div>
                            <SearchableSelect
                                v-model="form.customer_id"
                                :options="customerOptions"
                                :on-search="searchCustomers"
                                placeholder="Search by customer name or company…"
                                :has-error="!!errors.customer_id"
                                @update:modelValue="newlyRegistered = false" />
                            <p v-if="newlyRegistered" class="mt-1 text-xs text-indigo-500">Newly registered customer</p>
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
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                            <h2 class="font-semibold text-gray-900">Jobs</h2>
                            <button type="button" @click="addJob"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Job
                            </button>
                        </div>

                        <p v-if="errors.jobs" class="px-6 pt-3 text-xs text-red-500">{{ errors.jobs[0] }}</p>

                        <div class="p-4 space-y-3">
                            <div v-for="(job, i) in form.jobs" :key="i"
                                class="border border-gray-100 rounded-xl p-4 space-y-4 bg-gray-50/50">

                                <!-- Card header -->
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-gray-400">Item {{ i + 1 }}</span>
                                    <button type="button" @click="removeJob(i)" :disabled="form.jobs.length === 1"
                                        class="p-1 text-red-400 hover:text-red-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                    </button>
                                </div>

                                <!-- Row 1: Product + Service -->
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

                                <!-- Row 2: Qty, Unit Price, Total, Delivery Date -->
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

                                <!-- Note -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Note</label>
                                    <textarea v-model="job.note" rows="3" placeholder="Optional job note…"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white resize-none"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                            <h2 class="font-semibold text-gray-900">Payment</h2>
                            <button v-if="!includePayment" type="button" @click="includePayment = true"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Payment
                            </button>
                        </div>

                        <div v-if="includePayment" class="p-4">
                            <div class="border border-gray-100 rounded-xl p-4 space-y-4 bg-gray-50/50">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-gray-400">Payment 1</span>
                                    <button type="button" @click="includePayment = false"
                                        class="p-1 text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Payment Type <span class="text-red-400">*</span></label>
                                        <select v-model.number="form.payment.payment_type_id"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="errors['payment.payment_type_id'] ? 'border-red-300' : 'border-gray-300'">
                                            <option value="">Select payment type…</option>
                                            <option v-for="b in paymentTypes" :key="b.id" :value="b.id">{{ b.name }}</option>
                                        </select>
                                        <p v-if="errors['payment.payment_type_id']" class="mt-1 text-xs text-red-500">{{ errors['payment.payment_type_id'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Stage <span class="text-red-400">*</span></label>
                                        <select v-model.number="form.payment.stage"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="errors['payment.stage'] ? 'border-red-300' : 'border-gray-300'">
                                            <option value="">Select stage…</option>
                                            <option v-for="s in paymentMeta?.stages?.filter(s => s.id !== paymentMeta?.stage_refund)" :key="s.id" :value="s.id">{{ s.name }}</option>
                                        </select>
                                        <p v-if="errors['payment.stage']" class="mt-1 text-xs text-red-500">{{ errors['payment.stage'][0] }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Amount <span class="text-red-400">*</span></label>
                                        <input v-model.number="form.payment.amount" type="number" placeholder="0"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="errors['payment.amount'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['payment.amount']" class="mt-1 text-xs text-red-500">{{ errors['payment.amount'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Payment Date <span class="text-red-400">*</span></label>
                                        <input v-model="form.payment.payment_date" type="date"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white"
                                            :class="errors['payment.payment_date'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['payment.payment_date']" class="mt-1 text-xs text-red-500">{{ errors['payment.payment_date'][0] }}</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Note</label>
                                    <textarea v-model="form.payment.note" rows="2" placeholder="Optional note…"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-white resize-none"></textarea>
                                </div>
                            </div>
                        </div>

                        <p v-if="!includePayment" class="px-6 py-4 text-sm text-gray-400">No payment added.</p>
                    </div>

                    <!-- Summary + Submit -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
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
                            </div>

                            <div class="flex items-center gap-3">
                                <p v-if="generalError" class="text-xs text-red-500">{{ generalError }}</p>
                                <button type="button" @click="goBack('/order/invoices', 'invoice-list-back')" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</button>
                                <button type="submit" :disabled="submitting"
                                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                                    {{ submitting ? 'Creating…' : 'Create Invoice' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- New Customer Modal -->
    <Teleport to="body">
        <div v-if="showNewCustomer" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeNewCustomer"></div>

            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Modal header -->
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Register New Customer</h3>
                    <button type="button" @click="closeNewCustomer" class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Name <span class="text-red-400">*</span></label>
                            <input v-model="newCustomer.name" type="text" placeholder="Full name"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="newCustomerErrors.name ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="newCustomerErrors.name" class="mt-1 text-xs text-red-500">{{ newCustomerErrors.name[0] }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone <span class="text-red-400">*</span></label>
                            <input v-model="newCustomer.phone" type="text" placeholder="+95 9 xxx xxx xxx"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="newCustomerErrors.phone ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="newCustomerErrors.phone" class="mt-1 text-xs text-red-500">{{ newCustomerErrors.phone[0] }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email</label>
                            <input v-model="newCustomer.email" type="email" placeholder="email@example.com"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="newCustomerErrors.email ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="newCustomerErrors.email" class="mt-1 text-xs text-red-500">{{ newCustomerErrors.email[0] }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Company Name</label>
                                <input v-model="newCustomer.company_name" type="text" placeholder="Company name"
                                    class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                    :class="newCustomerErrors.company_name ? 'border-red-300' : 'border-gray-300'" />
                                <p v-if="newCustomerErrors.company_name" class="mt-1 text-xs text-red-500">{{ newCustomerErrors.company_name[0] }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Title / Role</label>
                                <input v-model="newCustomer.title" type="text" placeholder="e.g. Manager"
                                    class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                    :class="newCustomerErrors.title ? 'border-red-300' : 'border-gray-300'" />
                                <p v-if="newCustomerErrors.title" class="mt-1 text-xs text-red-500">{{ newCustomerErrors.title[0] }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Address</label>
                            <textarea v-model="newCustomer.address" rows="2" placeholder="Customer address"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50 resize-none"
                                :class="newCustomerErrors.address ? 'border-red-300' : 'border-gray-300'"></textarea>
                            <p v-if="newCustomerErrors.address" class="mt-1 text-xs text-red-500">{{ newCustomerErrors.address[0] }}</p>
                        </div>
                    </div>

                    <p v-if="newCustomerGeneralError" class="text-xs text-red-500">{{ newCustomerGeneralError }}</p>
                </div>

                <!-- Modal footer -->
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="button" @click="closeNewCustomer" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</button>
                    <button type="button" @click="submitNewCustomer" :disabled="savingCustomer"
                        class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                        {{ savingCustomer ? 'Registering…' : 'Register Customer' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>

    <DeleteModal
        :show="deleteJobModal.show"
        title="Remove Job"
        message="Are you sure you want to remove this job?"
        @confirm="confirmDeleteJob"
        @cancel="deleteJobModal.show = false"
    />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import DeleteModal from '../../../components/DeleteModal.vue'
import SearchableSelect from '../../../components/SearchableSelect.vue'
import { useGoBack } from '../../../composables/useGoBack'
const router = useRouter()
const { goBack } = useGoBack()
const loading = ref(true)
const submitting = ref(false)
const errors = ref({})
const generalError = ref('')
const paymentTypes = ref([])
const products = ref([])
const services = ref([])
const paymentMeta = ref(null)
const customerOptions    = ref([])
const showNewCustomer    = ref(false)
const newlyRegistered    = ref(false)
const savingCustomer = ref(false)
const newCustomerErrors = ref({})
const newCustomerGeneralError = ref('')
const newCustomer = ref({
    name: '', phone: '', email: '', company_name: '', title: '', address: '',
})

function closeNewCustomer() {
    showNewCustomer.value = false
    newCustomerErrors.value = {}
    newCustomerGeneralError.value = ''
    newCustomer.value = { name: '', phone: '', email: '', company_name: '', title: '', address: '' }
}

async function submitNewCustomer() {
    newCustomerErrors.value = {}
    newCustomerGeneralError.value = ''
    savingCustomer.value = true
    try {
        const { data } = await axios.post('/api/order/customers', newCustomer.value)
        customerOptions.value = [{
            value: data.id,
            label: [data.name, data.company_name].filter(Boolean).join(' — '),
            sub:   data.phone,
        }]
        form.value.customer_id = data.id
        newlyRegistered.value = true
        closeNewCustomer()
    } catch (e) {
        if (e?.response?.status === 422) newCustomerErrors.value = e.response.data.errors ?? {}
        else newCustomerGeneralError.value = 'Something went wrong. Please try again.'
    } finally {
        savingCustomer.value = false
    }
}

async function searchCustomers(q) {
    if (!q || q.length < 2) return []
    const { data } = await axios.get('/api/order/customers/search', { params: { search: q } })
    return data.map(c => ({
        value: c.id,
        label: [c.name, c.company_name].filter(Boolean).join(' — '),
        sub:   c.phone,
    }))
}


const includePayment = ref(false)

const form = ref({
    customer_id: '',
    discount: 0,
    note: '',
    jobs: [newJob()],
    payment: { payment_type_id: '', stage: '', amount: 0, payment_date: '', note: '' },
})

function newJob() {
    return { service_id: '', product_id: '', quantity: 1, unit_price: 0, delivery_date: '', note: '' }
}

function addJob() { form.value.jobs.push(newJob()) }
const deleteJobModal = ref({ show: false, index: null })
function removeJob(i) { deleteJobModal.value = { show: true, index: i } }
function confirmDeleteJob() {
    form.value.jobs.splice(deleteJobModal.value.index, 1)
    deleteJobModal.value = { show: false, index: null }
}

function jobError(i, field) {
    return errors.value[`jobs.${i}.${field}`]
}

const today = new Date().toISOString().slice(0, 10)

const subtotal = computed(() =>
    form.value.jobs.reduce((sum, j) => sum + (j.quantity || 0) * (j.unit_price || 0), 0)
)
const grandTotal = computed(() => Math.max(0, subtotal.value - (form.value.discount || 0)))

async function submit() {
    errors.value = {}
    generalError.value = ''
    submitting.value = true
    try {
        const { data } = await axios.post('/api/order/invoices', {
            ...form.value,
            payment: includePayment.value ? form.value.payment : null,
        })
        router.push(`/order/invoices/${data.id}`)
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
        const [paymentTypeRes, metaRes, productRes, serviceRes] = await Promise.all([
            axios.get('/api/order/payment-types'),
            axios.get('/api/order/payments/meta'),
            axios.get('/api/order/products', { params: { per_page: 100, sort_by: 'name', sort_dir: 'asc' } }),
            axios.get('/api/order/services', { params: { per_page: 100, sort_by: 'name', sort_dir: 'asc' } }),
        ])
        paymentTypes.value = paymentTypeRes.data.data ?? []
        products.value    = productRes.data.data ?? []
        services.value    = serviceRes.data.data ?? []
        paymentMeta.value = metaRes.data
        loading.value     = false
    } catch {
        generalError.value = 'Failed to load form data.'
    }
})
</script>
