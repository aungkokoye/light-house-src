<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 flex items-center justify-center px-4">
        <div class="w-full max-w-lg">

            <!-- Logo -->
            <div class="flex items-center justify-center mb-8">
                <img :src="'/images/logo.png'" alt="Light House" class="h-16 w-auto" />
            </div>

            <div class="bg-white rounded-2xl shadow-xl shadow-gray-100 border border-gray-400 p-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Complete your profile</h1>
                    <p class="text-sm text-gray-500 mt-1">Tell us about your company to get started.</p>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Company Name</label>
                        <input v-model="form.name" type="text" placeholder="Your company name"
                            class="w-full px-4 py-2.5 rounded-xl border text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            :class="errors.name ? 'border-red-400' : 'border-gray-400'" />
                        <p v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Role / Title</label>
                        <input v-model="form.role" type="text" placeholder="Your role or title"
                            class="w-full px-4 py-2.5 rounded-xl border text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            :class="errors.role ? 'border-red-400' : 'border-gray-400'" />
                        <p v-if="errors.role" class="text-xs text-red-500 mt-1">{{ errors.role[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                        <input v-model="form.phone" type="text" placeholder="+95 9 xxx xxx xxx"
                            class="w-full px-4 py-2.5 rounded-xl border text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            :class="errors.phone ? 'border-red-400' : 'border-gray-400'" />
                        <p v-if="errors.phone" class="text-xs text-red-500 mt-1">{{ errors.phone[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                        <input v-model="form.address" type="text" placeholder="Company address"
                            class="w-full px-4 py-2.5 rounded-xl border text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            :class="errors.address ? 'border-red-400' : 'border-gray-400'" />
                        <p v-if="errors.address" class="text-xs text-red-500 mt-1">{{ errors.address[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Description
                            <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <textarea v-model="form.description" rows="3" placeholder="Brief description of your company"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-400 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none"></textarea>
                    </div>

                    <p v-if="generalError" class="text-sm text-red-500">{{ generalError }}</p>

                    <button type="submit" :disabled="loading"
                        class="w-full bg-indigo-600 text-white font-medium py-2.5 rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed mt-2">
                        <span v-if="loading" class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
                            </svg>
                            Saving…
                        </span>
                        <span v-else>Save & Continue</span>
                    </button>

                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const loading = ref(false)
const errors = ref({})
const generalError = ref('')

const form = reactive({
    name: '',
    role: '',
    phone: '',
    address: '',
    description: '',
})

async function handleSubmit() {
    loading.value = true
    errors.value = {}
    generalError.value = ''
    try {
        await axios.post('/api/profile/company', form)
        router.replace('/')
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors ?? {}
        } else {
            generalError.value = 'Something went wrong. Please try again.'
        }
    } finally {
        loading.value = false
    }
}
</script>
