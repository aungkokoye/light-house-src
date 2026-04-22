<template>
    <AppHeader />

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 pt-24 pb-12 px-4">
        <div class="max-w-5xl mx-auto">
            <LoadingSpinner v-if="loading" />

            <template v-else-if="log">
                <div class="mb-8 flex items-center gap-3">
                    <RouterLink to="/admin/audit-logs" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </RouterLink>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Audit Log</h1>
                        <p class="text-sm text-gray-500 mt-0.5">#{{ log.id }}</p>
                    </div>
                </div>

                <!-- Meta -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50 mb-4">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">ID</span>
                        <span class="text-sm font-mono text-gray-600">{{ log.id }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Event</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="eventClass(log.event)">
                            {{ eventLabel(log.event) }}
                        </span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">User Email</span>
                        <span class="text-sm text-gray-700">{{ log.user_email ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Model</span>
                        <span class="text-sm font-mono text-gray-600">{{ shortType(log.auditable_type) }} #{{ log.auditable_id }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">IP Address</span>
                        <span class="text-sm font-mono text-gray-600">{{ log.ip_address ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-start justify-between gap-4">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide shrink-0">User Agent</span>
                        <span class="text-xs text-gray-500 text-right break-all">{{ log.user_agent ?? '—' }}</span>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Date</span>
                        <span class="text-sm text-gray-600">{{ formatDate(log.created_at, true) }}</span>
                    </div>
                </div>

                <!-- Diff table -->
                <div v-if="log.old_values || log.new_values"
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                    <!-- Side-by-side: both exist -->
                    <template v-if="log.old_values && log.new_values">
                        <div class="grid grid-cols-[200px_1fr_1fr] border-b border-gray-100 bg-gray-50/60 text-xs font-semibold text-gray-500">
                            <div class="px-5 py-3">Field</div>
                            <div class="px-5 py-3 border-l border-gray-100">Before</div>
                            <div class="px-5 py-3 border-l border-gray-100">After</div>
                        </div>
                        <template v-for="row in diffRows" :key="row.key">
                            <div v-if="row.isHeader"
                                class="grid grid-cols-[200px_1fr_1fr] border-b border-gray-50 bg-gray-50/50">
                                <div class="py-2 text-xs font-semibold text-gray-500 col-span-3" :style="indentStyle(row.level)">
                                    {{ row.label }}
                                </div>
                            </div>
                            <div v-else-if="row.isSubHeader"
                                class="grid grid-cols-[200px_1fr_1fr] border-b border-gray-50 bg-gray-50/30">
                                <div class="py-2 text-xs font-medium text-gray-400 col-span-3" :style="indentStyle(row.level)">
                                    {{ row.label }}
                                </div>
                            </div>
                            <div v-else class="grid grid-cols-[200px_1fr_1fr] border-b border-gray-50 last:border-0 items-start">
                                <div class="py-3 text-xs font-medium text-gray-400" :style="indentStyle(row.level)">
                                    {{ row.label }}
                                </div>
                                <div class="px-5 py-3 border-l border-gray-100 min-w-0" :class="row.changed ? 'bg-red-50/70' : ''">
                                    <pre class="text-xs whitespace-pre-wrap break-words leading-relaxed font-mono"
                                        :class="row.changed ? 'text-red-700' : 'text-gray-600'">{{ formatValue(row.oldVal) }}</pre>
                                </div>
                                <div class="px-5 py-3 border-l border-gray-100 min-w-0" :class="row.changed ? 'bg-emerald-50/70' : ''">
                                    <pre class="text-xs whitespace-pre-wrap break-words leading-relaxed font-mono"
                                        :class="row.changed ? 'text-emerald-700' : 'text-gray-600'">{{ formatValue(row.newVal) }}</pre>
                                </div>
                            </div>
                        </template>
                    </template>

                    <!-- Single column: only one side exists -->
                    <template v-else>
                        <div class="grid grid-cols-[200px_1fr] border-b border-gray-100 bg-gray-50/60 text-xs font-semibold text-gray-500">
                            <div class="px-5 py-3">Field</div>
                            <div class="px-5 py-3 border-l border-gray-100">{{ log.old_values ? 'Before' : 'After' }}</div>
                        </div>
                        <template v-for="row in singleRows" :key="row.key">
                            <div v-if="row.isHeader"
                                class="grid grid-cols-[200px_1fr] border-b border-gray-50 bg-gray-50/50">
                                <div class="py-2 text-xs font-semibold text-gray-500 col-span-2" :style="indentStyle(row.level)">{{ row.label }}</div>
                            </div>
                            <div v-else-if="row.isSubHeader"
                                class="grid grid-cols-[200px_1fr] border-b border-gray-50 bg-gray-50/30">
                                <div class="py-2 text-xs font-medium text-gray-400 col-span-2" :style="indentStyle(row.level)">{{ row.label }}</div>
                            </div>
                            <div v-else class="grid grid-cols-[200px_1fr] border-b border-gray-50 last:border-0 items-start">
                                <div class="py-3 text-xs font-medium text-gray-400" :style="indentStyle(row.level)">{{ row.label }}</div>
                                <div class="px-5 py-3 border-l border-gray-100 min-w-0">
                                    <pre class="text-xs text-gray-600 whitespace-pre-wrap break-words leading-relaxed font-mono">{{ formatValue(row.val) }}</pre>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>

            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppHeader from '../../../components/AppHeader.vue'
import LoadingSpinner from '../../../components/LoadingSpinner.vue'
import { useAdminGuard } from '../../../composables/useAdminGuard'
import { useFormatDate } from '../../../composables/useFormatDate'

const router = useRouter()
const route = useRoute()
const { requireAdmin } = useAdminGuard()
const { formatDate } = useFormatDate()
const loading = ref(true)
const log = ref(null)

const SKIP = ['updated_at', 'created_at']

function isObj(val) {
    return val !== null && typeof val === 'object' && !Array.isArray(val)
}

function isObjArray(val) {
    return Array.isArray(val) && val.length > 0 && val.every(isObj)
}

function indentStyle(level) {
    return { paddingLeft: `${20 + level * 16}px`, paddingRight: '20px' }
}

function diffRowsRecursive(oldObj, newObj, prefix, level, rows) {
    const keys = [...new Set([...Object.keys(oldObj ?? {}), ...Object.keys(newObj ?? {})])]
    for (const key of keys) {
        if (SKIP.includes(key)) continue
        const o = oldObj?.[key]
        const n = newObj?.[key]
        const rk = prefix ? `${prefix}.${key}` : key

        if (isObjArray(o) || isObjArray(n)) {
            rows.push({ key: rk, label: formatKey(key), isHeader: true, level })
            const len = Math.max((o ?? []).length, (n ?? []).length)
            for (let i = 0; i < len; i++) {
                const io = (o ?? [])[i] ?? {}
                const in_ = (n ?? [])[i] ?? {}
                rows.push({ key: `${rk}[${i}]`, label: `#${i + 1}`, isSubHeader: true, level: level + 1 })
                diffRowsRecursive(io, in_, `${rk}[${i}]`, level + 2, rows)
            }
        } else if (isObj(o) || isObj(n)) {
            rows.push({ key: rk, label: formatKey(key), isHeader: true, level })
            diffRowsRecursive(isObj(o) ? o : {}, isObj(n) ? n : {}, rk, level + 1, rows)
        } else {
            rows.push({ key: rk, label: formatKey(key), level, oldVal: o, newVal: n, changed: JSON.stringify(o) !== JSON.stringify(n) })
        }
    }
}

function singleRowsRecursive(obj, prefix, level, rows) {
    for (const key of Object.keys(obj ?? {})) {
        if (SKIP.includes(key)) continue
        const val = obj[key]
        const rk = prefix ? `${prefix}.${key}` : key

        if (isObjArray(val)) {
            rows.push({ key: rk, label: formatKey(key), isHeader: true, level })
            val.forEach((item, i) => {
                rows.push({ key: `${rk}[${i}]`, label: `#${i + 1}`, isSubHeader: true, level: level + 1 })
                singleRowsRecursive(item, `${rk}[${i}]`, level + 2, rows)
            })
        } else if (isObj(val)) {
            rows.push({ key: rk, label: formatKey(key), isHeader: true, level })
            singleRowsRecursive(val, rk, level + 1, rows)
        } else {
            rows.push({ key: rk, label: formatKey(key), level, val })
        }
    }
}

const diffRows = computed(() => {
    const rows = []
    diffRowsRecursive(log.value?.old_values, log.value?.new_values, '', 0, rows)
    return rows
})

const singleRows = computed(() => {
    const rows = []
    singleRowsRecursive(log.value?.old_values ?? log.value?.new_values, '', 0, rows)
    return rows
})

function formatKey(key) {
    return key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

function formatValue(val) {
    if (val === null || val === undefined) return '—'
    if (typeof val === 'object') return JSON.stringify(val, null, 2)
    if (typeof val === 'boolean') return val ? 'true' : 'false'
    return String(val)
}

function eventLabel(event) {
    return event.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

function eventClass(event) {
    const map = {
        created:          'bg-emerald-50 text-emerald-700',
        updated:          'bg-sky-50 text-sky-700',
        deleted:          'bg-red-50 text-red-600',
        login:            'bg-indigo-50 text-indigo-700',
        logout:           'bg-gray-100 text-gray-600',
        password_changed: 'bg-amber-50 text-amber-700',
    }
    return map[event] ?? 'bg-gray-100 text-gray-600'
}

function shortType(type) {
    return type ? type.split('\\').pop() : '—'
}

onMounted(async () => {
    const me = await requireAdmin()
    if (!me) return
    try {
        const { data } = await axios.get(`/api/admin/audit-logs/${route.params.id}`)
        log.value = data
    } catch {
        router.push('/admin/audit-logs')
    } finally {
        loading.value = false
    }
})
</script>
