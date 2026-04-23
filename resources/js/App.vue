<template>
    <RouterView />
    <AiChatWidget :user="currentUser" />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AiChatWidget from './components/AiChatWidget.vue'
import axios from 'axios'

const currentUser = ref(null)

// Keep currentUser in sync whenever any page calls /api/me
axios.interceptors.response.use(res => {
    if (res.config.url?.includes('/api/me') && res.data) {
        currentUser.value = res.data
    }
    return res
})

// Also fetch immediately on mount so the widget shows without waiting for a page to call /api/me
onMounted(async () => {
    if (!localStorage.getItem('token')) return
    try {
        const { data } = await axios.get('/api/me')
        currentUser.value = data
    } catch {}
})
</script>
