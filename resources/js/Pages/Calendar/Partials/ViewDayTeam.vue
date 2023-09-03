<template>
    <div>
        <div v-if="showDates[0].date_fr" class="font-bold text-gray-400 mt-4 text-2xl text-center m-0">{{ showDates[0].date_fr }}</div>
        <div class="flex items-center justify-center p-5">
            <div v-for="item in showDates" :key="item" class="flex flex-col space-y-2">
                <div v-for="planning in item.plannings" class="p-5 text-center" :class="checkBgColor(planning.type_day)">
                    <p class="text-md font-bold">Planning de {{ planning.user.name }}</p>
                    <br>
                    <div v-if="planning.type_day !== 'Planifié'">
                        <p class="text-md font-bold">{{ planning.type_day }}</p>
                        <br>
                    </div>
                    <p v-if="planning.type_day !== 'Repos' && planning.hours" class="text-md font-bold">Heure : {{ planning.hours }}</p>
                    <p v-if="planning.type_day !== 'Repos' && planning.debut_journee" class="text-md font-bold">Début de Journée : {{ planning.debut_journee }}</p>
                    <p v-if="planning.type_day !== 'Repos' && planning.debut_pause" class="text-md font-bold">Début de Pause : {{ planning.debut_pause }}</p>
                    <p v-if="planning.type_day !== 'Repos' && planning.fin_pause" class="text-md font-bold">Fin de Pause : {{ planning.fin_pause }}</p>
                    <p v-if="planning.type_day !== 'Repos' && planning.fin_journee" class="text-md font-bold">Fin de Journée : {{ planning.fin_journee }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ViewDayTeam",
    props: {
        showDates: Object
    },
    methods: {
        checkBgColor (type_day) {
            let color = '';
            if (type_day === 'Planifié') {
                color = 'bg-[#7bed9f]';
            } else if (type_day === 'Congés Payés' || type_day === 'Récup JF') {
                color = 'bg-[#7ed6df]';
            } else if (type_day === 'Repos' || type_day === 'JF') {
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
