<template>
    <section class="dark:bg-gray-900">
        <div class="mx-5 py-10 mx-auto">
            <div v-if="days && days.length > 0" :class="{ 'fade': animateDays }" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-7 w-full p-2">
                <div v-for="day in days" :key="day" class="h-full rounded-lg flex flex-col justify-between">
                    <div
                        @click.prevent="selectDate(day)"
                        class="w-full rounded-lg p-1 min-h-72 cursor-pointer hover:bg-[#2f3542] hover:text-white dark:hover:text-gray-600 dark:hover:bg-[#ffffff]"
                        :class="[
                                {'selected': isDaySelected(day)},
                                {'isToday' : isToday === day.date_fr},
                                checkBgColor(day.plannings[0].type_day)
                            ]">

                            <div class="flex justify-center">
                                <h1 class="font-bold text-md mr-2">{{ day.date_fr }} {{ day.is_holiday ? '(Férié)' : null }}</h1>
                                <svg @click.stop="viewPlanningTeam(day)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                            </div>
                        <div v-for="planning in day.plannings" :key="planning" class="flex flex-col p-2 h-[9rem]">
                            <div class="flex justify-center items-center">
                                <div class="mr-1">
                                    <p class="text-lg font-bold">{{ planning.type_day !== 'Planifié' ? planning.type_day : '' }}</p>
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
                                <p v-if="planning.debut_journee" class="text-lg font-bold">Début Journée : {{ planning.debut_journee }}</p>
                                <p v-if="planning.debut_pause" class="text-lg font-bold">Début Pause : {{ planning.debut_pause }}</p>
                                <p v-if="planning.fin_pause" class="text-lg font-bold">Fin Pause : {{ planning.fin_pause }}</p>
                                <p v-if="planning.fin_journee" class="text-lg font-bold">Fin Journée : {{ planning.fin_journee }}</p>
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
            <div v-else class="flex justify-center bg-white dark:bg-gray-900">
                <h2 class="text-2xl dark:text-white ">-- Aucun planning --</h2>
            </div>
        </div>

    <ModalShowPlanningTeam v-if="showPlanningTeam && showDay" :show="showPlanningTeam" :showDay="showDay" @close="this.showPlanningTeam = false"></ModalShowPlanningTeam>
    <ButtonNav :daySelected="daySelected" @shareSchedule="this.$emit('shareSchedule')" @planningFull="this.$emit('planningFull')" @openUpdateDay="this.showUpdateDay = true"></ButtonNav>
    <ModalUpdateDay v-if="showUpdateDay && daySelected.length > 0" :show="showUpdateDay" :daySelected="daySelected" @update="data => this.updatePlanning(data)" @close="this.showUpdateDay = false; this.daySelected= []" @deleteDayList="data => this.selectDate(data)"></ModalUpdateDay>
    </section>
</template>

<script>

import ButtonNav from "@/Components/ButtonNav.vue";
import tippy from "tippy.js";
import ModalUpdateDay from "@/Pages/Calendar/Modal/ModalUpdateDay.vue";
import ModalShowPlanningTeam from "@/Pages/Calendar/Modal/ModalShowPlanningTeam.vue";
import 'tippy.js/dist/tippy.css';

export default {
    name: "Calendar",
    emits: ['planningFull', 'shareSchedule'],
    components: {ModalShowPlanningTeam, ModalUpdateDay, ButtonNav},
    props: {
        daysProps: Object,
        isToday: String
    },
    data () {
        return {
            days: this.daysProps,
            showDay: null,
            showUpdateDay: false,
            showPlanningTeam: false,
            animateDays: false,
            daySelected: []
        }
    },
    watch: {
        daysProps () {
            this.days = this.daysProps
            this.animateDays = true;

            setTimeout(() => {
                this.animateDays = false;
            }, 1000);
        }
    },
    methods: {
        resetDaySelected () {
            this.daySelected = [];
        },
        viewPlanningTeam (day) {
            this.showDay = day;
            this.showPlanningTeam = true;
        },
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
        updatePlanning(data) {
            for (let i = 0; i < this.days.length; i++) {
                for (let j = 0; j < data.length; j++) {
                    if (this.days[i].id === data[j].id) {
                        this.days[i] = data[j];
                        break;
                    }
                }
            }

            this.$notify({
                title: "Succès",
                type: "success",
                text: "Planning modifié avec succès!",
            });
            this.daySelected = [];
            this.showUpdateDay = false;
        },
        selectDate(day) {
            if (this.$page.props.auth.isCoordinateur || this.$page.props.auth.team.params.update_planning) {
                const index = this.daySelected.findIndex(selectedDay => selectedDay.date === day.date);
                if (index === -1) {
                    this.daySelected.push(day);
                } else {
                    this.daySelected.splice(index, 1);
                    if (this.daySelected.length === 0) {
                        this.showUpdateDay = false
                    }
                }
                this.daySelected.sort((a, b) => a.id - b.id);
            }
        },
        isDaySelected(day) {
            return this.daySelected.some(selectedDay => selectedDay.date === day.date);
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


.selected {
    background-image: linear-gradient(
        45deg,
        rgba(0, 0, 0, 0.15) 25%,
        transparent 25%,
        transparent 50%,
        rgba(0, 0, 0, 0.15) 50%,
        rgba(0, 0, 0, 0.15) 75%,
        transparent 75%,
        transparent
    );
    background-size: 40px 40px;
}
.isToday {
    box-shadow: cornflowerblue 0px 22px 70px 4px;
    /*box-shadow: 0 0 4px 4px cornflowerblue;*/
    transition: box-shadow 0.3s, transform 0.3s;

}
/*.selected.isToday {*/
/*    box-shadow: 0 0 3px 3px #eb2f06;*/
/*    transition: box-shadow 0.3s, transform 0.3s;*/
/*}*/
</style>
