<template>
    <div class="p-6">
        <h3 class="text-xl font-bold leading-none text-gray-900 mb-4 dark:text-white">Gestion des alertes horaires</h3>
        <p class="dark:text-white mb-6 text-gray-600">Vous pouvez préciser pour chaque jour de la semaine et créneau horaire le nombre de conseillers que vous voulez, auquel cas il vous en informera en début de semaine.</p>

        <div class="flex flex-col">
            <div class="overflow-x-auto rounded-lg">
                <div class="align-middle inline-block min-w-full">
                    <table class="table-auto w-full">
                        <thead class="bg-gray-300 dark:bg-gray-700">
                        <tr>
                            <th class="px-8 py-2 dark:text-white">Heures</th>
                            <th v-for="day in days" :key="day" class="px-4 py-2 dark:text-white">{{ day }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(time, index) in times" :key="index">
                            <td class="border px-4 dark:text-white py-2">{{ time }}</td>
                            <td v-for="(day, indexDay) in days" :key="indexDay" class="border px-4 py-2">
                                <input v-model="inputs[index][indexDay]"
                                       type="number"
                                       :class="[inputs[index][indexDay] > 0 ? 'bg-green-200' : '']"
                                       class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded outline-none"
                                       @input="debounceStore"
                                />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { debounce } from "lodash";
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    name: "TeamHoraire",
    components: {PrimaryButton},
    props: {
        team: Object,
        schedules: Object,
    },
    data() {
        return {
            days: ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'],
            times: [
                '8h - 9h',
                '9h - 10h',
                '10h - 11h',
                '11h - 12h',
                '12h - 13h',
                '13h - 14h',
                '14h - 15h',
                '15h - 16h',
                '16h - 17h',
                '17h - 18h',
                '18h - 19h',
                '19h - 20h',
                '20h - 21h'
            ],
            inputs: Array(13).fill().map(() => Array(7).fill(null)) // Créer un tableau 2D 13x7 avec toutes les valeurs à null
        }
    },
    methods: {
        initializeInputs() {
            if (this.schedules) {
                Object.values(this.schedules).forEach(schedule => {
                    const dayIndex = this.days.indexOf(schedule.day);
                    const timeIndex = this.times.indexOf(this.convertTimeFormat(schedule.time));
                    if (dayIndex !== -1 && timeIndex !== -1) {
                        this.inputs[timeIndex][dayIndex] = schedule.value;
                    }
                });
            }
        },
        convertTimeFormat(time) {
            // convertir '08:00:00 - 09:00:00' en '8h - 9h'
            return time.split(' - ').map(t => parseInt(t)).join('h - ') + 'h';
        },
        store() {
            let data = [];

            this.times.forEach((time, timeIndex) => {
                this.days.forEach((day, dayIndex) => {
                    const value = this.inputs[timeIndex][dayIndex];

                    if (value !== null) {
                        data.push({
                            day: day,
                            time: time,
                            value: value,
                            team_id: this.team.id,
                        });
                    }
                });
            });

            axios.post('/team/schedule', data)
                .then(() => {
                    this.$notify({
                        type: 'success',
                        title: 'Succès',
                        text: 'Les alertes ont bien été enregistrés.',
                    });
                })
                .catch(error => {
                    this.$notify({
                        type: 'error',
                        title: 'Erreur',
                        text: error.response.data.message,
                    });
                });

        },
        debounceStore: debounce(function() {
            this.store();
        }, 1000)
    },
    created() {
        this.initializeInputs();
    }
}
</script>

<style scoped>

</style>
