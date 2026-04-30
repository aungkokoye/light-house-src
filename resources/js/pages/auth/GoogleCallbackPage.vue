<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="text-center">
            <svg class="w-8 h-8 animate-spin text-indigo-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z" />
            </svg>
            <p class="text-sm text-gray-500">Signing you in…</p>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { clearAuth } from '../../bootstrap'

onMounted(() => {
    const params = new URLSearchParams(window.location.search)
    const token = params.get('token')

    if (token) {
        clearAuth()
        localStorage.setItem('token', token)
        const needsProfile = params.get('needs_profile') === '1'
        const pending = params.get('pending') === '1'
        if (needsProfile) {
            window.location.replace('/complete-profile?pending=1')
        } else if (pending) {
            window.location.replace('/login?pending=1')
        } else {
            window.location.replace('/')
        }
    } else {
        window.location.replace('/login?error=google_failed')
    }
})
</script>
