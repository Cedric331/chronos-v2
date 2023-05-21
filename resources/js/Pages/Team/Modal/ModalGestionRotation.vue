<template>
    <Modal :show="showRotation" maxWidth="4xl">
        <div class="mx-auto w-full border p-4">
            <div class="mt-6 flex justify-between">
                <div>
                    <label for="name" class="block mb-2 text-lg font-medium text-gray-400">*Nom de la Rotation</label>
                    <input v-model="name" type="text" id="name" max="3" class="border border-gray-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="5 caractères maximum" required>
                </div>
                <InputError :message="message" :canClose="true" @close="this.message = null" class="w-1/3 mx-auto"></InputError>

                <div class="border-t-0 px-6 align-middle border-l-0 border-r-0 flex justify-between p-4">
                    <label class="inline-flex items-center mt-3">
                        <input v-model="synchronise" :checked="synchronise" type="checkbox" class="form-checkbox h-5 w-5"><span class="ml-2 text-gray-400">Synchroniser les jours</span>
                    </label>
                </div>
            </div>

            <div class="mt-6">
                <div class="block w-full overflow-x-auto">
                    <table class="items-center bg-transparent w-full border-collapse ">
                        <thead>
                        <tr>
                            <th class="px-6 text-gray-400 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                Jour de la semaine
                            </th>
                            <th class="px-6 text-gray-400 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                Jour OFF
                            </th>
                            <th class="px-6 text-gray-400 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                Télétravail
                            </th>
                            <th class="px-6 text-gray-400 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                Technicien
                            </th>
                            <th class="px-6 text-gray-400 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                Début Journée
                            </th>
                            <th class="px-6 text-gray-400 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                Début Pause Déjeuner
                            </th>
                            <th class="px-6 text-gray-400 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                Fin Pause Déjeuner
                            </th>
                            <th class="px-6 text-gray-400 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                Fin Journée
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(jour, days) in jours" :key="days">
                            <th class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-lg whitespace-nowrap p-4 text-left text-gray-400 font-bold">
                                {{ days.charAt(0).toUpperCase() + days.slice(1) }}
                            </th>
                            <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                <checkbox v-model="jours[days]['is_off']" :checked="jours[days]['is_off']"></checkbox>
                            </td>
                            <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                <checkbox v-model="jours[days]['teletravail']" :checked="jours[days]['teletravail']"></checkbox>
                            </td>
                            <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                <checkbox v-model="jours[days]['technicien']" :checked="jours[days]['technicien']"></checkbox>
                            </td>
                            <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                <select @change="checkHours(jours[days], days); synchroniseValue(jours[days], 'debut_journee')" :disabled="jours[days]['is_off']" v-model="jours[days]['debut_journee']" class="block w-full text-sm leading-4 font-medium rounded-md text-gray-500 rounded transition ease-in-out m-0">
                                    <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                        {{ horaire }}
                                    </option>
                                </select>
                            </td>
                            <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                <select @change="checkHours(jours[days], days); synchroniseValue(jours[days], 'debut_pause')" :disabled="jours[days]['is_off']" v-model="jours[days]['debut_pause']" class="block w-full text-sm leading-4 font-medium rounded-md text-gray-500 rounded transition ease-in-out m-0">
                                    <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                        {{ horaire }}
                                    </option>
                                </select>
                            </td>
                            <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                <select @change="checkHours(jours[days], days); synchroniseValue(jours[days], 'fin_pause')" :disabled="jours[days]['is_off'] || !jours[days]['debut_journee'] || !jours[days]['debut_pause']" v-model="jours[days]['fin_pause']" class="block w-full text-sm leading-4 font-medium rounded-md text-gray-500 rounded transition ease-in-out m-0">
                                    <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                        {{ horaire }}
                                    </option>
                                </select>
                            </td>
                            <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                <select @change="checkHours(jours[days], days); synchroniseValue(jours[days], 'fin_journee')" :disabled="jours[days]['is_off'] || !jours[days]['debut_journee']" v-model="jours[days]['fin_journee']" class="block w-full text-sm leading-4 font-medium rounded-md text-gray-500 rounded transition ease-in-out m-0">
                                    <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                        {{ horaire }}
                                    </option>
                                </select>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        <div class="px-4 py-3 sm:px-6 flex justify-between sm:flex sm:flex-row-reverse">
            <PrimaryButton @click="submit()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                {{ this.rotation ? 'Modifier' : 'Créer' }}
            </PrimaryButton>
            <SecondaryButton @click="this.$emit('close')" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-gray-100 text-base font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Annuler
            </SecondaryButton>
        </div>
    </Modal>
</template>

<script>

import Checkbox from "@/Components/Checkbox.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";

export default {
    name: "ModalGestionRotation",
    emits: ['storeRotation', 'close'],
    components: {SecondaryButton, PrimaryButton, Modal, Checkbox, InputError},
    props: {
        team_id: Number,
        rotation: Object,
        showRotation: Boolean
    },
    data () {
        return {
            synchronise: false,
            message: null,
            jours: {
                lundi: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': null,
                    'fin_pause': null,
                    'fin_journee': null,
                },
                mardi: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': null,
                    'fin_pause': null,
                    'fin_journee': null,
                },
                mercredi: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': null,
                    'fin_pause': null,
                    'fin_journee': null,
                },
                jeudi: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': null,
                    'fin_pause': null,
                    'fin_journee': null,
                },
                vendredi: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': null,
                    'fin_pause': null,
                    'fin_journee': null,
                },
                samedi: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': null,
                    'fin_pause': null,
                    'fin_journee': null,
                },
                dimanche: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': null,
                    'fin_pause': null,
                    'fin_journee': null,
                },
            },
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
            name: null
        }
    },
    methods: {
        synchroniseValue (data, element) {
            if (this.synchronise) {
                Object.keys(this.jours).forEach((item, key) => {
                    if (!this.jours[item]['is_off']) {
                        this.jours[item][element] = data[element]
                    }
                })
            }
        },
        checkHours(data, days) {
            this.message = null;
            let debut_journee = data['debut_journee'] ? data['debut_journee'].split('h') : null;
            let debut_pause = data['debut_pause'] ? data['debut_pause'].split('h') : null;
            let fin_pause = data['fin_pause'] ? data['fin_pause'].split('h') : null;
            let fin_journee = data['fin_journee'] ? data['fin_journee'].split('h') : null;
            if (debut_journee) {
                debut_journee = (+debut_journee[0]) * 60 * 60 + (+debut_journee[1]) * 60;
            }
            if (debut_pause) {
                debut_pause = (+debut_pause[0]) * 60 * 60 + (+debut_pause[1]) * 60;
            }
            if (fin_pause) {
                fin_pause = (+fin_pause[0]) * 60 * 60 + (+fin_pause[1]) * 60;
            }
            if (fin_journee) {
                fin_journee = (+fin_journee[0]) * 60 * 60 + (+fin_journee[1]) * 60;
            }
            if (debut_journee && fin_journee) {
                if (debut_journee >= fin_journee) {
                    this.message = 'Le début de journée de ' + days + ' doit commencer avant la fin de journée';
                }
            }
            if (debut_pause && fin_pause) {
                if (debut_pause >= fin_pause) {
                    this.message = 'Le début de pause de ' + days + ' doit commencer avant la fin de pause';
                }
            }
        },
        submit () {
            if (this.rotation) {
                this.update()
            } else {
                this.store()
            }
        },
        checkPause () {
            Object.keys(this.jours).forEach((item, key) => {
                if (!this.jours[item]['debut_pause']) {
                    this.jours[item]['fin_pause'] = null
                }
            })
        },
        store () {
            this.message = null
            this.checkPause()
            axios.post('/team/rotation/' + this.team_id, {
                team_id: this.team_id,
                name: this.name,
                jours: this.jours,
            })
                .then(res => {
                    this.$emit('storeRotation', res.data)
                    this.closeModal()
                })
                .catch(error => {
                    this.message = error.response.data.message
                })
        },
        update () {
            this.message = null
            this.checkPause()
            axios.patch('/team/rotation/' + this.rotation.id, {
                name: this.name,
                jours: this.jours,
            })
                .then(res => {
                    this.$emit('storeRotation', res.data, true)
                    this.closeModal()
                })
                .catch(error => {
                    this.message = error.response.data.message
                })
        },
        closeModal () {
            this.name = null
            this.message = null
            this.$emit('close', false)
        },
    },
    mounted () {

        if (this.rotation !== null) {
            this.name = this.rotation.name;
            this.rotation.details.forEach(item => {

                this.jours[item.day.toLowerCase()]['debut_journee'] = item.debut_journee;
                this.jours[item.day.toLowerCase()]['debut_pause'] = item.debut_pause;
                this.jours[item.day.toLowerCase()]['fin_pause'] = item.fin_pause;
                this.jours[item.day.toLowerCase()]['fin_journee'] = item.fin_journee;
                this.jours[item.day.toLowerCase()]['technicien'] = item.technicien;
                this.jours[item.day.toLowerCase()]['teletravail'] = item.teletravail;
                this.jours[item.day.toLowerCase()]['is_off'] = item.is_off;
                this.jours[item.day.toLowerCase()]['id'] = item.id;
            });
        }
    }
}
</script>
