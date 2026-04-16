<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">

            <LoadingSpinner v-if="loading" />

            <template v-else>
                <!-- Header -->
                <div class="mb-8 flex items-center gap-3">
                    <RouterLink :to="route.query.back || '/admin/users'" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">New User</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Create a new user account.</p>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <form @submit.prevent="submit" class="space-y-5">

                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Name</label>
                            <input v-model="form.name" type="text" placeholder="Full name"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.name ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name[0] }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email</label>
                            <input v-model="form.email" type="email" placeholder="email@example.com"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.email ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email[0] }}</p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Password</label>
                            <input v-model="form.password" type="password" placeholder="Min. 8 chars, A-z & 0-9"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.password ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.password" class="mt-1 text-xs text-red-500">{{ errors.password[0] }}</p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Confirm Password</label>
                            <input v-model="form.password_confirmation" type="password" placeholder="Repeat password"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50" />
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Role</label>
                            <select v-model="form.role"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.role ? 'border-red-300' : 'border-gray-300'">
                                <option v-for="role in allRoles" :key="role" :value="role">{{ role }}</option>
                            </select>
                            <p v-if="errors.role" class="mt-1 text-xs text-red-500">{{ errors.role[0] }}</p>
                        </div>

                        <!-- Permissions -->
                        <div v-if="allPermissions.length">
                            <label class="block text-xs font-medium text-gray-600 mb-2">Permissions</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label v-for="p in allPermissions" :key="p"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition-colors">
                                    <input type="checkbox" :value="p" v-model="form.permissions"
                                        class="w-3.5 h-3.5 rounded accent-indigo-600" />
                                    <span class="text-xs text-gray-700 capitalize">{{ p }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- ── Company Profile ── -->
                        <template v-if="form.role === 'customer'">
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Company Profile</p>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Company Name <span class="text-red-400">*</span></label>
                                        <input v-model="form.company_profile.name" type="text"                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['company_profile.name'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['company_profile.name']" class="mt-1 text-xs text-red-500">{{ errors['company_profile.name'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Role / Title <span class="text-red-400">*</span></label>
                                        <input v-model="form.company_profile.role" type="text"                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['company_profile.role'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['company_profile.role']" class="mt-1 text-xs text-red-500">{{ errors['company_profile.role'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                                        <textarea v-model="form.company_profile.description" rows="3"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50 resize-none"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Address <span class="text-red-400">*</span></label>
                                        <input v-model="form.company_profile.address" type="text"                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['company_profile.address'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['company_profile.address']" class="mt-1 text-xs text-red-500">{{ errors['company_profile.address'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone <span class="text-red-400">*</span></label>
                                        <input v-model="form.company_profile.phone" type="text" placeholder="+95 9 xxx xxx xxx"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['company_profile.phone'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['company_profile.phone']" class="mt-1 text-xs text-red-500">{{ errors['company_profile.phone'][0] }}</p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- ── Staff Profile ── -->
                        <template v-if="form.role && form.role !== 'customer'">
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Staff Profile</p>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                                        <input v-model="form.staff_profile.full_name" type="text"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_profile.full_name'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['staff_profile.full_name']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.full_name'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">NRC No. <span class="text-red-400">*</span></label>
                                        <input v-model="form.staff_profile.nrc_no" type="text"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_profile.nrc_no'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['staff_profile.nrc_no']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.nrc_no'][0] }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Date of Birth <span class="text-red-400">*</span></label>
                                            <input v-model="form.staff_profile.dob" type="date"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_profile.dob'] ? 'border-red-300' : 'border-gray-300'" />
                                            <p v-if="errors['staff_profile.dob']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.dob'][0] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Start Date <span class="text-red-400">*</span></label>
                                            <input v-model="form.staff_profile.start_date" type="date"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_profile.start_date'] ? 'border-red-300' : 'border-gray-300'" />
                                            <p v-if="errors['staff_profile.start_date']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.start_date'][0] }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Address <span class="text-red-400">*</span></label>
                                        <input v-model="form.staff_profile.address" type="text"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_profile.address'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['staff_profile.address']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.address'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone <span class="text-red-400">*</span></label>
                                        <input v-model="form.staff_profile.phone" type="text" placeholder="+95 9 xxx xxx xxx"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_profile.phone'] ? 'border-red-300' : 'border-gray-300'" />
                                        <p v-if="errors['staff_profile.phone']" class="mt-1 text-xs text-red-500">{{ errors['staff_profile.phone'][0] }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Staff Role -->
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Staff Role</p>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Position <span class="text-red-400">*</span></label>
                                        <select v-model="form.staff_role.staff_position_id"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_role.staff_position_id'] ? 'border-red-300' : 'border-gray-300'">
                                            <option :value="null">— Select position —</option>
                                            <option v-for="pos in allPositions" :key="pos.id" :value="pos.id">{{ pos.name }}</option>
                                        </select>
                                        <p v-if="errors['staff_role.staff_position_id']" class="mt-1 text-xs text-red-500">{{ errors['staff_role.staff_position_id'][0] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Site <span class="text-red-400">*</span></label>
                                        <select v-model="form.staff_role.site_id"
                                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                            :class="errors['staff_role.site_id'] ? 'border-red-300' : 'border-gray-300'">
                                            <option :value="null">— Select site —</option>
                                            <option v-for="site in allSites" :key="site.id" :value="site.id">{{ site.name }}</option>
                                        </select>
                                        <p v-if="errors['staff_role.site_id']" class="mt-1 text-xs text-red-500">{{ errors['staff_role.site_id'][0] }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Salary <span class="text-red-400">*</span></label>
                                            <input v-model.number="form.staff_role.salary" type="number" min="0"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_role.salary'] ? 'border-red-300' : 'border-gray-300'" />
                                            <p v-if="errors['staff_role.salary']" class="mt-1 text-xs text-red-500">{{ errors['staff_role.salary'][0] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Start Date <span class="text-red-400">*</span></label>
                                            <input v-model="form.staff_role.start_date" type="date"
                                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                                :class="errors['staff_role.start_date'] ? 'border-red-300' : 'border-gray-300'" />
                                            <p v-if="errors['staff_role.start_date']" class="mt-1 text-xs text-red-500">{{ errors['staff_role.start_date'][0] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Toggles -->
                        <div class="grid grid-cols-2 gap-4 pt-1">
                            <label class="flex items-center justify-between p-3 rounded-xl border border-gray-200 bg-gray-100 cursor-pointer">
                                <span class="text-sm text-gray-600">Activated</span>
                                <button type="button" @click="form.activated = !form.activated"
                                    class="relative inline-flex h-5 w-9 shrink-0 rounded-full transition-colors overflow-hidden"
                                    :class="form.activated ? 'bg-indigo-600' : 'bg-gray-400'">
                                    <span class="inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform mt-0.5"
                                        :class="form.activated ? 'translate-x-4' : 'translate-x-0.5'"></span>
                                </button>
                            </label>

                            <label class="flex items-center justify-between p-3 rounded-xl border border-gray-200 bg-gray-100 cursor-pointer">
                                <span class="text-sm text-gray-600">Email Verified</span>
                                <button type="button" @click="form.email_verified = !form.email_verified"
                                    class="relative inline-flex h-5 w-9 shrink-0 rounded-full transition-colors overflow-hidden"
                                    :class="form.email_verified ? 'bg-indigo-600' : 'bg-gray-400'">
                                    <span class="inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform mt-0.5"
                                        :class="form.email_verified ? 'translate-x-4' : 'translate-x-0.5'"></span>
                                </button>
                            </label>
                        </div>

                        <!-- General error -->
                        <p v-if="generalError" class="text-xs text-red-500">{{ generalError }}</p>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <RouterLink :to="route.query.back || '/admin/users'"
                                class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                Cancel
                            </RouterLink>
                            <button type="submit" :disabled="submitting"
                                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                {{ submitting ? 'Creating…' : 'Create User' }}
                            </button>
                        </div>

                    </form>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
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
const allPermissions = ref([])
const allRoles = ref([])
const allPositions = ref([])
const allSites = ref([])

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
    activated: true,
    email_verified: false,
    permissions: [],
    company_profile: {
        name: '',
        role: '',
        description: '',
        address: '',
        phone: '',
    },
    staff_profile: {
        full_name: '',
        nrc_no: '',
        dob: '',
        address: '',
        phone: '',
        start_date: '',
    },
    staff_role: {
        staff_position_id: null,
        site_id: null,
        salary: null,
        start_date: '',
    },
})

watch(() => form.value.role, (role) => {
    form.value.permissions = role === 'admin' ? [...allPermissions.value] : []
})

function buildPayload() {
    const base = {
        name: form.value.name,
        email: form.value.email,
        password: form.value.password,
        password_confirmation: form.value.password_confirmation,
        role: form.value.role,
        activated: form.value.activated,
        email_verified: form.value.email_verified,
        permissions: form.value.permissions,
    }

    if (form.value.role === 'customer') {
        base.company_profile = { ...form.value.company_profile }
    } else if (form.value.role && form.value.role !== 'customer') {
        base.staff_profile = { ...form.value.staff_profile }
        base.staff_role = { ...form.value.staff_role }
    }

    return base
}

async function submit() {
    errors.value = {}
    generalError.value = ''
    submitting.value = true
    try {
        const { data } = await axios.post('/api/admin/users', buildPayload())
        router.push(`/admin/users/${data.id}`)
    } catch (e) {
        if (e?.response?.status === 422) {
            errors.value = e.response.data.errors ?? {}
        } else {
            generalError.value = 'Something went wrong. Please try again.'
        }
    } finally {
        submitting.value = false
    }
}

onMounted(async () => {
    const me = await requireAdmin()
    if (!me) return

    const myPermissions = me.permissions?.map(p => p.name) ?? []

    try {
        const [
            { data: perms },
            { data: roles },
            { data: positions },
            { data: sites },
        ] = await Promise.all([
            axios.get('/api/admin/permissions'),
            axios.get('/api/admin/roles/all'),
            axios.get('/api/admin/staff-positions/all'),
            axios.get('/api/admin/sites/all'),
        ])

        const hasSuper = myPermissions.includes('super')
        allPermissions.value = perms.map(p => p.name).filter(p => p !== 'super' || hasSuper)
        allRoles.value = roles.map(r => r.name)
        allPositions.value = positions
        allSites.value = sites

        if (!form.value.role || !allRoles.value.includes(form.value.role)) {
            form.value.role = allRoles.value[0] ?? ''
        }
    } finally {
        loading.value = false
    }
})
</script>
