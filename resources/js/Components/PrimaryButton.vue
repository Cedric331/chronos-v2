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
        class="inline-flex items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-gray-500 active:bg-gray-900 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>
