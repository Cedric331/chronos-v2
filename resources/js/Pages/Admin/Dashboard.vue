<template>
    <AuthenticatedLayout>
        <div class="flex overflow-hidden" :class="[$store.state.isDarkMode ? 'bg-gray-900' : 'bg-gray-100']">
            <div id="main-content" class="h-full w-full relative overflow-y-auto" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color2 }">
                <main>
                    <div class="container mx-auto px-4 py-8">
                        <!-- Dashboard Header with stats -->
                        <div class="mb-8">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                                <div>
                                    <h1 class="text-3xl font-bold mb-2" :class="$store.state.isDarkMode ? 'text-white' : 'text-gray-800'">
                                        <i class="fas fa-tachometer-alt mr-2"></i> Tableau de bord administrateur
                                    </h1>
                                    <p class="text-sm" :class="$store.state.isDarkMode ? 'text-gray-300' : 'text-gray-600'">
                                        Gérez vos équipes et consultez les activités du système
                                    </p>
                                </div>
                            </div>

                            <!-- Quick stats cards -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border-l-4 border-blue-500 transition-all duration-300 hover:shadow-md">
                                    <div class="flex items-center">
                                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                                            <i class="fas fa-users text-blue-500 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Équipes</p>
                                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ teamsProps.length }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border-l-4 border-green-500 transition-all duration-300 hover:shadow-md">
                                    <div class="flex items-center">
                                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 mr-4">
                                            <i class="fas fa-user-tie text-green-500 dark:text-green-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Coordinateurs</p>
                                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ coordinateursProps.length }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border-l-4 border-purple-500 transition-all duration-300 hover:shadow-md">
                                    <div class="flex items-center">
                                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 mr-4">
                                            <i class="fas fa-calendar-alt text-purple-500 dark:text-purple-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</p>
                                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ currentDate }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border-l-4 border-amber-500 transition-all duration-300 hover:shadow-md">
                                    <div class="flex items-center">
                                        <div class="p-3 rounded-full bg-amber-100 dark:bg-amber-900 mr-4">
                                            <i class="fas fa-clock text-amber-500 dark:text-amber-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Heure</p>
                                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ currentTime }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs Navigation -->
                        <div class="mb-6">
                            <div class="border-b border-gray-200 dark:border-gray-700">
                                <ul class="flex flex-wrap -mb-px">
                                    <li class="mr-2" v-for="(tab, index) in tabs" :key="index">
                                        <a @click.prevent="activeTab = tab.id"
                                           :class="[activeTab === tab.id ?
                                                'border-indigo-500 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' :
                                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                                                'inline-flex items-center py-4 px-4 font-medium text-sm border-b-2 cursor-pointer transition-all duration-200']"
                                        >
                                            <i :class="[tab.icon, 'mr-2']"></i> {{ tab.name }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Tab Content -->
                        <div class="transition-all duration-300 ease-in-out">
                            <!-- Teams Section -->
                            <div v-if="activeTab === 'teams'" class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg">
                                <div class="p-4 bg-indigo-50 dark:bg-indigo-900 border-b border-indigo-100 dark:border-indigo-800">
                                    <h2 class="text-lg font-semibold text-indigo-700 dark:text-indigo-300">
                                        <i class="fas fa-users mr-2"></i> Gestion des équipes
                                    </h2>
                                </div>
                                <Teams :teams-props="teamsProps" :coordinateursProps="coordinateursProps" />
                            </div>

                            <!-- Log Section -->
                            <div v-if="activeTab === 'logs'" class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg">
                                <div class="p-4 bg-indigo-50 dark:bg-indigo-900 border-b border-indigo-100 dark:border-indigo-800">
                                    <h2 class="text-lg font-semibold text-indigo-700 dark:text-indigo-300">
                                        <i class="fas fa-history mr-2"></i> Journal d'activités
                                    </h2>
                                </div>
                                <Log></Log>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import {defineComponent} from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Teams from "@/Pages/Admin/Components/Teams.vue";
import Log from "@/Pages/Team/Components/Log.vue";

export default defineComponent({
    components: {
        Log,
        Teams,
        SecondaryButton,
        PrimaryButton,
        AuthenticatedLayout
    },
    props: {
        teamsProps: {
            type: Array,
            required: true
        },
        coordinateursProps: {
            type: Array,
            required: true
        }
    },
    data () {
        return {
            activeTab: 'teams',
            tabs: [
                { id: 'teams', name: 'Équipes', icon: 'fas fa-users' },
                { id: 'logs', name: 'Journal d\'activités', icon: 'fas fa-history' }
            ],
            currentDate: '',
            currentTime: '',
            timer: null
        }
    },
    mounted() {
        this.updateDateTime();
        this.timer = setInterval(this.updateDateTime, 60000); // Update every minute
    },
    beforeUnmount() {
        clearInterval(this.timer);
    },
    methods: {
        updateDateTime() {
            const now = new Date();
            this.currentDate = now.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
            this.currentTime = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }
    },
})
</script>

<style scoped>
.container {
    max-width: 1600px;
}

/* Transitions pour les éléments */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.duration-300 {
    transition-duration: 300ms;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

/* Hover effects */
.hover-lift {
    transition: transform 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-3px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>
