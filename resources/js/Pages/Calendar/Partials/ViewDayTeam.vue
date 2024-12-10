<template>
    <div>
        <div v-if="showDates[0].date_fr" class="font-bold text-gray-400 mt-4 text-2xl text-center m-0">{{ showDates[0].date_fr }}</div>
        <div class="flex items-center justify-center p-5">
            <div v-for="item in showDates" :key="item" class="flex flex-col space-y-2">
                <div v-for="planning in item.plannings" class="p-5 text-center" :class="checkBgColor(planning.type_day, planning.is_technician)">
                    <p class="text-md font-bold">Planning de {{ planning.user.name }}</p>
                    <br>
                    <div class="flex justify-center">
                        <p class="text-md font-bold text-center">{{ planning.type_day }} {{ planning.rotation ? ' - ' + planning.rotation.name : '' }}</p>
                        <div :id="'event-'+planning.id" v-if="planning.event_plannings.length" class="font-bold ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ff9f43" class="w-5 h-5">
                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <p v-if="planning.type_day !== 'Repos' && planning.hours" class="text-md font-bold">{{ planning.hours }}</p>
                    <br>
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
import tippy from "tippy.js";

export default {
    name: "ViewDayTeam",
    props: {
        showDates: Object
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
