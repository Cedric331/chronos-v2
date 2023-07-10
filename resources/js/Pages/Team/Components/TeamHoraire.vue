<template>
    <div class="h-auto bg-gray-300 dark:bg-gray-800 p-5" :style="{ backgroundColor: this.$store.state.isDarkMode ? '' : $page.props.auth.team.params.color1 }">
        <h3 class="text-xl font-bold leading-none text-gray-900 my-2 dark:text-white">Gestion des alertes horaires</h3>
        <p class="dark:text-white my-2">Vous pouvez indiquer pour chaque jour de la semaine et créneau le nombre de conseiller souhaité auquel cas cela vous en informera.</p>
        <table class="table-auto w-full">
            <thead class="bg-gray-300 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2 dark:text-white">Heures</th>
                <th v-for="day in days" :key="day" class="px-4 py-2 dark:text-white">{{ day }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(time, index) in times" :key="index">
                <td class="border px-4 dark:text-white py-2">{{ time }}</td>
                <td v-for="(day, indexDay) in days" :key="indexDay" class="border px-4 py-2">
                    <input v-model="inputs[index][indexDay]"
                           type="number"
                           class="w-full px-3 py-2 text-gray-700 bg-gray-200 rounded outline-none"
                           @input="debounceStore"
                    />
                </td>
            </tr>
            </tbody>
        </table>
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

            // Envoyer toutes les données en une seule requête
            axios.post('/team/schedule', data)
                .then(response => {
                    console.log(response.data);
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
        }, 3000)
    },
    created() {
        this.initializeInputs();
    }
}
</script>

<style scoped>

</style>
