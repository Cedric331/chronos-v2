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
                        <div class="flex justify-center">
                            <p class="text-md font-bold text-center">{{ planning.type_day }} {{ planning.rotation ? ' - ' + planning.rotation.name : '' }}</p>
                            <div :id="'event-'+planning.id" v-if="planning.event_plannings.length" class="font-bold ml-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ff9f43" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <p v-if="planning.debut_journee" class="text-md">Début de Journée : <strong>{{ planning.debut_journee }}</strong></p>
                        <p v-if="planning.debut_pause" class="text-md">Début de Pause : <strong>{{ planning.debut_pause }}</strong></p>
                        <p v-if="planning.fin_pause" class="text-md">Fin de Pause : <strong>{{ planning.fin_pause }}</strong></p>
                        <p v-if="planning.fin_journee" class="text-md">Fin de Journée : <strong>{{ planning.fin_journee }}</strong></p>
                    </div>
                    <div v-else class="mx-auto">
                        <p class="text-md font-bold text-center">{{ planning.type_day }} {{ planning.rotation ? ' - ' + planning.rotation.name : '' }}</p>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
import tippy from "tippy.js";

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
            } else if (type_day === 'Congés Payés' || type_day === 'Congés sans solde' || type_day === 'Récup JF') {
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
        loadEvent() {
            this.$nextTick(() => {
                this.showDates.forEach(item => {
                    item.plannings.forEach(planning => {
                        if (planning.event_plannings && planning.event_plannings.length) {
                            let content = '';
                            planning.event_plannings.forEach(event => {
                                content += `<p><strong>Titre :</strong> ${event.title}</p>`;
                                if (event.description) {
                                    content += `<p><strong>Description :</strong> ${event.description}</p>`;
                                }
                                content += '<br>';
                            });

                            tippy(`#event-${planning.id}`, {
                                placement: 'right',
                                content: content,
                                allowHTML: true,
                            });
                        }
                    });
                });
            });
        }
    },
    mounted() {
        this.loadEvent()
    }
}
</script>

<style scoped>

</style>
