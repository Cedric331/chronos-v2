<template>
    <AuthenticatedLayout>
        <Head title="Gestion des congés payés" />
        <section class="p-6">
            <div class="bg-gray-300 w-[70%] dark:bg-gray-800 shadow p-4 2xl:col-span-2" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
                <div class="mb-4  flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            Gestion des congés
                        </h3>
                    </div>
                    <div class="flex-shrink-0">
                        <SecondaryButton>
                            Exporter les données
                        </SecondaryButton>
                    </div>
                </div>
                <div class="flex flex-col">
                    <div class="overflow-x-auto rounded-lg">
                        <div class="align-middle inline-block min-w-full">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-white dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                        Demandeur
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                        Commentaire
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                        Période
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre de jours
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th v-if="$page.props.auth.isCoordinateur" scope="col" class="p-4 text-left text-xs dark:text-white font-medium text-gray-500 uppercase tracking-wider"></th>
                                </tr>
                                </thead>

                                <tbody class="bg-white dark:bg-gray-200 table-auto">
                                <tr v-for="(paidLeave, i) in paidLeaves" class="border-b-[1px] border-black" :class="[i % 2 === 0 && paidLeave.status === 'En attente' ? 'bg-gray-100' : 'bg-gray-200', paidLeave.status === 'Accepté' ? 'bg-green-100' : '', paidLeave.status === 'Refusé' ? 'bg-red-100' : '']">
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                        {{ paidLeave.user.name }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                        {{ paidLeave.type }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        <div :id="'comment-' + i" v-if="paidLeave.comment">
                                            {{ paidLeave.comment.slice(0, 60) }} {{ paidLeave.comment.length > 60 ? '...' : '' }}
                                        </div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        <div v-for="date in paidLeave.dates">
                                            {{ date }}
                                        </div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ paidLeave.number_days }} {{ paidLeave.number_days > 1 ? 'jours' : 'jour' }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ paidLeave.status }}
                                    </td>
                                    <td v-if="$page.props.auth.isCoordinateur" class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        <div v-if="paidLeave.status === 'En attente'">
                                            <button
                                                v-if="paidLeave.status !== 'Accepté'"
                                                @click="this.showConfirm = true; this.selectPaid = paidLeave; this.isAccept = true; this.title = 'Accepter la demande de congés'; this.message = 'Êtes-vous sûr de vouloir accepter cette demande de congés ?'"
                                                class="middle none center bg-[#1dd1a1] mr-4 h-8 max-h-[32px] w-8 max-w-[32px] rounded-lg font-sans text-xs font-bold uppercase text-white shadow-md transition-all hover:shadow-lg focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                                                data-ripple-light="true">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button
                                                @click="this.showConfirm = true; this.selectPaid = paidLeave; this.isAccept = false; this.title = 'Refuser la demande de congés'; this.message = 'Êtes-vous sûr de vouloir refuser cette demande de congés ?'"
                                                class="middle none bg-[#ff6b6b] center mr-4 h-8 max-h-[32px] w-8 max-w-[32px] rounded-lg font-sans text-xs font-bold uppercase text-white shadow-md transition-all hover:shadow-lg focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                                                data-ripple-light="true">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <ModalConfirm v-if="showConfirm" :isAccept="isAccept" :title="title" :message="message" @accept-confirm="this.accepted()" @delete-confirm="this.refused()" @close="this.closeConfirm()"></ModalConfirm>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import tippy from "tippy.js";
import DangerButton from "@/Components/DangerButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ModalConfirm from "@/Components/Modal/ModalConfirm.vue";
import axios from "axios";

export default {
    name: "PaidLeave",
    components: {
        ModalConfirm,
        PrimaryButton,
        DangerButton,
        SecondaryButton,
        Head,
        AuthenticatedLayout
    },
    props: {
        leavesProps: Object
    },
    data () {
      return {
        paidLeaves: null,
        selectPaid: null,
        showConfirm: false,
        isAccept: false,
        title: '',
        message: ''
      }
    },
    methods: {
        closeConfirm () {
            this.showConfirm = false
        },
        accepted () {
            axios.post('/paidleave/accepted', {
                id: this.selectPaid.id
            }).then(response => {
                this.$notify({
                    type: 'success',
                    title: 'Succès',
                    text: 'La demande de congés a bien été acceptée.',
                });
                this.paidLeaves.map(paidLeave => {
                    if (paidLeave.id ===  response.data.id) {
                        paidLeave.status = response.data.status
                    }
                })
            }).catch(error => {
                this.$notify({
                    type: 'error',
                    title: 'Erreur',
                    text: error.response.data.message ? error.response.data.message : 'Une erreur est survenue.',
                });
            }).finally(() => {
                this.showConfirm = false
                this.selectPaid = null
            })
        },
        refused () {
            axios.post('/paidleave/refused', {
                id: this.selectPaid.id
            }).then(response => {
                this.showConfirm = false
                this.$notify({
                    type: 'success',
                    title: 'Succès',
                    text: 'La demande de congés a bien été refusée.',
                });
                this.paidLeaves.map(paidLeave => {
                    if (paidLeave.id ===  response.data.id) {
                        paidLeave.status = response.data.status
                    }
                })
            })
            .catch(error => {
                this.$notify({
                    type: 'error',
                    title: 'Erreur',
                    text: error.response.data.message ? error.response.data.message : 'Une erreur est survenue.',
                });
            }).finally(() => {
                this.showConfirm = false
                this.selectPaid = null
            })
        }
    },
    mounted() {
        this.paidLeaves = this.$page.props.leavesProps
        setTimeout(() => {
            this.paidLeaves.forEach((paidLeave, i) => {
                tippy('#comment-' + i, {
                    placement: 'left',
                    content: paidLeave.comment,
                });
            })
        }, 100)
    }
}
</script>

<style scoped>

</style>
