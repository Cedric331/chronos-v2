<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 hover:bg-gray-400 text-white bg-gray-600 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs dark:text-gray-300 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>
