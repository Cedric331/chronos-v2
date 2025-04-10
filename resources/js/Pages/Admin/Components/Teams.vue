<template>
    <div class="h-full w-full relative overflow-y-auto">
        <!-- Header with team selector and actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-6 transition-all duration-300">
            <div class="p-4 md:p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex flex-col md:flex-row items-start md:items-center gap-4 w-full md:w-auto">
                    <div class="flex items-center">
                        <i class="fas fa-users-cog text-indigo-500 dark:text-indigo-400 text-xl mr-3"></i>
                        <h3 class="text-xl font-bold" :class="$store.state.isDarkMode ? 'text-white' : 'text-gray-800'">Gestion des équipes</h3>
                    </div>
                    <div class="relative w-full md:w-auto">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-building text-gray-400"></i>
                        </div>
                        <select
                            v-model="team"
                            class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg w-full md:w-auto focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm transition-all duration-200"
                        >
                            <option v-for="item in teams" :value="item">{{ item.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <PrimaryButton
                        v-if="$page.props.auth.isAdministrateur"
                        @click.prevent="refreshTypeDays()"
                        class="bg-amber-600 hover:bg-amber-700 text-white border-0 shadow-sm transition-all duration-200 transform hover:scale-105 flex items-center"
                    >
                        <i class="fas fa-sync-alt mr-2"></i> Mettre à jour les types de jour
                    </PrimaryButton>
                    <PrimaryButton
                        @click.prevent="createTeam()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-sm transition-all duration-200 transform hover:scale-105 flex items-center"
                    >
                        <i class="fas fa-plus-circle mr-2"></i> Créer une équipe
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <!-- Team Management Content -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
            <!-- Team Settings Panel -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
                <div class="p-4 bg-indigo-50 dark:bg-indigo-900 border-b border-indigo-100 dark:border-indigo-800">
                    <h2 class="text-lg font-semibold text-indigo-700 dark:text-indigo-300 flex items-center">
                        <i class="fas fa-cogs mr-2"></i> Configuration de l'équipe
                    </h2>
                </div>
                <div class="p-0">
                    <TeamGestion :team="team" :key="team.id" :coordinateursProps="coordinateursProps"></TeamGestion>
                </div>
            </div>

            <!-- Team Users Panel -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
                <div class="p-4 bg-indigo-50 dark:bg-indigo-900 border-b border-indigo-100 dark:border-indigo-800">
                    <h2 class="text-lg font-semibold text-indigo-700 dark:text-indigo-300 flex items-center">
                        <i class="fas fa-user-friends mr-2"></i> Membres de l'équipe
                    </h2>
                </div>
                <div class="p-0">
                    <TeamUser :usersProps="team.users" :key="team.id + '-users'"></TeamUser>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for creating a new team -->
    <ModalTeam
        v-if="showModalCreateTeam"
        :show="showModalCreateTeam"
        :coordinateursProps="coordinateursProps"
        @store-team="args => this.storeTeam(args)"
        @close="this.showModalCreateTeam = false"
    ></ModalTeam>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ModalTeam from "@/Pages/Admin/Modal/ModalTeam.vue";
import TeamUser from "@/Pages/Team/Components/TeamUser.vue";
import TeamGestion from "@/Pages/Team/Components/TeamGestion.vue";

export default {
    components: {
        TeamGestion, TeamUser,
        ModalTeam,
        PrimaryButton
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
    },
    data () {
        return {
            team: this.teamsProps[0],
            teams: this.teamsProps,
            showModalCreateTeam: false
        }
    },
    methods: {
        refreshTypeDays () {
            axios.put('/chronos-admin/team/typedays')
                .then(() => {
                this.$notify({
                    type: 'success',
                    title: 'Success',
                    text: 'Types de jours mis à jour avec succès.'
                })
            }).catch(error => {
                this.$notify({
                    type: 'error',
                    title: 'Error',
                    text: 'Une erreur est survenue lors de la mise à jour des types de jours.'
                })
            })
        },
        createTeam() {
            this.showModalCreateTeam = true
        },
        storeTeam(args) {
            this.teams = args
            this.showModalCreateTeam = false
            this.$notify({
                type: 'success',
                title: 'Success',
                text: 'Team créée avec succès.'
            })
        },
    }
}
</script>

<style scoped>
/* Table styles */
.team-table {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    overflow: hidden;
}

.table-header, .team-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr;
    border-bottom: 1px solid #e5e7eb;
}

.table-header {
    background-color: #f9fafb;
    font-weight: 600;
}

.col {
    padding: 0.75rem 1rem;
}

.team-details {
    grid-column: span 6; /* Pour que les détails s'étendent sur toutes les colonnes */
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    background-color: #f9fafb;
}

/* Animation and transitions */
.team-details {
    overflow: hidden;
    transition: all 0.3s ease-in-out;
}

[expandedTeamId] .team-details {
    max-height: 30rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-header, .team-row {
        grid-template-columns: 1fr 1fr;
    }

    .team-details {
        grid-column: span 2;
    }
}

/* Button hover effects */
.hover-scale {
    transition: transform 0.2s ease;
}

.hover-scale:hover {
    transform: scale(1.05);
}

/* Card styles */
.card {
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}
</style>
