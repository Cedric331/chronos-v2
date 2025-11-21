<template>
    <AuthenticatedLayout>
        <div class="flex overflow-hidden min-h-screen" :class="[$store.state.isDarkMode ? 'bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800' : 'bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200']">
            <div id="main-content" class="h-full w-full relative overflow-y-auto" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color2 }">
                <main>
                    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
                        <!-- Dashboard Header -->
                        <div class="mb-8 lg:mb-12">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                                <div class="mb-4 md:mb-0">
                                    <div class="flex items-center mb-3">
                                        <div class="p-3 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg mr-4 transform hover:scale-110 transition-transform duration-300">
                                            <i class="fas fa-tachometer-alt text-white text-2xl"></i>
                                        </div>
                                        <div>
                                            <h1 class="text-4xl lg:text-5xl font-bold mb-2 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400">
                                                Tableau de bord
                                            </h1>
                                            <p class="text-base lg:text-lg" :class="$store.state.isDarkMode ? 'text-gray-400' : 'text-gray-600'">
                                                Gérez vos équipes et consultez les activités du système
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Stats Cards -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 mb-8">
                                <!-- Teams Card -->
                                <div class="group relative overflow-hidden bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-200/50 dark:border-gray-700/50 hover:border-blue-400/50 dark:hover:border-blue-500/50 transform hover:-translate-y-2">
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-blue-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    <div class="relative p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="p-4 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                                <i class="fas fa-users text-white text-2xl"></i>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-1">{{ teamsProps.length }}</p>
                                                <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Équipes</p>
                                            </div>
                                        </div>
                                        <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full" :style="{ width: Math.min((teamsProps.length / 10) * 100, 100) + '%' }"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Responsables Card -->
                                <div class="group relative overflow-hidden bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-200/50 dark:border-gray-700/50 hover:border-orange-400/50 dark:hover:border-orange-500/50 transform hover:-translate-y-2">
                                    <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-orange-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    <div class="relative p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="p-4 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                                <i class="fas fa-user-shield text-white text-2xl"></i>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-1">{{ stats?.responsables || 0 }}</p>
                                                <p class="text-xs font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wider">Responsables</p>
                                            </div>
                                        </div>
                                        <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-orange-500 to-orange-600 rounded-full" :style="{ width: Math.min(((stats?.responsables || 0) / 10) * 100, 100) + '%' }"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Coordinators Card -->
                                <div class="group relative overflow-hidden bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-200/50 dark:border-gray-700/50 hover:border-green-400/50 dark:hover:border-green-500/50 transform hover:-translate-y-2">
                                    <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-green-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    <div class="relative p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="p-4 rounded-xl bg-gradient-to-br from-green-500 to-green-600 shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                                <i class="fas fa-user-tie text-white text-2xl"></i>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-1">{{ coordinateursProps.length }}</p>
                                                <p class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider">Coordinateurs</p>
                                            </div>
                                        </div>
                                        <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-green-500 to-green-600 rounded-full" :style="{ width: Math.min((coordinateursProps.length / 10) * 100, 100) + '%' }"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conseillers Card -->
                                <div class="group relative overflow-hidden bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-200/50 dark:border-gray-700/50 hover:border-cyan-400/50 dark:hover:border-cyan-500/50 transform hover:-translate-y-2">
                                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-cyan-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    <div class="relative p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="p-4 rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                                <i class="fas fa-user-friends text-white text-2xl"></i>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-1">{{ stats?.conseillers || 0 }}</p>
                                                <p class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 uppercase tracking-wider">Conseillers</p>
                                            </div>
                                        </div>
                                        <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-full" :style="{ width: Math.min(((stats?.conseillers || 0) / 50) * 100, 100) + '%' }"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Utilisateurs Actifs Card -->
                                <div class="group relative overflow-hidden bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-200/50 dark:border-gray-700/50 hover:border-emerald-400/50 dark:hover:border-emerald-500/50 transform hover:-translate-y-2">
                                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    <div class="relative p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                                <i class="fas fa-user-check text-white text-2xl"></i>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-1">{{ stats?.usersActifs || 0 }}</p>
                                                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Utilisateurs Actifs</p>
                                            </div>
                                        </div>
                                        <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full" :style="{ width: stats?.totalUsers > 0 ? Math.min(((stats?.usersActifs || 0) / stats.totalUsers) * 100, 100) + '%' : '0%' }"></div>
                                        </div>
                                    </div>
                                </div>

                                
                                <!-- Date & Time Card (Fusionné) -->
                                <div class="group relative overflow-hidden bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-200/50 dark:border-gray-700/50 hover:border-purple-400/50 dark:hover:border-purple-500/50 transform hover:-translate-y-2">
                                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-purple-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    <div class="relative p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="p-4 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 shadow-lg transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                                <i class="fas fa-calendar-alt text-white text-2xl"></i>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">{{ currentDate }}  {{ currentTime }}</p>
                                                <p class="text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Date & Heure</p>
                                            </div>
                                        </div>
                                        <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full">
                                            <div class="h-full bg-gradient-to-r from-purple-500 to-purple-600 rounded-full w-full animate-pulse"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Tabs Navigation -->
                        <div class="mb-8">
                            <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-md rounded-2xl p-2 shadow-lg border border-gray-200/50 dark:border-gray-700/50">
                                <ul class="flex flex-wrap gap-2 justify-center sm:justify-start">
                                    <li v-for="(tab, index) in tabs" :key="index" class="flex-1 sm:flex-initial">
                                        <a @click.prevent="activeTab = tab.id"
                                           :class="[
                                               activeTab === tab.id ?
                                                   'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg' :
                                                   'bg-gray-100/80 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600/50',
                                               'inline-flex items-center justify-center py-3.5 px-6 font-semibold text-sm rounded-xl cursor-pointer transition-all duration-300 w-full sm:w-auto min-w-[160px]'
                                           ]"
                                        >
                                            <i :class="[tab.icon, 'mr-2 text-base']"></i>
                                            <span class="hidden sm:inline whitespace-nowrap">{{ tab.name }}</span>
                                            <span class="sm:hidden whitespace-nowrap">{{ tab.shortName || tab.name }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Tab Content with Enhanced Animations -->
                        <div class="transition-all duration-500 ease-in-out">
                            <!-- Teams Section -->
                            <div v-if="activeTab === 'teams'" class="animate-fade-in">
                                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden border border-gray-200/50 dark:border-gray-700/50 transition-all duration-500 hover:shadow-2xl">
                                    <div class="p-6 bg-gradient-to-r from-indigo-500 to-purple-600 border-b border-indigo-400/20">
                                        <h2 class="text-xl font-bold text-white flex items-center">
                                            <i class="fas fa-users mr-3 text-2xl"></i>
                                            <span>Gestion des équipes</span>
                                        </h2>
                                    </div>
                                    <div class="p-6">
                                        <Teams :teams-props="teamsProps" :coordinateursProps="coordinateursProps" />
                                    </div>
                                </div>
                            </div>

                            <!-- Log Section -->
                            <div v-if="activeTab === 'logs'" class="animate-fade-in">
                                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden border border-gray-200/50 dark:border-gray-700/50 transition-all duration-500 hover:shadow-2xl">
                                    <div class="p-6 bg-gradient-to-r from-indigo-500 to-purple-600 border-b border-indigo-400/20">
                                        <h2 class="text-xl font-bold text-white flex items-center">
                                            <i class="fas fa-history mr-3 text-2xl"></i>
                                            <span>Journal d'activités</span>
                                        </h2>
                                    </div>
                                    <div class="p-6">
                                        <Log></Log>
                                    </div>
                                </div>
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
        },
        stats: {
            type: Object,
            default: () => ({
                conseillers: 0,
                responsables: 0,
                usersActifs: 0,
                totalUsers: 0,
                ticketsOuverts: 0,
                ticketsTotal: 0
            })
        }
    },
    data () {
        return {
            activeTab: 'teams',
            tabs: [
                { id: 'teams', name: 'Équipes', shortName: 'Équipes', icon: 'fas fa-users' },
                { id: 'logs', name: 'Journal d\'activités', shortName: 'Journal', icon: 'fas fa-history' }
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

/* Enhanced Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

.animate-slide-in {
    animation: slideIn 0.4s ease-out;
}

/* Glassmorphism effect */
.backdrop-blur-sm {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

.backdrop-blur-md {
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

/* Enhanced transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Gradient text */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
}

.text-transparent {
    color: transparent;
}

/* Smooth hover effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\:rotate-6 {
    transform: rotate(6deg);
}

/* Card hover effects */
.hover-lift {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-lift:hover {
    transform: translateY(-8px);
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    h1 {
        font-size: 2rem;
    }
}

/* Dark mode enhancements */
@media (prefers-color-scheme: dark) {
    .backdrop-blur-sm {
        background-color: rgba(31, 41, 55, 0.8);
    }
}

/* Custom scrollbar */
#main-content::-webkit-scrollbar {
    width: 8px;
}

#main-content::-webkit-scrollbar-track {
    background: transparent;
}

#main-content::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.3);
    border-radius: 4px;
}

#main-content::-webkit-scrollbar-thumb:hover {
    background: rgba(99, 102, 241, 0.5);
}
</style>
