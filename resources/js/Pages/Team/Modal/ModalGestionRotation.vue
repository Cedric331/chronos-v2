<template>
    <Modal :show="showRotation" maxWidth="6xl" @close="$emit('close')">
        <div class="p-6 sm:p-8 bg-white dark:bg-gray-800">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h2 class="text-2xl font-bold mb-2" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                        {{ rotation ? 'Modifier la rotation' : 'Créer une nouvelle rotation' }}
                    </h2>
                    <p class="text-sm" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                        Configurez les horaires et les paramètres pour chaque jour de la semaine
                    </p>
                </div>
                <button
                    @click="$emit('close')"
                    class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-opacity-75"
                    aria-label="Fermer la modal"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Nom de la rotation et synchronisation -->
            <div class="mb-6 space-y-4">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nom de la rotation -->
                        <div>
                            <label for="name" class="block text-sm font-medium mb-1.5" :style="{ color: isDarkMode ? '#e5e7eb' : '#374151' }">
                                Nom de la rotation <span class="text-red-500">*</span>
                            </label>
                            <input 
                                v-model="name" 
                                type="text" 
                                id="name" 
                                maxlength="5"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors text-sm"
                                placeholder="5 caractères maximum"
                                required
                            />
                            <p class="mt-1 text-xs" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                                {{ name ? name.length : 0 }}/5 caractères
                            </p>
                        </div>

                        <!-- Synchronisation -->
                        <div class="flex items-end">
                            <div class="flex items-center p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg w-full">
                                <Switch :modelValue="synchronise" @update:modelValue="val => synchronise = val" />
                                <label class="ml-2 text-sm font-medium cursor-pointer" :style="{ color: isDarkMode ? '#fbbf24' : '#92400e' }">
                                    <div class="font-semibold">Synchroniser les jours</div>
                                    <div class="text-xs opacity-75">Mêmes horaires pour tous les jours actifs</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Message d'erreur -->
                    <div v-if="message" class="mt-3">
                        <InputError :message="message" :canClose="true" @close="this.message = null" />
                    </div>
                </div>
            </div>

            <!-- Configuration des jours -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                        Configuration des jours de la semaine
                    </h3>
                    <div class="text-xs" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                        {{ getActiveDaysCount() }} jour{{ getActiveDaysCount() > 1 ? 's' : '' }} actif{{ getActiveDaysCount() > 1 ? 's' : '' }}
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">
                    <div 
                        v-for="(jour, dayKey) in jours" 
                        :key="dayKey"
                        :class="[
                            'bg-gray-50 dark:bg-gray-800 rounded-lg border-2 p-3 transition-all duration-200',
                            jours[dayKey]['is_off'] 
                                ? 'border-gray-200 dark:border-gray-700 opacity-60' 
                                : 'border-amber-200 dark:border-amber-800 shadow-sm hover:shadow-md'
                        ]"
                    >
                        <!-- Header du jour -->
                        <div class="flex items-center justify-between mb-2 pb-2 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-bold capitalize" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                {{ dayKey.charAt(0).toUpperCase() + dayKey.slice(1) }}
                            </h4>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <Switch 
                                    :modelValue="jours[dayKey]['is_off']"
                                    @update:modelValue="val => { jours[dayKey]['is_off'] = val; if (val) { jours[dayKey]['debut_journee'] = null; jours[dayKey]['fin_journee'] = null; jours[dayKey]['debut_pause'] = 'Pas de pause'; jours[dayKey]['fin_pause'] = null; } }"
                                />
                                <span class="ml-1.5 text-xs font-medium" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                                    Repos
                                </span>
                            </label>
                        </div>

                        <!-- Contenu du jour -->
                        <div v-if="!jours[dayKey]['is_off']" class="space-y-2">
                            <!-- Options -->
                            <div class="flex flex-col space-y-1.5">
                                <label class="flex items-center cursor-pointer">
                                    <Switch 
                                        :modelValue="jours[dayKey]['teletravail']"
                                        @update:modelValue="val => jours[dayKey]['teletravail'] = val"
                                    />
                                    <span class="ml-1.5 text-xs" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                                        <svg class="w-3 h-3 inline mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                        Télétravail
                                    </span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <Switch 
                                        :modelValue="jours[dayKey]['technicien']"
                                        @update:modelValue="val => jours[dayKey]['technicien'] = val"
                                    />
                                    <span class="ml-1.5 text-xs" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                                        <svg class="w-3 h-3 inline mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                                        </svg>
                                        Technicien
                                    </span>
                                </label>
                            </div>

                            <!-- Horaires -->
                            <div class="space-y-1.5">
                                <div>
                                    <label class="block text-xs font-medium mb-0.5" :style="{ color: isDarkMode ? '#d1d5db' : '#4b5563' }">
                                        Début
                                    </label>
                                    <select 
                                        @change="checkHours(jours[dayKey], dayKey); synchroniseValue(jours[dayKey], 'debut_journee')" 
                                        v-model="jours[dayKey]['debut_journee']" 
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-amber-500 focus:border-amber-500 transition-colors"
                                    >
                                        <option :value="null" disabled>-</option>
                                        <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                            {{ horaire }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-0.5" :style="{ color: isDarkMode ? '#d1d5db' : '#4b5563' }">
                                        Pause
                                    </label>
                                    <select 
                                        @change="checkHours(jours[dayKey], dayKey); synchroniseValue(jours[dayKey], 'debut_pause')" 
                                        v-model="jours[dayKey]['debut_pause']" 
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-amber-500 focus:border-amber-500 transition-colors"
                                    >
                                        <option value="Pas de pause">-</option>
                                        <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                            {{ horaire }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-0.5" :style="{ color: isDarkMode ? '#d1d5db' : '#4b5563' }">
                                        Fin pause
                                    </label>
                                    <select 
                                        @change="checkHours(jours[dayKey], dayKey); synchroniseValue(jours[dayKey], 'fin_pause')" 
                                        :disabled="!jours[dayKey]['debut_journee'] || !jours[dayKey]['debut_pause'] || jours[dayKey]['debut_pause'] === 'Pas de pause'"
                                        v-model="jours[dayKey]['fin_pause']" 
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-amber-500 focus:border-amber-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <option :value="null" disabled>-</option>
                                        <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                            {{ horaire }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-0.5" :style="{ color: isDarkMode ? '#d1d5db' : '#4b5563' }">
                                        Fin
                                    </label>
                                    <select 
                                        @change="checkHours(jours[dayKey], dayKey); synchroniseValue(jours[dayKey], 'fin_journee')" 
                                        :disabled="!jours[dayKey]['debut_journee']"
                                        v-model="jours[dayKey]['fin_journee']" 
                                        class="w-full px-2 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-amber-500 focus:border-amber-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <option :value="null" disabled>-</option>
                                        <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                            {{ horaire }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- État repos -->
                        <div v-else class="text-center py-2">
                            <svg class="w-5 h-5 mx-auto text-gray-400 dark:text-gray-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-xs" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                                Repos
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <SecondaryButton @click="$emit('close')">
                    Annuler
                </SecondaryButton>
                <PrimaryButton 
                    @click="submit()" 
                    :disabled="!name || name.length === 0"
                    :class="[!name || name.length === 0 ? 'opacity-50 cursor-not-allowed' : '']"
                >
                    <svg v-if="rotation" class="w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg v-else class="w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ rotation ? 'Modifier' : 'Créer' }} la rotation
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>

<script>
import Switch from "@/Components/Switch.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import { useStore } from 'vuex';
import { useNotification } from '@/composables/useNotification';
import { computed } from 'vue';
import axios from "axios";

export default {
    name: "ModalGestionRotation",
    emits: ['storeRotation', 'close'],
    components: {
        SecondaryButton, 
        PrimaryButton, 
        Modal, 
        Switch, 
        InputError
    },
    props: {
        team_id: Number,
        rotation: Object,
        showRotation: Boolean
    },
    setup() {
        const store = useStore();
        const isDarkMode = computed(() => store.state.isDarkMode);
        const { showSuccess, showError } = useNotification();
        return { isDarkMode, showSuccess, showError };
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
                    'debut_pause': 'Pas de pause',
                    'fin_pause': null,
                    'fin_journee': null,
                },
                mardi: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': 'Pas de pause',
                    'fin_pause': null,
                    'fin_journee': null,
                },
                mercredi: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': 'Pas de pause',
                    'fin_pause': null,
                    'fin_journee': null,
                },
                jeudi: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': 'Pas de pause',
                    'fin_pause': null,
                    'fin_journee': null,
                },
                vendredi: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': 'Pas de pause',
                    'fin_pause': null,
                    'fin_journee': null,
                },
                samedi: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': 'Pas de pause',
                    'fin_pause': null,
                    'fin_journee': null,
                },
                dimanche: {
                    'id': null,
                    'technicien': false,
                    'teletravail': false,
                    'is_off': false,
                    'debut_journee': null,
                    'debut_pause': 'Pas de pause',
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
        getActiveDaysCount() {
            return Object.values(this.jours).filter(jour => !jour.is_off).length;
        },
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
            if (fin_pause && fin_journee) {
                if (fin_pause >= fin_journee) {
                    this.message = 'La fin de pause de ' + days + ' doit se terminer avant la fin de journée';
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
                if (!this.jours[item]['debut_pause'] || this.jours[item]['debut_pause'] === 'Pas de pause') {
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
                    this.showSuccess('Rotation créée avec succès');
                    this.$emit('storeRotation', res.data)
                    this.closeModal()
                })
                .catch(error => {
                    if (error.response?.data?.message) {
                        this.message = error.response.data.message;
                        this.showError(error.response.data.message);
                    } else {
                        this.showError('Une erreur est survenue lors de la création de la rotation');
                    }
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
                    this.showSuccess('Rotation modifiée avec succès');
                    this.$emit('storeRotation', res.data, true)
                    this.closeModal()
                })
                .catch(error => {
                    if (error.response?.data?.message) {
                        this.message = error.response.data.message;
                        this.showError(error.response.data.message);
                    } else {
                        this.showError('Une erreur est survenue lors de la modification de la rotation');
                    }
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

<style scoped>

</style>
