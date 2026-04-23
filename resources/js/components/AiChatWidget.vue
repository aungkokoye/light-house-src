<template>
    <div v-if="visible">
        <!-- Floating button -->
        <button v-if="!open" @click="open = true"
            class="fixed bottom-6 right-6 z-50 w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-xl hover:shadow-indigo-300 hover:scale-105 transition-all flex flex-col items-center justify-center gap-0.5">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09z" />
            </svg>
            <span class="text-[9px] font-semibold tracking-wide uppercase opacity-90">AI</span>
        </button>

        <!-- Chat panel -->
        <div v-else
            class="fixed bottom-6 right-6 z-50 w-96 max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl border border-gray-100 flex flex-col"
            style="height: 520px;">

            <!-- Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 rounded-t-2xl bg-indigo-600">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center relative">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09z" />
                        </svg>
                        <!-- Live pulse dot -->
                        <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 rounded-full border-2 border-indigo-600">
                            <span class="absolute inset-0 rounded-full bg-emerald-400 animate-ping opacity-75" />
                        </span>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <p class="text-sm font-semibold text-white">LightHouse AI Assistant</p>
                        </div>
                        <p class="text-xs text-indigo-200">Powered by GPT · Always here to help</p>
                    </div>
                </div>
                <button @click="close" class="text-white/70 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Messages -->
            <div ref="messagesEl" class="flex-1 overflow-y-auto px-4 py-4 space-y-3">
                <div v-if="messages.length === 0" class="flex items-center justify-center h-full">
                    <p class="text-sm text-gray-400 text-center">Hi! Ask me anything about LightHouse.</p>
                </div>
                <template v-for="(msg, i) in messages" :key="i">
                    <!-- User -->
                    <div v-if="msg.role === 'user'" class="flex justify-end">
                        <div class="max-w-[80%] bg-indigo-600 text-white text-sm rounded-2xl rounded-tr-sm px-4 py-2.5 leading-relaxed">
                            {{ msg.content }}
                        </div>
                    </div>
                    <!-- Assistant -->
                    <div v-else class="flex justify-start">
                        <div class="max-w-[80%] bg-gray-100 text-gray-800 text-sm rounded-2xl rounded-tl-sm px-4 py-2.5 leading-relaxed whitespace-pre-wrap">
                            {{ msg.content }}<span v-if="msg.streaming" class="inline-block w-1.5 h-3.5 ml-0.5 bg-gray-500 animate-pulse rounded-sm" />
                        </div>
                    </div>
                </template>
            </div>

            <!-- Input -->
            <div class="px-4 py-3 border-t border-gray-100">
                <form @submit.prevent="send" class="flex gap-2">
                    <input
                        ref="inputEl"
                        v-model="draft"
                        :disabled="streaming"
                        type="text"
                        placeholder="Type a message..."
                        class="flex-1 text-sm bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-transparent disabled:opacity-50"
                    />
                    <button type="submit" :disabled="!draft.trim() || streaming"
                        class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center hover:bg-indigo-700 transition-colors disabled:opacity-40">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick, watch } from 'vue'

const props = defineProps({
    user: { type: Object, default: null },
})

const visible = ref(false)
const open = ref(false)
const draft = ref('')
const streaming = ref(false)
const messages = ref([])
const messagesEl = ref(null)
const inputEl = ref(null)

watch(() => props.user, (u) => {
    const enabled = window.__AI_CHAT_ENABLED__ === true
    const isAdmin = u?.roles?.some(r => r.name === 'admin')
    const isSuper = u?.permissions?.some(p => p.name === 'super')
    visible.value = enabled && isAdmin && isSuper
}, { immediate: true })

function close() {
    open.value = false
}

async function scrollToBottom() {
    await nextTick()
    if (messagesEl.value) {
        messagesEl.value.scrollTop = messagesEl.value.scrollHeight
    }
}

async function send() {
    const text = draft.value.trim()
    if (!text || streaming.value) return

    draft.value = ''
    messages.value.push({ role: 'user', content: text })
    await scrollToBottom()

    const history = messages.value
        .slice(0, -1)
        .map(m => ({ role: m.role, content: m.content }))

    messages.value.push({ role: 'assistant', content: '', streaming: true })
    streaming.value = true

    const idx = messages.value.length - 1

    try {
        const token = localStorage.getItem('token')
        const response = await fetch('/api/chat/stream', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'text/event-stream',
                'Authorization': `Bearer ${token}`,
            },
            body: JSON.stringify({ message: text, history }),
        })

        if (!response.ok) {
            messages.value[idx].content = 'Something went wrong. Please try again.'
            messages.value[idx].streaming = false
            return
        }

        const reader = response.body.getReader()
        const decoder = new TextDecoder()
        let buffer = ''

        while (true) {
            const { done, value } = await reader.read()
            if (done) break
            buffer += decoder.decode(value, { stream: true })
            const lines = buffer.split('\n')
            buffer = lines.pop()

            for (const line of lines) {
                if (!line.startsWith('data: ')) continue
                const payload = line.slice(6)
                if (payload === '[DONE]') break
                try {
                    const { content } = JSON.parse(payload)
                    messages.value[idx].content += content
                    await scrollToBottom()
                } catch {}
            }
        }
    } catch {
        messages.value[idx].content = 'Connection error. Please try again.'
    } finally {
        messages.value[idx].streaming = false
        streaming.value = false
        await nextTick()
        inputEl.value?.focus()
    }
}
</script>
