<template>
    <ShareLayout>
    <section>
        <div class="mx-5 py-10 mx-auto">
            <h1 class="flex justify-center text-2xl font-bold my-5">Bienvenue sur Chronos</h1>
            <p class="flex justify-center mb-5 text-lg">Vous consulter actuellement le planning de {{ user }}</p>
            <div v-if="days && days.length > 0" :class="{ 'fade': animateDays }" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-7 w-full p-2">
                <div v-for="day in days" :key="day" class="h-full rounded-lg flex flex-col justify-between">
                    <div
                        class="w-full rounded-lg p-1 min-h-72"
                        :class="[
                                {'isToday' : isToday === day.date_fr},
                                checkBgColor(day.plannings[0].type_day)
                            ]">

                        <div class="flex justify-center">
                            <h1 class="font-bold text-md mr-2">{{ day.date_fr }} {{ day.is_holiday ? '(Férié)' : null }}</h1>
                        </div>
                        <div v-for="planning in day.plannings" :key="planning" class="flex flex-col p-2 h-[9rem]">
                            <div class="flex justify-center items-center">
                                <div class="mr-1">
                                    <p class="text-lg font-bold">{{ planning.type_day !== 'Planifié' ? planning.type_day : '' }} {{ day.is_holiday ? '(Jour Férié)' : null }}</p>
                                </div>
                            </div>
                            <div class="flex justify-center">
                                <div class="flex items-center">
                                    <div v-if="planning.hours" class="font-bold">
                                        {{ planning.hours }}
                                    </div>
                                    <div v-if="planning.is_technician" id="is_technician">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                                        </svg>
                                    </div>
                                    <div v-if="planning.telework" id="telework">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div v-if="planning.type_day !== 'Repos'">
                                <p v-if="planning.debut_journee" class="text-lg font-bold">Début de Journée : {{ planning.debut_journee }}</p>
                                <p v-if="planning.debut_pause" class="text-lg font-bold">Début de Pause : {{ planning.debut_pause }}</p>
                                <p v-if="planning.fin_pause" class="text-lg font-bold">Fin de Pause : {{ planning.fin_pause }}</p>
                                <p v-if="planning.fin_journee" class="text-lg font-bold">Fin de Journée : {{ planning.fin_journee }}</p>
                            </div>
                            <div v-else>
                                <p class="invisible">Invisible text</p>
                                <p class="invisible">Invisible text</p>
                                <p class="invisible">Invisible text</p>
                                <p class="invisible">Invisible text</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="day.zone" class="w-full mt-1">
                        <div :class="[day.zone.includes('A') ? 'bg-blue-600 p-1 m-1' : '']" id="zoneA"></div>
                        <div :class="[day.zone.includes('B') ? 'bg-green-600 p-1 m-1' : '']" id="zoneB"></div>
                        <div :class="[day.zone.includes('C') ? 'bg-red-600 p-1 m-1' : '']" id="zoneC"></div>
                    </div>
                </div>
            </div>
            <div v-else class="flex justify-center">
                <h2 class="text-2xl">-- Aucun planning défini --</h2>
            </div>
        </div>
    </section>
    </ShareLayout>
</template>

<script>

import tippy from "tippy.js";
import 'tippy.js/dist/tippy.css';
import ShareLayout from "@/Layouts/ShareLayout.vue";
export default {
    name: "ShareCalendar",
    components: {ShareLayout},
    props: {
        daysProps: Object,
        user: String,
        isToday: String
    },
    data () {
        return {
            days: this.daysProps,
            animateDays: false,
        }
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
        },
    },
    mounted() {
        tippy('#telework', {
            placement: 'top',
            content: 'Télétravail',
        });
        tippy('#is_technician', {
            placement: 'top',
            content: 'Technicien',
        });
        tippy('#zoneA', {
            placement: 'top',
            content: 'Vacance Zone A',
        });
        tippy('#zoneB', {
            placement: 'top',
            content: 'Vacance Zone B',
        });
        tippy('#zoneC', {
            placement: 'top',
            content: 'Vacance Zone C',
        });
    }
}
</script>

<style>
.fade {
    animation: fadeEffect 1s;
}

@keyframes fadeEffect {
    from {opacity: 0;}
    to {opacity: 1;}
}

.isToday {
    box-shadow: cornflowerblue 0px 22px 70px 4px;
    /*box-shadow: 0 0 4px 4px cornflowerblue;*/
    transition: box-shadow 0.3s, transform 0.3s;

}
</style>
