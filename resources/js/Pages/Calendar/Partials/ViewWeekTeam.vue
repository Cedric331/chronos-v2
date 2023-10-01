<template>
    <table class="table-auto w-full">
        <thead class="text-md font-semibold font-bold uppercase bg-white">
        <tr>
            <th class="p-4 whitespace-nowrap">
                <div class="font-bold text-center">Conseiller</div>
            </th>
            <th class="p-4 whitespace-nowrap">
                <div class="font-bold text-center">Heures</div>
            </th>
            <th v-for="planning in showDates[0].plannings" class="p-4 whitespace-nowrap">
                <div v-if="planning.calendar" class="font-bold text-center">{{ planning.calendar.date_fr }}</div>
            </th>
        </tr>
        </thead>
        <tbody class="text-sm bg-white">
        <tr v-for="item in showDates" :key="item" class="h-24">
            <td class="p-2 whitespace-nowrap border-2 border-black">
                <div class="flex items-center justify-center">
                    <div class="font-bold">{{item.name}}</div>
                </div>
            </td>
            <td class="p-2 whitespace-nowrap border-2 border-black">
                <div :class="weeklyHours[item.plannings[0].number_week] !== '35h00' ? 'text-red-600' : ''" class="flex flex-col p-2 rounded-lg items-center justify-center font-bold" :style="{ margin: '8px' }">
                    {{ weeklyHours[item.plannings[0].number_week] || '00h00' }}
                </div>
            </td>
            <td v-for="planning in item.plannings" class="p-2 whitespace-nowrap border-2 border-black shadow" :class="checkBgColor(planning.type_day, planning.is_technician)">
                <div class="flex items-center">

                    <div class="flex flex-col justify-center mx-auto" v-if="planning.debut_journee">
                        <p class="text-md font-bold text-center">{{ planning.type_day }} - {{ planning.rotation.name }}</p>
                        <p v-if="planning.debut_journee" class="text-md">Début de Journée : <strong>{{ planning.debut_journee }}</strong></p>
                        <p v-if="planning.debut_pause" class="text-md">Début de Pause : <strong>{{ planning.debut_pause }}</strong></p>
                        <p v-if="planning.fin_pause" class="text-md">Fin de Pause : <strong>{{ planning.fin_pause }}</strong></p>
                        <p v-if="planning.fin_journee" class="text-md">Fin de Journée : <strong>{{ planning.fin_journee }}</strong></p>
                    </div>
                    <div v-else class="mx-auto">
                        <p class="text-md font-bold text-center">{{ planning.type_day }} - {{ planning.rotation.name }}</p>
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
        showDates: Object,
        weeklyHours: Object
    },
    methods: {
        checkBgColor (type_day, isTech) {
            let color = '';
            if (type_day === 'Planifié') {
                if (isTech) {
                    color = 'bg-[#ff6b6b]';
                } else {
                    color = 'bg-[#7bed9f]';
                }
            } else if (type_day === 'Congés Payés' || type_day === 'Récup JF') {
                color = 'bg-[#7ed6df]';
            } else if (type_day === 'Repos' || type_day === 'Jour Férié') {
                color = 'bg-[#48dbfb]';
            } else if (type_day === 'Maladie') {
                color = 'bg-[#feca57]';
            } else {
                color = 'bg-[#b8e994]';
            }
            return { [color]: true };
        },
    }
}
</script>

<style scoped>

</style>
