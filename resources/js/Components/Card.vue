<template>
    <div 
        :class="[
            'bg-white dark:bg-gray-800 rounded-xl shadow-md',
            'transition-all duration-300',
            hoverable ? 'hover:shadow-xl hover:-translate-y-1 cursor-pointer' : '',
            padding,
            border ? 'border border-gray-200 dark:border-gray-700' : '',
        ]"
    >
        <div v-if="$slots.header" class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
            <slot name="header" />
        </div>
        <slot />
        <div v-if="$slots.footer" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    hoverable: { type: Boolean, default: false },
    padding: { type: String, default: 'p-6' },
    border: { type: Boolean, default: false },
});
</script>

