<template>
<Modal :show="show" @close="this.$emit('close')" :maxWidth="'4xl'">
    <div>
        <div class="relative block md:flex w-full">
            <div class="w-full md:w-2/5 relative z-1 bg-gray-100 shadow-lg overflow-hidden">
                <div class="text-lg font-medium text-green-500 uppercase text-center border-b border-gray-200 tracking-wide p-4">Création d'un horaire spécifique</div>
                <section class="container p-6 mx-auto bg-white">
                    <div>
                        <div class="rounded-lg w-auto w-4/6">
                            <div>
                                <label class="text-gray-800 font-semibold block my-3 text-md">Type</label>
                                <select :disabled="this.rotation !== null" v-model="type" class="block w-full text-sm leading-4 font-medium rounded-md text-gray-500 rounded transition ease-in-out m-0">
                                    <option v-for="params in  this.$page.props.auth.team.params.type_day" :value="params">
                                        {{params}}
                                    </option>
                                </select>
                            </div>
                            <div v-if="isTypePlanifieOrFormation">
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
                                <div class="flex justify-start">
                                    <div class="form-check mt-6">
                                        <input :disabled="this.rotation !== null" v-model="is_technician" class="form-check-input appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="checkbox">
                                        <label class="form-check-label inline-block text-gray-800">
                                            Technicien
                                        </label>
                                    </div>
                                    <div class="form-check mt-6 ml-4">
                                        <input :disabled="this.rotation !== null" v-model="telework" class="form-check-input appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="checkbox">
                                        <label class="form-check-label inline-block text-gray-800">
                                            Télétravail
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="w-full md:w-2/5 relative z-1 bg-gray-100 overflow-hidden">
                   {{daySelected}}
            </div>
            <div class="w-full md:w-1/5 relative z-1 bg-gray-100 overflow-hidden overflow-y-auto max-h-[600px]">
                <h1 class="mx-auto textlg p-1 flex bg-gray-800 text-white mt-2 w-[90%] justify-center">{{ daySelected.length > 1 ? 'Dates Sélectionnées' : 'Date Sélectionnée' }}</h1>
                <div v-for="day in daySelected" class="flex justify-around mt-2">
                    <div class=" rounded-lg p-1 flex bg-gray-800 text-white w-[60%] justify-center">
                        <p>{{ day.date }}</p>
                    </div>
                    <svg @click="deleteDay(day)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mt-2 mr-2 cursor-pointer hover:text-red-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";

export default {
    name: "ModalUpdateDay",
    emits: ['deleteDayList'],
    components: {
        Modal
    },
    props: {
        show: Boolean,
        daySelected: Object
    },
    data () {
        return {
            rotation: null,
            horaires: [
                '8h00',
                '8h30',
                '9h00',
                '9h30',
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
            type: '',
            debut_journee: null,
            debut_pause: 'Pas de pause',
            fin_pause: null,
            fin_journee: null,
            telework: false,
            is_technician: false,
        }
    },
    methods: {
        deleteDay (day) {
            this.$emit('deleteDayList', day)
        }
    },
    watch: {
        daySelected: {
            handler(val) {
                if (val && val[0] && val[0].plannings[0]) {
                    this.type = val[0].plannings[0].type;
                    this.debut_journee = val[0].plannings[0].debut_journee;
                    this.debut_pause = val[0].plannings[0].debut_pause || 'Pas de pause';
                    this.fin_pause = val[0].plannings[0].fin_pause;
                    this.fin_journee = val[0].plannings[0].fin_journee;
                    this.telework = val[0].plannings[0].telework;
                    this.is_technician = val[0].plannings[0].is_technician;
                }
            },
            immediate: true,
        }
    },
    computed: {
        isTypePlanifieOrFormation() {
            return this.type === 'Planifié' || this.type === 'Formation';
        }
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
