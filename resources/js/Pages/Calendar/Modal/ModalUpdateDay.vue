<template>
    <Modal :show="show" :closeable="true" :maxWidth="'4xl'">
        <div class="p-0">
            <!-- Header avec onglets -->
            <TabGroup as="div" @change="changeTab">
                <TabList class="flex bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <Tab 
                        v-slot="{ selected }"
                        class="flex-1 focus:outline-none"
                    >
                        <div 
                            class="px-6 py-4 text-center transition-all duration-200 cursor-pointer"
                            :class="selected 
                                ? 'bg-white dark:bg-gray-700 text-amber-600 dark:text-amber-400 border-b-2 border-amber-500 font-semibold' 
                                : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-750'"
                        >
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span class="text-base font-medium">Horaire spécifique</span>
                            </div>
                        </div>
                    </Tab>
                    <Tab 
                        v-slot="{ selected }"
                        class="flex-1 focus:outline-none"
                    >
                        <div 
                            class="px-6 py-4 text-center transition-all duration-200 cursor-pointer"
                            :class="selected 
                                ? 'bg-white dark:bg-gray-700 text-amber-600 dark:text-amber-400 border-b-2 border-amber-500 font-semibold' 
                                : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-750'"
                        >
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span class="text-base font-medium">Depuis une rotation</span>
                            </div>
                        </div>
                    </Tab>
                </TabList>

                <TabPanels>
                    <!-- Panel 1: Horaire spécifique -->
                    <TabPanel class="focus:outline-none">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                            <!-- Formulaire principal -->
                            <div class="lg:col-span-2 space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold mb-4" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                        Configuration de l'horaire
                                    </h3>
                                    
                                    <!-- Type de jour -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium mb-2" :style="{ color: isDarkMode ? '#e5e7eb' : '#374151' }">
                                            Type de jour
                                        </label>
                                        <select 
                                            :disabled="rotation !== null"
                                            v-model="type_day" 
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:ring-opacity-75 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <option v-for="params in $page.props.auth.team.params.type_day" :key="params" :value="params">
                                                {{ params }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Horaires (si Planifié ou Formation) -->
                                    <div v-if="isTypeDayPlanifieOrFormation" class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium mb-2" :style="{ color: isDarkMode ? '#e5e7eb' : '#374151' }">
                                                    Début journée
                                                </label>
                                                <select 
                                                    :disabled="rotation !== null"
                                                    v-model="debut_journee" 
                                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:ring-opacity-75 transition-all duration-200 disabled:opacity-50"
                                                >
                                                    <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                                        {{ horaire }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-2" :style="{ color: isDarkMode ? '#e5e7eb' : '#374151' }">
                                                    Début pause
                                                </label>
                                                <select 
                                                    :disabled="rotation !== null"
                                                    v-model="debut_pause" 
                                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:ring-opacity-75 transition-all duration-200 disabled:opacity-50"
                                                >
                                                    <option selected>Pas de pause</option>
                                                    <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                                        {{ horaire }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div v-if="debut_pause !== null && debut_pause !== 'Pas de pause'">
                                                <label class="block text-sm font-medium mb-2" :style="{ color: isDarkMode ? '#e5e7eb' : '#374151' }">
                                                    Fin pause
                                                </label>
                                                <select 
                                                    :disabled="rotation !== null"
                                                    v-model="fin_pause" 
                                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:ring-opacity-75 transition-all duration-200 disabled:opacity-50"
                                                >
                                                    <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                                        {{ horaire }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium mb-2" :style="{ color: isDarkMode ? '#e5e7eb' : '#374151' }">
                                                    Fin journée
                                                </label>
                                                <select 
                                                    :disabled="rotation !== null"
                                                    v-model="fin_journee" 
                                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:ring-opacity-75 transition-all duration-200 disabled:opacity-50"
                                                >
                                                    <option v-for="(horaire, index) in horaires" :key="index" :value="horaire">
                                                        {{ horaire }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Options -->
                                        <div class="flex flex-wrap gap-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center space-x-3">
                                                <SwitchGroup>
                                                    <div class="flex items-center">
                                                        <SwitchLabel class="text-sm font-medium mr-3" :style="{ color: isDarkMode ? '#e5e7eb' : '#374151' }">
                                                            Technicien
                                                        </SwitchLabel>
                                                        <Switch
                                                            v-model="is_technician"
                                                            :disabled="rotation !== null"
                                                            :class="is_technician ? 'bg-amber-500' : 'bg-gray-300 dark:bg-gray-600'"
                                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-opacity-75 focus:ring-offset-2 disabled:opacity-50"
                                                        >
                                                            <span
                                                                :class="is_technician ? 'translate-x-6' : 'translate-x-1'"
                                                                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                                            />
                                                        </Switch>
                                                    </div>
                                                </SwitchGroup>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <SwitchGroup>
                                                    <div class="flex items-center">
                                                        <SwitchLabel class="text-sm font-medium mr-3" :style="{ color: isDarkMode ? '#e5e7eb' : '#374151' }">
                                                            Télétravail
                                                        </SwitchLabel>
                                                        <Switch
                                                            v-model="telework"
                                                            :disabled="rotation !== null"
                                                            :class="telework ? 'bg-amber-500' : 'bg-gray-300 dark:bg-gray-600'"
                                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-opacity-75 focus:ring-offset-2 disabled:opacity-50"
                                                        >
                                                            <span
                                                                :class="telework ? 'translate-x-6' : 'translate-x-1'"
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

                            <!-- Dates sélectionnées -->
                            <div class="lg:col-span-1">
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 h-full">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-sm font-semibold" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                            {{ daySelected.length > 1 ? 'Dates sélectionnées' : 'Date sélectionnée' }}
                                        </h3>
                                        <Badge variant="info" size="sm">
                                            {{ daySelected.length }}
                                        </Badge>
                                    </div>
                                    <div class="space-y-2 max-h-96 overflow-y-auto custom-scrollbar">
                                        <div 
                                            v-for="day in daySelected" 
                                            :key="day.id"
                                            class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200"
                                        >
                                            <span class="text-sm font-medium" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                                {{ day.date }}
                                            </span>
                                            <button
                                                @click="deleteDay(day)"
                                                class="p-1.5 rounded-md text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-75"
                                                aria-label="Supprimer cette date"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TabPanel>

                    <!-- Panel 2: Rotation -->
                    <TabPanel class="focus:outline-none">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                            <!-- Liste des rotations -->
                            <div class="lg:col-span-2">
                                <h3 class="text-lg font-semibold mb-4" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                    Sélectionner une rotation
                                </h3>
                                <div class="space-y-3 max-h-[600px] overflow-y-auto custom-scrollbar pr-2">
                                    <div 
                                        v-for="rotation in $page.props.auth.team.rotations" 
                                        :key="rotation.id"
                                        @click.prevent="selectRotation(rotation.id)"
                                        :id="rotation.name"
                                        class="p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 transform hover:scale-102"
                                        :class="selectedRotation === rotation.id
                                            ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20 shadow-lg'
                                            : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-md'"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </div>
                                                <span class="font-semibold" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                                    {{ rotation.name }}
                                                </span>
                                            </div>
                                            <div v-if="selectedRotation === rotation.id" class="flex items-center justify-center w-6 h-6 bg-amber-500 rounded-full">
                                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dates sélectionnées -->
                            <div class="lg:col-span-1">
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 h-full">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-sm font-semibold" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                            {{ daySelected.length > 1 ? 'Dates sélectionnées' : 'Date sélectionnée' }}
                                        </h3>
                                        <Badge variant="info" size="sm">
                                            {{ daySelected.length }}
                                        </Badge>
                                    </div>
                                    <div class="space-y-2 max-h-96 overflow-y-auto custom-scrollbar">
                                        <div 
                                            v-for="day in daySelected" 
                                            :key="day.id"
                                            class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200"
                                        >
                                            <span class="text-sm font-medium" :style="{ color: isDarkMode ? '#ffffff' : '#111827' }">
                                                {{ day.date }}
                                            </span>
                                            <button
                                                @click="deleteDay(day)"
                                                class="p-1.5 rounded-md text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-75"
                                                aria-label="Supprimer cette date"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TabPanel>
                </TabPanels>
            </TabGroup>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0 sm:space-x-4">
                <p class="text-xs text-center sm:text-left" :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }">
                    <i class="fas fa-info-circle mr-1"></i>
                    Les modifications seront appliquées aux dates sélectionnées
                </p>
                <div class="flex space-x-3 w-full sm:w-auto">
                    <SecondaryButton @click="$emit('close')" class="w-full sm:w-auto">
                        Annuler
                    </SecondaryButton>
                    <PrimaryButton @click="updatePlannig()" class="w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Enregistrer
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import {Switch, SwitchGroup, SwitchLabel, Tab, TabGroup, TabList, TabPanel, TabPanels} from '@headlessui/vue'
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Badge from "@/Components/Badge.vue";
import { useStore } from 'vuex';
import { useNotification } from '@/composables/useNotification';
import tippy from "tippy.js";
import 'tippy.js/dist/tippy.css';
import axios from "axios";

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
        Modal,
        Badge
    },
    props: {
        show: Boolean,
        daySelected: Object
    },
    data () {
        return {
            selectedRotation: null,
            rotation: null,
            horaires: [
                '08h00', '08h30', '09h00', '09h30', '10h00', '10h30',
                '11h00', '11h30', '12h00', '12h30', '13h00', '13h30',
                '14h00', '14h30', '15h00', '15h30', '16h00', '16h30',
                '17h00', '17h30', '18h00', '18h30', '19h00', '19h30',
                '20h00', '20h30', '21h00'
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
        initTooltips() {
            if (!this.$page.props.auth.team || !this.$page.props.auth.team.rotations) {
                return;
            }

            // Utiliser setTimeout pour s'assurer que le DOM est complètement rendu
            setTimeout(() => {
                // Nettoyer les tooltips existants
                this.$page.props.auth.team.rotations.forEach(rotation => {
                    const element = document.getElementById(rotation.name);
                    if (element && element._tippy) {
                        element._tippy.destroy();
                    }
                });

                // Créer les nouveaux tooltips
                this.$page.props.auth.team.rotations.forEach(rotation => {
                    if (!rotation.details || rotation.details.length === 0) {
                        return;
                    }

                    const isDark = this.isDarkMode;
                    const bgColor = isDark ? '#1f2937' : '#ffffff';
                    const textColor = isDark ? '#f3f4f6' : '#111827';
                    const borderColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';
                    const secondaryTextColor = isDark ? '#9ca3af' : '#6b7280';
                    const titleColor = isDark ? '#ffffff' : '#111827';
                    
                    let content = `<div style='padding: 12px; min-width: 220px; background-color: ${bgColor}; color: ${textColor};'>`;
                    content += `<div style='font-weight: 600; margin-bottom: 10px; font-size: 14px; color: ${titleColor};'>${rotation.name}</div>`;
                    
                    rotation.details.forEach((day, index) => {
                        const isLast = index === rotation.details.length - 1;
                        content += `<div style='margin-bottom: ${isLast ? '0' : '8px'}; padding-bottom: ${isLast ? '0' : '8px'}; border-bottom: ${isLast ? 'none' : `1px solid ${borderColor}`};'>`;
                        content += `<div style='font-weight: 500; margin-bottom: 4px; color: ${textColor};'>${day.day}</div>`;
                        
                        if (day.is_off) {
                            content += `<div style='color: ${secondaryTextColor}; font-size: 12px;'>Repos</div>`;
                        } else if (day.debut_journee) {
                            content += `<div style='font-size: 12px; color: ${textColor};'>`;
                            content += `<div style='margin-bottom: 2px;'>Début: ${day.debut_journee}</div>`;
                            if (day.debut_pause && day.fin_pause) {
                                content += `<div style='margin-bottom: 2px;'>Pause: ${day.debut_pause} - ${day.fin_pause}</div>`;
                            }
                            content += `<div>Fin: ${day.fin_journee}</div>`;
                            content += `</div>`;
                        } else {
                            content += `<div style='color: ${secondaryTextColor}; font-size: 12px;'>Non planifié</div>`;
                        }
                        content += `</div>`;
                    });
                    content += "</div>";

                    const element = document.getElementById(rotation.name);
                    if (element) {
                        tippy(element, {
                            placement: 'right',
                            content: content,
                            allowHTML: true,
                            interactive: false,
                            delay: [200, 0],
                            appendTo: () => document.body,
                            theme: isDark ? 'dark' : 'light',
                        });
                    }
                });
            }, 100);
        },
        changeTab(index) {
            this.tab = index
            if (index === 1) {
                // Utiliser un double nextTick pour s'assurer que le DOM est complètement rendu
                this.$nextTick(() => {
                    this.$nextTick(() => {
                        this.initTooltips();
                    });
                });
            } else {
                // Nettoyer les tooltips quand on quitte l'onglet rotation
                if (this.$page.props.auth.team && this.$page.props.auth.team.rotations) {
                    this.$page.props.auth.team.rotations.forEach(rotation => {
                        const element = document.getElementById(rotation.name);
                        if (element && element._tippy) {
                            element._tippy.destroy();
                        }
                    });
                }
                this.selectedRotation = null;
            }
        },
        deleteDay (day) {
            this.$emit('deleteDayList', day)
        },
        selectRotation (rotation) {
            if (this.selectedRotation === rotation) {
                this.selectedRotation = null
            } else {
                this.selectedRotation = rotation
            }
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
                        this.showSuccess('Horaire mis à jour avec succès');
                        this.$emit('update', response.data)
                        setTimeout(() => {
                            this.$emit('close')
                        }, 500);
                    })
                    .catch(error => {
                        console.log(error)
                        this.showError(error.response?.data?.message || 'Une erreur est survenue lors de la mise à jour');
                    })
            } else {
                if (!this.selectedRotation) {
                    this.showError('Veuillez sélectionner une rotation');
                    return;
                }
                axios.patch('planning/update/rotation', {
                    days: this.daySelected,
                    rotation_id: this.selectedRotation,
                    })
                    .then(response => {
                        this.showSuccess('Rotation appliquée avec succès');
                        this.$emit('update', response.data)
                        setTimeout(() => {
                            this.$emit('close')
                        }, 500);
                    })
                    .catch(error => {
                        console.log(error)
                        this.showError('Une erreur est survenue lors de l\'application de la rotation');
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
        },
        show: {
            handler(newVal) {
                if (newVal && this.tab === 1) {
                    // Si la modal s'ouvre et l'onglet rotation est actif, initialiser les tooltips
                    this.initTooltips();
                }
            },
            immediate: false,
        }
    },
    computed: {
        isTypeDayPlanifieOrFormation() {
            return this.type_day === 'Planifié' || this.type_day === 'Formation' || this.type_day === 'CP Après-midi' || this.type_day === 'CP Matin';
        },
        isDarkMode() {
            return this.$store.state.isDarkMode;
        }
    },
    created() {
        const { showSuccess, showError } = useNotification();
        this.showSuccess = showSuccess;
        this.showError = showError;
    },
    beforeUnmount() {
        // Nettoyer tous les tooltips avant la destruction du composant
        if (this.$page.props.auth.team && this.$page.props.auth.team.rotations) {
            this.$page.props.auth.team.rotations.forEach(rotation => {
                const element = document.getElementById(rotation.name);
                if (element && element._tippy) {
                    element._tippy.destroy();
                }
            });
        }
    }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    @apply bg-gray-100 dark:bg-gray-800 rounded-lg;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-gray-300 dark:bg-gray-600 rounded-lg;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    @apply bg-gray-400 dark:bg-gray-500;
}

.hover\:scale-102:hover {
    transform: scale(1.02);
}
</style>
