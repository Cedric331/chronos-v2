<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-gray-700 dark:bg-gray-100 dark:text-black border border-transparent rounded-md font-semibold text-xs text-white hover:bg-gray-400 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>
