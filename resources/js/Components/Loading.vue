<template>
    <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="inset-0 bg-black/50 dark:bg-gray-900/80 backdrop-blur-sm fixed flex w-full h-full items-center justify-center z-[6000]">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-2xl">
                <div class="flex flex-col items-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-amber-500 border-t-transparent"></div>
                    <p v-if="messageLoading" class="mt-4 text-gray-700 dark:text-gray-300 font-medium text-sm sm:text-base text-center max-w-xs">
                        {{ messageLoading }}
                    </p>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script>
export default {
    name: "Loading",
    props: {
        show: Boolean,
        messageLoading: {
            type: String,
            default: "Chargement en cours..."
        }
    }
}
</script>

<style scoped>

</style>
