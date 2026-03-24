<template>
    <!-- Nav -->
    <header class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 1 1 0 10A5 5 0 0 1 12 7z" />
                    </svg>
                </div>
                <span class="font-semibold text-gray-900 text-lg">Light House</span>
            </div>
            <nav class="hidden md:flex items-center gap-8 text-sm text-gray-500">
                <a href="#features" class="hover:text-gray-900 transition-colors">Features</a>
                <a href="#how-it-works" class="hover:text-gray-900 transition-colors">How it works</a>
                <a href="#contact" class="hover:text-gray-900 transition-colors">Contact</a>
            </nav>
            <div class="flex items-center gap-3">
                <template v-if="isLoggedIn">
                    <RouterLink v-if="isAdmin" to="/admin" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                        Admin Dashboard
                    </RouterLink>
                    <RouterLink to="/profile" class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                        <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                            {{ userInitials }}
                        </div>
                        <span v-if="userName" class="hidden sm:block font-medium">{{ userName }}</span>
                    </RouterLink>
                    <button @click="logout" class="text-sm text-gray-500 hover:text-gray-900 transition-colors">
                        Log out
                    </button>
                </template>
                <template v-else>
                    <RouterLink to="/login" class="text-sm text-gray-500 hover:text-gray-900 transition-colors">
                        Log in
                    </RouterLink>
                    <RouterLink to="/register" class="text-sm font-medium bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        Get started
                    </RouterLink>
                </template>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="min-h-screen flex items-center bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-16">
        <div class="max-w-6xl mx-auto px-6 py-24 text-center">
            <span class="inline-block text-xs font-semibold tracking-widest text-indigo-600 uppercase bg-indigo-50 px-3 py-1 rounded-full mb-6">
                Now in beta
            </span>
            <h1 class="text-5xl md:text-7xl font-bold text-gray-900 leading-tight mb-6">
                Clarity through<br />
                <span class="text-indigo-600">every signal</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-500 max-w-2xl mx-auto mb-10">
                Light House helps your team cut through the noise — surfacing the insights that matter, right when you need them.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#" class="w-full sm:w-auto bg-indigo-600 text-white font-medium px-8 py-3.5 rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
                    Start for free
                </a>
                <a href="#how-it-works" class="w-full sm:w-auto text-gray-600 font-medium px-8 py-3.5 rounded-xl border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-colors">
                    See how it works
                </a>
            </div>

            <!-- Hero visual -->
            <div class="mt-20 relative">
                <div class="absolute inset-0 -z-10 bg-gradient-to-t from-white to-transparent bottom-0 h-32 pointer-events-none"></div>
                <div class="bg-white rounded-2xl shadow-2xl shadow-gray-200 border border-gray-100 p-6 max-w-3xl mx-auto">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div v-for="card in statCards" :key="card.label" class="bg-gray-50 rounded-xl p-4 text-left">
                            <p class="text-xs text-gray-400 mb-1">{{ card.label }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ card.value }}</p>
                            <p :class="card.positive ? 'text-green-500' : 'text-red-500'" class="text-xs font-medium mt-1">
                                {{ card.change }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 h-24 bg-gray-50 rounded-xl flex items-end gap-1 px-4 pb-3">
                        <div v-for="(bar, i) in bars" :key="i"
                            class="flex-1 bg-indigo-200 rounded-t-sm transition-all"
                            :class="i === bars.length - 1 ? 'bg-indigo-600' : 'bg-indigo-200'"
                            :style="{ height: bar + '%' }">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Everything you need</h2>
                <p class="text-gray-500 max-w-xl mx-auto">A focused set of tools designed to keep your team aligned and moving fast.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div v-for="feature in features" :key="feature.title"
                    class="group p-6 rounded-2xl border border-gray-100 hover:border-indigo-100 hover:bg-indigo-50/30 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center mb-4 group-hover:bg-indigo-600 transition-colors">
                        <component :is="feature.icon" class="w-5 h-5 text-indigo-600 group-hover:text-white transition-colors" />
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ feature.title }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ feature.description }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="how-it-works" class="py-24 bg-slate-50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Up and running in minutes</h2>
                <p class="text-gray-500 max-w-xl mx-auto">No complex setup. Connect your tools and get insights immediately.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div v-for="(step, i) in steps" :key="step.title" class="relative">
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 h-full">
                        <span class="text-4xl font-bold text-indigo-100">{{ String(i + 1).padStart(2, '0') }}</span>
                        <h3 class="font-semibold text-gray-900 mt-2 mb-2">{{ step.title }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ step.description }}</p>
                    </div>
                    <div v-if="i < steps.length - 1"
                        class="hidden md:block absolute top-1/2 -right-3 w-6 h-0.5 bg-gray-200 z-10">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section id="contact" class="py-24 bg-indigo-600">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to get started?</h2>
            <p class="text-indigo-200 mb-8">Join teams already using Light House to make better decisions, faster.</p>
            <a href="#" class="inline-block bg-white text-indigo-600 font-semibold px-8 py-3.5 rounded-xl hover:bg-indigo-50 transition-colors">
                Start for free
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-10">
        <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4 text-sm">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-indigo-600 flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 1 1 0 10A5 5 0 0 1 12 7z" />
                    </svg>
                </div>
                <span class="text-white font-medium">Light House</span>
            </div>
            <p>© {{ new Date().getFullYear() }} Light House. All rights reserved.</p>
        </div>
    </footer>
</template>

<script setup>
import { h, ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const isLoggedIn = ref(!!localStorage.getItem('token'))
const userName = ref('')
const isAdmin = ref(false)

const userInitials = computed(() =>
    userName.value
        .split(' ')
        .map(w => w[0])
        .slice(0, 2)
        .join('')
        .toUpperCase() || '?'
)

onMounted(async () => {
    if (isLoggedIn.value) {
        try {
            const { data } = await axios.get('/api/me')
            userName.value = data.name
            isAdmin.value = data.roles?.some(r => r.name === 'admin') ?? false
        } catch {
            // token invalid — clear and treat as logged out
            localStorage.removeItem('token')
            isLoggedIn.value = false
        }
    }
})

async function logout() {
    await axios.post('/api/logout')
    localStorage.removeItem('token')
    isLoggedIn.value = false
    router.push('/login')
}

const statCards = [
    { label: 'Active users', value: '12.4k', change: '+8.2% this week', positive: true },
    { label: 'Signals tracked', value: '98.1%', change: '+1.4% this week', positive: true },
    { label: 'Avg. response', value: '1.2s', change: '-0.3s this week', positive: true },
]

const bars = [40, 55, 35, 60, 45, 70, 50, 65, 55, 80, 60, 95]

const IconBolt = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '2', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M13 10V3L4 14h7v7l9-11h-7z' })]) }
const IconChart = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '2', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z' })]) }
const IconShield = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '2', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' })]) }
const IconBell = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '2', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9' })]) }
const IconUsers = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '2', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z' })]) }
const IconCode = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '2', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M10 20l4-16m4 4 4 4-4 4M6 16l-4-4 4-4' })]) }

const features = [
    { icon: IconBolt, title: 'Real-time signals', description: 'Monitor events as they happen. No delays, no polling — instant visibility into what matters.' },
    { icon: IconChart, title: 'Smart analytics', description: 'Understand trends at a glance with auto-generated charts and summaries tailored to your data.' },
    { icon: IconShield, title: 'Reliable & secure', description: 'Built with security-first principles. Your data stays yours, always encrypted at rest and in transit.' },
    { icon: IconBell, title: 'Instant alerts', description: 'Get notified the moment something needs your attention, through the channels your team already uses.' },
    { icon: IconUsers, title: 'Team collaboration', description: 'Share dashboards, assign signals, and keep everyone on the same page without extra meetings.' },
    { icon: IconCode, title: 'Simple API', description: 'Integrate with anything in minutes. Clean REST API with SDKs for all major languages.' },
]

const steps = [
    { title: 'Connect your sources', description: 'Link your existing tools — databases, APIs, or custom webhooks. Setup takes under five minutes.' },
    { title: 'Define what matters', description: 'Set thresholds, rules, and filters so Light House focuses on the signals that are relevant to you.' },
    { title: 'Act on insights', description: 'Get clear, actionable reports and alerts. Make decisions with confidence backed by real data.' },
]
</script>
