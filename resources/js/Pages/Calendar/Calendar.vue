<template>
    <section class="dark:bg-gray-900 bg-gray-50 min-h-screen">
        <div class="mx-2 py-10">
            <div v-if="days && days.length > 0" class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 w-full p-2">
                <template v-for="(day, index) in days" :key="index">
                    <div v-if="isStartOfWeek(day)" :class="weeklyHours[day.number_week] !== '35h00' ? 'text-red-600' : ''" class="hidden 2xl:flex flex-col p-2 min-h-[11rem] bg-gray-200 rounded-lg items-center justify-center text-xl font-bold" :style="{ margin: '8px' }">
                       {{ weeklyHours[day.number_week] || '00h00' }}
                    </div>
                    <div class="rounded-lg flex flex-col justify-between">
                        <div
                            @click.prevent="selectDate(day)"
                            class="w-full rounded-lg p-1 cursor-pointer hover:bg-[#2f3542] hover:text-white dark:hover:text-gray-600 dark:hover:bg-[#ffffff]"
                            :class="[
                                    {'halfLeaveMorning': isHalfLeave(day,'Morning')},
                                    {'halfLeaveAfternoon': isHalfLeave(day, 'Afternoon')},
                                    {'selected': isDaySelected(day)},
                                    {'isToday' : isToday === day.date_fr},
                                    checkBgColor(day.plannings[0].type_day, day.plannings[0].is_technician)
                                ]">

                                <div class="flex justify-center">
                                    <h1 class="font-bold text-sm mr-2">{{ day.date_fr }}</h1>
                                    <svg @click.stop="viewPlanningTeam(day)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                    </svg>
                                </div>
                            <div v-for="planning in day.plannings" :key="planning" class="flex flex-col p-2 min-h-[11rem]">

                                <div class="flex justify-center">
                                    <div class="flex items-center">
                                        <div v-if="planning.hours" class="font-bold">
                                            {{ planning.hours }}
                                        </div>
                                        <div v-if="planning.rotation" class="font-bold ml-2">
                                            {{ planning.rotation.name }}
                                        </div>
                                        <div :id="'event-'+day.id" v-if="planning.event_plannings.length" class="font-bold ml-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ff9f43" class="w-5 h-5">
                                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                            </svg>
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

                                <div class="flex justify-center">
                                    <div class="flex items-center">
                                        <div class="mr-1">
                                            <p class="text-lg font-bold">{{ planning.type_day !== 'Planifié' && planning.type_day !== 'CP Après-midi' ? planning.type_day : '' }}</p>
                                        </div>
                                        <div class="mr-1">
                                            <p class="text-lg font-bold">{{ day.is_holiday && planning.type_day !== 'Jour Férié' ? ' (Férié)' : null }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div :class="[planning.type_day === 'Planifié' || planning.type_day === 'CP Matin' && !day.is_holiday ? 'mt-6': '']">
                                    <p v-if="planning.debut_journee" class="text-md font-bold">Début Journée : {{ planning.debut_journee }}</p>
                                    <p v-if="planning.debut_pause" class="text-md font-bold">Début Pause : {{ planning.debut_pause }}</p>
                                    <p v-if="planning.fin_pause" class="text-md font-bold">Fin Pause : {{ planning.fin_pause }}</p>
                                    <p v-if="planning.fin_journee" class="text-md font-bold">Fin Journée : {{ planning.fin_journee }}</p>
                                </div>

                                <div v-if="planning.type_day === 'CP Après-midi'" class="flex justify-center">
                                    <div class="flex items-center">
                                        <div class="mt-6">
                                            <p class="text-lg font-bold">{{ planning.type_day }}</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div v-if="day.zone" class="w-full mt-1">
                            <div :class="[day.zone.includes('A') ? 'bg-blue-600 p-1 m-1' : '']" id="zoneA"></div>
                            <div :class="[day.zone.includes('B') ? 'bg-green-600 p-1 m-1' : '']" id="zoneB"></div>
                            <div :class="[day.zone.includes('C') ? 'bg-red-600 p-1 m-1' : '']" id="zoneC"></div>
                        </div>
                    </div>
                </template>
            </div>
            <div v-else class="flex justify-center bg-gray-50 dark:bg-gray-900">
                <h2 class="text-2xl dark:text-white ">-- Aucun planning --</h2>
            </div>
        </div>

    <ModalConfirm v-if="showModalConfirm" :show="showModalConfirm" @close="this.showModalConfirm = false" @delete-confirm="this.deletePlanning()"></ModalConfirm>
    <ModalManagelEvent v-if="showModalEvent" :showEvent="showModalEvent" :days="daySelected" @store="data => newEvent(data)" @close="this.showModalEvent = false"></ModalManagelEvent>
    <ModalShowPlanningTeam v-if="showPlanningTeam && showDay" :show="showPlanningTeam" :showDay="showDay" @close="this.showPlanningTeam = false"></ModalShowPlanningTeam>
    <ButtonNav :key="getAllPlanning"
               :isMyPlanning="isMyPlanning"
               :daySelected="daySelected"
               :getAllPlanning="getAllPlanning"
               @openPaidLeave="this.showModalPaidLeave = true"
               @shareSchedule="this.$emit('shareSchedule')"
               @planningFull="this.$emit('planningFull')"
               @deleteEvent="this.confirmDeleteEvent()"
               @openEvent="this.showModalEvent = true"
               @openUpdateDay="this.showUpdateDay = true">
    </ButtonNav>
    <ModalUpdateDay v-if="showUpdateDay && daySelected.length > 0" :show="showUpdateDay" :daySelected="daySelected" @update="data => this.updatePlanning(data)" @close="this.showUpdateDay = false; this.daySelected= []" @deleteDayList="data => this.selectDate(data)"></ModalUpdateDay>
    <ModalPaidLeave v-if="showModalPaidLeave" :show="showModalPaidLeave" :daysProps="daySelected" @deleteDayList="data => this.selectDate(data)" @close="this.showModalPaidLeave = false"></ModalPaidLeave>
    </section>
</template>

<script>

import ButtonNav from "@/Components/ButtonNav.vue";
import tippy from "tippy.js";
import ModalUpdateDay from "@/Pages/Calendar/Modal/ModalUpdateDay.vue";
import ModalShowPlanningTeam from "@/Pages/Calendar/Modal/ModalShowPlanningTeam.vue";
import 'tippy.js/dist/tippy.css';
import ModalManagelEvent from "@/Pages/Calendar/Modal/ModalManagelEvent.vue";
import ModalConfirm from "@/Components/Modal/ModalConfirm.vue";
import ModalPaidLeave from "@/Pages/PaidLeave/Modal/ModalPaidLeave.vue";

export default {
    name: "Calendar",
    emits: ['planningFull', 'shareSchedule'],
    components: {ModalPaidLeave, ModalConfirm, ModalManagelEvent, ModalShowPlanningTeam, ModalUpdateDay, ButtonNav},
    props: {
        daysProps: Object,
        weeklyHours: {
            type: Object,
            default: () => []
        },
        getAllPlanning: Boolean,
        isToday: String
    },
    watch: {
        daysProps () {
            this.days = this.daysProps
            this.loadEvent()
        }
    },
    data () {
        return {
            days: this.daysProps,
            showDay: null,
            isMyPlanning: false,
            showModalPaidLeave: false,
            showModalEvent: false,
            showUpdateDay: false,
            showModalConfirm: false,
            showPlanningTeam: false,
            daySelected: []
        }
    },
    methods: {
        checkIsMyPlanning () {
            if (this.daySelected.length > 0) {
                this.isMyPlanning = this.daySelected[0].plannings[0].user_id === this.$page.props.auth.user.id
            } else {
                this.isMyPlanning = false
            }
        },
        isStartOfWeek(day) {
            return day.date_fr.startsWith('Lundi');
        },
        deletePlanning () {
          axios.delete('/event', {
              data: {
                  days: this.daySelected
            }
          })
          .then(response => {
              this.daySelected = [];
              response.data.forEach(planning => {
                  this.days.forEach(day => {
                      day.plannings.forEach(item => {
                          if (planning.id === item.id) {
                              item.event_plannings = [];
                          }
                      })
                  })
              })
              this.$notify({
                  title: "Succès",
                  type: "success",
                  text: "Les évènements sont supprimés avec succès!",
              });
              this.showModalConfirm = false;
          })
          .catch(() => {
              this.$notify({
                  title: "Erreur",
                  type: "error",
                  text: "Une erreur est survenue lors de la suppression du planning!",
              });
          })
        },
        confirmDeleteEvent () {
          this.showModalConfirm = true;
        },
        resetDaySelected () {
            this.daySelected = [];
        },
        viewPlanningTeam (day) {
            this.showDay = day;
            this.showPlanningTeam = true;
        },
        checkBgColor (type_day, isTech) {
            let color = '';
            if (type_day === 'Planifié' || type_day === 'CP Matin' || type_day === 'CP Après-midi') {
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
        updatePlanning(data) {
            this.days.map((day, index) => {
                data.forEach(item => {
                    if (day.id === item.id) {
                        this.days[index] = item
                    }
                })
            })

            this.$notify({
                title: "Succès",
                type: "success",
                text: "Planning modifié avec succès!",
            });
            this.daySelected = [];
            this.showUpdateDay = false;
        },
        selectDate(day) {
            if (this.$page.props.auth.isCoordinateur || this.$page.props.auth.team.params.update_planning || this.$page.props.auth.team.params.paid_leave) {
                const index = this.daySelected.findIndex(selectedDay => selectedDay.date === day.date);
                if (index === -1) {
                    this.daySelected.push(day);
                } else {
                    this.daySelected.splice(index, 1);
                    if (this.daySelected.length === 0) {
                        this.showUpdateDay = false
                        this.showModalPaidLeave = false
                    }
                }
                this.daySelected.sort((a, b) => a.id - b.id);
                this.checkIsMyPlanning()
            }
        },
        isDaySelected(day) {
            return this.daySelected.some(selectedDay => selectedDay.date === day.date);
        },
        isHalfLeave(day, type) {
            if (type === 'Morning')
                return day.plannings.some(planning => planning.type_day === 'CP Matin');
            else if (type === 'Afternoon') {
                return day.plannings.some(planning => planning.type_day === 'CP Après-midi');
            }
        },
        newEvent (events) {
            events.forEach(event => {
                this.days.forEach(day => {
                    day.plannings.forEach(planning => {
                        if (event.planning_id === planning.id) {
                            planning.event_plannings.push(event);
                        }
                    })
                })
            })

            this.$notify({
                title: "Succès",
                type: "success",
                text: "Événement ajouté avec succès!",
            });
            this.loadEvent()
            this.showModalEvent = false;
            this.daySelected = [];
        },
        loadEvent () {
            this.$nextTick(() => {
                this.days.forEach(day => {
                    let content = "";
                    content += '<br>'
                    day.plannings.forEach(planning => {
                        planning.event_plannings.forEach(event => {
                            content += `<p>Titre : ${event.title}</p>`;
                            content += event.description ? `<p>Description : ${event.description}</p>` : '';
                            content += '<br>'
                        });
                    });
                    tippy('#' + 'event-' + day.id, {
                        placement: 'right',
                        content: content,
                        allowHTML: true,
                    })
                })
            })
        }
    },
    mounted() {
        this.loadEvent()

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
    ) !important;
    background-size: 40px 40px !important;
}

.halfLeaveMorning {
    background-image: linear-gradient(
        to bottom,
        #7ed6df 50%,
        transparent 50%
    );
    background-size: 100% 100%;
}


.halfLeaveAfternoon{
    background-image: linear-gradient(
        to bottom,
        transparent 50%,
        #7ed6df 50%
    );
    background-size: 100% 100%;
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
