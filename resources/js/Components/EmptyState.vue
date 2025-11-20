<template>
    <div class="text-center py-12 px-4">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
            <svg 
                v-if="!customIcon"
                class="h-8 w-8 text-gray-400" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
            >
                <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="2" 
                    :d="iconPath"
                />
            </svg>
            <slot v-else name="icon" />
        </div>
        <h3 
            v-if="title"
            class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2"
        >
            {{ title }}
        </h3>
        <p 
            v-if="description"
            class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-6"
        >
            {{ description }}
        </p>
        <div v-if="$slots.action" class="flex justify-center">
            <slot name="action" />
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    icon: {
        type: String,
        default: 'empty',
        validator: (v) => ['empty', 'error', 'search', 'no-results'].includes(v),
    },
    customIcon: {
        type: Boolean,
        default: false,
    },
});

const iconPaths = {
    empty: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
    error: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    search: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
    'no-results': 'M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
};

const iconPath = iconPaths[props.icon] || iconPaths.empty;
</script>

