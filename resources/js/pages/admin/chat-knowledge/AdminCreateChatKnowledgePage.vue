<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-2xl mx-auto">
            <LoadingSpinner v-if="loading" />

            <template v-else>
                <div class="mb-8 flex items-center gap-3">
                    <RouterLink to="/admin/chat-knowledge" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">New Knowledge Entry</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Add content to the AI chatbot knowledge base.</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Category <span class="text-red-400">*</span></label>
                                <select v-model.number="form.chat_knowledge_category_id"
                                    class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                    :class="errors.chat_knowledge_category_id ? 'border-red-300' : 'border-gray-300'">
                                    <option value="">Select a category</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                                <p v-if="errors.chat_knowledge_category_id" class="mt-1 text-xs text-red-500">{{ errors.chat_knowledge_category_id[0] }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Sort Order</label>
                                <input v-model.number="form.sort_order" type="number" min="0" placeholder="0"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50" />
                                <p v-if="errors.sort_order" class="mt-1 text-xs text-red-500">{{ errors.sort_order[0] }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Title <span class="text-red-400">*</span></label>
                            <input v-model="form.title" type="text" placeholder="e.g. Offset Printing Services"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50"
                                :class="errors.title ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.title" class="mt-1 text-xs text-red-500">{{ errors.title[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Content <span class="text-red-400">*</span></label>
                            <textarea v-model="form.content" rows="8"
                                placeholder="Describe this topic in detail. This content will be used as context for the AI assistant."
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 bg-gray-50 resize-none"
                                :class="errors.content ? 'border-red-300' : 'border-gray-300'" />
                            <p v-if="errors.content" class="mt-1 text-xs text-red-500">{{ errors.content[0] }}</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="button" @click="form.active = !form.active"
                                class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors"
                                :class="form.active ? 'bg-indigo-600' : 'bg-gray-200'">
                                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform"
                                    :class="form.active ? 'translate-x-4' : 'translate-x-0.5'" />
                            </button>
                            <span class="text-sm text-gray-600">Active <span class="text-xs text-gray-400">(inactive entries are excluded from the chatbot)</span></span>
                        </div>

                        <p v-if="generalError" class="text-xs text-red-500">{{ generalError }}</p>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <RouterLink to="/admin/chat-knowledge" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">Cancel</RouterLink>
                            <button type="submit" :disabled="submitting"
                                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                                {{ submitting ? 'Creating…' : 'Create Entry' }}
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
const categories = ref([])
const form = ref({ chat_knowledge_category_id: '', title: '', content: '', active: true, sort_order: 0 })

async function submit() {
    errors.value = {}
    generalError.value = ''
    submitting.value = true
    try {
        await axios.post('/api/admin/chat-knowledge', form.value)
        router.push('/admin/chat-knowledge')
    } catch (e) {
        if (e?.response?.status === 422) errors.value = e.response.data.errors ?? {}
        else if (e?.response?.status === 403) generalError.value = 'You do not have permission to perform this action.'
        else generalError.value = 'Something went wrong. Please try again.'
    } finally {
        submitting.value = false
    }
}

onMounted(async () => {
    const me = await requireAdmin()
    if (!me) return
    if (!me.permissions?.some(p => p.name === 'super')) {
        router.replace('/403')
        return
    }
    try {
        const { data } = await axios.get('/api/admin/chat-knowledge-categories/all')
        categories.value = data
    } catch {}
    loading.value = false
})
</script>
