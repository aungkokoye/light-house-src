<template>
    <header class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <RouterLink to="/" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 1 1 0 10A5 5 0 0 1 12 7z" />
                    </svg>
                </div>
                <span class="font-semibold text-gray-900 text-lg">Light House</span>
            </RouterLink>
            <nav class="flex items-center gap-6 text-sm">
                <RouterLink to="/" class="text-gray-500 hover:text-gray-900 transition-colors">Home</RouterLink>
                <RouterLink to="/dashboard" class="text-gray-500 hover:text-gray-900 transition-colors">Dashboard</RouterLink>
                <RouterLink to="/profile" class="text-gray-500 hover:text-gray-900 transition-colors">Profile</RouterLink>
                <button @click="logout" class="text-gray-500 hover:text-gray-900 transition-colors">Log out</button>
            </nav>
        </div>
    </header>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">
            <div v-if="loading" class="flex items-center justify-center py-24">
                <svg class="w-6 h-6 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                </svg>
            </div>

            <template v-else>
                <div class="mb-8 flex items-center gap-3">
                    <RouterLink to="/admin/permissions" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Permission</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Update <span class="font-medium text-gray-700">{{ form.name }}</span>.</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Permission Name</label>
                            <input v-model="form.name" type="text"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.name ? 'border-red-300' : 'border-gray-400'" />
                            <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name[0] }}</p>
                        </div>

                        <p v-if="generalError" class="text-xs text-red-500">{{ generalError }}</p>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <RouterLink to="/admin/permissions" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</RouterLink>
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

const router = useRouter()
const route = useRoute()
const loading = ref(true)
const submitting = ref(false)
const errors = ref({})
const generalError = ref('')
const form = ref({ name: '' })

async function logout() {
    try { await axios.post('/api/logout') } finally {
        localStorage.removeItem('token')
        router.push('/login')
    }
}

async function submit() {
    errors.value = {}
    generalError.value = ''
    submitting.value = true
    try {
        await axios.put(`/api/admin/permissions/${route.params.id}`, form.value)
        router.push('/admin/permissions')
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
        const { data: me } = await axios.get('/api/me')
        if (!me.roles?.some(r => r.name === 'admin')) { router.replace('/403'); return }
        if (!me.permissions?.some(p => p.name === 'super')) { router.replace('/403'); return }
    } catch {
        router.push('/login'); return
    }
    try {
        const { data } = await axios.get(`/api/admin/permissions/${route.params.id}`)
        form.value.name = data.name
    } catch {
        router.push('/admin/permissions')
    } finally {
        loading.value = false
    }
})
</script>