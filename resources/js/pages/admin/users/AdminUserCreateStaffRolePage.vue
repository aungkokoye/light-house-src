<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-xl mx-auto">

            <LoadingSpinner v-if="loading" />

            <template v-else>
                <!-- Header -->
                <div class="mb-8 flex items-center gap-3">
                    <RouterLink :to="route.query.back || `/admin/users/${route.params.id}/staff-roles`"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">New Staff Role</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Assign a new role to <span class="font-medium text-gray-700">{{ userName }}</span>.</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <form @submit.prevent="submit" class="space-y-5">

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Position</label>
                            <select v-model="form.staff_position_id"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.staff_position_id ? 'border-red-300' : 'border-gray-300'">
                                <option :value="null">— Select position —</option>
                                <option v-for="pos in allPositions" :key="pos.id" :value="pos.id">{{ pos.name }}</option>
                            </select>
                            <p v-if="errors.staff_position_id" class="mt-1 text-xs text-red-500">{{ errors.staff_position_id[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Site</label>
                            <select v-model="form.site_id"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.site_id ? 'border-red-300' : 'border-gray-300'">
                                <option :value="null">— None —</option>
                                <option v-for="site in allSites" :key="site.id" :value="site.id">{{ site.name }}</option>
                            </select>
                            <p v-if="errors.site_id" class="mt-1 text-xs text-red-500">{{ errors.site_id[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Salary</label>
                            <input v-model.number="form.salary" type="number" min="0"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.salary ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.salary" class="mt-1 text-xs text-red-500">{{ errors.salary[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Start Date</label>
                            <input v-model="form.start_date" type="date"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.start_date ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.start_date" class="mt-1 text-xs text-red-500">{{ errors.start_date[0] }}</p>
                        </div>

                        <p v-if="generalError" class="text-xs text-red-500">{{ generalError }}</p>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <RouterLink :to="route.query.back || `/admin/users/${route.params.id}/staff-roles`"
                                class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                Cancel
                            </RouterLink>
                            <button type="submit" :disabled="submitting"
                                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                {{ submitting ? 'Creating…' : 'Create Role' }}
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
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import { useAdminGuard } from '../../../composables/useAdminGuard'

const route = useRoute()
const router = useRouter()
const { requireAdmin } = useAdminGuard()
const loading = ref(true)
const submitting = ref(false)
const errors = ref({})
const generalError = ref('')
const userName = ref('')
const allPositions = ref([])
const allSites = ref([])

const form = ref({
    staff_position_id: null,
    site_id: null,
    salary: null,
    start_date: '',
})

async function submit() {
    errors.value = {}
    generalError.value = ''
    submitting.value = true
    try {
        await axios.post(`/api/admin/users/${route.params.id}/staff-roles`, form.value)
        router.push(route.query.back || `/admin/users/${route.params.id}/staff-roles`)
    } catch (e) {
        if (e?.response?.status === 422) {
            errors.value = e.response.data.errors ?? {}
            generalError.value = e.response.data.message && !Object.keys(errors.value).length
                ? e.response.data.message : ''
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

    try {
        const [
            { data: user },
            { data: positions },
            { data: sites },
        ] = await Promise.all([
            axios.get(`/api/admin/users/${route.params.id}`),
            axios.get('/api/admin/staff-positions/all'),
            axios.get('/api/admin/sites/all'),
        ])
        userName.value    = user.name
        allPositions.value = positions
        allSites.value     = sites
    } catch {
        router.push(`/admin/users/${route.params.id}/staff-roles`)
        return
    } finally {
        loading.value = false
    }
})
</script>
