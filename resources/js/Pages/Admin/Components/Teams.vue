<template>
    <div id="main-content" class="h-full w-full relative overflow-y-auto" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color2 }">
        <div class="h-20 flex items-center justify-between dark:bg-gray-600 bg-gray-200" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color2 }">
            <div class="m-4 flex justify-start">
                <h3 class="text-xl font-bold" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'">Administration de la Team</h3>
                <select v-model="team" class="rounded-lg h-10 ml-5">
                    <option v-for="item in teams" :value="item">{{ item.name }}</option>
                </select>
            </div>
            <div class="flex-shrink-0 m-4">
                <PrimaryButton v-if="$page.props.auth.isAdministrateur" @click.prevent="refreshTypeDays()" class="mr-5">Mettre à jour les types de jour</PrimaryButton>
                <PrimaryButton @click.prevent="createTeam()">Créer une Team</PrimaryButton>
            </div>
        </div>
        <hr class="h-1 border-t-0">
        <div class="w-full grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-4 mb-2">
            <TeamGestion :team="team" :key="team" :coordinateursProps="coordinateursProps"></TeamGestion>
            <TeamUser :users="team.users" :key="team"></TeamUser>
        </div>
    </div>
    <ModalTeam v-if="showModalCreateTeam" :show="showModalCreateTeam" :coordinateursProps="coordinateursProps" @store-team="args => this.storeTeam(args)" @close="this.showModalCreateTeam = false"></ModalTeam>
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
.team-table {
    border: 1px solid #ddd;
}

.table-header, .team-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr;
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
