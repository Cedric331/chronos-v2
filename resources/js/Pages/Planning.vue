<template>
    <AuthenticatedLayout>
        <Loading :show="isLoading"></Loading>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="font-semibold text-xl flex justify-center items-center text-gray-800 dark:text-gray-200 leading-tight group">
                    <div class="relative mr-3">
                        <img :src="selectedUser.avatar" class="h-10 md:h-9 rounded-full overflow-hidden shadow-md border-2 border-white dark:border-gray-700 transition-all duration-300" alt="Avatar">
                        <div class="absolute -bottom-1 -right-1 h-3 w-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-700"></div>
                    </div>
                    <span class="transition-all duration-300">Planning de <span class="font-bold">{{ selectedUser.name }}</span></span>
                </h2>
                <div class="w-full md:w-auto">
                    <Listbox v-model="selectedUser" class="min-w-[230px]">
                        <div class="relative">
                            <ListboxButton class="relative w-full cursor-pointer rounded-lg dark:text-white bg-white dark:bg-gray-700 py-2.5 pl-4 pr-10 text-left shadow-md hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-600 focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-opacity-75 focus-visible:ring-offset-2 focus-visible:ring-offset-indigo-300 sm:text-sm">
                                <div class="flex items-center">
                                    <img :src="selectedUser.avatar" class="h-6 w-6 rounded-full mr-2 border border-gray-200 dark:border-gray-600" alt="User avatar">
                                    <span class="block truncate font-medium ">{{ selectedUser.name }}</span>
                                </div>
                                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500 dark:text-gray-300">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </span>
                            </ListboxButton>

                            <transition
                                enter-active-class="transition duration-200 ease-out"
                                enter-from-class="transform scale-95 opacity-0"
                                enter-to-class="transform scale-100 opacity-100"
                                leave-active-class="transition duration-150 ease-in"
                                leave-from-class="transform scale-100 opacity-100"
                                leave-to-class="transform scale-95 opacity-0">
                                <ListboxOptions
                                    class="absolute z-50 mt-2 max-h-60 w-full overflow-auto rounded-md bg-white dark:bg-gray-700 py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                    <ListboxOption
                                        v-slot="{ active, selected }"
                                        v-for="user in users"
                                        :key="user.name"
                                        :value="user"
                                        as="template">
                                        <li :class="[
                                          active ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-900 dark:text-indigo-100' : 'text-gray-900 dark:text-gray-100',
                                          'relative cursor-pointer select-none py-2 pl-10 pr-4 transition-colors duration-150',
                                        ]">
                                        <div class="flex items-center">
                                            <img :src="user.avatar" class="h-5 w-5 rounded-full mr-2 absolute left-2" alt="User avatar">
                                            <span :class="[selected ? 'font-medium' : 'font-normal', 'block truncate']">{{ user.name }}</span>
                                        </div>
                                        <span
                                            v-if="selected"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-indigo-600 dark:text-indigo-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </span>
                                        </li>
                                    </ListboxOption>
                                </ListboxOptions>
                            </transition>
                        </div>
                    </Listbox>
                </div>
            </div>
        </template>

        <div class="py-4">
            <div class="w-full px-2 sm:px-4">
                <div class="dark:bg-gray-800 overflow-hidden rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl min-h-screen"
                     :style="{background: $store.state.isDarkMode? '': 'linear-gradient(135deg, ' + $page.props.auth.team.params.color2 + '33, ' + $page.props.auth.team.params.color1 + '33)'}">
                    <div class="p-2 sm:p-4">
                        <div class="flex justify-between mb-2">
                            <button @click="showWidgetManager = true"
                                    class="hidden md:inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                Widgets
                            </button>
                            <div class="hidden md:flex">
                                <button @click="getAllPlanning = !getAllPlanning"
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors duration-200"
                                        :class="getAllPlanning ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-100' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    {{ getAllPlanning ? 'Revenir au planning du jour' : 'Voir le planning complet' }}
                                </button>
                                <button @click="showShare = true"
                                        class="ml-2 inline-flex items-center px-3 py-1.5 text-sm font-medium bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-200 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                    Partager
                                </button>
                            </div>
                        </div>

                        <!-- Planning Widgets -->
                        <PlanningWidgets :userWidgets="planningWidgets" @update="handleWidgetUpdate" @remove="handleWidgetRemove" />
                        <Calendar
                            ref="calendar"
                            :daysProps="daysProps"
                            :weeklyHours="weeklyProps"
                            :isToday="isToday"
                            :getAllPlanning="getAllPlanning"
                            @shareSchedule="this.showShare = true"
                            @planningFull="this.getAllPlanning = !this.getAllPlanning">
                        </Calendar>
                    </div>
                </div>
            </div>
            <ModalSharePlanning v-if="showShare" :show="showShare" @close="this.showShare = false"></ModalSharePlanning>
            <PlanningWidgetManager
                v-if="showWidgetManager"
                :show="showWidgetManager"
                :selectedWidgets="planningWidgets"
                @close="showWidgetManager = false"
                @update="updateWidgets"
            ></PlanningWidgetManager>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Calendar from "@/Pages/Calendar/Calendar.vue";
import {
    Listbox,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
} from '@headlessui/vue'
import Loading from "@/Components/Loading.vue";
import ModalSharePlanning from "@/Pages/Calendar/Modal/ModalSharePlanning.vue";
import PlanningWidgets from "@/Components/PlanningWidgets.vue";
import PlanningWidgetManager from "@/Components/PlanningWidgetManager.vue";
import { useNotification } from '@/composables/useNotification';

export default {
    components: {
        ModalSharePlanning,
        Loading,
        ListboxOption,
        Listbox,
        ListboxButton,
        ListboxOptions,
        Calendar,
        AuthenticatedLayout,
        Head,
        PlanningWidgets,
        PlanningWidgetManager
    },
    props: {
        isToday: String,
        user: Object,
        users: Object,
        calendar: Object,
        weeklyHours: {
            type: Object,
            default: () => []
        },
        planningWidgetsPrefs: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            isLoading: false,
            getAllPlanning: false,
            showShare: false,
            showWidgetManager: false,
            selectedUser: this.user,
            daysProps: null,
            weeklyProps: null,
            planningWidgets: this.planningWidgetsPrefs || []
        }
    },
    watch: {
        selectedUser () {
            this.isLoading = true
            this.getPlanning()
        },
        getAllPlanning () {
            this.isLoading = true
            this.getPlanning()
        }
    },
    methods: {
        getPlanning () {
            axios.post('/planning', {
                user: this.selectedUser,
                getAllPlanning: this.getAllPlanning
            })
            .then(response => {
                this.daysProps = response.data.calendar
                this.weeklyProps = response.data.weeklyHours
            })
            .catch(error => {
                console.error('Erreur lors du chargement du planning:', error);
                const { showError } = useNotification();
                showError('Une erreur est survenue lors du chargement du planning. Veuillez réessayer.');
            })
            .finally(() => {
                setTimeout(() => {
                    this.isLoading = false
                    this.$refs.calendar.resetDaySelected()
                }, 200)
            })
        },

        // Méthode pour mettre à jour les widgets sans recharger la page
        updateWidgets(widgets) {
            // Créer une nouvelle référence pour forcer la réactivité
            this.planningWidgets = JSON.parse(JSON.stringify(widgets));

            // Enregistrer les préférences dans le localStorage pour persister entre les sessions
            localStorage.setItem('planningWidgets', JSON.stringify(widgets));

            // Forcer la mise à jour du composant PlanningWidgets
            this.$nextTick(() => {
                // Cette ligne force Vue à ré-évaluer le rendu des widgets
                this.planningWidgets = [...this.planningWidgets];
            });
        },

        // Gérer la mise à jour d'un widget individuel
        handleWidgetUpdate(updatedWidget) {
            // Trouver l'index du widget mis à jour
            const widgetIndex = this.planningWidgets.findIndex(widget => widget.id === updatedWidget.id);

            if (widgetIndex !== -1) {
                // Mettre à jour le widget dans le tableau
                this.planningWidgets[widgetIndex] = updatedWidget;

                // Créer une nouvelle référence pour forcer la réactivité
                this.planningWidgets = [...this.planningWidgets];

                // Enregistrer les préférences dans le localStorage
                localStorage.setItem('planningWidgets', JSON.stringify(this.planningWidgets));
            }
        },

        // Gérer la suppression d'un widget
        handleWidgetRemove(widgetToRemove) {
            // Filtrer le tableau pour supprimer le widget
            this.planningWidgets = this.planningWidgets.filter(widget => widget.id !== widgetToRemove.id);

            // Enregistrer les préférences dans le localStorage
            localStorage.setItem('planningWidgets', JSON.stringify(this.planningWidgets));

            // Enregistrer les préférences sur le serveur
            axios.post('/planning/widgets/preferences', {
                widgets: this.planningWidgets
            })
            .then(response => {
                console.log('Widget supprimé avec succès');
            })
            .catch(error => {
                console.error('Erreur lors de la suppression du widget:', error);
            });
        }
    },
    beforeMount () {
        this.daysProps = this.calendar
        this.weeklyProps = this.weeklyHours

        // Si des widgets sont déjà enregistrés dans le localStorage, les utiliser
        const savedWidgets = localStorage.getItem('planningWidgets');
        if (savedWidgets) {
            try {
                this.planningWidgets = JSON.parse(savedWidgets);
            } catch (e) {
                console.error('Erreur lors du chargement des widgets depuis le localStorage:', e);
            }
        }
    }
}
</script>

<style>
li {
    list-style-type: none;
}

.dark .dark\:bg-gray-800 {
    background-color: rgba(31, 41, 55, 0.95);
}



/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom scrollbar for the dropdown */
.overflow-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.overflow-auto::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Dark mode scrollbar */
.dark .overflow-auto::-webkit-scrollbar-track {
    background: #374151;
}

.dark .overflow-auto::-webkit-scrollbar-thumb {
    background: #6B7280;
}

.dark .overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #9CA3AF;
}
</style>
