<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">

            <LoadingSpinner v-if="loading" />

            <template v-else>
                <!-- Header -->
                <div class="mb-8 flex items-center gap-3">
                    <RouterLink to="/admin/users" class="text-gray-400 hover:text-gray-600 transition-colors">
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
                            <input v-model="form.password" type="password" placeholder="Min. 8 characters"
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
                            <RouterLink to="/admin/users"
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
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import { useAdminGuard } from '../../../composables/useAdminGuard'

const router = useRouter()
const { requireAdmin } = useAdminGuard()
const loading = ref(true)
const submitting = ref(false)
const errors = ref({})
const generalError = ref('')
const allPermissions = ref([])
const allRoles = ref([])

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
    activated: true,
    email_verified: false,
    permissions: [],
})

watch(() => form.value.role, (role) => {
    form.value.permissions = role === 'admin' ? [...allPermissions.value] : []
})

async function submit() {
    errors.value = {}
    generalError.value = ''
    submitting.value = true
    try {
        await axios.post('/api/admin/users', form.value)
        router.push('/admin/users')
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
        const [{ data: perms }, { data: roles }] = await Promise.all([
            axios.get('/api/admin/permissions'),
            axios.get('/api/admin/roles'),
        ])
        const hasSuper = myPermissions.includes('super')
        allPermissions.value = perms.map(p => p.name).filter(p => p !== 'super' || hasSuper)
        allRoles.value = roles.map(r => r.name)
        if (!form.value.role || !allRoles.value.includes(form.value.role)) {
            form.value.role = allRoles.value[0] ?? ''
        }
    } finally {
        loading.value = false
    }
})
</script>