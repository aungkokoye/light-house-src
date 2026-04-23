<template>
    <RouterView />
    <AiChatWidget :user="currentUser" />
</template>

<script setup>
import { ref, provide } from 'vue'
import AiChatWidget from './components/AiChatWidget.vue'
import axios from 'axios'

const currentUser = ref(null)

// Refresh user on each navigation so chat widget reacts to login/logout
axios.interceptors.response.use(res => {
    if (res.config.url?.endsWith('/api/me') && res.data) {
        currentUser.value = res.data
    }
    return res
})
</script>
