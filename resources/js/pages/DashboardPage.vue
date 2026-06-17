<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-6xl mx-auto">

            <!-- Loading -->
            <LoadingSpinner v-if="loading" />

            <template v-else>
                <!-- Header -->
                <div class="mb-10">
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-sm text-gray-500 mt-1">Welcome back, {{ user?.name }}.</p>
                </div>

                <div class="space-y-10">

                    <!-- Order Section -->
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Order</h2>
                            <div class="flex-1 h-px bg-gray-100"></div>
                        </div>
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">

                            <!-- Invoices -->
                            <RouterLink v-if="can.invoices" to="/order/invoices"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-orange-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center group-hover:bg-orange-100 transition-colors">
                                    <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Invoices</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Manage customer invoices</p>
                                </div>
                            </RouterLink>

                            <!-- Customers -->
                            <RouterLink v-if="can.customers" to="/order/customers"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-cyan-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-cyan-50 flex items-center justify-center group-hover:bg-cyan-100 transition-colors">
                                    <svg class="w-7 h-7 text-cyan-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Customers</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Manage customer records</p>
                                </div>
                            </RouterLink>

                            <!-- Price List -->
                            <RouterLink v-if="can.price_list" to="/order/price-list"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-indigo-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6 0M9 10.5h6M12 3v1.5M6.375 6.375l-1.06-1.06M17.625 6.375l1.06-1.06M3 12h1.5M19.5 12H21M6.375 17.625l-1.06 1.06M17.625 17.625l1.06 1.06M12 19.5V21M12 6.75a5.25 5.25 0 1 0 0 10.5 5.25 5.25 0 0 0 0-10.5z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Price List</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Manage service price list</p>
                                </div>
                            </RouterLink>

                            <!-- Products -->
                            <RouterLink v-if="can.products" to="/order/products"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-emerald-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Products</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Manage products</p>
                                </div>
                            </RouterLink>

                            <!-- Services -->
                            <RouterLink v-if="can.services" to="/order/services"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-violet-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center group-hover:bg-violet-100 transition-colors">
                                    <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l5.653-4.655m5.8-5.8 1.875-1.875a2.625 2.625 0 0 1 3.712 3.712l-1.875 1.875M11.42 15.17 9.17 12.92" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Services</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Manage job services</p>
                                </div>
                            </RouterLink>

                            <!-- Payment Types -->
                            <RouterLink v-if="can.payment_types" to="/order/payment-types"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-amber-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Payment Types</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Manage payment types</p>
                                </div>
                            </RouterLink>

                            <!-- Chat Knowledge -->
                            <RouterLink v-if="can.chat_knowledge" to="/admin/chat-knowledge"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-teal-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center group-hover:bg-teal-100 transition-colors">
                                    <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Chat Knowledge</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Manage AI chatbot content</p>
                                </div>
                            </RouterLink>

                            <!-- Chat Knowledge Categories -->
                            <RouterLink v-if="can.chat_knowledge_categories" to="/admin/chat-knowledge-categories"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-teal-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center group-hover:bg-teal-100 transition-colors">
                                    <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Chat Knowledge Categories</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Manage knowledge categories</p>
                                </div>
                            </RouterLink>

                        </div>
                    </div>

                    <!-- User Section -->
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-widest">User</h2>
                            <div class="flex-1 h-px bg-gray-100"></div>
                        </div>
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">

                            <!-- Users -->
                            <RouterLink v-if="can.users" to="/admin/users"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-indigo-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Users</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Manage all users</p>
                                </div>
                            </RouterLink>

                            <!-- Staff Positions -->
                            <RouterLink v-if="can.staff_positions" to="/admin/staff-positions"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-sky-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center group-hover:bg-sky-100 transition-colors">
                                    <svg class="w-7 h-7 text-sky-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Staff Positions</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Manage staff positions</p>
                                </div>
                            </RouterLink>

                            <!-- Sites / Business Locations -->
                            <RouterLink v-if="can.sites" to="/admin/sites"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-emerald-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Business Locations</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Manage sites & locations</p>
                                </div>
                            </RouterLink>

                            <!-- Roles -->
                            <RouterLink v-if="can.roles" to="/admin/roles"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-indigo-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Roles</p>
                                    <p class="text-xs text-gray-400 mt-0.5">View roles & permissions</p>
                                </div>
                            </RouterLink>

                            <!-- Permissions -->
                            <RouterLink v-if="can.permissions" to="/admin/permissions"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-rose-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-rose-50 flex items-center justify-center group-hover:bg-rose-100 transition-colors">
                                    <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Permissions</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Manage permissions</p>
                                </div>
                            </RouterLink>

                            <!-- Audit Logs -->
                            <RouterLink v-if="can.audit_logs" to="/admin/audit-logs"
                                class="group bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col items-center gap-4 hover:shadow-md hover:border-violet-100 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center group-hover:bg-violet-100 transition-colors">
                                    <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="font-semibold text-gray-900">Audit Logs</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Track all system activity</p>
                                </div>
                            </RouterLink>

                        </div>
                    </div>

                </div>
            </template>

        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AppHeader from '../components/AppHeader.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import { useAuth } from '../composables/useAuth'

const { requireAuth } = useAuth()
const loading = ref(true)
const user = ref(null)
const can = ref({})

onMounted(async () => {
    await requireAuth(user, loading)
    if (!user.value) return
    try {
        const { data } = await axios.get('/api/admin/abilities')
        can.value = data.can ?? {}
    } catch {
        // leave can empty
    }
})
</script>
