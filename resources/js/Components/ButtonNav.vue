<template>
    <div>
        <Loading :show="isLoading" :messageLoading="messageLoading"></Loading>
        <div class="fixed bottom-4 right-4 z-50">

            <!-- Bouton fixe -->
            <div @click.prevent="isMenuOpen = !isMenuOpen" class="h-12 w-12 bg-[#70a1ff] rounded-full flex items-center justify-center text-black cursor-pointer relative">
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
                        @click.prevent="updateDay()"
                        class="bg-[#eccc68] px-3 py-3 rounded-full text-white dark:text-black text-sm flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 dark:text-black text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </div>

                <div v-show="daySelected.length > 0" class="rounded-full py-2 space-y-2">
                    <button
                        id="event"
                        @click.prevent="openEvent()"
                        class="bg-[#00d2d3] px-3 py-3 rounded-full text-white dark:text-black text-sm flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                        </svg>
                    </button>
                </div>

                <div v-show="daySelected.length > 0 && hasEvent" class="rounded-full py-2 space-y-2">
                    <button
                        id="event-delete"
                        @click.prevent="deleteEvent()"
                        class="bg-[#ff6b6b] px-3 py-3 rounded-full text-white dark:text-black text-sm flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </div>
                <button @click.prevent="this.exportPlanning()"
                        id="exportExcel"
                        class="bg-[#20bf6b] px-3 py-3 rounded-full text-black text-sm flex items-center justify-center cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" data-slot="icon" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </button>
                <div class="rounded-full py-2 space-y-2">
                    <button @click.prevent="this.$emit('planningFull')"
                            id="viewAllPlanning"
                            class="bg-[#58B19F] px-3 py-3 rounded-full text-black text-sm flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div v-else
                class="absolute bottom-16 left-0 right-0 transition-all duration-300 transform origin-bottom"
                :class="{'scale-y-0': !isMenuOpen, 'scale-y-100': isMenuOpen}">
                <button v-show="daySelected.length > 0 && this.$page.props.auth.team.params.paid_leave && isMyPlanning" @click.prevent="openModalPaidLeave()"
                        id="paidLeave"
                        class="bg-[#eccc68] px-3 py-3 rounded-full text-black text-sm flex items-center justify-center cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.893 13.393l-1.135-1.135a2.252 2.252 0 01-.421-.585l-1.08-2.16a.414.414 0 00-.663-.107.827.827 0 01-.812.21l-1.273-.363a.89.89 0 00-.738 1.595l.587.39c.59.395.674 1.23.172 1.732l-.2.2c-.212.212-.33.498-.33.796v.41c0 .409-.11.809-.32 1.158l-1.315 2.191a2.11 2.11 0 01-1.81 1.025 1.055 1.055 0 01-1.055-1.055v-1.172c0-.92-.56-1.747-1.414-2.089l-.655-.261a2.25 2.25 0 01-1.383-2.46l.007-.042a2.25 2.25 0 01.29-.787l.09-.15a2.25 2.25 0 012.37-1.048l1.178.236a1.125 1.125 0 001.302-.795l.208-.73a1.125 1.125 0 00-.578-1.315l-.665-.332-.091.091a2.25 2.25 0 01-1.591.659h-.18c-.249 0-.487.1-.662.274a.931.931 0 01-1.458-1.137l1.411-2.353a2.25 2.25 0 00.286-.76m11.928 9.869A9 9 0 008.965 3.525m11.928 9.868A9 9 0 118.965 3.525" />
                    </svg>
                </button>
                    <div v-show="daySelected.length > 0 && $page.props.auth.team.params.update_planning && isMyPlanning" class="rounded-full py-2 space-y-2">
                        <button
                            id="horaires1"
                            @click.prevent="updateDay()"
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
<!--                    <button @click.prevent="openAuthWindow()"-->
<!--                        id="google"-->
<!--                        class="bg-[#eccc68] opacity-75 px-3 py-3 rounded-full text-black text-sm flex items-center justify-center cursor-pointer">-->
<!--                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />-->
<!--                        </svg>-->
<!--                    </button>-->
                    <button v-show="daySelected.length > 0 && this.$page.props.auth.team.params.paid_leave" @click.prevent="openModalPaidLeave()"
                            id="paidLeave"
                            class="bg-[#eccc68] px-3 py-3 rounded-full text-black text-sm flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.893 13.393l-1.135-1.135a2.252 2.252 0 01-.421-.585l-1.08-2.16a.414.414 0 00-.663-.107.827.827 0 01-.812.21l-1.273-.363a.89.89 0 00-.738 1.595l.587.39c.59.395.674 1.23.172 1.732l-.2.2c-.212.212-.33.498-.33.796v.41c0 .409-.11.809-.32 1.158l-1.315 2.191a2.11 2.11 0 01-1.81 1.025 1.055 1.055 0 01-1.055-1.055v-1.172c0-.92-.56-1.747-1.414-2.089l-.655-.261a2.25 2.25 0 01-1.383-2.46l.007-.042a2.25 2.25 0 01.29-.787l.09-.15a2.25 2.25 0 012.37-1.048l1.178.236a1.125 1.125 0 001.302-.795l.208-.73a1.125 1.125 0 00-.578-1.315l-.665-.332-.091.091a2.25 2.25 0 01-1.591.659h-.18c-.249 0-.487.1-.662.274a.931.931 0 01-1.458-1.137l1.411-2.353a2.25 2.25 0 00.286-.76m11.928 9.869A9 9 0 008.965 3.525m11.928 9.868A9 9 0 118.965 3.525" />
                        </svg>
                    </button>
                    <button @click.prevent="this.exportPlanning()"
                            id="exportExcel"
                            class="bg-[#20bf6b] px-3 py-3 rounded-full text-black text-sm flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" data-slot="icon" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </button>
                    <button @click.prevent="this.$emit('planningFull')"
                            id="viewAllPlanning"
                            class="bg-[#58B19F] px-3 py-3 rounded-full text-black text-sm flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                        </svg>
                    </button>
                    <button v-show="$page.props.auth.team.params.share_link_planning && isMyPlanning" @click.prevent="this.$emit('shareSchedule')"
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
import axios from "axios";

export default {
    components: {Loading},
    emits: ['openUpdateDay', 'openPaidLeave', 'planningFull', 'shareSchedule', 'openEvent', 'deleteEvent'],
    props: {
        daySelected: Object,
        getAllPlanning: Boolean,
        isMyPlanning: Boolean,
    },
    data() {
        return {
            isMenuOpen: false,
            isLoading: false,
            hasEvent: false,
            messageLoading: null,
            authWindow: null
        }
    },
    watch: {
        isMenuOpen: function () {
            this.checkHasEvent()
        },
        daySelected: function () {
            if (this.daySelected.length === 0) {
                this.isMenuOpen = false
            }
        }
    },
    methods: {
        updateDay () {
            this.$emit('openUpdateDay')
        },
        openEvent () {
            this.$emit('openEvent')
        },
        deleteEvent () {
            this.$emit('deleteEvent')
        },
        openAuthWindow() {
            if (!this.$page.props.auth.user.hasPlanning) {
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
        openModalPaidLeave () {
            if (this.daySelected.length > 0) {
                this.$emit('openPaidLeave')
            } else {
                this.$notify({
                    type: 'warn',
                    title: 'Action impossible',
                    text: 'Vous devez sélectionner au moins un jour.',
                });
            }
        },
        exportPlanning () {
            axios.get('/planning/user/export', {
                responseType: 'blob'
            })
                .then(response => {
                    let blob = new Blob([response.data], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
                    let link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'planning.xlsx';
                    link.click();
                })
                .catch(error => {
                    this.$notify({
                        type: 'error',
                        title: 'Error',
                        text: 'Une erreur est survenue lors de l\'export du planning.'
                    })
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
        checkHasEvent () {
            if (this.$page.props.auth.isCoordinateur && this.daySelected.length > 0) {
                if (this.daySelected.length) {
                    this.daySelected.forEach(day => {
                        day.plannings.forEach(planning => {
                            if (planning.event_plannings.length) {
                                this.hasEvent = true
                            }
                        })
                    })
                }
            }
        }
    },
    mounted() {
        this.checkHasEvent()
        let message = this.getAllPlanning ? 'Revenir au planning du jour' : 'Voir le planning complet'
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
        tippy('#shareSchedule', {
            placement: 'left',
            content: 'Créer un lien pour partager mon planning',
        });
        tippy('#viewAllPlanning', {
            placement: 'left',
            content: message,
        });
        tippy('#event', {
            placement: 'left',
            content: 'Créer un évènement',
        });
        tippy('#event-delete', {
            placement: 'left',
            content: 'Supprimer les évènements de la journée',
        });
        tippy('#paidLeave', {
            placement: 'left',
            content: 'Demande de congé payé',
        });
        tippy('#exportExcel', {
            placement: 'left',
            content: 'Exporter votre planning au format Excel',
        });
    }
}
</script>
