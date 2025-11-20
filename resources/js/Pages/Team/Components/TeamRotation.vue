<template>
    <div class="p-6 sm:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold mb-2" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                    Gestion des rotations
                </h2>
                <p class="text-sm" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                    Créez et gérez les rotations de votre équipe pour automatiser la génération des plannings
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <PrimaryButton 
                    @click="createPlanning()" 
                    :disabled="team.rotations.length === 0"
                    :class="[
                        'w-full sm:w-auto',
                        team.rotations.length === 0 ? 'opacity-50 cursor-not-allowed' : ''
                    ]"
                    :title="team.rotations.length === 0 ? 'Vous devez créer des rotations avant de pouvoir générer un planning' : ''"
                >
                    <svg class="w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $t('team_rotation.button_planning') }}
                </PrimaryButton>
                <button
                    @click="addRotation()"
                    type="button"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 text-white border-0 rounded-md font-semibold text-xs shadow-sm transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ $t('team_rotation.button_rotation') }}
                </button>
            </div>
        </div>

        <!-- Liste des rotations -->
        <div v-if="team.rotations && team.rotations.length > 0" class="space-y-4">
            <div 
                v-for="rotation in team.rotations" 
                :key="rotation.id"
                class="group relative bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-amber-500 dark:hover:border-amber-600 shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden"
            >
                <!-- Header de la rotation -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-700">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                    {{ rotation.name }}
                                </h3>
                                <Badge 
                                    :variant="rotation.total_hours === '35h00' ? 'success' : 'warning'"
                                    size="md"
                                >
                                    {{ rotation.total_hours }}
                                </Badge>
                            </div>
                            <p v-if="rotation.total_hours !== '35h00'" class="text-sm text-red-600 dark:text-red-400 flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Le total d'heures n'est pas de 35h00
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button
                                @click="editRotation(rotation)"
                                class="p-2 rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75"
                                aria-label="Modifier la rotation"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                            <button
                                @click="deleteRotation(rotation)"
                                class="p-2 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-75"
                                aria-label="Supprimer la rotation"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Détails de la rotation -->
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 gap-4">
                        <div 
                            v-for="detail in rotation.details" 
                            :key="detail.day"
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200"
                        >
                            <!-- Jour -->
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-sm" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                    {{ detail.day }}
                                </h4>
                                <div class="flex items-center space-x-1">
                                    <div v-if="detail.technicien" class="p-1 rounded bg-blue-100 dark:bg-blue-900/30" title="Technicien">
                                        <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                                        </svg>
                                    </div>
                                    <div v-if="detail.teletravail" class="p-1 rounded bg-green-100 dark:bg-green-900/30" title="Télétravail">
                                        <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenu -->
                            <div v-if="detail.is_off" class="text-center">
                                <Badge variant="default" size="sm" class="w-full justify-center">
                                    Repos
                                </Badge>
                            </div>
                            <div v-else class="space-y-2">
                                <div class="text-xs" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                                    <div class="flex items-center justify-between mb-1">
                                        <span>Début:</span>
                                        <span class="font-semibold" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">{{ detail.debut_journee }}</span>
                                    </div>
                                    <div v-if="detail.debut_pause && detail.debut_pause !== 'Pas de pause'" class="flex items-center justify-between mb-1">
                                        <span>Pause:</span>
                                        <span class="font-semibold" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                            {{ detail.debut_pause }} - {{ detail.fin_pause }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span>Fin:</span>
                                        <span class="font-semibold" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">{{ detail.fin_journee }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- État vide -->
        <EmptyState
            v-else
            icon="empty"
            title="Aucune rotation créée"
            description="Créez votre première rotation pour commencer à générer des plannings automatiquement"
        >
            <template #action>
                <button
                    @click="addRotation()"
                    type="button"
                    class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 text-white border-0 rounded-md font-semibold text-xs shadow-sm transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Créer une rotation
                </button>
            </template>
        </EmptyState>

        <!-- Modals -->
        <ModalPlanning v-if="showPlanning" :showPlanning="showPlanning" :team="team" @close="this.showPlanning = false"></ModalPlanning>
        <ModalGestionRotation v-if="showRotation" :showRotation="showRotation" :rotation="rotation" :team_id="this.team.id" @storeRotation="(data, isUpdate) => {storeRotation(data, isUpdate)}" @close="this.showRotation = false; this.rotation = null"></ModalGestionRotation>
        <ModalConfirm 
            v-if="confirm" 
            @close="confirm = false" 
            @delete-confirm="this.confirmDeleteRotation()" 
            title="Supprimer la rotation" 
            text="Êtes-vous sûr de vouloir supprimer cette rotation ? Cette action est irréversible."
            confirm="Oui, supprimer" 
            cancel="Annuler"
        ></ModalConfirm>
    </div>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Badge from "@/Components/Badge.vue";
import EmptyState from "@/Components/EmptyState.vue";
import ModalGestionRotation from "@/Pages/Team/Modal/ModalGestionRotation.vue";
import ModalPlanning from "@/Pages/Team/Modal/ModalPlanning.vue";
import ModalConfirm from "@/Components/Modal/ModalConfirm.vue";
import { computed } from 'vue';
import { useStore } from 'vuex';
import { useNotification } from '@/composables/useNotification';
import axios from "axios";

export default {
    name: "TeamRotation",
    emits: ['rotation-deleted'],
    components: {
        ModalConfirm,
        ModalPlanning,
        ModalGestionRotation,
        SecondaryButton,
        PrimaryButton,
        Badge,
        EmptyState
    },
    props: {
        team: Object
    },
    setup() {
        const store = useStore();
        const isDarkMode = computed(() => store.state.isDarkMode);
        const { showSuccess, showError } = useNotification();
        return { isDarkMode, showSuccess, showError };
    },
    data () {
        return {
            showRotation: false,
            showPlanning: false,
            confirm: false,
            item: null,
            rotation: null
        }
    },
    methods: {
        editRotation (rotation) {
            this.rotation = rotation
            this.showRotation = true
        },
        deleteRotation (rotation) {
            this.rotation = rotation
            this.confirm = true
        },
        confirmDeleteRotation () {
            axios.delete('/team/rotation/' + this.rotation.id)
                .then(response => {
                    this.showSuccess('Rotation supprimée avec succès');
                    this.team.rotations = response.data
                })
                .catch(error => {
                    if (error.response?.data?.message) {
                        this.showError(error.response.data.message);
                    } else {
                        this.showError('Une erreur est survenue lors de la suppression de la rotation');
                    }
                })
                .finally(() => {
                    this.confirm = false
                })
        },
        storeRotation (data, isUpdate = false) {
            this.team.rotations = data
            this.showRotation = false
            if (isUpdate) {
                this.showSuccess('Rotation modifiée avec succès');
            } else {
                this.showSuccess('Rotation ajoutée avec succès');
            }
        },
        createPlanning () {
          this.showPlanning = true
        },
        addRotation () {
            this.rotation = null
            this.showRotation = true
        }
    },
    mounted () {
        this.item = JSON.parse(JSON.stringify(this.team))
    }
}
</script>

<style scoped>

</style>
