<template>
    <AuthenticatedLayout>
        <Head title="Gestion des congés payés" />
        <section class="p-6 flex justify-between">
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
                                        Dates
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
                            <div class="flex flex-col items-center mt-10">
                                <!-- Help text -->
                                <span class="text-sm text-gray-700 dark:text-gray-400">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ total }}</span> Résultats
                                </span>
                                <div class="inline-flex mt-2 xs:mt-0">
                                    <!-- a -->
                                    <a v-if="prev_page_url" :href="prev_page_url" class="flex items-center justify-center px-3 h-8 text-sm font-medium text-white bg-gray-800 rounded-l hover:bg-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                        <svg class="w-3.5 h-3.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
                                        </svg>
                                        Précédant
                                    </a>
                                    <a v-if="next_page_url" :href="next_page_url" class="flex items-center justify-center px-3 h-8 text-sm font-medium text-white bg-gray-800 border-0 border-l border-gray-700 rounded-r hover:bg-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                        Suivant
                                        <svg class="w-3.5 h-3.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <ModalConfirm v-if="showConfirm" :isAccept="isAccept" :title="title" :message="message" @accept-confirm="this.accepted()" @delete-confirm="this.refused()" @close="this.closeConfirm()"></ModalConfirm>
            </div>
            <div class="w-[30%]">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 flex justify-center">
                        Information sur les congés
                    </h3>
                    <div v-if="paidLeaves">
                        <div v-if="paidLeaves && total > 0">
                            <DoughnutChart ref="doughnutRef" :chartData="chartData" :options="options"></DoughnutChart>
                            <h1 class="flex justify-center pt-5" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'">Nombre de demande : <span class="ml-4 text-md" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'">{{ totalLeave }}</span></h1>
                        </div>
                    </div>
                </div>
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
import {DoughnutChart, BarChart} from 'vue-chart-3';
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

export default {
    name: "PaidLeave",
    components: {
        ModalConfirm,
        PrimaryButton,
        DangerButton,
        SecondaryButton,
        DoughnutChart,
        Head,
        AuthenticatedLayout
    },
    props: {
        leavesProps: Object
    },
    data () {
      return {
        chartData: {},
        options: {},
        paidLeaves: null,
        selectPaid: null,
        showConfirm: false,
        isAccept: false,
        title: '',
        message: '',
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      }
    },
    computed: {
        acceptedPercent() {
            if (this.paidLeaves) {
                return (this.paidLeaves.filter(paidLeave => paidLeave.status === 'Accepté').length / this.total) * 100;
            }
        },
        rejectedPercent() {
            if (this.paidLeaves) {
                return (this.paidLeaves.filter(paidLeave => paidLeave.status === 'Refusé').length / this.total) * 100;
            }
        },
        pendingPercent() {
            if (this.paidLeaves) {
                return (this.paidLeaves.filter(paidLeave => paidLeave.status === 'En attente').length / this.total) * 100;
            }
        },
        totalLeave() {
            if (this.paidLeaves) {
                return this.paidLeaves.length;
            }
        }
    },
    methods: {
        closeConfirm () {
            this.showConfirm = false
        },
        updateChartData() {
            this.chartData = {
                labels: ['Acceptées', 'Refusées', 'En attente'],
                datasets: [
                    {
                        label: ['Acceptées', 'Refusées', 'En attente'],
                        data: [
                            this.acceptedPercent,
                            this.rejectedPercent,
                            this.pendingPercent,
                        ],
                        backgroundColor: ['#1dd1a1', '#ff6b6b', '#feca57'],
                    },
                ],
            }
            this.options = {
                responsive: true,
                    plugins: {
                    legend: {
                        labels: {
                            color: this.$store.state.isDarkMode  ? '#ffffff' : '#000000',
                                boxWidth: 20,
                                padding: 20,
                                font: {
                                weight: 'bold'
                            }
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ' : ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed.toFixed(2) + '%';
                                }
                                return label;
                            }
                        }
                    }
                },
                title: {
                    display: false
                },
            }
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
                this.updateChartData();
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
                this.updateChartData()
            })
        }
    },
    mounted() {
        this.paidLeaves = this.$page.props.leavesProps.data
        this.total = this.$page.props.leavesProps.total
        this.next_page_url = this.$page.props.leavesProps.next_page_url
        this.prev_page_url = this.$page.props.leavesProps.prev_page_url

        this.updateChartData()

        setTimeout(() => {
            this.paidLeaves.forEach((paidLeave, i) => {
                tippy('#comment-' + i, {
                    placement: 'left',
                    content: paidLeave.comment,
                })
            })
        }, 100)
    }
}
</script>

<style scoped>
.progress-container {
    width: 100%;
    background-color: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 20px;
    background-color: green;
    transition: width 0.5s ease;
}

.progress-bar.rejected {
    background-color: red;
}

.progress-bar.pending {
    background-color: yellow;
}

.progress-labels div {
    margin-top: 10px;
}
</style>
