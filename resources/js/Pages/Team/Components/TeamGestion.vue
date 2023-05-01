<template>
    <div class="bg-white dark:bg-gray-800  2xl:col-span-2 shadow rounded-lg p-4">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $t('team_gestion.titre') }}</h3>
            </div>
            <div class="flex-shrink-0">
                <SecondaryButton>Ajouter un membre</SecondaryButton>
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
                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                <PrimaryButton @click="editTeam()">
                                    {{ $t('team_gestion.buttonUpdate') }}
                                </PrimaryButton>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <ModalTeamUpdate :show="show" :team="team" @close="close()"></ModalTeamUpdate>
    </div>
</template>

<script>
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ModalTeamUpdate from "@/Pages/Team/Modal/ModalTeamUpate.vue";

export default {
    name: "TeamGestion",
    components: {
        ModalTeamUpdate,
        PrimaryButton,
        SecondaryButton
    },
    props: {
        team: Object
    },
    data () {
        return {
            show: false
        }
    },
    methods: {
        editTeam () {
            this.show = true
        },
        close () {
            this.show = false
        }
    }
}
</script>

<style scoped>

</style>
