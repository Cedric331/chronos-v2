<template>
    <table class="table-auto w-full">
        <thead class="text-md font-semibold font-bold uppercase bg-white">
        <tr>
            <th class="p-4 whitespace-nowrap">
                <div class="font-bold text-center">Conseiller</div>
            </th>
            <th v-for="planning in showDates[0].plannings" class="p-4 whitespace-nowrap">
                <div v-if="planning.calendar" class="font-bold text-center">{{ planning.calendar.date_fr }}</div>
            </th>
        </tr>
        </thead>
        <tbody class="text-sm bg-white">
        <tr v-for="item in showDates" :key="item">
            <td class="p-2 whitespace-nowrap border-2 border-black">
                <div class="flex items-center">
                    <div class="font-bold">{{item.name}}</div>
                </div>
            </td>
            <td v-for="planning in item.plannings" class="p-2 whitespace-nowrap border-2 border-black shadow" :class="checkBgColor(planning.type_day)">
                <div class="flex items-center">
                    <div class="flex flex-col justify-center mx-auto" v-if="planning.type_day !== 'Repos'">
                        <p v-if="planning.debut_journee" class="text-md">Début de Journée : <strong>{{ planning.debut_journee }}</strong></p>
                        <p v-if="planning.debut_pause" class="text-md">Début de Pause : <strong>{{ planning.debut_pause }}</strong></p>
                        <p v-if="planning.fin_pause" class="text-md">Fin de Pause : <strong>{{ planning.fin_pause }}</strong></p>
                        <p v-if="planning.fin_journee" class="text-md">Fin de Journée : <strong>{{ planning.fin_journee }}</strong></p>
                    </div>
                    <div v-else class="mx-auto">
                        <p class="text-lg font-bold text-center">{{ planning.type_day }}</p>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    name: "ViewWeekTeam",
    props: {
        showDates: Object
    },
    methods: {
        checkBgColor (type_day) {
            let color = '';
            if (type_day === 'Planifié') {
                color = 'bg-[#7bed9f]';
            } else if (type_day === 'Congé Payé' || type_day === 'Récup JF') {
                color = 'bg-[#60a3bc]';
            } else if (type_day === 'Repos' || type_day === 'JF') {
                color = 'bg-[#82ccdd]';
            } else if (type_day === 'Maladie') {
                color = 'bg-[#f8c291]';
            } else {
                color = 'bg-[#b8e994]';
            }
            return { [color]: true };
        }
    }
}
</script>

<style scoped>

</style>
