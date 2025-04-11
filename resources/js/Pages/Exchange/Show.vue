<template>
    <AuthenticatedLayout>
        <Head title="Détails de l'échange" />
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold">Détails de la demande d'échange</h2>
                            <Link :href="route('exchanges.index')" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                Retour à la liste
                            </Link>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Informations générales -->
                                <div>
                                    <h3 class="text-lg font-medium mb-4">Informations générales</h3>
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Statut:</span>
                                            <span :class="getStatusClass(exchange.status)" class="ml-2">
                                                {{ getStatusText(exchange.status) }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Demandeur:</span>
                                            <span class="ml-2">{{ exchange.requester.name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Destinataire:</span>
                                            <span class="ml-2">{{ exchange.requested.name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Date de la demande:</span>
                                            <span class="ml-2">{{ formatDateTime(exchange.created_at) }}</span>
                                        </div>
                                        <div v-if="exchange.responded_at">
                                            <span class="text-gray-500 dark:text-gray-400">Date de réponse:</span>
                                            <span class="ml-2">{{ formatDateTime(exchange.responded_at) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Plannings concernés -->
                                <div>
                                    <h3 class="text-lg font-medium mb-4">Plannings concernés</h3>
                                    <div class="space-y-4">
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600">
                                            <h4 class="font-medium mb-2">Planning de {{ exchange.requester.name }}</h4>
                                            <div class="space-y-1">
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400">Date:</span>
                                                    <span class="ml-2">{{ formatDate(exchange.requester_planning.calendar.date) }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400">Type de jour:</span>
                                                    <span class="ml-2">{{ exchange.requester_planning.type_day }}</span>
                                                </div>
                                                <div v-if="exchange.requester_planning.debut_journee">
                                                    <span class="text-gray-500 dark:text-gray-400">Horaires:</span>
                                                    <span class="ml-2">{{ exchange.requester_planning.debut_journee }} - {{ exchange.requester_planning.fin_journee }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600">
                                            <h4 class="font-medium mb-2">Planning de {{ exchange.requested.name }}</h4>
                                            <div class="space-y-1">
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400">Date:</span>
                                                    <span class="ml-2">{{ formatDate(exchange.requested_planning.calendar.date) }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500 dark:text-gray-400">Type de jour:</span>
                                                    <span class="ml-2">{{ exchange.requested_planning.type_day }}</span>
                                                </div>
                                                <div v-if="exchange.requested_planning.debut_journee">
                                                    <span class="text-gray-500 dark:text-gray-400">Horaires:</span>
                                                    <span class="ml-2">{{ exchange.requested_planning.debut_journee }} - {{ exchange.requested_planning.fin_journee }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Commentaires -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Commentaires</h3>
                            <div class="space-y-4">
                                <div v-if="exchange.requester_comment" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                <span class="text-indigo-800 dark:text-indigo-200 font-medium">{{ exchange.requester.name.charAt(0) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ exchange.requester.name }} (Demandeur)
                                            </div>
                                            <div class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                                {{ exchange.requester_comment }}
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                {{ formatDateTime(exchange.created_at) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="exchange.requested_comment" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                                <span class="text-green-800 dark:text-green-200 font-medium">{{ exchange.requested.name.charAt(0) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ exchange.requested.name }} (Destinataire)
                                            </div>
                                            <div class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                                {{ exchange.requested_comment }}
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                {{ formatDateTime(exchange.responded_at) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="!exchange.requester_comment && !exchange.requested_comment" class="text-gray-500 dark:text-gray-400">
                                    Aucun commentaire pour cette demande d'échange.
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div v-if="showActions" class="flex justify-end space-x-3">
                            <!-- Actions pour le destinataire -->
                            <template v-if="isRecipient && exchange.status === 'pending'">
                                <div class="flex space-x-3">
                                    <SecondaryButton @click="showRejectModal = true" class="bg-red-600 hover:bg-red-700 text-white">
                                        Refuser
                                    </SecondaryButton>
                                    <PrimaryButton @click="showAcceptModal = true">
                                        Accepter
                                    </PrimaryButton>
                                </div>
                            </template>

                            <!-- Actions pour le demandeur -->
                            <template v-if="isRequester && exchange.status === 'pending'">
                                <DangerButton @click="openCancelModal">
                                    Annuler la demande
                                </DangerButton>
                            </template>


                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal pour accepter l'échange -->
        <Modal :show="showAcceptModal" @close="showAcceptModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Accepter la demande d'échange
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Vous êtes sur le point d'accepter cette demande d'échange. Vous pouvez ajouter un commentaire si vous le souhaitez.
                </p>
                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Commentaire (optionnel)
                    </label>
                    <textarea
                        id="comment"
                        v-model="acceptForm.comment"
                        rows="3"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Ajoutez un commentaire (optionnel)..."
                    ></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <SecondaryButton @click="showAcceptModal = false">
                        Annuler
                    </SecondaryButton>
                    <PrimaryButton @click="acceptExchange" :disabled="isSubmitting">
                        <span v-if="isSubmitting">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Traitement...
                        </span>
                        <span v-else>Confirmer</span>
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Modal pour refuser l'échange -->
        <Modal :show="showRejectModal" @close="showRejectModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Refuser la demande d'échange
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Vous êtes sur le point de refuser cette demande d'échange. Vous pouvez ajouter un commentaire pour expliquer votre décision.
                </p>
                <div class="mb-4">
                    <label for="reject-comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Commentaire (optionnel)
                    </label>
                    <textarea
                        id="reject-comment"
                        v-model="rejectForm.comment"
                        rows="3"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Ajoutez un commentaire pour expliquer votre décision..."
                    ></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <SecondaryButton @click="showRejectModal = false">
                        Annuler
                    </SecondaryButton>
                    <DangerButton @click="rejectExchange" :disabled="isSubmitting">
                        <span v-if="isSubmitting">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Traitement...
                        </span>
                        <span v-else>Confirmer le refus</span>
                    </DangerButton>
                </div>
            </div>
        </Modal>

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
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import ModalConfirm from '@/Components/Modal/ModalConfirm.vue';
import axios from 'axios';

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Link,
        PrimaryButton,
        SecondaryButton,
        DangerButton,
        Modal,
        ModalConfirm
    },
    props: {
        exchange: Object,
        isCoordinateur: Boolean
    },
    data() {
        return {
            showAcceptModal: false,
            showRejectModal: false,
            showCancelModal: false,
            isSubmitting: false,
            acceptForm: {
                comment: ''
            },
            rejectForm: {
                comment: ''
            },
            modalTitle: 'Annuler la demande',
            modalMessage: 'Êtes-vous sûr de vouloir annuler cette demande d\'\u00e9change ?'
        };
    },
    computed: {
        isRequester() {
            return this.$page.props.auth.user.id === this.exchange.requester_id;
        },
        isRecipient() {
            return this.$page.props.auth.user.id === this.exchange.requested_id;
        },
        showActions() {
            return this.isRequester || this.isRecipient || this.isCoordinateur;
        }
    },
    methods: {
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        },
        formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
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
        acceptExchange() {
            this.isSubmitting = true;
            axios.post(route('exchanges.accept', this.exchange.id), {
                requested_comment: this.acceptForm.comment
            })
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
                })
                .finally(() => {
                    this.isSubmitting = false;
                    this.showAcceptModal = false;
                });
        },
        rejectExchange() {
            this.isSubmitting = true;
            axios.post(route('exchanges.reject', this.exchange.id), {
                requested_comment: this.rejectForm.comment
            })
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
                })
                .finally(() => {
                    this.isSubmitting = false;
                    this.showRejectModal = false;
                });
        },
        openCancelModal() {
            this.showCancelModal = true;
        },

        cancelExchange() {
            this.isSubmitting = true;
            axios.post(route('exchanges.cancel', this.exchange.id))
                .then(() => {
                    this.$notify({
                        title: "Succès",
                        text: "Demande d'échange annulée",
                        type: 'success',
                    });
                    window.location.href = route('exchanges.index');
                })
                .catch(error => {
                    this.$notify({
                        title: "Erreur",
                        text: error.response?.data?.message || "Une erreur est survenue",
                        type: 'error',
                    });
                })
                .finally(() => {
                    this.isSubmitting = false;
                    this.showCancelModal = false;
                });
        },

        closeCancelModal() {
            this.showCancelModal = false;
        },

    }
};
</script>
