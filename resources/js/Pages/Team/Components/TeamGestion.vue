<template>
    <div class="bg-gray-300 dark:bg-gray-800 2xl:col-span-2 shadow p-4" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $t('team_gestion.titre') }}</h3>
            </div>
        </div>
        <div class="flex flex-col">
            <div class="overflow-x-auto rounded-lg">
                <div class="align-middle inline-block min-w-full overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white dark:bg-gray-800">
                        <tr>
                            <th v-if="$page.props.config.logo" scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Logo
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                {{ $t('name') }}
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                {{ $t('department') }}
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Coordinateur
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider"></th>
                        </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-200">
                        <tr>
                            <td v-if="$page.props.config.logo" class="p-4 whitespace-nowrap text-sm font-normal text-gray-900 flex justify-start">
                                <div v-if="team.logo_url" class="relative inline-block">
                                    <img :src="team.logo_url" alt="Logo" class="rounded-full w-10 h-10" />
                                    <button @click.prevent="deleteLogo()" id="deleteImage" class="absolute top-1 right-0 bg-[#ff4757] text-white rounded-full w-4 h-4 p-2.5 flex items-center justify-center transform translate-x-1/2 -translate-y-1/2">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </button>
                                </div>
                                <div v-else class="relative inline-block">
                                        <img src="/images/logo.png" alt="Logo" :class="[$page.props.auth.user ? 'w-10 h-10' : 'w-34']" class="rounded-full" />
                                </div>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                {{ team.name }}
                            </td>

                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                {{ team.departement ? team.departement : '' }}
                                {{ team.departement && team.code_departement ? ' - ' : '' }}
                                {{ team.code_departement ? team.code_departement : '' }}
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                {{ team.coordinateur ? team.coordinateur.name : '' }}
                            </td>
                            <td class="p-4 whitespace-nowrap items-end text-sm font-semibold">
                                <SecondaryButton @click="editTeam()">
                                    Modifier
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
                            <th v-if="$page.props.config.logo" scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-white uppercase tracking-wider">
                                Paramètre
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase dark:text-white tracking-wider">
                                Valeur
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-200">
                        <tr>
                            <th scope="col" class="p-4 flex justify-start text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <svg id="sendCoordinateur" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-2">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                                Afficher le Coordinateur dans le planning
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <checkbox @update:checked="val => updateTeamParamsCheck('send_coordinateur', val)" :checked="team.params.send_coordinateur"></checkbox>
                            </th>
                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="p-4 flex justify-start text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <svg id="updatePlanning" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-2">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                                Autoriser la modification du planning
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <checkbox @update:checked="val => updateTeamParamsCheck('update_planning', val)" :checked="team.params.update_planning"></checkbox>
                            </th>
                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="p-4 flex justify-start text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <svg id="shareLinkPlanning" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-2">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                                Activer le partage de planning
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <checkbox @update:checked="val => updateTeamParamsCheck('share_link_planning', val)" :checked="team.params.share_link_planning"></checkbox>
                            </th>
                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="p-4 flex justify-start text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <svg id="paid_leave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-2">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                                Activer le module de gestion des congés
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <checkbox @update:checked="val => updateTeamParamsCheck('paid_leave', val)" :checked="team.params.paid_leave"></checkbox>
                            </th>
                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="p-4 flex justify-start text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <svg id="moduleAlert" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-2">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                                Activer le module alerte
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <checkbox @update:checked="val => updateTeamParamsCheck('module_alert', val)" :checked="team.params.module_alert"></checkbox>
                            </th>
                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="p-4 flex justify-start text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <svg id="shareLink" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-2">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                                Activer le module de partage des liens
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <checkbox @update:checked="val => updateTeamParamsCheck('share_link', val)" :checked="team.params.share_link"></checkbox>
                            </th>
                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <modal-team-update :show="show" :team="team" :coordinateursProps="coordinateursProps" @close="close()"></modal-team-update>
<!--        <modal-update-type-day :show="showTypeDay" :team="team" @update:type_day="data => {updateTeamParamsTypeDay(data)}" @close="close()"></modal-update-type-day>-->
    </div>
</template>

<script>
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ModalTeamUpdate from "@/Pages/Team/Modal/ModalTeamUpate.vue";
import Checkbox from "@/Components/Checkbox.vue";
// import ModalUpdateTypeDay from "@/Pages/Team/Modal/ModalUpdateTypeDay.vue";
import tippy from "tippy.js";
import {Inertia} from "@inertiajs/inertia";
import { router } from '@inertiajs/vue3'
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

export default {
    name: "TeamGestion",
    components: {
        ApplicationLogo,
        Checkbox,
        ModalTeamUpdate,
        PrimaryButton,
        SecondaryButton
    },
    props: {
        team: Object,
        coordinateursProps: {
            type: Array,
            required: true
        }
    },
    data () {
        return {
            showTypeDay: false,
            show: false
        }
    },
    methods: {
        // updateTeamParamsTypeDay (type_day) {
        //     this.team.params.type_day = type_day
        //     this.updateTeamParamsCheck(this.team.params.update_planning)
        //     this.close()
        // },
        deleteLogo () {
            axios.delete('/team/logo/delete/' + this.team.id)
                .then(() => {
                    this.$notify({
                        title: "Succès",
                        type: "success",
                        text: "Logo supprimé avec succès!",
                    });
                    this.team.logo_url = null
                    Inertia.reload({ only: ['auth.team'] });
                })
                .catch(error => {
                    console.log(error)
                })
        },
        updateTeamParamsCheck (paramName, newVal) {
            this.team.params[paramName] = newVal;
            axios.patch('/team/params/update/' + this.team.team_params_id, {
                send_coordinateur: this.team.params.send_coordinateur,
                update_planning: this.team.params.update_planning,
                module_alert: this.team.params.module_alert,
                paid_leave: this.team.params.paid_leave,
                share_link: this.team.params.share_link,
                share_link_planning: this.team.params.share_link_planning,
                // type_day: this.team.params.type_day
            })
                .then(response => {
                    this.$notify({
                        title: "Succès",
                        type: "success",
                        text: "Modification effectuée avec succès!",
                    });
                    this.team.params = response.data
                    router.reload({ only: ['auth'] })
                })
                .catch(error => {
                    console.log(error)
                })
        },

        editTeam () {
            this.show = true
        },
        // editTypeDay () {
        //     this.showTypeDay = true
        // },
        close () {
            this.show = false
            this.showTypeDay = false
        }
    },
    mounted() {
        tippy('#deleteImage', {
            placement: 'top',
            content: 'Supprimer le logo',
        });
        tippy('#sendCoordinateur', {
            placement: 'top',
            content: 'Permets d\'afficher le Coordinateur de la Team dans le planning.',
        });
        tippy('#updatePlanning', {
            placement: 'top',
            content: 'Permets d\'autoriser aux membres de l\'équipe de modifier leurs plannings respectifs.',
        });
        tippy('#moduleAlert', {
            placement: 'top',
            content: 'Permets d\'activer le module d\'alerte afin d\'être informé si le nombre de personnes présentes est inférieur au nombre de personnes attendues par créneau. (Vérification effectuée sur deux semaines)',
        });
        tippy('#shareLink', {
            placement: 'top',
            content: 'Permets d\'activer le module de partage des liens dans l\'équipe. En cas de désactivation, les liens déjà partagés ne seront pas supprimés.',
        });
        tippy('#shareLinkPlanning', {
            placement: 'top',
            content: 'Permets le partage du planning via un lien unique. Ce lien vous permet de faire part de votre emploi du temps à des personnes de l\'extérieur.',
        });
        tippy('#paid_leave', {
            placement: 'top',
            content: 'Permets d\'activer le module de gestion des congés. Ce module permet aux membres de l\'équipe de faire une demande de congés et de pouvoir les gérer.',
        });
    }
}
</script>

<style scoped>

</style>
