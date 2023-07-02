<template>
<Modal :show="show" :closeable="false" :maxWidth="'3xl'">
    <div>
        <TabGroup @change="changeTab">
            <TabList class="flex justify-around bg-gray-600">
                <Tab :class="[tab === 0 ? 'bg-[#70a1ff] rounded-lg' : '']" class="w-2/4"><h1 class="text-lg text-white font-medium text-center p-4">Création d'un horaire spécifique</h1></Tab>
                <Tab :class="[tab === 1 ? 'bg-[#70a1ff] rounded-lg' : '']" class="w-2/4"><h1 class="text-lg text-white font-medium text-center p-4">Création d'un horaire depuis une rotation</h1></Tab>
            </TabList>
            <TabPanels>
                <TabPanel>
                <div class="relative block md:flex w-full h-[560px]">
                    <div class="w-full md:w-3/5 relative z-1  shadow-lg overflow-hidden">
                        <h1 :class="[isDarkMode ? 'text-white' : 'text-gray-600']" class="text-lg font-medium text-center border-b border-gray-200 tracking-wide p-4">Création d'un horaire spécifique</h1>

                        <section class="container p-6 mx-auto bg-gray-100 h-[499px]">
                            <div>
                                <div class="rounded-lg w-auto w-full">
                                    <div>
                                        <label class="text-gray-800 font-semibold block my-3 text-md">Type</label>
                                        <select :disabled="this.rotation !== null" v-model="type_day" class="block w-full text-sm leading-4 font-medium rounded-md text-gray-500 rounded transition ease-in-out m-0">
                                            <option v-for="params in  this.$page.props.auth.team.params.type_day" :value="params">
                                                {{params}}
                                            </option>
                                        </select>
                                    </div>
                                    <div v-if="isTypeDayPlanifieOrFormation">
                                        <div>
                                            <label class="text-gray-800 font-semibold block my-3 text-md">Début journée</label>
                                            <select :disabled="this.rotation !== null" v-model="debut_journee" class="block w-full text-sm leading-4 font-medium rounded-md text-gray-500 rounded transition ease-in-out m-0">
                                                <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                                    {{ horaire }}
                                                </option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-gray-800 font-semibold block my-3 text-md">Début pause</label>
                                            <select :disabled="this.rotation !== null" v-model="debut_pause" class="block w-full text-sm leading-4 font-medium rounded-md text-gray-500 rounded transition ease-in-out m-0">
                                                <option selected>Pas de pause</option>
                                                <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                                    {{ horaire }}
                                                </option>
                                            </select>
                                        </div>
                                        <div v-if="debut_pause !== null && debut_pause !== 'Pas de pause'">
                                            <label class="text-gray-800 font-semibold block my-3 text-md">Fin pause</label>
                                            <select :disabled="this.rotation !== null" v-model="fin_pause" class="block w-full text-sm leading-4 font-medium rounded-md text-gray-500 rounded transition ease-in-out m-0">
                                                <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                                    {{ horaire }}
                                                </option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-gray-800 font-semibold block my-3 text-md">Fin journée</label>
                                            <select :disabled="this.rotation !== null" v-model="fin_journee" class="block w-full text-sm leading-4 font-medium rounded-md text-gray-500 rounded transition ease-in-out m-0">
                                                <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                                    {{ horaire }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="flex justify-between">
                                            <div class="form-check mt-6">
                                                <SwitchGroup>
                                                    <div class="flex items-center">
                                                        <SwitchLabel class="mr-4">Technicien</SwitchLabel>
                                                        <Switch
                                                            v-model="is_technician"
                                                            :disabled="this.rotation !== null"
                                                            :class='is_technician ? "bg-[#70a1ff]" : "bg-gray-400"'
                                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                <span
                                                    :class='is_technician ? "translate-x-6" : "translate-x-1"'
                                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                                />
                                                        </Switch>
                                                    </div>
                                                </SwitchGroup>
                                            </div>
                                            <div class="form-check mt-6 ml-4">
                                                <SwitchGroup>
                                                    <div class="flex items-center">
                                                        <SwitchLabel class="mr-4">Télétravail</SwitchLabel>
                                                        <Switch
                                                            v-model="telework"
                                                            :disabled="this.rotation !== null"
                                                            :class='telework ? "bg-[#70a1ff]" : "bg-gray-400"'
                                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                <span
                                                    :class='telework ? "translate-x-6" : "translate-x-1"'
                                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                                />
                                                        </Switch>
                                                    </div>
                                                </SwitchGroup>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="w-full md:w-2/5 relative z-1 h-[499px] hidden md:block">
                        <h1 :class="[isDarkMode ? 'text-white' : 'text-gray-600']" class="text-lg font-medium text-center border-b border-gray-200 tracking-wide p-4">{{ daySelected.length > 1 ? 'Dates Sélectionnées' : 'Date Sélectionnée' }}</h1>
                        <section class="container p-6 mx-auto h-full bg-gray-100 overflow-hidden overflow-y-auto">
                            <div v-for="day in daySelected" class="flex justify-around mt-2">
                                <div class=" rounded-lg p-1 flex text-white bg-gray-600 w-[80%] justify-center">
                                    <p>{{ day.date }}</p>
                                </div>
                                <svg @click="deleteDay(day)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff6b81" class="w-5 h-5 mt-2 mr-2 cursor-pointer hover:text-red-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </div>
                        </section>
                    </div>
                </div>
            </TabPanel>
                <TabPanel>
                    <div class="relative block md:flex w-full h-[560px]">
                        <div class="w-full md:w-3/5 relative z-1  shadow-lg overflow-hidden">
                            <h1 :class="[isDarkMode ? 'text-white' : 'text-gray-600']" class="text-lg font-medium text-center border-b border-gray-200 tracking-wide p-4">Création d'un horaire depuis une rotation</h1>

                            <section class="container p-6 mx-auto bg-gray-100 h-[499px]">
                                <div>
                                    <div v-for="rotation in this.$page.props.auth.team.rotations" class="flex justify-around mt-2">
                                        <div @click.prevent="this.selectedRotation = rotation.id" :id="rotation.name" :class="[selectedRotation === rotation.id ? 'bg-[#1e90ff]' : '']" class="rounded-lg p-1 flex text-white bg-gray-600 w-[80%] justify-center cursor-pointer hover:bg-[#70a1ff]">
                                            <p>{{ rotation.name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="w-full md:w-2/5 relative z-1 h-[499px] hidden md:block">
                            <h1 :class="[isDarkMode ? 'text-white' : 'text-gray-600']" class="text-lg font-medium text-center border-b border-gray-200 tracking-wide p-4">{{ daySelected.length > 1 ? 'Dates Sélectionnées' : 'Date Sélectionnée' }}</h1>
                            <section class="container p-6 mx-auto h-full bg-gray-100 overflow-hidden overflow-y-auto">
                                <div v-for="day in daySelected" class="flex justify-around mt-2">
                                    <div class=" rounded-lg p-1 flex text-white bg-gray-600 w-[80%] justify-center">
                                        <p>{{ day.date }}</p>
                                    </div>
                                    <svg @click="deleteDay(day)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ff6b81" class="w-5 h-5 mt-2 mr-2 cursor-pointer hover:text-red-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </div>
                            </section>
                        </div>
                    </div>
                </TabPanel>
            </TabPanels>
        </TabGroup>
        <div class="px-4 py-3 sm:px-6 flex justify-between">
            <SecondaryButton @click="this.$emit('close')" type="button">
                Annuler
            </SecondaryButton>
            <PrimaryButton @click="updatePlannig()" type="button" >
                Enregistrer
            </PrimaryButton>
        </div>
    </div>
</Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import {Switch, SwitchGroup, SwitchLabel, Tab, TabGroup, TabList, TabPanel, TabPanels} from '@headlessui/vue'
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import tippy from "tippy.js";

export default {
    name: "ModalUpdateDay",
    emits: ['deleteDayList', 'update', 'close'],
    components: {
        TabPanel,
        TabPanels,
        Tab,
        TabGroup,
        TabList,
        SecondaryButton,
        PrimaryButton,
        SwitchGroup,
        SwitchLabel,
        Switch,
        Modal
    },
    props: {
        show: Boolean,
        daySelected: Object
    },
    data () {
        return {
            isDarkMode: false,
            selectedRotation: null,
            rotation: null,
            horaires: [
                '08h00',
                '08h30',
                '09h00',
                '09h30',
                '10h00',
                '10h30',
                '11h00',
                '11h30',
                '12h00',
                '12h30',
                '13h00',
                '13h30',
                '14h00',
                '14h30',
                '15h00',
                '15h30',
                '16h00',
                '16h30',
                '17h00',
                '17h30',
                '18h00',
                '18h30',
                '19h00',
                '19h30',
                '20h00',
                '20h30',
                '21h00'
            ],
            type_day: '',
            debut_journee: null,
            debut_pause: 'Pas de pause',
            fin_pause: null,
            fin_journee: null,
            telework: false,
            is_technician: false,
            tab: 0,
        }
    },
    methods: {
        changeTab(index) {
            this.tab = index
            if (index === 1) {
                this.$nextTick(() => {
                    this.$page.props.auth.team.rotations.forEach(rotation => {
                        let content = "";
                        content += '<br>'
                        rotation.details.forEach(day => {
                            content += `<p>Jour: ${day.day}</p>`;
                            if (day.debut_journee) {
                                content += `<p>Début de journée: ${day.debut_journee}</p>`;
                                if (day.debut_pause) {
                                    content += `<p>Début de pause: ${day.debut_pause}</p>`;
                                    content += `<p>Fin de pause: ${day.fin_pause}</p>`;
                                }
                                content += `<p>Fin de journée: ${day.fin_journee}</p>`;
                            } else {
                                content += `<p>Non planifié</p>`;
                            }
                            content += '<br>'
                        });

                        tippy('#' + rotation.name, {
                            placement: 'right',
                            content: content,
                            allowHTML: true,
                        })
                    })
                })
            } else {
                this.selectedRotation = null
            }
        },
        deleteDay (day) {
            this.$emit('deleteDayList', day)
        },
        updatePlannig () {
            if (this.tab === 0) {
                axios.patch('planning/update', {
                    days: this.daySelected,
                    type_day: this.type_day,
                    debut_journee: this.debut_journee,
                    debut_pause: this.debut_pause,
                    fin_pause: this.fin_pause,
                    fin_journee: this.fin_journee,
                    telework: this.telework,
                    is_technician: this.is_technician,
                    })
                    .then(response => {
                        this.$emit('update', response.data)
                    })
                    .catch(error => {
                        console.log(error)
                        this.$notify({
                            type: 'error',
                            title: 'Erreur',
                            text: this.error.response.data.message,
                        })
                    })
            } else {
                axios.patch('planning/update/rotation', {
                    days: this.daySelected,
                    rotation_id: this.selectedRotation,
                    })
                    .then(response => {
                        this.$emit('update', response.data)
                    })
                    .catch(error => {
                        console.log(error)
                    })
            }
        }
    },
    watch: {
        daySelected: {
            handler(val) {
                if (val && val[0] && val[0].plannings[0]) {
                    this.type_day = val[0].plannings[0].type_day
                    this.debut_journee = val[0].plannings[0].debut_journee
                    this.debut_pause = val[0].plannings[0].debut_pause || 'Pas de pause'
                    this.fin_pause = val[0].plannings[0].fin_pause
                    this.fin_journee = val[0].plannings[0].fin_journee
                    this.telework = !!val[0].plannings[0].telework
                    this.is_technician = !!val[0].plannings[0].is_technician
                }
            },
            immediate: true,
        }
    },
    computed: {
        isTypeDayPlanifieOrFormation() {
            return this.type_day === 'Planifié' || this.type_day === 'Formation';
        }
    },
    beforeMount() {
        this.isDarkMode = localStorage.getItem('isDarkMode') === 'true';
    }
}
</script>

<style scoped>
body {
    background: #e2e8f0;
}
*:hover {
    transition: all 150ms ease-in;
}
</style>
