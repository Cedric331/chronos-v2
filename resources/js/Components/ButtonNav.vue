<template>
    <div>
        <Loading :show="isLoading" :messageLoading="messageLoading"></Loading>
        <div class="fixed bottom-4 right-4 z-50">

            <!-- Bouton fixe -->
            <div v-show="$page.props.auth.isCoordinateur && daySelected.length > 0 || !$page.props.auth.isCoordinateur" @click.prevent="isMenuOpen = !isMenuOpen" class="h-12 w-12 bg-[#70a1ff] rounded-full flex items-center justify-center text-black cursor-pointer relative">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <div v-if="daySelected.length > 0" class="absolute top-1 right-1 bg-[#ff4757] text-white rounded-full w-4 h-4 p-2.5 flex items-center justify-center transform translate-x-1/2 -translate-y-1/2">
                    {{ daySelected.length }}
                </div>
            </div>

            <!-- Menu -->
            <div v-if="$page.props.auth.isCoordinateur"
                class="absolute bottom-16 left-0 right-0 transition-all duration-300 transform origin-bottom"
                :class="{'scale-y-0': !isMenuOpen, 'scale-y-100': isMenuOpen}">
                <div v-show="daySelected.length > 0" class="rounded-full py-2 space-y-2">
                    <button
                        id="horaires"
                        @click.prevent="openUpdateDay()"
                        class="bg-[#eccc68] px-3 py-3 rounded-full text-white dark:text-black text-sm flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 dark:text-black text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </div>

                <div v-show="daySelected.length > 0" class="rounded-full py-2 space-y-2">
                    <button
                        id="event"
                        @click.prevent="openUpdateDay()"
                        class="bg-[#00d2d3] px-3 py-3 rounded-full text-white dark:text-black text-sm flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div v-else
                class="absolute bottom-16 left-0 right-0 transition-all duration-300 transform origin-bottom"
                :class="{'scale-y-0': !isMenuOpen, 'scale-y-100': isMenuOpen}">
                    <div v-show="daySelected.length > 0 && $page.props.auth.team.params.update_planning" class="rounded-full py-2 space-y-2">
                        <button
                            id="horaires1"
                            @click.prevent="openUpdateDay()"
                            class="bg-[#1e90ff] relative px-3 py-3 rounded-full text-black text-sm flex items-center justify-center cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                        <div v-if="daySelected.length > 0" class="absolute top-1 right-1 bg-[#ff4757] text-white rounded-full w-4 h-4 p-2.5 flex items-center justify-center transform translate-x-1/2 -translate-y-1/2">
                            {{ daySelected.length }}
                        </div>
                    </div>
                <div class="rounded-full py-2 space-y-2">
                    <button @click.prevent="openAuthWindow()"
                        id="google"
                        class="bg-[#eccc68] px-3 py-3 rounded-full text-black text-sm flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                        </svg>
                    </button>
                    <button @click.prevent="this.$emit('planningFull')"
                            id="viewAllPlanning"
                            class="bg-[#34e7e4] px-3 py-3 rounded-full text-black text-sm flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                        </svg>
                    </button>
                    <button v-show="$page.props.auth.team.params.share_link_planning" @click.prevent="this.$emit('shareSchedule')"
                            id="shareSchedule"
                            class="bg-[#ffdd59] px-3 py-3 rounded-full text-black text-sm flex items-center justify-center cursor-pointer">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                        </svg>
                    </button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import tippy from "tippy.js";
import Loading from "@/Components/Loading.vue";

export default {
    components: {Loading},
    emits: ['openUpdateDay', 'planningFull', 'shareSchedule'],
    props: {
        daySelected: Object
    },
    data() {
        return {
            isMenuOpen: false,
            isLoading: false,
            messageLoading: null,
            authWindow: null
        }
    },
    methods: {
        openUpdateDay () {
            this.$emit('openUpdateDay')
        },
        openAuthWindow() {
            if (!this.$page.props.auth.user.has_planning) {
                this.$notify({
                    type: 'warn',
                    title: 'Action impossible',
                    text: 'Vous devez en avoir un planning afin de le synchroniser avec votre Google Agenda.',
                });
                return
            }
            this.messageLoading = "Synchronisation en cours..."
            this.isLoading = true;
            this.authWindow = window.open('/redirect/google', 'authWindow', 'width=500,height=500');

            this.authWindow.addEventListener('load', () => {
                this.isLoading = false;
            });
        },
        handleAuthMessage(event) {
            // if (event.origin !== 'http://127.0.0.1:8000') {
            //     return;
            // }

            if (this.authWindow) {
                this.authWindow.close();
                this.authWindow = null;
            }
            this.isLoading = false;
        },
    },
    mounted() {
        tippy('#horaires', {
            placement: 'left',
            content: 'Modification des horaires',
        });
        tippy('#horaires1', {
            placement: 'left',
            content: 'Modification des horaires',
        });
        tippy('#google', {
            placement: 'left',
            content: 'Synchroniser avec Google Agenda',
        });
        tippy('#viewAllPlanning', {
            placement: 'left',
            content: 'Voir le planning complet',
        });
        tippy('#shareSchedule', {
            placement: 'left',
            content: 'Créer un lien pour partager mon planning',
        });
        tippy('#event', {
            placement: 'left',
            content: 'Créer un évènement',
        });
    }
}
</script>
