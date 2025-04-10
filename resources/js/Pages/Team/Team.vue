<template>
    <AuthenticatedLayout>
        <div id="main-content" class="min-h-screen relative overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800"
             :style="getBackgroundStyle()">

            <!-- Header with team info -->
            <div class="relative overflow-hidden mb-8">
                <div class="absolute inset-0 bg-gradient-to-r"
                     :style="getHeaderGradient()"></div>
                <div class="relative z-10 px-8 py-10 flex flex-col md:flex-row items-start md:items-center justify-between">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="w-16 h-16 rounded-full bg-white shadow-lg flex items-center justify-center overflow-hidden mr-4">
                            <img v-if="hasTeamLogo()" :src="getTeamLogo()" alt="Team Logo" class="w-14 h-14 object-cover">
                            <i v-else class="fas fa-users-cog text-3xl" :style="{ color: getTeamColor1() }"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">{{ getTeamName() }}</h1>
                            <p class="text-white text-opacity-80">{{ getUsersCount() }} membres</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button v-for="tab in tabs" :key="tab.id"
                            @click="activeTab = tab.id"
                            class="px-4 py-2 rounded-full transition-all duration-200 ease-in-out flex items-center"
                            :class="{
                                'bg-white text-gray-800 shadow-md font-medium': activeTab === tab.id,
                                'bg-white bg-opacity-20 text-white hover:bg-opacity-30': activeTab !== tab.id
                            }">
                            <i :class="getTabIcon(tab.id)" class="mr-2"></i>
                            {{ tab.name }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main content area -->
            <div class="px-6 pb-10">
                <transition name="fade" mode="out-in">
                    <!-- Team tab -->
                    <div v-if="activeTab === 'team'" class="space-y-6">
                        <div class="w-full grid grid-cols-1 2xl:grid-cols-2 gap-8">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transform transition-all duration-300 hover:shadow-md">
                                <TeamGestion :team="team" :coordinateursProps="coordinateursProps"></TeamGestion>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transform transition-all duration-300 hover:shadow-md">
                                <TeamUser :usersProps="team.users"></TeamUser>
                            </div>
                        </div>
                    </div>

                    <!-- Planning tab -->
                    <div v-else-if="activeTab === 'planning'" class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transform transition-all duration-300 hover:shadow-md">
                            <TeamRotation :team="team"></TeamRotation>
                        </div>
                    </div>

                    <!-- Module tab -->
                    <div v-else-if="activeTab === 'module'" class="space-y-6">
                        <div v-if="team.params.module_alert" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transform transition-all duration-300 hover:shadow-md">
                            <TeamHoraire :team="team" :schedules="schedules"></TeamHoraire>
                        </div>
                    </div>

                    <!-- Statistique tab -->
                    <div v-else-if="activeTab === 'statistique'" class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transform transition-all duration-300 hover:shadow-md">
                            <TeamStatistique :users="team.users"></TeamStatistique>
                        </div>
                    </div>

                    <!-- Log tab -->
                    <div v-else-if="activeTab === 'log'" class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transform transition-all duration-300 hover:shadow-md">
                            <Log :team="team"></Log>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Calendar from "@/Pages/Calendar/Calendar.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ModalUserUpdate from "@/Pages/Team/Modal/ModalUser.vue";
import TeamUser from "@/Pages/Team/Components/TeamUser.vue";
import TeamGestion from "@/Pages/Team/Components/TeamGestion.vue";
import TeamRotation from "@/Pages/Team/Components/TeamRotation.vue";
import TeamHoraire from "@/Pages/Team/Components/TeamHoraire.vue";
import Log from "@/Pages/Team/Components/Log.vue";
import TeamStatistique from "@/Pages/Team/Components/TeamStatistique.vue";

export default {
    name: 'Team',
    components: {
        TeamStatistique,
        Log,
        TeamHoraire,
        TeamRotation,
        TeamGestion,
        TeamUser,
        ModalUserUpdate,
        SecondaryButton,
        PrimaryButton,
        Calendar,
        AuthenticatedLayout,
        Head
    },
    props: {
        team: Object,
        schedules: Object,
        coordinateursProps: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            activeTab: 'team',
            baseTabs: [
                { id: 'team', name: 'Team' },
                { id: 'planning', name: 'Rotation & Planning' },
                { id: 'statistique', name: 'Statistique' },
                { id: 'log', name: 'Log de la team' }
            ]
        }
    },
    computed: {
        tabs() {
            if (this.team.params.module_alert) {
                return [
                    ...this.baseTabs.slice(0, 2),
                    { id: 'module', name: 'Module alerte' },
                    ...this.baseTabs.slice(2)
                ];
            }
            return this.baseTabs;
        }
    },
    methods: {
        getBackgroundStyle() {
            if (this.$store.state.isDarkMode) {
                return { backgroundImage: 'none' };
            }

            try {
                if (!this.team || !this.team.params || !this.team.params.color2) {
                    return { backgroundImage: 'none' };
                }

                const color2 = this.team.params.color2;
                const lightColor1 = this.lightenColor(color2, 90);
                const lightColor2 = this.lightenColor(color2, 95);

                return { backgroundImage: `linear-gradient(to bottom right, ${lightColor1}, ${lightColor2})` };
            } catch (error) {
                console.error('Error in getBackgroundStyle:', error);
                return { backgroundImage: 'none' };
            }
        },
        getHeaderGradient() {
            if (this.$store.state.isDarkMode) {
                return { backgroundImage: 'linear-gradient(to right, #1e293b, #0f172a)' };
            }

            try {
                if (!this.team || !this.team.params) {
                    return { backgroundImage: 'linear-gradient(to right, #4f46e5, #3b82f6)' }; // Fallback gradient
                }

                const color1 = this.team.params.color1;
                const color2 = this.team.params.color2;

                if (!color1 || !color2) {
                    return { backgroundImage: 'linear-gradient(to right, #4f46e5, #3b82f6)' }; // Fallback gradient
                }

                return { backgroundImage: `linear-gradient(to right, ${color1}, ${color2})` };
            } catch (error) {
                console.error('Error in getHeaderGradient:', error);
                return { backgroundImage: 'linear-gradient(to right, #4f46e5, #3b82f6)' };
            }
        },
        getTabIcon(tabId) {
            const icons = {
                'team': 'fas fa-users',
                'planning': 'fas fa-calendar-alt',
                'module': 'fas fa-bell',
                'statistique': 'fas fa-chart-bar',
                'log': 'fas fa-history'
            };
            return icons[tabId] || 'fas fa-circle';
        },
        hasTeamLogo() {
            try {
                return this.team && this.team.logo_url;
            } catch (error) {
                console.error('Error in hasTeamLogo:', error);
                return false;
            }
        },
        getTeamLogo() {
            try {
                return this.team && this.team.logo_url ? this.team.logo_url : '';
            } catch (error) {
                console.error('Error in getTeamLogo:', error);
                return '';
            }
        },
        getTeamColor1() {
            try {
                return this.team && this.team.params && this.team.params.color1
                    ? this.team.params.color1 : '#4f46e5';
            } catch (error) {
                console.error('Error in getTeamColor1:', error);
                return '#4f46e5';
            }
        },
        getTeamName() {
            try {
                return this.team && this.team.name ? this.team.name : 'Team';
            } catch (error) {
                console.error('Error in getTeamName:', error);
                return 'Team';
            }
        },
        getUsersCount() {
            try {
                // Utiliser la prop team au lieu de $page.props.auth.team
                return this.team && this.team.users ? this.team.users.length : 0;
            } catch (error) {
                console.error('Error in getUsersCount:', error);
                return 0;
            }
        },
        lightenColor(hex, percent) {
            // Vérifier si la couleur est définie et au format hexadécimal
            if (!hex || typeof hex !== 'string' || !hex.startsWith('#') || hex.length !== 7) {
                return '#ffffff'; // Retourner blanc par défaut
            }

            try {
                // Convertir le hex en RGB
                let r = parseInt(hex.slice(1, 3), 16);
                let g = parseInt(hex.slice(3, 5), 16);
                let b = parseInt(hex.slice(5, 7), 16);

                // Vérifier si les valeurs sont valides
                if (isNaN(r) || isNaN(g) || isNaN(b)) {
                    return '#ffffff';
                }

                // Éclaircir
                r = Math.min(255, Math.floor(r + (255 - r) * (percent / 100)));
                g = Math.min(255, Math.floor(g + (255 - g) * (percent / 100)));
                b = Math.min(255, Math.floor(b + (255 - b) * (percent / 100)));

                // Convertir en hex
                return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
            } catch (error) {
                console.error('Erreur lors de la conversion de couleur:', error);
                return '#ffffff';
            }
        }
    }
}
</script>

<style scoped>
/* Transition animations for tab content */
.fade-enter-active,
.fade-leave-active {
    transition: all 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

/* Card hover effects */
.hover\:shadow-md:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .md\:flex-row {
        flex-direction: column;
    }

    .md\:items-center {
        align-items: flex-start;
    }

    .px-8 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>
