<script setup>
defineProps(['message', 'canClose']);
</script>

<template>
    <div v-show="message">
        <div class="bg-red-50 border-l-8 border-red-900 mb-2 w-full p-2">
                <div class="p-2 flex justify-between">
                    <div class="ml-2">
                        <p class="text-md font-bold text-red-500 text-sm">{{ message }}</p>
                    </div>
                    <div>
                        <svg v-if="canClose" @click="this.$emit('close')" class="h-6 w-6 text-red-900 mr-2 cursor-pointer"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
            </div>
        </div>
    </div>
</template>
