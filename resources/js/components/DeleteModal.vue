<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="$emit('cancel')"></div>

            <!-- Dialog -->
            <div class="relative bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ title }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">This action cannot be undone.</p>
                    </div>
                </div>

                <p class="text-sm text-gray-600 mb-6">{{ message }}</p>

                <div class="flex items-center justify-end gap-3">
                    <button @click="$emit('cancel')"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button @click="$emit('confirm')"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors">
                        Yes, delete
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
        default: 'Confirm Delete',
    },
    message: {
        type: String,
        default: 'Are you sure you want to delete this item?',
    },
})

defineEmits(['confirm', 'cancel'])
</script>
