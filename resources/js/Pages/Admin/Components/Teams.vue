<template>
    <div id="main-content" class="h-full w-full relative overflow-y-auto p-1 rounded-sm bg-gray-200" :style="{ backgroundColor: this.$store.state.isDarkMode ? '' : $page.props.auth.team.params.color2 }">
        <div class="h-20 flex items-center justify-between dark:bg-gray-800 bg-gray-200" :style="{ backgroundColor: this.$store.state.isDarkMode ? '' : $page.props.auth.team.params.color2 }">
            <div class="m-4">
                <h3 class="text-xl font-bold" :class="this.$store.state.isDarkMode ? 'text-white' : 'text-black'">Liste des Teams</h3>
            </div>
            <div class="flex-shrink-0 m-4">
                <PrimaryButton @click.prevent="createTeam()">Créer une Team</PrimaryButton>
            </div>
        </div>
        <div class="flex flex-col">
            <div class="overflow-x-auto max-h-[40rem]">
                <div class="align-middle inline-block min-w-full">
                    <div class="overflow-hidden">
                        <div class="team-list">

                            <div class="team-table">
                                <!-- En-tête du tableau -->
                                <div class="table-header flex justify-between p-4" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
                                    <div class="col">Team</div>
                                    <div class="col">Nombre d'utilisateur</div>
                                    <div class="col">Partage de liens</div>
                                    <div class="col">Module d'alerte</div>
                                    <div class="col">Partage du planning</div>
                                    <div class="col">Autorise la modification du planning</div>
                                </div>

                                <div v-for="team in teams" :key="team.id">
                                    <!-- Ligne du tableau pour chaque équipe -->
                                    <div class="team-row flex justify-between p-4 cursor-pointer" @click="toggleTeamDetails(team.id)">
                                        <div class="col flex items-center">
                                            <svg v-if="expandedTeamId !== team.id" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                            </svg>
                                            {{ team.name }}
                                        </div>
                                        <div class="col">{{ team.users_count }}</div>
                                        <div class="col">
                                            <div v-if="team.params.share_link">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1dd1a1" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div>
                                            <div v-else>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff6b6b" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div v-if="team.params.module_alert">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1dd1a1" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div>
                                            <div v-else>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff6b6b" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div v-if="team.params.share_link_planning">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1dd1a1" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div>
                                            <div v-else>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff6b6b" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </div>
                                        </div>
                                            <div class="col">
                                            <div v-if="team.params.update_planning">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1dd1a1" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </div>
                                            <div v-else>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff6b6b" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Détails de l'équipe (utilisateurs) sous la forme d'un accordeon -->
                                    <div v-if="expandedTeamId === team.id" class="team-details transition-all ease-in-out duration-300">
                                        <ul>
                                            <li v-for="user in team.users" :key="user.id" class="p-2 bg-gray-100 text-[#222f3e] border-b-2 border-black border-0 rounded-lg mt-1 hover:bg-gray-50">{{ user.name }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>


                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <ModalTeam v-if="showModalCreateTeam" :show="showModalCreateTeam" @store-team="args => this.storeTeam(args)" @close="this.showModalCreateTeam = false"></ModalTeam>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ModalTeam from "@/Pages/Admin/Modal/ModalTeam.vue";

export default {
    components: {
        ModalTeam,
        PrimaryButton
    },
    props: {
        teamsProps: {
            type: Array,
            required: true
        }
    },
    data () {
        return {
            expandedTeamId: null,
            teams: this.teamsProps,
            showModalCreateTeam: false
        }
    },
    methods: {
        toggleTeamDetails(teamId) {
            if (this.expandedTeamId === teamId) {
                this.expandedTeamId = null;  // Si l'équipe est déjà déployée, la replier
            } else {
                this.expandedTeamId = teamId;  // Sinon, déployer cette équipe
            }
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
.team-table {
    border: 1px solid #ddd;
}

.table-header, .team-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr; /* Répétez "1fr" pour autant de colonnes que vous avez dans votre tableau */
    border-bottom: 1px solid #ddd;
}

.col {
    padding: 0.5rem;
}

.team-details {
    grid-column: span 6; /* Pour que les détails s'étendent sur toutes les colonnes */
    padding: 0.5rem;
    border-bottom: 1px solid #ddd;
}


.team-details {
    /* autres styles */
    overflow: hidden;
    transition: max-height 0.3s ease-in-out;
}

[expandedTeamId] .team-details {
    max-height: 30rem; /* ou un autre nombre si vous le souhaitez */
}

</style>
