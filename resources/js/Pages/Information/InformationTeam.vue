<template>
    <AuthenticatedLayout>
        <div id="main-content" class="min-h-screen w-full relative overflow-y-auto transition-colors duration-300" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color2 }">
            <main>
                <!-- Hero Banner -->
                <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 dark:from-blue-800 dark:via-indigo-800 dark:to-indigo-900 shadow-xl">
                    <div class="absolute inset-0 bg-pattern opacity-10"></div>
                    <div class="max-w-7xl mx-auto py-12 px-6 sm:px-8 relative z-10">
                        <div class="flex flex-col md:flex-row justify-between items-center">
                            <div class="mb-8 md:mb-0 text-center md:text-left">
                                <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3 tracking-tight">Tableau de bord de l'équipe</h1>
                                <p class="text-blue-100 text-lg max-w-2xl leading-relaxed">Gérez les informations de votre équipe et partagez des ressources</p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-3xl font-bold text-white">{{ users ? users.length : 0 }}</div>
                                        <div class="text-blue-100 text-sm font-medium">Membres</div>
                                    </div>
                                </div>
                                <div v-if="$page.props.auth.team.params.share_link" class="stat-card">
                                    <div class="stat-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-3xl font-bold text-white">{{ linksProps ? linksProps.total : 0 }}</div>
                                        <div class="text-blue-100 text-sm font-medium">Liens partagés</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto pt-8 px-4 sm:px-6 lg:px-8">
                    <!-- Loading indicator -->
                    <div v-if="loading" class="flex justify-center items-center py-20">
                        <div class="relative">
                            <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-200 border-t-blue-600 dark:border-gray-700 dark:border-t-blue-500"></div>
                            <div class="absolute top-0 left-0 right-0 bottom-0 flex items-center justify-center">
                                <div class="h-8 w-8 bg-white dark:bg-gray-900 rounded-full"></div>
                            </div>
                        </div>
                        <span class="ml-4 text-lg text-gray-600 dark:text-gray-300 font-medium">Chargement des données...</span>
                    </div>

                    <!-- Content -->
                    <div v-else>
                        <!-- Quick Actions -->
                        <div class="mb-10 fade-in">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">Actions rapides</h2>
                            <div class="flex flex-wrap gap-4 justify-start">
                                <button v-if="$page.props.auth.team.params.share_link" @click="openLinkModal()" class="action-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    Partager un lien
                                </button>
                            </div>
                        </div>

                        <!-- Main Content Grid -->
                        <div class="w-full grid grid-cols-1 xl:grid-cols-2 gap-8 mb-12" :class="[$page.props.auth.team.params.share_link ? '2xl:grid-cols-2' : '2xl:grid-cols-1']">
                            <InformationUser ref="userComponent" v-if="users" :users="users" class="content-card fade-in-up"></InformationUser>
                            <InformationLink ref="linkComponent" v-if="$page.props.auth.team.params.share_link" :linksProps="linksProps" class="content-card fade-in-up delay-100"></InformationLink>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InformationUser from "@/Pages/Information/Partials/InformationUser.vue";
import InformationLink from "@/Pages/Information/Partials/InformationLink.vue";
import {Head} from "@inertiajs/vue3";

export default {
    name: "InformationTeam",
    components: {
        Head,
        InformationLink,
        InformationUser,
        AuthenticatedLayout
    },
    props: {
        usersProps: Object,
        linksProps: Object
    },
    data () {
      return {
        users: [],
        loading: true,
        showUserModal: false
      }
    },
    methods: {
        openLinkModal() {
            if (this.$refs.linkComponent) {
                this.$refs.linkComponent.show = true;
            }
        },
        handleStoreUsers(newUser) {
            if (this.users) {
                this.users.push(newUser);
            }
            this.closeUserModal();
        }
    },
    mounted() {
        // Simuler un court délai de chargement pour une meilleure expérience utilisateur
        setTimeout(() => {
            this.users = this.usersProps;
            this.loading = false;
        }, 300);
    }
}
</script>

<style scoped>
/* Background pattern */
.bg-pattern {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.15'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

/* Stat cards */
.stat-card {
    @apply flex items-center p-5 bg-white bg-opacity-20 backdrop-filter backdrop-blur-lg rounded-xl shadow-lg;
    transition: all 0.3s ease;
}

.stat-card:hover {
    @apply bg-opacity-25 transform -translate-y-1;
}

.stat-icon {
    @apply mr-4 p-3 rounded-full bg-white bg-opacity-25 text-white;
}

.stat-content {
    @apply flex flex-col;
}

/* Action buttons */
.action-button {
    @apply flex items-center px-5 py-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 font-medium;
}

.action-button:hover {
    @apply transform -translate-y-1 shadow-md bg-gray-50 dark:bg-gray-700;
}

/* Content cards */
.content-card {
    @apply bg-white dark:bg-gray-800 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-lg;
    transition: all 0.3s ease;
}

.content-card:hover {
    @apply shadow-xl transform -translate-y-1;
}

/* Animations */
.fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

.delay-100 {
    animation-delay: 0.1s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Override component styles */
:deep(.dark\:bg-gray-800),
:deep(.bg-gray-300) {
    @apply rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700;
    transition: all 0.3s ease;
}

:deep(table) {
    @apply rounded-lg overflow-hidden;
}

:deep(th) {
    @apply bg-gray-50 dark:bg-gray-700 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider py-3 px-4;
}

:deep(td) {
    @apply py-3 px-4;
}

:deep(tbody tr) {
    @apply hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150;
}

:deep(.dark\:bg-gray-800 h3),
:deep(.bg-gray-300 h3) {
    @apply text-xl font-bold mb-4 flex items-center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .fade-in-up {
        animation-duration: 0.4s;
    }

    .stat-card {
        @apply w-full;
    }
}
</style>
