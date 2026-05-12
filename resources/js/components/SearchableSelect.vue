<template>
    <div class="relative" ref="container">
        <div
            class="w-full flex items-center gap-2 px-3 py-2 text-sm border rounded-lg bg-gray-50 cursor-pointer focus-within:ring-2 focus-within:ring-indigo-200"
            :class="[hasError ? 'border-red-300' : 'border-gray-300', disabled ? 'opacity-50 cursor-not-allowed' : '']"
            @click="!disabled && open()">
            <input
                ref="inputEl"
                v-model="query"
                type="text"
                :placeholder="selectedLabel || placeholder"
                :class="[selectedLabel && !isOpen ? 'text-gray-900' : 'text-gray-500', 'flex-1 bg-transparent outline-none min-w-0 text-sm']"
                :disabled="disabled"
                @focus="open"
                @input="isOpen = true"
                @keydown.esc="close"
                @keydown.enter.prevent="selectHighlighted"
                @keydown.arrow-down.prevent="moveHighlight(1)"
                @keydown.arrow-up.prevent="moveHighlight(-1)" />
            <button v-if="modelValue !== '' && modelValue != null && !disabled" type="button" @click.stop="clear"
                class="text-gray-300 hover:text-gray-500 transition-colors shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
            <svg class="w-4 h-4 text-gray-300 shrink-0 transition-transform" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </div>

        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-1">
                <div v-if="isOpen"
                    :style="dropdownStyle"
                    class="fixed z-[9999] bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                    <ul ref="listEl" class="max-h-52 overflow-y-auto py-1">
                        <li v-if="asyncLoading" class="px-3 py-2 text-xs text-gray-400 text-center">Searching…</li>
                        <li v-else-if="onSearch && !query.trim()" class="px-3 py-2 text-xs text-gray-400 text-center">Type to search…</li>
                        <li v-else-if="filtered.length === 0" class="px-3 py-2 text-xs text-gray-400 text-center">No results</li>
                        <template v-else>
                            <li v-for="(opt, i) in filtered" :key="opt.value"
                                @mousedown.prevent="select(opt)"
                                @mouseenter="highlighted = i"
                                class="px-3 py-2 text-sm cursor-pointer transition-colors"
                                :class="[opt.value == modelValue ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700', highlighted === i ? 'bg-gray-100' : '']">
                                {{ opt.display ?? opt.label }}
                                <span v-if="opt.sub" class="ml-1 text-xs text-gray-400">{{ opt.sub }}</span>
                            </li>
                        </template>
                    </ul>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'

const props = defineProps({
    modelValue: { default: '' },
    options:    { type: Array, default: () => [] }, // [{ value, label, sub?, display? }]
    onSearch:   { type: Function, default: null },  // async (query: string) => Option[]
    placeholder: { type: String, default: 'Search…' },
    hasError:   { type: Boolean, default: false },
    disabled:   { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue'])

const container     = ref(null)
const inputEl       = ref(null)
const listEl        = ref(null)
const isOpen        = ref(false)
const query         = ref('')
const highlighted   = ref(0)
const dropdownStyle = ref({})
const asyncOptions  = ref([])
const asyncLoading  = ref(false)
const lastSelected  = ref(null)
let debounceTimer   = null

// Track selected option label — lastSelected wins, then fall back to props.options (edit-page seed)
const selectedLabel = computed(() => {
    if (props.modelValue === '' || props.modelValue == null) return ''
    if (lastSelected.value?.value == props.modelValue) {
        return lastSelected.value.display ?? lastSelected.value.label
    }
    const opt = props.options.find(o => o.value == props.modelValue)
    return opt ? (opt.display ?? opt.label) : ''
})

const filtered = computed(() => {
    if (props.onSearch) return asyncOptions.value
    const q = query.value.toLowerCase().trim()
    if (!q) return props.options
    return props.options.filter(o =>
        o.label.toLowerCase().includes(q) || (o.sub ?? '').toLowerCase().includes(q)
    )
})

// Seed lastSelected from props.options when modelValue is set externally (edit page)
watch(() => props.modelValue, (val) => {
    if (val === '' || val == null) { lastSelected.value = null; return }
    if (lastSelected.value?.value == val) return
    const opt = props.options.find(o => o.value == val)
    if (opt) lastSelected.value = opt
}, { immediate: true })

// Debounced server search
watch(query, (q) => {
    if (!props.onSearch || !isOpen.value) return
    clearTimeout(debounceTimer)
    asyncLoading.value = true
    debounceTimer = setTimeout(async () => {
        try {
            asyncOptions.value = await props.onSearch(q.trim())
        } catch {
            asyncOptions.value = []
        } finally {
            asyncLoading.value = false
        }
    }, 300)
})

watch(isOpen, async v => {
    if (v) {
        await nextTick()
        updatePosition()
        highlighted.value = Math.max(0, filtered.value.findIndex(o => o.value == props.modelValue))
    } else {
        query.value = ''
        clearTimeout(debounceTimer)
        asyncLoading.value = false
    }
})

function updatePosition() {
    if (!container.value) return
    const rect = container.value.getBoundingClientRect()
    dropdownStyle.value = {
        top:   `${rect.bottom + 4}px`,
        left:  `${rect.left}px`,
        width: `${rect.width}px`,
    }
}

function open() {
    if (isOpen.value) return
    isOpen.value = true
    query.value = ''
}

function close() {
    isOpen.value = false
}

function select(opt) {
    lastSelected.value = opt
    emit('update:modelValue', opt.value)
    close()
}

function clear() {
    lastSelected.value = null
    emit('update:modelValue', '')
    query.value = ''
}

function selectHighlighted() {
    if (filtered.value[highlighted.value]) select(filtered.value[highlighted.value])
}

function moveHighlight(dir) {
    highlighted.value = Math.max(0, Math.min(filtered.value.length - 1, highlighted.value + dir))
    if (listEl.value) {
        listEl.value.children[highlighted.value]?.scrollIntoView({ block: 'nearest' })
    }
}

function onClickOutside(e) {
    if (container.value && !container.value.contains(e.target)) close()
}

function onScroll() {
    if (isOpen.value) updatePosition()
}

onMounted(() => {
    document.addEventListener('mousedown', onClickOutside)
    window.addEventListener('scroll', onScroll, true)
    window.addEventListener('resize', onScroll)
})
onUnmounted(() => {
    clearTimeout(debounceTimer)
    document.removeEventListener('mousedown', onClickOutside)
    window.removeEventListener('scroll', onScroll, true)
    window.removeEventListener('resize', onScroll)
})
</script>
