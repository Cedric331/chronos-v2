<template>
    <Modal :show="showPlanning" maxWidth="5xl" @close="$emit('close')">
        <Loading :show="isLoading"></Loading>
        
        <div class="p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h2 class="text-2xl font-bold mb-2" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                        {{ $t('team_rotation.title_planning') }}
                    </h2>
                    <p class="text-sm" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                        Créez un planning automatique en sélectionnant les dates, le collaborateur et les rotations
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

            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <!-- Step 1 -->
                    <div class="flex items-center flex-1">
                        <div class="flex flex-col items-center">
                            <div 
                                :class="[
                                    'flex items-center justify-center w-12 h-12 rounded-full border-2 transition-all duration-300',
                                    tabs >= 1 && dateStart && dateEnd && user 
                                        ? 'bg-green-500 border-green-500 text-white' 
                                        : tabs >= 1 
                                            ? 'bg-blue-500 border-blue-500 text-white' 
                                            : 'bg-gray-200 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'
                                ]"
                            >
                                <svg v-if="tabs >= 1 && dateStart && dateEnd && user" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <svg v-else class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            <p class="mt-2 text-xs font-medium text-center max-w-[100px]" :style="{ color: tabs >= 1 ? (isDarkMode ? '#ffffff' : '#111827') : (isDarkMode ? '#6b7280' : '#9ca3af') }">
                                Dates & Collaborateur
                            </p>
                        </div>
                        <div 
                            :class="[
                                'flex-1 h-0.5 mx-2 transition-all duration-300',
                                tabs >= 2 ? 'bg-blue-500' : 'bg-gray-200 dark:bg-gray-700'
                            ]"
                        ></div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex items-center flex-1">
                        <div class="flex flex-col items-center">
                            <div 
                                :class="[
                                    'flex items-center justify-center w-12 h-12 rounded-full border-2 transition-all duration-300',
                                    tabs >= 2 && rotations.length > 0
                                        ? 'bg-green-500 border-green-500 text-white'
                                        : tabs >= 2
                                            ? 'bg-blue-500 border-blue-500 text-white'
                                            : 'bg-gray-200 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'
                                ]"
                            >
                                <svg v-if="tabs >= 2 && rotations.length > 0" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <svg v-else class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                </svg>
                            </div>
                            <p class="mt-2 text-xs font-medium text-center max-w-[100px]" :style="{ color: tabs >= 2 ? (isDarkMode ? '#ffffff' : '#111827') : (isDarkMode ? '#6b7280' : '#9ca3af') }">
                                Sélection des rotations
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 1: Dates & Collaborateur -->
            <div v-if="tabs === 1" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                    <h3 class="text-lg font-semibold mb-6" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                        Sélection des dates et du collaborateur
                    </h3>

                    <div class="space-y-6">
                        <!-- Date de début -->
                        <div>
                            <label for="start" class="block text-sm font-medium mb-2" :style="{ color: isDarkMode ? '#e5e7eb' : '#374151' }">
                                Semaine de début du planning
                            </label>
                            <input 
                                type="week" 
                                id="start" 
                                name="trip-start" 
                                v-model="dateStart"
                                :min="dateLimitStart" 
                                :max="dateEnd"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors"
                            />
                        </div>

                        <!-- Date de fin -->
                        <div>
                            <label for="end" class="block text-sm font-medium mb-2" :style="{ color: isDarkMode ? '#e5e7eb' : '#374151' }">
                                Semaine de fin du planning
                            </label>
                            <input 
                                type="week" 
                                id="end" 
                                name="trip-end"
                                v-model="dateEnd"
                                :min="dateStart" 
                                :max="dateLimitEnd"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors"
                            />
                        </div>

                        <!-- Sélection du collaborateur -->
                        <div>
                            <label for="user" class="block text-sm font-medium mb-2" :style="{ color: isDarkMode ? '#e5e7eb' : '#374151' }">
                                Choisir le conseiller
                            </label>
                            <select 
                                id="user"
                                v-model="user" 
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors"
                            >
                                <option :value="null" disabled>Sélectionnez un collaborateur</option>
                                <option v-for="userOption in team.users" :key="userOption.id" :value="userOption">
                                    {{ userOption.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Avertissement si planning existant -->
                        <div v-if="user && user.hasPlanning" class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-amber-800 dark:text-amber-300 mb-2">
                                        Ce collaborateur dispose déjà d'un planning
                                    </p>
                                    <p class="text-sm text-amber-700 dark:text-amber-400 mb-3">
                                        Souhaitez-vous écraser les jours suivants ? <strong>(Congés Payés, RJF, Maladie, Formation, CP Matin, CP Après-midi, Congés sans solde)</strong>
                                    </p>
                                    <div class="flex items-center">
                                        <Switch v-model="type_fix" />
                                        <span class="ml-3 text-sm text-amber-700 dark:text-amber-400">Écraser les jours existants</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Sélection des rotations -->
            <div v-if="tabs === 2" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                    <h3 class="text-lg font-semibold mb-6" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                        Choix des rotations
                    </h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Rotations sélectionnées -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-semibold" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                    Rotations sélectionnées
                                </h4>
                                <Badge v-if="rotations.length > 0" variant="success" size="sm">
                                    {{ rotations.length }} rotation{{ rotations.length > 1 ? 's' : '' }}
                                </Badge>
                            </div>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <div v-if="rotations.length === 0" class="p-8 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-sm" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                                        Aucune rotation sélectionnée
                                    </p>
                                </div>
                                <div v-else class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                                    <div 
                                        v-for="(rotation, index) in rotations" 
                                        :key="rotation.id"
                                        class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors flex items-center justify-between group"
                                    >
                                        <div class="flex-1">
                                            <p class="font-semibold" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                                {{ rotation.name }}
                                            </p>
                                            <p class="text-xs mt-1" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                                                {{ rotation.total_hours }}
                                            </p>
                                        </div>
                                        <button
                                            @click="rotations.splice(index, 1)"
                                            class="p-2 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100 focus:outline-none focus:ring-2 focus:ring-red-500"
                                            aria-label="Retirer la rotation"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rotations disponibles -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-semibold" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                    Rotations disponibles
                                </h4>
                                <Badge variant="info" size="sm">
                                    {{ team.rotations.length }} rotation{{ team.rotations.length > 1 ? 's' : '' }}
                                </Badge>
                            </div>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <div v-if="team.rotations.length === 0" class="p-8 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-sm" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                                        Aucune rotation disponible
                                    </p>
                                </div>
                                <div v-else class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                                    <div 
                                        v-for="rotation in team.rotations" 
                                        :key="rotation.id"
                                        class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors flex items-center justify-between group cursor-pointer"
                                        @click="addRotation(rotation)"
                                    >
                                        <div class="flex-1">
                                            <p class="font-semibold" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                                {{ rotation.name }}
                                            </p>
                                            <div class="flex items-center mt-1 space-x-2">
                                                <Badge 
                                                    :variant="rotation.total_hours === '35h00' ? 'success' : 'warning'"
                                                    size="xs"
                                                >
                                                    {{ rotation.total_hours }}
                                                </Badge>
                                            </div>
                                        </div>
                                        <button
                                            @click.stop="addRotation(rotation)"
                                            class="p-2 rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            aria-label="Ajouter la rotation"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation & Actions -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button
                    v-if="tabs > 1"
                    @click="tabs--"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Précédent
                </button>
                <div v-else></div>

                <div class="flex items-center space-x-3">
                    <SecondaryButton @click="$emit('close')">
                        Annuler
                    </SecondaryButton>
                    <button
                        v-if="tabs < 2"
                        @click="tabs++"
                        :disabled="tabs === 1 && (!dateStart || !dateEnd || !user)"
                        :class="[
                            'inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors',
                            tabs === 1 && (!dateStart || !dateEnd || !user) ? 'opacity-50 cursor-not-allowed' : ''
                        ]"
                    >
                        Suivant
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <PrimaryButton 
                        v-else
                        @click="generatePlanning()" 
                        :disabled="!user || !dateStart || !dateEnd || rotations.length === 0"
                        :class="[
                            !user || !dateStart || !dateEnd || rotations.length === 0 ? 'opacity-50 cursor-not-allowed' : ''
                        ]"
                    >
                        <svg class="w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Générer le Planning
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Loading from "@/Components/Loading.vue";
import Switch from "@/Components/Switch.vue";
import Badge from "@/Components/Badge.vue";
import { useStore } from 'vuex';
import { useNotification } from '@/composables/useNotification';
import { computed } from 'vue';
import axios from "axios";

export default {
    name: "ModalPlanning",
    components: {
        Switch,
        Loading,
        Modal,
        PrimaryButton,
        SecondaryButton,
        Badge
    },
    props: {
        team: Object,
        showPlanning: Boolean
    },
    emits: ['close'],
    setup() {
        const store = useStore();
        const isDarkMode = computed(() => store.state.isDarkMode);
        const { showSuccess, showError } = useNotification();
        return { isDarkMode, showSuccess, showError };
    },
    data () {
        return {
            tabs: 1,
            rotations: [],
            isLoading: false,
            type_fix: false,
            user: null,
            dateStart: null,
            dateEnd: null,
        }
    },
    computed: {
        dateLimitStart() {
            const now = new Date();
            const day = now.getDay();
            const diff = now.getDate() - day + (day == 0 ? -6 : 1);
            const monday = new Date(now.setDate(diff));
            const weekNumber = this.getWeekNumber(monday);
            return `${monday.getFullYear()}-W${weekNumber.toString().padStart(2, '0')}`;
        },
        dateLimitEnd() {
            const now = new Date();
            now.setFullYear(now.getFullYear() + 1);
            now.setMonth(now.getMonth() + 1);
            const weekNumber = this.getWeekNumber(now);
            return `${now.getFullYear()}-W${weekNumber.toString().padStart(2, '0')}`;
        },
    },
    methods: {
        addRotation(rotation) {
            if (!this.rotations.find(r => r.id === rotation.id)) {
                this.rotations.push(rotation);
            }
        },
        generatePlanning () {
            this.isLoading = true
            axios.post('/planning/generate', {
              type_fix: this.type_fix,
              team: this.team.id,
              user: this.user.id,
              rotations: this.rotations,
              dateStart: this.dateStart,
              dateEnd: this.dateEnd,
          })
              .then(() => {
                  this.showSuccess('Planning généré avec succès !');
                  this.$emit('close')
              })
              .catch(error => {
                  console.log(error)
                  this.showError('Une erreur est survenue lors de la génération du planning');
              })
              .finally(() => {
                  this.isLoading = false
              })
        },
        getWeekNumber(d) {
            d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
            d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
            const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
            const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
            return weekNo;
        }
    }
}
</script>

<style scoped>

</style>
