
<template>
    <div class="p-6">
        <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white mb-4">Statistique</h3>
        <p class="dark:text-white mb-6 text-gray-600">
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

                        <div v-if="typeGraph">
                            <DoughnutChart :chartData="chartData" :options="options" />
                        </div>
                        <div v-else>
                            <BarChart :chartData="chartData" :options="options" />
                        </div>
                        <div class="inline-flex items-center">
                            <SwitchGroup>
                                <div class="flex items-center">
                                    <SwitchLabel class="mr-4" :class="$store.state.isDarkMode ? 'text-white' : 'text-black'"> {{ typeGraph ? 'Graphique en camembert' : 'Graphique en barre' }}</SwitchLabel>
                                    <Switch
                                        v-model="typeGraph"
                                        :class='typeGraph ? "bg-[#70a1ff]" : "bg-gray-400"'
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                <span
                                                    :class='typeGraph ? "translate-x-6" : "translate-x-1"'
                                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                                />
                                    </Switch>
                                </div>
                            </SwitchGroup>
                        </div>
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
import {DoughnutChart, BarChart} from 'vue-chart-3';
import { Chart, registerables } from "chart.js";
import {Switch, SwitchGroup, SwitchLabel} from "@headlessui/vue";

Chart.register(...registerables);
export default {
    name: "Statistique",
    components: {SwitchGroup, SwitchLabel, Switch, SecondaryButton, DoughnutChart, BarChart},
    props: {
        users: Array
    },
    data() {
        return {
            typeGraph: true,
            chartData: {
                labels: [],
                datasets: [
                    {
                        label: 'Statistique',
                        data: [],
                        backgroundColor: ['#00a8ff', '#9c88ff', '#fbc531', '#4cd137', '#487eb0','#38ada9','#e84118', '#e1b12c', '#8e44ad', '#4a69bd', '#1e3799', '#0c2461', '#b71540', '#079992', '#4b658'],
                    },
                ],
            },
            options: {
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
                },
                title: {
                    display: false
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
    },
    mounted() {
        this.chartData.labels = this.$page.props.type_days_default;
    }
}
</script>


<style scoped>

</style>
