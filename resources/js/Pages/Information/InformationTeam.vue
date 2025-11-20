<template>
    <Head title="Tableau de bord de l'équipe" />
    
    <AuthenticatedLayout>
        <div id="main-content" class="min-h-screen w-full relative overflow-y-auto transition-colors duration-300" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color2 }">
            <main>
                <!-- Header -->
                <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                    Tableau de bord de l'équipe
                                </h1>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Gérez les informations de votre équipe et partagez des ressources
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-4">
                                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 dark:from-blue-600 dark:to-indigo-700 rounded-xl p-4 shadow-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-2xl font-bold text-white">{{ users ? users.length : 0 }}</div>
                                            <div class="text-blue-100 text-sm font-medium">Membres</div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="$page.props.auth.team.params.share_link" class="bg-gradient-to-br from-amber-500 to-orange-600 dark:from-amber-600 dark:to-orange-700 rounded-xl p-4 shadow-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3">
                                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-2xl font-bold text-white">{{ linksProps ? linksProps.total : 0 }}</div>
                                            <div class="text-amber-100 text-sm font-medium">Liens partagés</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                    <div v-else class="space-y-8">
                        <!-- Bloc Partage de liens (en haut car plus utilisé) -->
                        <div v-if="$page.props.auth.team.params.share_link" class="fade-in-up">
                            <InformationLink ref="linkComponent" :linksProps="linksProps" class="content-card"></InformationLink>
                        </div>

                        <!-- Bloc Informations utilisateurs (en bas) -->
                        <div v-if="users" class="fade-in-up delay-200">
                            <InformationUser ref="userComponent" :users="users" class="content-card"></InformationUser>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InformationUser from "@/Pages/Information/Partials/InformationUser.vue";
import InformationLink from "@/Pages/Information/Partials/InformationLink.vue";

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
/* Content cards */
.content-card {
    @apply bg-white dark:bg-gray-800 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-lg;
    transition: all 0.3s ease;
}

.content-card:hover {
    @apply shadow-xl;
    transform: translateY(-2px);
}

/* Animations */
.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

.delay-200 {
    animation-delay: 0.2s;
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

/* Responsive adjustments */
@media (max-width: 768px) {
    .fade-in-up {
        animation-duration: 0.4s;
    }
}
</style>
