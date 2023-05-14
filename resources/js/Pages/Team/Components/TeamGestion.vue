<template>
    <div class="bg-gray-300 dark:bg-gray-800  2xl:col-span-2 shadow rounded-lg p-4">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $t('team_gestion.titre') }}</h3>
            </div>
        </div>
        <div class="flex flex-col">
            <div class="overflow-x-auto rounded-lg">
                <div class="align-middle inline-block min-w-full">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white dark:bg-gray-800">
                        <tr>
                            <th v-if="$page.props.config.logo" scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Logo
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $t('name') }}
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $t('department') }}
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-200">
                        <tr>
                            <td v-if="$page.props.config.logo" class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                <img v-if="team.logo_url" :src="team.logo_url" alt="Logo" class="rounded-full w-10 h-10" />
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                {{ team.name }}
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                {{ team.departement ? team.departement : '' }}
                                {{ team.departement && team.code_departement ? ' - ' : '' }}
                                {{ team.code_departement ? team.code_departement : '' }}
                            </td>
                            <td class="p-4 whitespace-nowrap items-end text-sm font-semibold text-gray-900">
                                <SecondaryButton @click="editTeam()">
                                    {{ $t('team_gestion.buttonUpdate') }}
                                </SecondaryButton>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="align-middle inline-block min-w-full mt-5">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white dark:bg-gray-800">
                        <tr>
                            <th v-if="$page.props.config.logo" scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Paramètre
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valeur
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-200">
                        <tr>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type de Jour
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <p v-for="type_day in team.params.type_day" :key="type_day">{{ type_day }}</p>
                            </th>
                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                <SecondaryButton @click="editTypeDay()">
                                    {{ $t('team_gestion.buttonUpdate') }}
                                </SecondaryButton>
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Autoriser la modification du planning
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <checkbox @update:checked="data => {updateTeamParamsCheck(data)}" :checked="team.params.update_planning"></checkbox>
                            </th>
                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <modal-team-update :show="show" :team="team" @close="close()"></modal-team-update>
        <modal-update-type-day :show="showTypeDay" :team="team" @update:type_day="data => {updateTeamParamsTypeDay(data)}" @close="close()"></modal-update-type-day>
    </div>
</template>

<script>
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ModalTeamUpdate from "@/Pages/Team/Modal/ModalTeamUpate.vue";
import Checkbox from "@/Components/Checkbox.vue";
import ModalUpdateTypeDay from "@/Pages/Team/Modal/ModalUpdateTypeDay.vue";

export default {
    name: "TeamGestion",
    components: {
        ModalUpdateTypeDay,
        Checkbox,
        ModalTeamUpdate,
        PrimaryButton,
        SecondaryButton
    },
    props: {
        team: Object
    },
    data () {
        return {
            showTypeDay: false,
            show: false
        }
    },
    methods: {
        updateTeamParamsTypeDay (type_day) {
            this.team.params.type_day = type_day
            this.updateTeamParamsCheck(this.team.params.update_planning)
            this.close()
        },
        updateTeamParamsCheck (update_planning) {
            axios.patch('/team/params/update/' + this.team.team_params_id, {
                update_planning: update_planning,
                type_day: this.team.params.type_day
            })
            .then(response => {
                this.$notify({
                    title: "Succès",
                    type: "success",
                    text: "Modification effectuée avec succès!",
                });
                this.team.params = response.data
            })
            .catch(error => {
                console.log(error)
            })
        },
        editTeam () {
            this.show = true
        },
        editTypeDay () {
            this.showTypeDay = true
        },
        close () {
            this.show = false
            this.showTypeDay = false
        }
    }
}
</script>

<style scoped>

</style>
