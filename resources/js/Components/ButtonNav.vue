<template>
    <div>
        <div class="fixed bottom-4 right-4 z-50">
            <!-- Bouton fixe -->
            <div @click.prevent="isMenuOpen = !isMenuOpen" class="h-12 w-12 bg-gray-800 dark:bg-white rounded-full flex items-center justify-center text-white dark:text-black cursor-pointer relative">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <div class="absolute top-1 right-0 bg-[#ff4757] text-white rounded-full w-4 h-4 p-2.5 flex items-center justify-center transform translate-x-1/2 -translate-y-1/2">
                    {{ daySelected.length }}
                </div>
            </div>

            <!-- Menu -->
            <div
                class="absolute bottom-16 left-0 right-0 transition-all duration-300 transform origin-bottom"
                :class="{'scale-y-0': !isMenuOpen, 'scale-y-100': isMenuOpen}">
                <div class="rounded-full py-2 space-y-2">
                    <button
                        id="horaires"
                        @click.prevent="openUpdateDay()"
                        class="px-3 py-3 rounded-full bg-gray-800 dark:bg-white text-white dark:text-black text-sm flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 dark:text-black text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import tippy from "tippy.js";

export default {
    emits: ['openUpdateDay'],
    props: {
        daySelected: Object
    },
    data() {
        return {
            isMenuOpen: false
        }
    },
    methods: {
        openUpdateDay () {
            this.$emit('openUpdateDay')
        }
    },
    mounted() {
        tippy('#horaires', {
            placement: 'left',
            content: 'Modification des horaires',
        });
    }
}
</script>
