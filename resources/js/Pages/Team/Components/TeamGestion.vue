<template>
    <div class="p-6">
        <!-- Team Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-6 overflow-hidden transition-all duration-300 hover:shadow-md">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center">
                    <div class="mr-4">
                        <div v-if="team.logo_url" class="relative inline-block">
                            <img :src="team.logo_url" alt="Logo" class="rounded-full w-16 h-16 object-cover border-2 border-indigo-100 dark:border-indigo-900 shadow-sm" />
                            <button @click.prevent="deleteLogo()" id="deleteImage"
                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center transform translate-x-1/3 -translate-y-1/3 shadow-sm hover:bg-red-600 transition-colors duration-200">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <div v-else class="relative inline-block bg-gray-100 dark:bg-gray-700 rounded-full w-16 h-16 flex items-center justify-center">
                            <i class="fas fa-building text-indigo-500 dark:text-indigo-400 text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ team.name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <span v-if="team.departement">{{ team.departement }}</span>
                            <span v-if="team.departement && team.code_departement"> - </span>
                            <span v-if="team.code_departement">{{ team.code_departement }}</span>
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center" v-if="team.coordinateur">
                            <i class="fas fa-user-tie mr-1 text-indigo-500 dark:text-indigo-400"></i>
                            Coordinateur: {{ team.coordinateur.name }}
                        </p>
                    </div>
                </div>
                <div>
                    <SecondaryButton @click="editTeam()" class="bg-indigo-600 hover:bg-indigo-700 text-white border-0 shadow-sm transition-all duration-200 transform hover:scale-105 flex items-center">
                        <i class="fas fa-edit mr-2"></i> Modifier
                    </SecondaryButton>
                </div>
            </div>
        </div>

        <!-- Team Settings Parameters -->
        <div class="flex flex-col mt-6">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                <i class="fas fa-toggle-on mr-2 text-indigo-500 dark:text-indigo-400"></i> Options et modules
            </h4>
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow-sm transition-all duration-300 hover:shadow-md">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Option
                            </th>
                            <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Statut
                            </th>
                        </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900 mr-3">
                                    <i id="sendCoordinateur" class="fas fa-user-tie text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <span>Afficher le Coordinateur dans le planning</span>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('send_coordinateur', val)" :checked="team.params.send_coordinateur" class="toggle-checkbox"></checkbox>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="p-2 rounded-full bg-green-100 dark:bg-green-900 mr-3">
                                    <i id="updatePlanning" class="fas fa-calendar-check text-green-600 dark:text-green-400"></i>
                                </div>
                                <span>Autoriser la modification du planning</span>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('update_planning', val)" :checked="team.params.update_planning" class="toggle-checkbox"></checkbox>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="p-2 rounded-full bg-indigo-100 dark:bg-indigo-900 mr-3">
                                    <i id="shareLinkPlanning" class="fas fa-share-alt text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <span>Partage du planning</span>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('share_link_planning', val)" :checked="team.params.share_link_planning" class="toggle-checkbox"></checkbox>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="p-2 rounded-full bg-amber-100 dark:bg-amber-900 mr-3">
                                    <i id="paid_leave" class="fas fa-umbrella-beach text-amber-600 dark:text-amber-400"></i>
                                </div>
                                <span>Gestion des congés</span>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('paid_leave', val)" :checked="team.params.paid_leave" class="toggle-checkbox"></checkbox>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="p-2 rounded-full bg-red-100 dark:bg-red-900 mr-3">
                                    <i id="moduleAlert" class="fas fa-bell text-red-600 dark:text-red-400"></i>
                                </div>
                                <span>Module d'alerte</span>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('module_alert', val)" :checked="team.params.module_alert" class="toggle-checkbox"></checkbox>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-900 mr-3">
                                    <i id="shareLink" class="fas fa-link text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <span>Partage de liens</span>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('share_link', val)" :checked="team.params.share_link" class="toggle-checkbox"></checkbox>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="p-2 rounded-full bg-teal-100 dark:bg-teal-900 mr-3">
                                    <i id="exchangeModule" class="fas fa-exchange-alt text-teal-600 dark:text-teal-400"></i>
                                </div>
                                <span>Module d'échange de planning</span>
                            </td>
                            <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('exchange_module', val)" :checked="team.params.exchange_module" class="toggle-checkbox"></checkbox>
                                </div>
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
            // Sauvegarder l'ancienne valeur au cas où la requête échoue
            const oldVal = this.team.params[paramName];

            // Mettre à jour la valeur localement
            this.team.params[paramName] = newVal;

            // Préparer les données pour la requête
            const data = {
                send_coordinateur: this.team.params.send_coordinateur,
                update_planning: this.team.params.update_planning,
                module_alert: this.team.params.module_alert,
                paid_leave: this.team.params.paid_leave,
                share_link: this.team.params.share_link,
                share_link_planning: this.team.params.share_link_planning,
                exchange_module: this.team.params.exchange_module,
                // type_day: this.team.params.type_day
            };

            axios.patch('/team/params/update/' + this.team.team_params_id, data)
                .then(response => {

                    this.$notify({
                        title: "Succès",
                        type: "success",
                        text: "Modification effectuée avec succès!",
                    });

                    // Mettre à jour les paramètres avec les données retournées par le serveur
                    this.team.params = response.data;
                })
                .catch(error => {
                    // Restaurer l'ancienne valeur en cas d'erreur
                    this.team.params[paramName] = oldVal;

                    // Afficher une notification d'erreur
                    this.$notify({
                        title: "Erreur",
                        type: "error",
                        text: error.response?.data?.message || "Une erreur est survenue lors de la modification des paramètres.",
                    });
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
        tippy('#exchangeModule', {
            placement: 'top',
            content: 'Permets d\'activer le module d\'échange de planning. Ce module permet aux membres de l\'équipe de proposer et d\'accepter des échanges de planning entre eux.',
        });
    }
}
</script>

<style scoped>
/* Toggle checkbox styling */
.toggle-checkbox {
    /* Reset tous les styles par défaut */
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background-image: none !important;
    background-color: #d1d5db !important; /* bg-gray-300 */
    border: none !important;
    box-shadow: none !important;

    /* Nos styles personnalisés */
    position: relative;
    cursor: pointer;
    height: 1.5rem;
    width: 3rem;
    border-radius: 9999px;
    transition: background-color 0.3s ease;
}

.dark .toggle-checkbox {
    background-color: #4b5563 !important; /* dark:bg-gray-600 */
}

.toggle-checkbox:checked {
    background-color: #6366f1 !important; /* bg-indigo-500 */
    background-image: none !important;
}

.toggle-checkbox:focus {
    outline: none !important;
    box-shadow: 0 0 0 2px #fff, 0 0 0 4px #6366f1 !important;
}

.toggle-checkbox:checked:after {
    transform: translateX(1.5rem);
}

.toggle-checkbox:after {
    content: '';
    position: absolute;
    top: 0.25rem;
    left: 0.25rem;
    height: 1rem;
    width: 1rem;
    border-radius: 9999px;
    background-color: white;
    transition: transform 0.3s ease;
}

/* Card hover effects */
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .p-6 {
        padding: 1rem;
    }
}
</style>
