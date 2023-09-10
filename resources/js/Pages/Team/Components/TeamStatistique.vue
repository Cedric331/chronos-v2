
<template>
    <div class="bg-gray-300 dark:bg-gray-800  2xl:col-span-2 shadow p-4" :style="{ backgroundColor: $store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
        <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Statistique</h3>
        <p class="dark:text-white my-2">
            Vous pouvez consulter le détail de la planification de chaque conseiller.
        </p>

        <div class="flex flex-col">
            <div class="overflow-x-auto rounded-lg">
                <div class="flex w-full">
                    <div class="w-1/2 flex flex-col p-2">
                        <label for="user" class="text-sm font-bold leading-tight tracking-normal" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'">Conseiller</label>
                        <select v-model="selectedUser" id="user" class="rounded-lg h-10">
                            <option v-for="user in users" :value="user.id">{{ user.name }}</option>
                        </select>

                        <label for="startDate" class="text-sm mt-5 font-bold leading-tight tracking-normal" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'">Date de début:</label>
                        <input type="date" v-model="startDate" id="startDate" />

                        <label for="endDate" class="text-sm mt-5 font-bold leading-tight tracking-normal" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'">Date de fin:</label>
                        <input type="date" v-model="endDate" id="endDate" />

                        <SecondaryButton @click="submitForm()" class="mt-8 w-2/5 mx-auto flex items-center justify-center">
                            Récupérer les statistiques
                        </SecondaryButton>

                        <div v-if="errors"  class="flex justify-center my-5 w-full mt-2 sm:align-middle font-medium text-sm text-red-600">
                            {{ errors }}
                        </div>
                    </div>

                    <div v-if="this.chartData.datasets[0].data.length > 0" class="w-1/2 flex flex-col p-2">
                        <DoughnutChart :chartData="chartData" :options="options" />
                    </div>
                    <div v-else class="flex items-center justify-center mx-auto">
                        <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white">-- Aucune donnée --</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {DoughnutChart} from 'vue-chart-3';
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);
export default {
    name: "Statistique",
    components: {SecondaryButton, DoughnutChart},
    props: {
        users: Array
    },
    data() {
        return {
            chartData: {
                labels: [
                    'Planifié',
                    'Récup JF',
                    'Congés Payés',
                    'Jour Férié',
                    'Maladie',
                    'Repos',
                    'Formation',
                ],
                datasets: [
                    {
                        data: [],
                        backgroundColor: ['#00a8ff', '#9c88ff', '#fbc531', '#4cd137', '#487eb0','#38ada9','#e84118'],
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: this.$store.state.isDarkMode  ? '#ffffff' : '#000000',
                            boxWidth: 20,
                            padding: 20,
                            font: {
                                weight: 'bold'
                            }
                        },
                    },
                },
                title: {
                    display: true,
                    text: 'Statistique',
                },
            },
            errors: null,
            selectedUser: null,
            startDate: '',
            endDate: ''
        };
    },
    methods: {
        submitForm() {
            this.errors = null;
            if (!this.selectedUser || !this.startDate || !this.endDate) {
                this.errors = 'Veuillez remplir tous les champs';
                return;
            }
            axios.post('/statistics', {
                user_id: this.selectedUser,
                start_date: this.startDate,
                end_date: this.endDate
            })
            .then(response => {
                this.chartData.datasets[0].data = Object.values(response.data);
            })
            .catch(error => {
                this.errors = error.response.data.message;
            });
        }
    }
}
</script>


<style scoped>

</style>
