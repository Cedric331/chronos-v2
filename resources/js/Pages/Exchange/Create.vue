<template>
    <AuthenticatedLayout>
        <Head title="Proposer un échange" />
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-6">
                            <h2 class="text-2xl font-semibold">Proposer un échange de planning</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">
                                Sélectionnez un collègue et les dates que vous souhaitez échanger.
                            </p>
                        </div>

                        <form @submit.prevent="submitForm">
                            <!-- Sélection du collègue -->
                            <div class="mb-6">
                                <label for="requested_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Collègue
                                </label>
                                <select
                                    id="requested_id"
                                    v-model="form.requested_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                    :class="{ 'border-red-500': errors.requested_id }"
                                    @change="loadUserPlannings"
                                >
                                    <option value="" disabled>Sélectionnez un collègue</option>
                                    <option v-for="user in teamUsers" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <p v-if="errors.requested_id" class="mt-2 text-sm text-red-600 dark:text-red-500">
                                    {{ errors.requested_id }}
                                </p>
                            </div>

                            <!-- Sélection des dates à échanger -->
                            <div v-if="form.requested_id" class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Dates à échanger
                                </label>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    Sélectionnez une ou plusieurs dates pour lesquelles vous souhaitez échanger votre planning avec celui de votre collègue.
                                </p>

                                <!-- Indicateur de chargement -->
                                <div v-if="isLoading" class="flex justify-center items-center py-8">
                                    <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="ml-3 text-sm text-gray-600 dark:text-gray-400">Chargement des dates disponibles...</span>
                                </div>

                                <!-- Liste des dates disponibles -->
                                <div v-else-if="availableDates.length > 0" class="space-y-4">
                                    <div v-for="(date, index) in availableDates" :key="index" class="flex items-start">
                                        <div class="flex h-5 items-center">
                                            <input
                                                :id="`date-${index}`"
                                                type="checkbox"
                                                v-model="selectedDates[index]"
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800"
                                            />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label :for="`date-${index}`" class="font-medium text-gray-700 dark:text-gray-300">
                                                {{ formatDate(date.calendar.date) }} ({{ getDayOfWeek(date.calendar.date) }})
                                            </label>
                                            <div class="mt-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-md">
                                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Votre planning:</p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        Type: <span class="font-medium">{{ date.yourPlanning.type_day }}</span>
                                                    </p>
                                                    <p v-if="date.yourPlanning.debut_journee" class="text-sm text-gray-600 dark:text-gray-400">
                                                        Horaires: <span class="font-medium">{{ date.yourPlanning.debut_journee }} - {{ date.yourPlanning.fin_journee }}</span>
                                                    </p>
                                                    <p v-if="date.yourPlanning.telework" class="text-sm text-gray-600 dark:text-gray-400">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                                            Télétravail
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-md">
                                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Planning de {{ getColleagueName() }}:</p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        Type: <span class="font-medium">{{ date.colleaguePlanning.type_day }}</span>
                                                    </p>
                                                    <p v-if="date.colleaguePlanning.debut_journee" class="text-sm text-gray-600 dark:text-gray-400">
                                                        Horaires: <span class="font-medium">{{ date.colleaguePlanning.debut_journee }} - {{ date.colleaguePlanning.fin_journee }}</span>
                                                    </p>
                                                    <p v-if="date.colleaguePlanning.telework" class="text-sm text-gray-600 dark:text-gray-400">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                                            Télétravail
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <p v-if="errors.dates" class="mt-2 text-sm text-red-600 dark:text-red-500">
                                        {{ errors.dates }}
                                    </p>
                                </div>

                                <!-- Message si aucune date disponible (seulement après chargement) -->
                                <div v-else-if="!isLoading && availableDates.length === 0" class="p-4 bg-yellow-50 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-100 rounded-md">
                                    <p>Aucune date disponible pour un échange avec ce collègue. Cela peut être dû à l'une des raisons suivantes :</p>
                                    <ul class="list-disc list-inside mt-2">
                                        <li>Vous n'avez pas de plannings communs pour les mêmes dates</li>
                                        <li>Il existe déjà des demandes d'échange en cours pour ces dates</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Commentaire -->
                            <div class="mb-6">
                                <label for="requester_comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Commentaire (optionnel)
                                </label>
                                <textarea
                                    id="requester_comment"
                                    v-model="form.requester_comment"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.requester_comment }"
                                    placeholder="Ajoutez un commentaire pour expliquer votre demande d'échange..."
                                ></textarea>
                                <p v-if="errors.requester_comment" class="mt-2 text-sm text-red-600 dark:text-red-500">
                                    {{ errors.requester_comment }}
                                </p>
                            </div>

                            <!-- Message d'erreur général -->
                            <div v-if="errors.general" class="mb-6 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-100 rounded-md">
                                {{ errors.general }}
                            </div>

                            <!-- Boutons d'action -->
                            <div class="flex justify-end space-x-3">
                                <SecondaryButton type="button" @click="cancel">
                                    Annuler
                                </SecondaryButton>
                                <PrimaryButton
                                    type="submit"
                                    :disabled="isLoading || !hasSelectedDates || !form.requested_id"
                                >
                                    <span v-if="isLoading">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Envoi en cours...
                                    </span>
                                    <span v-else>Envoyer la demande</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import axios from 'axios';

export default {
    components: {
        AuthenticatedLayout,
        Head,
        PrimaryButton,
        SecondaryButton
    },
    props: {
        teamUsers: Array,
        userPlannings: Array
    },
    data() {
        return {
            form: {
                requested_id: '',
                requester_comment: '',
                exchanges: []
            },
            availableDates: [],
            selectedDates: [],
            errors: {},
            isLoading: false
        };
    },
    computed: {
        hasSelectedDates() {
            return this.selectedDates.some(selected => selected === true);
        }
    },
    methods: {
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        },
        getDayOfWeek(dateString) {
            const date = new Date(dateString);
            const days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
            return days[date.getDay()];
        },
        getColleagueName() {
            const colleague = this.teamUsers.find(user => user.id === this.form.requested_id);
            return colleague ? colleague.name : '';
        },
        loadUserPlannings() {
            // Réinitialiser les données
            this.availableDates = [];
            this.selectedDates = [];

            if (!this.form.requested_id) {
                return;
            }

            // Activer l'indicateur de chargement
            this.isLoading = true;

            // Ajouter un petit délai pour éviter les flashs d'interface
            setTimeout(() => {
                axios.get(`/api/users/${this.form.requested_id}/exchange-dates`)
                    .then(response => {
                        this.availableDates = response.data;
                        // Initialiser le tableau de sélection avec false pour chaque date
                        this.selectedDates = new Array(this.availableDates.length).fill(false);
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des dates disponibles:', error);
                        this.$notify({
                            title: "Erreur",
                            text: "Impossible de charger les dates disponibles pour cet utilisateur",
                            type: 'error',
                        });
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
            }, 100); // Un petit délai de 100ms pour une meilleure expérience utilisateur
        },
        submitForm() {
            this.isLoading = true;
            this.errors = {};

            // Préparer les données d'échange
            const exchanges = [];
            this.selectedDates.forEach((selected, index) => {
                if (selected) {
                    exchanges.push({
                        requester_planning_id: this.availableDates[index].yourPlanning.id,
                        requested_planning_id: this.availableDates[index].colleaguePlanning.id
                    });
                }
            });

            if (exchanges.length === 0) {
                this.errors.dates = "Veuillez sélectionner au moins une date à échanger.";
                this.isLoading = false;
                return;
            }

            const formData = {
                requested_id: this.form.requested_id,
                requester_comment: this.form.requester_comment,
                exchanges: exchanges
            };

            axios.post(route('exchanges.store'), formData)
                .then(response => {
                    this.$notify({
                        title: "Succès",
                        text: "Votre demande d'échange a été envoyée avec succès",
                        type: 'success',
                    });
                    window.location.href = route('exchanges.index');
                })
                .catch(error => {
                    if (error.response && error.response.data.errors) {
                        this.errors = error.response.data.errors;
                    } else {
                        this.errors.general = error.response?.data?.message || "Une erreur est survenue lors de l'envoi de la demande";
                    }
                    this.$notify({
                        title: "Erreur",
                        text: this.errors.general || "Veuillez corriger les erreurs dans le formulaire",
                        type: 'error',
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        cancel() {
            window.location.href = route('exchanges.index');
        }
    },
    mounted() {
        // Si un utilisateur est déjà sélectionné, charger ses plannings
        if (this.form.requested_id) {
            this.loadUserPlannings();
        }
    }
};
</script>
