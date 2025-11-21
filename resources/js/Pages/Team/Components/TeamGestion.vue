<template>
    <div class="p-6">
        <!-- Modern Team Info Card -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 rounded-xl shadow-lg mb-6 overflow-hidden transition-all duration-300 hover:shadow-xl border border-gray-200/50 dark:border-gray-700/50">
            <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                <div class="flex items-center">
                    <div class="mr-5">
                        <div v-if="team.logo_url" class="relative inline-block">
                            <img :src="team.logo_url" alt="Logo" class="rounded-2xl w-20 h-20 object-cover border-4 border-indigo-200 dark:border-indigo-800 shadow-lg ring-2 ring-indigo-100 dark:ring-indigo-900" />
                            <button @click.prevent="deleteLogo()" id="deleteImage"
                                class="absolute -top-1 -right-1 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-lg hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-110 ring-2 ring-white dark:ring-gray-800">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <div v-else class="relative inline-block bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900 dark:to-purple-900 rounded-2xl w-20 h-20 flex items-center justify-center shadow-lg ring-2 ring-indigo-100 dark:ring-indigo-900">
                            <i class="fas fa-building text-indigo-600 dark:text-indigo-300 text-3xl"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ team.name }}</h3>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-indigo-500 dark:text-indigo-400"></i>
                            <span v-if="team.departement">{{ team.departement }}</span>
                            <span v-if="team.departement && team.code_departement"> - </span>
                            <span v-if="team.code_departement">{{ team.code_departement }}</span>
                        </p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 flex items-center" v-if="team.coordinateur">
                            <i class="fas fa-user-tie mr-2 text-indigo-500 dark:text-indigo-400"></i>
                            Coordinateur: <span class="font-semibold ml-1">{{ team.coordinateur.name }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <PrimaryButton 
                        @click="exportTeamPlannings()" 
                        :disabled="isExporting"
                        class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white border-0 shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center px-5 py-3 rounded-xl font-semibold disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                        <i v-if="!isExporting" class="fas fa-file-excel mr-2"></i>
                        <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                        {{ isExporting ? 'Export en cours...' : 'Exporter les plannings' }}
                    </PrimaryButton>
                    <SecondaryButton @click="editTeam()" class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white border-0 shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center px-5 py-3 rounded-xl font-semibold">
                        <i class="fas fa-edit mr-2"></i> Modifier
                    </SecondaryButton>
                </div>
            </div>
        </div>

        <!-- Modern Team Settings Parameters -->
        <div class="flex flex-col mt-6">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center">
                <div class="p-2 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 mr-3 shadow-lg">
                    <i class="fas fa-toggle-on text-white"></i>
                </div>
                Options et modules
            </h4>
            <div class="overflow-hidden rounded-xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg transition-all duration-300 hover:shadow-xl border border-gray-200/50 dark:border-gray-700/50">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/30 dark:to-purple-900/30">
                        <tr>
                            <th scope="col" class="p-5 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Option
                            </th>
                            <th scope="col" class="p-5 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Statut
                            </th>
                        </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="p-5 whitespace-nowrap text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                                <div class="p-3 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg mr-4">
                                    <i id="sendCoordinateur" class="fas fa-user-tie text-white"></i>
                                </div>
                                <span>Afficher le Coordinateur dans le planning</span>
                            </td>
                            <td class="p-5 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-300 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('send_coordinateur', val)" :checked="team.params.send_coordinateur" class="toggle-checkbox"></checkbox>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="p-5 whitespace-nowrap text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                                <div class="p-3 rounded-xl bg-gradient-to-br from-green-500 to-green-600 shadow-lg mr-4">
                                    <i id="updatePlanning" class="fas fa-calendar-check text-white"></i>
                                </div>
                                <span>Autoriser la modification du planning</span>
                            </td>
                            <td class="p-5 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-300 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('update_planning', val)" :checked="team.params.update_planning" class="toggle-checkbox"></checkbox>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="p-5 whitespace-nowrap text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                                <div class="p-3 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-lg mr-4">
                                    <i id="shareLinkPlanning" class="fas fa-share-alt text-white"></i>
                                </div>
                                <span>Partage du planning</span>
                            </td>
                            <td class="p-5 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-300 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('share_link_planning', val)" :checked="team.params.share_link_planning" class="toggle-checkbox"></checkbox>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="p-5 whitespace-nowrap text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                                <div class="p-3 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 shadow-lg mr-4">
                                    <i id="paid_leave" class="fas fa-umbrella-beach text-white"></i>
                                </div>
                                <span>Gestion des congés</span>
                            </td>
                            <td class="p-5 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-300 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('paid_leave', val)" :checked="team.params.paid_leave" class="toggle-checkbox"></checkbox>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="p-5 whitespace-nowrap text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                                <div class="p-3 rounded-xl bg-gradient-to-br from-red-500 to-red-600 shadow-lg mr-4">
                                    <i id="moduleAlert" class="fas fa-bell text-white"></i>
                                </div>
                                <span>Module d'alerte</span>
                            </td>
                            <td class="p-5 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-300 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('module_alert', val)" :checked="team.params.module_alert" class="toggle-checkbox"></checkbox>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="p-5 whitespace-nowrap text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                                <div class="p-3 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 shadow-lg mr-4">
                                    <i id="shareLink" class="fas fa-link text-white"></i>
                                </div>
                                <span>Partage de liens</span>
                            </td>
                            <td class="p-5 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-300 ease-in">
                                    <checkbox @update:checked="val => updateTeamParamsCheck('share_link', val)" :checked="team.params.share_link" class="toggle-checkbox"></checkbox>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="p-5 whitespace-nowrap text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                                <div class="p-3 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 shadow-lg mr-4">
                                    <i id="exchangeModule" class="fas fa-exchange-alt text-white"></i>
                                </div>
                                <span>Module d'échange de planning</span>
                            </td>
                            <td class="p-5 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-300 ease-in">
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
            show: false,
            isExporting: false
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
        },
        exportTeamPlannings() {
            if (this.isExporting) return;
            
            this.isExporting = true;
            
            // Utiliser fetch pour mieux gérer le téléchargement
            fetch(`/planning/team/export?team_id=${this.team.id}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de l\'export');
                }
                
                // Extraire le nom du fichier depuis les headers
                const contentDisposition = response.headers.get('content-disposition');
                let filename = 'plannings_equipe_' + this.team.name.replace(/\s+/g, '_') + '_' + new Date().toISOString().split('T')[0] + '.xlsx';
                if (contentDisposition) {
                    const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (filenameMatch && filenameMatch[1]) {
                        filename = filenameMatch[1].replace(/['"]/g, '');
                    }
                }
                
                return response.blob().then(blob => ({ blob, filename }));
            })
            .then(({ blob, filename }) => {
                // Créer un lien de téléchargement
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
                
                this.isExporting = false;
                this.$notify({
                    title: "Export réussi",
                    type: "success",
                    text: "Le fichier Excel a été téléchargé avec succès",
                });
            })
            .catch(error => {
                console.error('Erreur lors de l\'export:', error);
                this.isExporting = false;
                this.$notify({
                    title: "Erreur",
                    type: "error",
                    text: "Une erreur est survenue lors de l'export. Veuillez réessayer.",
                });
            });
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
/* Modern Toggle checkbox styling */
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
    height: 1.75rem;
    width: 3.5rem;
    border-radius: 9999px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.dark .toggle-checkbox {
    background-color: #4b5563 !important; /* dark:bg-gray-600 */
}

.toggle-checkbox:checked {
    background: linear-gradient(to right, #4f46e5, #7c3aed) !important; /* gradient indigo-purple plus foncé */
    background-image: none !important;
    box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.6) !important;
}

.dark .toggle-checkbox:checked {
    background: linear-gradient(to right, #6366f1, #a855f7) !important; /* gradient plus clair en dark mode */
    box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.7) !important;
}

.toggle-checkbox:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
}

.toggle-checkbox:checked:hover {
    box-shadow: 0 6px 20px 0 rgba(99, 102, 241, 0.5) !important;
}

.toggle-checkbox:focus {
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
}

.toggle-checkbox:checked:focus {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3), 0 4px 14px 0 rgba(99, 102, 241, 0.4) !important;
}

.toggle-checkbox:checked:after {
    transform: translateX(1.75rem);
}

.toggle-checkbox:after {
    content: '';
    position: absolute;
    top: 0.25rem;
    left: 0.25rem;
    height: 1.25rem;
    width: 1.25rem;
    border-radius: 9999px;
    background: linear-gradient(to bottom, #ffffff, #f9fafb);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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
