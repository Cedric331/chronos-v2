<template>
    <AuthenticatedLayout>
        <Head title="Gestion des congés payés" />
        <section class="p-4 md:p-6 flex flex-col md:flex-row justify-between gap-6 bg-gray-50 dark:bg-gray-900">
            <div class="bg-white w-auto lg:w-[70%] dark:bg-gray-800 shadow-md rounded-xl p-6 2xl:col-span-2">
                <div class="mb-4  flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            Gestion des congés
                        </h3>
                    </div>
                    <div class="flex-shrink-0 flex space-x-2">
                        <PrimaryButton @click="openPaidLeaveModal()" class="bg-indigo-600 hover:bg-indigo-700 shadow-md transition-all duration-200 transform hover:scale-105" id="new-leave-request-btn">
                            <i class="fa-solid fa-calendar-plus mr-2"></i> Nouvelle demande
                        </PrimaryButton>
                        <SecondaryButton v-if="$page.props.auth.isCoordinateur" @click="exportData()" class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 shadow-md transition-all duration-200">
                            <i class="fa-solid fa-file-export mr-2"></i> Exporter
                        </SecondaryButton>
                    </div>
                </div>
                <div class="mb-6 items-center flex flex-wrap justify-start gap-4">
                    <div class="relative">
                        <label for="user-filter" class="block mb-2 text-md font-medium text-gray-900 dark:text-gray-300">Filtrer par collaborateur</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fa-solid fa-user text-gray-500"></i>
                            </div>
                            <select id="user-filter" class="w-[250px] bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" v-model="user">
                                <option :value="null">Tous les collaborateurs</option>
                                <option v-for="item in usersProps" :value="item.id">{{ item.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="relative">
                        <label for="year-filter" class="block mb-2 text-md font-medium text-gray-900 dark:text-gray-300">Demande effectuée pour l'année</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fa-solid fa-calendar text-gray-500"></i>
                            </div>
                            <select id="year-filter" class="w-[250px] bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" v-model="year">
                                <option :value="null">Toutes les années</option>
                                <option v-for="item in yearsProps" :value="item.value">{{ item.option }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col">
                    <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="align-middle inline-block min-w-full">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Collaborateur
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Commentaire
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Dates
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nombre de jours
                                    </th>
                                    <th scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th v-if="$page.props.auth.isCoordinateur" scope="col" class="p-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                </tr>
                                </thead>

                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="(paidLeave, i) in paidLeaves" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 ease-in-out">
                                    <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-white">
                                        {{ paidLeave.user.name }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ paidLeave.type }}
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        <div :id="'comment-' + i" v-if="paidLeave.comment" class="cursor-help">
                                            {{ paidLeave.comment.slice(0, 60) }} {{ paidLeave.comment.length > 60 ? '...' : '' }}
                                        </div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        <div v-for="date in paidLeave.dates" class="mb-1">
                                            {{ date }}
                                        </div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full" :class="paidLeave.number_days > 5 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'">{{ paidLeave.number_days }} {{ paidLeave.number_days > 1 ? 'jours' : 'jour' }}</span>
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': paidLeave.status === 'En attente',
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': paidLeave.status === 'Accepté',
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': paidLeave.status === 'Refusé'
                                            }">
                                            {{ paidLeave.status }}
                                        </span>
                                    </td>
                                    <td v-if="$page.props.auth.isCoordinateur" class="p-4 whitespace-nowrap text-sm">
                                        <div class="flex space-x-2">
                                            <button
                                                v-if="paidLeave.status !== 'Accepté' && paidLeave.status === 'En attente'"
                                                @click="this.showConfirm = true; this.selectPaid = paidLeave; this.isAccept = true; this.delete = false; this.title = 'Accepter la demande'; this.message = 'Êtes-vous sûr de vouloir accepter cette demande ?'"
                                                class="p-1.5 bg-green-500 hover:bg-green-600 rounded-lg text-white transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                                title="Accepter">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button
                                                v-if="paidLeave.status === 'En attente'"
                                                @click="this.showConfirm = true; this.selectPaid = paidLeave; this.isAccept = false; this.delete = false; this.title = 'Refuser la demande'; this.message = 'Êtes-vous sûr de vouloir refuser cette demande ?'"
                                                class="p-1.5 bg-red-500 hover:bg-red-600 rounded-lg text-white transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                                title="Refuser">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                            <button
                                                @click="this.showConfirm = true; this.selectPaid = paidLeave; this.isAccept = false; this.delete = true; this.title = 'Supprimer la demande'; this.message = 'Êtes-vous sûr de vouloir supprimer cette demande ?'"
                                                class="p-1.5 bg-gray-500 hover:bg-gray-600 rounded-lg text-white transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                                title="Supprimer">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="flex flex-col items-center py-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ total }}</span> Résultats
                                </span>
                                <div class="inline-flex shadow-sm rounded-md">
                                    <a v-if="prev_page_url" :href="prev_page_url" class="flex items-center justify-center px-4 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Précédent
                                    </a>
                                    <a v-if="next_page_url" :href="next_page_url" class="flex items-center justify-center px-4 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200">
                                        Suivant
                                        <svg class="w-4 h-4 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <ModalConfirm v-if="showConfirm" :isAccept="isAccept" :title="title" :message="message" @accept-confirm="this.accepted()" @delete-confirm="this.refused()" @close="this.closeConfirm()"></ModalConfirm>
            </div>
            <div class="w-[30%] invisible md:visible bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">
                <div class="mb-6">
                    <h3 class="text-xl font-bold mb-6 flex justify-center items-center text-gray-800 dark:text-white">
                        <i class="fa-solid fa-chart-pie mr-2"></i> Statistiques des congés
                    </h3>
                    <div v-if="total > 0" class="p-2">
                        <DoughnutChart ref="doughnutRef" :chartData="chartData" :options="options" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'"></DoughnutChart>
                        <div class="flex justify-center items-center gap-2 mt-6 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm">
                            <span class="font-semibold text-gray-800 dark:text-white">Nombre de demandes:</span>
                            <span class="px-3 py-1 bg-blue-600 text-white rounded-full font-bold">{{ totalLeave }}</span>
                        </div>
                    </div>
                    <div v-else class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg text-center">
                        <h1 class="text-gray-800 dark:text-white font-medium">Aucune demande en cours</h1>
                    </div>
                </div>
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-white dark:bg-gray-800 px-4 text-sm text-gray-500 dark:text-gray-400">Détails</span>
                    </div>
                </div>
                <div class="w-full px-1 mx-auto">
                    <div class="relative flex flex-col min-w-0 break-words w-full mb-6 bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                        <div class="rounded-t mb-0 px-4 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex flex-wrap items-center">
                                <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                    <h3 class="font-semibold text-base text-gray-800 dark:text-white flex items-center">
                                        <i class="fa-solid fa-calendar-check mr-2"></i> Jours de congés pour {{ yearsRange }}
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <div class="block w-full overflow-x-auto">
                            <table class="items-center w-full border-collapse">
                                <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Collaborateur
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Jours acceptés
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="user in usersProps" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-white">
                                        {{ user.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                       <span :class="colorDay(user.days_paid_accepted_count)">{{ user.days_paid_accepted_count }}</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
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
        leavesProps: Object,
        usersProps: Object,
        yearsProps: Object,
        yearsRange: String,
    },
    data () {
      return {
        chartData: {},
        options: {},
        year: null,
        paidLeavesData: null,
        paidLeaves: null,
        selectPaid: null,
        showConfirm: false,
        delete: false,
        isAccept: false,
        user: null,
        title: '',
        message: '',
        total: 0,
        next_page_url: null,
        prev_page_url: null,
      }
    },
    computed: {
        colorDay() {
            return (count) => {
                if (count > 20) {
                    return 'bg-red-500 text-white font-bold rounded-full px-2 py-1 text-center'
                } else if (count > 15) {
                    return 'bg-yellow-500 text-white font-bold rounded-full px-2 py-1 text-center'
                } else if (count > 10) {
                    return 'bg-orange-500 text-white font-bold rounded-full px-2 py-1 text-center'
                } else if (count > 0) {
                    return 'bg-green-500 text-white font-bold rounded-full px-2 py-1 text-center'
                } else {
                    return 'bg-gray-200 text-gray-700 font-bold rounded-full px-2 py-1 text-center'
                }
            }
        },
        acceptedPercent() {
            if (this.paidLeavesData) {
                return (this.paidLeavesData.filter(paidLeave => paidLeave.status === 'Accepté').length / this.total) * 100;
            }
        },
        rejectedPercent() {
            if (this.paidLeavesData) {
                return (this.paidLeavesData.filter(paidLeave => paidLeave.status === 'Refusé').length / this.total) * 100;
            }
        },
        pendingPercent() {
            if (this.paidLeavesData) {
                return (this.paidLeavesData.filter(paidLeave => paidLeave.status === 'En attente').length / this.total) * 100;
            }
        },
        totalLeave() {
            if (this.paidLeavesData) {
                return this.paidLeavesData.length;
            }
        },
    },
    watch: {
        user () {
            this.searchData()
        },
        year () {
            this.searchData()
        }
    },
    methods: {
        closeConfirm () {
            this.showConfirm = false
        },
        searchData () {
            axios.post('/paidleave/search', {
                user: this.user,
                year: this.year
            }).then(response => {
                this.paidLeaves = response.data.data
                this.total = response.data.total
                this.next_page_url = response.data.next_page_url
                this.prev_page_url = response.data.prev_page_url
                this.loadGraphData()
            }).catch(() => {
                this.$notify({
                    type: 'error',
                    title: 'Erreur',
                    text: 'Impossible de charger les données du graphique.',
                });
            });
        },
        loadGraphData() {
            axios.get('/paidleave/statistics', {
                params: {
                    user: this.user,
                    year: this.year
                }
            })
            .then(response => {
                this.paidLeavesData = response.data
                this.updateChartData()
            }).catch(() => {
                this.$notify({
                    type: 'error',
                    title: 'Erreur',
                    text: 'Impossible de charger les données du graphique.',
                });
            });
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
                            this.pendingPercent
                        ],
                        backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                        borderWidth: 1,
                        borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                        hoverOffset: 4,
                    },
                ],
            }
            this.options = {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        labels: {
                            boxWidth: 15,
                            padding: 15,
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            color: this.$store.state.isDarkMode ? '#ffffff' : '#000000',
                            usePointStyle: true,
                            pointStyle: 'circle'
                        },
                        position: 'bottom',
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ' : ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed.toFixed(1) + '%';
                                }
                                return label;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1000
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
                this.loadGraphData()
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
            if (this.delete) {
                axios.delete('/paidleave/delete', {
                    data: {
                        id: this.selectPaid.id
                    }
                }).then(response => {
                    this.showConfirm = false
                    this.$notify({
                        type: 'success',
                        title: 'Succès',
                        text: 'La demande a bien été supprimée.',
                    });
                    this.paidLeaves = this.paidLeaves.filter(paidLeave => paidLeave.id !== response.data.id)
                    this.total = this.total - 1
                    this.loadGraphData()
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
            } else {
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
                    this.loadGraphData()
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
        exportData() {
            axios.get('/paidleave/export', {
                responseType: 'blob',
                params: {
                    year: this.year,
                    user: this.user
                }
            })
                .then(response => {
                    let blob = new Blob([response.data], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'})
                    let link = document.createElement('a')
                    link.href = window.URL.createObjectURL(blob)
                    link.download = this.$page.props.auth.team.name.toLowerCase() + '-conge.xlsx'
                    link.click()
                })
                .catch(error => {
                    this.$notify({
                        type: 'error',
                        title: 'Error',
                        text: 'Une erreur est survenue lors de l\'export du planning.'
                    })
                });
        },
        openPaidLeaveModal() {
            // Rediriger vers la page du calendrier pour sélectionner les jours
            window.location.href = '/planning';
        },

    },
    mounted() {
        this.paidLeaves = this.$page.props.leavesProps.data
        this.total = this.$page.props.leavesProps.total
        this.next_page_url = this.$page.props.leavesProps.next_page_url
        this.prev_page_url = this.$page.props.leavesProps.prev_page_url

        this.loadGraphData()

        setTimeout(() => {
            this.paidLeaves.forEach((paidLeave, i) => {
                tippy('#comment-' + i, {
                    placement: 'left',
                    content: paidLeave.comment,
                })
            })

            // Ajouter un tooltip pour le bouton de nouvelle demande
            tippy('#new-leave-request-btn', {
                placement: 'bottom',
                content: 'Vous serez redirigé vers le calendrier pour sélectionner les jours de congés',
                theme: 'light',
                animation: 'scale',
                arrow: true,
                delay: [200, 0],
                duration: [200, 200],
            })
        }, 100)
    }
}
</script>

<style scoped>

.progress-labels div {
    margin-top: 10px;
}
</style>
