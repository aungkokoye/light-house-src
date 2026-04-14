<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">

            <LoadingSpinner v-if="loading" />

            <template v-else>
                <!-- Header -->
                <div class="mb-8 flex items-center gap-3">
                    <button @click="router.back()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Update account details for <span class="font-medium text-gray-700">{{ form.name }}</span>.</p>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <form @submit.prevent="submit" class="space-y-5">

                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Name</label>
                            <input v-model="form.name" type="text"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.name ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name[0] }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email</label>
                            <input v-model="form.email" type="email"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.email ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email[0] }}</p>
                        </div>

                        <!-- Password (super only) -->
                        <div v-if="hasSuper">
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                New Password
                                <span class="text-gray-300 font-normal">(leave blank to keep current)</span>
                            </label>
                            <input v-model="form.password" type="password" placeholder="Min. 8 characters"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.password ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.password" class="mt-1 text-xs text-red-500">{{ errors.password[0] }}</p>
                        </div>

                        <!-- Confirm Password (super only) -->
                        <div v-if="hasSuper">
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Confirm New Password</label>
                            <input v-model="form.password_confirmation" type="password"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50" />
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Role</label>
                            <select v-model="form.role" :disabled="!hasSuper"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                :class="[errors.role ? 'border-red-300' : 'border-gray-300', !hasSuper ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-gray-50']">
                                <option v-for="role in allRoles" :key="role" :value="role">{{ role }}</option>
                            </select>
                            <p v-if="errors.role" class="mt-1 text-xs text-red-500">{{ errors.role[0] }}</p>
                        </div>

                        <!-- Permissions -->
                        <div v-if="allPermissions.length">
                            <label class="block text-xs font-medium text-gray-600 mb-2">
                                Permissions
                                <span v-if="!hasSuper" class="text-gray-300 font-normal">(read-only)</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <label v-for="p in allPermissions" :key="p"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 transition-colors"
                                    :class="hasSuper ? 'bg-gray-50 cursor-pointer hover:bg-indigo-50 hover:border-indigo-200' : 'bg-gray-100 cursor-not-allowed'">
                                    <input type="checkbox" :value="p" v-model="form.permissions" :disabled="!hasSuper"
                                        class="w-3.5 h-3.5 rounded accent-indigo-600" />
                                    <span class="text-xs capitalize" :class="hasSuper ? 'text-gray-700' : 'text-gray-400'">{{ p }}</span>
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
import { ref, watch, onMounted, nextTick } from 'vue'
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
const hasSuper = ref(false)
let initialLoad = true

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
    if (initialLoad) return
    form.value.permissions = role === 'admin' ? [...allPermissions.value] : []
})

async function submit() {
    errors.value = {}
    generalError.value = ''
    submitting.value = true
    try {
        await axios.put(`/api/admin/users/${route.params.id}`, form.value)
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
    hasSuper.value = myPermissions.includes('super')

    try {
        const [{ data: perms }, { data: roles }, { data }] = await Promise.all([
            axios.get('/api/admin/permissions'),
            axios.get('/api/admin/roles'),
            axios.get(`/api/admin/users/${route.params.id}`),
        ])

        const hasSuperVal = hasSuper.value
        allPermissions.value      = perms.map(p => p.name).filter(p => p !== 'super' || hasSuperVal)
        allRoles.value            = roles.map(r => r.name)
        form.value.name           = data.name
        form.value.email          = data.email
        form.value.role           = data.roles?.[0]?.name ?? 'user'
        form.value.activated      = data.activated
        form.value.email_verified = !!data.email_verified_at
        form.value.permissions    = data.permissions?.map(p => p.name) ?? []
        await nextTick()
        initialLoad = false
    } catch {
        router.push('/admin/users')
    } finally {
        loading.value = false
    }
})
</script>