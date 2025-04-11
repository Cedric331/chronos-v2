<template>
    <AuthenticatedLayout>
        <Head title="Échanges de planning" />
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold">Échanges de planning</h2>
                            <PrimaryButton @click="createExchange">Proposer un échange</PrimaryButton>
                        </div>

                        <!-- Filtres -->
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg mb-6 shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 md:space-x-4">
                                <div class="flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-4">
                                    <!-- Filtrage par statut -->
                                    <div>
                                        <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut</label>
                                        <select
                                            id="status-filter"
                                            v-model="filters.status"
                                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            @change="syncOnlyPendingWithStatus"
                                        >
                                            <option value="all">Tous les statuts</option>
                                            <option value="pending">En attente</option>
                                            <option value="accepted">Acceptés</option>
                                            <option value="rejected">Refusés</option>
                                            <option value="cancelled">Annulés</option>
                                        </select>
                                    </div>

                                    <!-- Filtrage par période -->
                                    <div>
                                        <label for="date-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Période</label>
                                        <select
                                            id="date-filter"
                                            v-model="filters.period"
                                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        >
                                            <option value="all">Toutes les dates</option>
                                            <option value="week">Cette semaine</option>
                                            <option value="month">Ce mois</option>
                                            <option value="year">Cette année</option>
                                        </select>
                                    </div>

                                    <!-- Recherche par nom -->
                                    <div>
                                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recherche</label>
                                        <input
                                            type="text"
                                            id="search"
                                            v-model="filters.search"
                                            placeholder="Nom d'utilisateur..."
                                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        />
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
                                    <!-- Filtres rapides -->
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center">
                                            <input
                                                type="checkbox"
                                                id="only-pending"
                                                v-model="filters.onlyPending"
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800"
                                                @change="handleOnlyPendingChange"
                                            />
                                            <label for="only-pending" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                                <span class="font-medium">Uniquement les demandes en attente</span>
                                            </label>
                                        </div>
                                    </div>



                                    <!-- Bouton pour réinitialiser les filtres -->
                                    <button
                                        @click="resetFilters"
                                        class="ml-4 inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Réinitialiser
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Demandes reçues -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-semibold">Demandes reçues</h3>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ filteredReceivedRequests.length }} / {{ receivedRequests.length }} demande(s)
                                </span>
                            </div>
                            <div v-if="receivedRequests.length === 0" class="text-gray-500 dark:text-gray-400">
                                Aucune demande reçue.
                            </div>
                            <div v-else-if="filteredReceivedRequests.length === 0" class="text-gray-500 dark:text-gray-400">
                                Aucune demande reçue ne correspond aux filtres sélectionnés.
                            </div>
                            <div v-else class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Demandeur
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Date proposée
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Date demandée
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Statut
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="request in filteredReceivedRequests" :key="request.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ request.requester.name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ formatDate(request.requester_planning.calendar.date) }} - {{ request.requester_planning.type_day }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ formatDate(request.requested_planning.calendar.date) }} - {{ request.requested_planning.type_day }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span :class="getStatusClass(request.status)">
                                                    {{ getStatusText(request.status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex space-x-2">
                                                    <Link :href="route('exchanges.show', request.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        Détails
                                                    </Link>
                                                    <template v-if="request.status === 'pending'">
                                                        <button @click="acceptExchange(request.id)" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                            Accepter
                                                        </button>
                                                        <button @click="rejectExchange(request.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                            Refuser
                                                        </button>
                                                    </template>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Demandes envoyées -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-semibold">Demandes envoyées</h3>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ filteredSentRequests.length }} / {{ sentRequests.length }} demande(s)
                                </span>
                            </div>
                            <div v-if="sentRequests.length === 0" class="text-gray-500 dark:text-gray-400">
                                Aucune demande envoyée.
                            </div>
                            <div v-else-if="filteredSentRequests.length === 0" class="text-gray-500 dark:text-gray-400">
                                Aucune demande envoyée ne correspond aux filtres sélectionnés.
                            </div>
                            <div v-else class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Destinataire
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Date proposée
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Date demandée
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Statut
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="request in filteredSentRequests" :key="request.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ request.requested.name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ formatDate(request.requester_planning.calendar.date) }} - {{ request.requester_planning.type_day }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ formatDate(request.requested_planning.calendar.date) }} - {{ request.requested_planning.type_day }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span :class="getStatusClass(request.status)">
                                                    {{ getStatusText(request.status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex space-x-2">
                                                    <Link :href="route('exchanges.show', request.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        Détails
                                                    </Link>
                                                    <template v-if="request.status === 'pending'">
                                                        <button @click="openCancelModal(request.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                            Annuler
                                                        </button>
                                                    </template>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmation pour l'annulation -->
        <ModalConfirm
            v-if="showCancelModal"
            :title="modalTitle"
            :message="modalMessage"
            @delete-confirm="cancelExchange"
            @close="closeCancelModal"
        />
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ModalConfirm from '@/Components/Modal/ModalConfirm.vue';
import axios from 'axios';

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Link,
        PrimaryButton,
        ModalConfirm
    },
    props: {
        sentRequests: Array,
        receivedRequests: Array,
        isCoordinateur: Boolean
    },
    data() {
        return {
            filters: {
                status: 'pending', // Par défaut, afficher uniquement les demandes en attente
                period: 'all',
                search: '',
                onlyPending: true // Activé par défaut
            },
            showCancelModal: false,
            exchangeToCancel: null,
            modalTitle: 'Annuler la demande',
            modalMessage: 'Êtes-vous sûr de vouloir annuler cette demande d\'\u00e9change ?'
        };
    },
    computed: {
        filteredSentRequests() {
            return this.applyFilters(this.sentRequests);
        },
        filteredReceivedRequests() {
            return this.applyFilters(this.receivedRequests);
        }
    },
    methods: {
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        },
        resetFilters() {
            this.filters = {
                status: 'pending', // Conserver le filtre sur les demandes en attente
                period: 'all',
                search: '',
                onlyPending: true // Conserver activé
            };
        },

        handleOnlyPendingChange() {
            // Si "Uniquement les demandes en attente" est activé, mettre à jour le filtre de statut
            if (this.filters.onlyPending) {
                this.filters.status = 'pending';
            } else {
                // Si désactivé, remettre le statut à "all" seulement si le statut était "pending"
                if (this.filters.status === 'pending') {
                    this.filters.status = 'all';
                }
            }
        },

        syncOnlyPendingWithStatus() {
            // Mettre à jour le filtre onlyPending en fonction du statut sélectionné
            this.filters.onlyPending = (this.filters.status === 'pending');
        },
        applyFilters(requests) {
            if (!requests) return [];

            // Synchroniser le filtre onlyPending avec le filtre status
            if (this.filters.onlyPending && this.filters.status !== 'pending') {
                this.filters.status = 'pending';
            }

            return requests.filter(request => {
                // Filtrer par statut
                if (this.filters.status !== 'all' && request.status !== this.filters.status) {
                    return false;
                }

                // Filtrer par période
                if (this.filters.period !== 'all') {
                    const requestDate = new Date(request.created_at);
                    const now = new Date();

                    if (this.filters.period === 'week') {
                        // Cette semaine
                        const startOfWeek = new Date(now);
                        startOfWeek.setDate(now.getDate() - now.getDay() + (now.getDay() === 0 ? -6 : 1)); // Lundi de la semaine courante
                        startOfWeek.setHours(0, 0, 0, 0);

                        if (requestDate < startOfWeek) {
                            return false;
                        }
                    } else if (this.filters.period === 'month') {
                        // Ce mois
                        const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);

                        if (requestDate < startOfMonth) {
                            return false;
                        }
                    } else if (this.filters.period === 'year') {
                        // Cette année
                        const startOfYear = new Date(now.getFullYear(), 0, 1);

                        if (requestDate < startOfYear) {
                            return false;
                        }
                    }
                }

                // Filtrer par recherche
                if (this.filters.search) {
                    const searchTerm = this.filters.search.toLowerCase();
                    const otherUserName = request.requester ? request.requester.name.toLowerCase() : request.requested.name.toLowerCase();

                    if (!otherUserName.includes(searchTerm)) {
                        return false;
                    }
                }

                return true;
            });
        },
        getStatusText(status) {
            switch (status) {
                case 'pending': return 'En attente';
                case 'accepted': return 'Accepté';
                case 'rejected': return 'Refusé';
                case 'cancelled': return 'Annulé';
                default: return status;
            }
        },
        getStatusClass(status) {
            switch (status) {
                case 'pending': return 'px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100';
                case 'accepted': return 'px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100';
                case 'rejected': return 'px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100';
                case 'cancelled': return 'px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                default: return '';
            }
        },
        createExchange() {
            window.location.href = route('exchanges.create');
        },
        acceptExchange(id) {
            if (confirm('Êtes-vous sûr de vouloir accepter cette demande d\'échange ?')) {
                axios.post(route('exchanges.accept', id))
                    .then(() => {
                        this.$notify({
                            title: "Succès",
                            text: "Demande d'échange acceptée avec succès",
                            type: 'success',
                        });
                        window.location.reload();
                    })
                    .catch(error => {
                        this.$notify({
                            title: "Erreur",
                            text: error.response?.data?.message || "Une erreur est survenue",
                            type: 'error',
                        });
                    });
            }
        },
        rejectExchange(id) {
            if (confirm('Êtes-vous sûr de vouloir refuser cette demande d\'échange ?')) {
                axios.post(route('exchanges.reject', id))
                    .then(() => {
                        this.$notify({
                            title: "Succès",
                            text: "Demande d'échange refusée",
                            type: 'success',
                        });
                        window.location.reload();
                    })
                    .catch(error => {
                        this.$notify({
                            title: "Erreur",
                            text: error.response?.data?.message || "Une erreur est survenue",
                            type: 'error',
                        });
                    });
            }
        },
        openCancelModal(id) {
            this.exchangeToCancel = id;
            this.showCancelModal = true;
        },

        cancelExchange() {
            if (!this.exchangeToCancel) return;

            axios.post(route('exchanges.cancel', this.exchangeToCancel))
                .then(() => {
                    this.$notify({
                        title: "Succès",
                        text: "Demande d'échange annulée",
                        type: 'success',
                    });
                    window.location.reload();
                })
                .catch(error => {
                    this.$notify({
                        title: "Erreur",
                        text: error.response?.data?.message || "Une erreur est survenue",
                        type: 'error',
                    });
                })
                .finally(() => {
                    this.showCancelModal = false;
                    this.exchangeToCancel = null;
                });
        },

        closeCancelModal() {
            this.showCancelModal = false;
            this.exchangeToCancel = null;
        },

    }
};
</script>
