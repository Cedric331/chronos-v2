<script setup>
import { ref, computed, onMounted } from 'vue';
import BaseWidget from './BaseWidget.vue';
import axios from 'axios';

const props = defineProps({
    widget: {
        type: Object,
        required: true
    },
    editable: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update', 'remove']);

const isLoading = ref(true);
const birthdays = ref([]);
const error = ref(null);

const settings = ref({
    daysAhead: 365, // Par défaut, afficher les anniversaires sur toute l'année
    teamOnly: true, // Par défaut, afficher uniquement les anniversaires de l'équipe
    limit: 10, // Par défaut, limiter à 10 anniversaires
    ...(props.widget.pivot?.settings || {})
});

// Filtrer et trier les anniversaires par date
const sortedBirthdays = computed(() => {
    return [...birthdays.value].sort((a, b) => new Date(a.date) - new Date(b.date));
});

const fetchBirthdays = async () => {
    isLoading.value = true;
    error.value = null;

    try {
        const response = await axios.post('/api/birthdays', {
            daysAhead: settings.value.daysAhead,
            teamOnly: settings.value.teamOnly,
            limit: settings.value.limit
        });

        birthdays.value = response.data || [];
    } catch (err) {
        console.error('Error fetching birthdays:', err);
        error.value = 'Impossible de récupérer les anniversaires';

        birthdays.value = [];
    } finally {
        isLoading.value = false;
    }
};

const updateSettings = () => {
    if (!props.editable) return;

    axios.patch(route('widgets.update.settings', props.widget.id), {
        settings: settings.value
    })
    .then(() => {
        fetchBirthdays();
        // Pas de notification, les changements sont visibles immédiatement
    })
    .catch(error => {
        console.error(error);
        // En cas d'erreur, on peut afficher une notification discrète
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed bottom-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-50';
        errorDiv.innerHTML = 'Erreur lors de la mise à jour des paramètres';
        document.body.appendChild(errorDiv);
        setTimeout(() => errorDiv.remove(), 3000);
    });
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    // Si c'est aujourd'hui
    if (date.toDateString() === today.toDateString()) {
        return "Aujourd'hui";
    }

    // Si c'est demain
    if (date.toDateString() === tomorrow.toDateString()) {
        return "Demain";
    }

    // Sinon, format standard
    return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long' });
};

const getDaysUntil = (dateString) => {
    const eventDate = new Date(dateString);
    const today = new Date();

    // Réinitialiser les heures pour comparer uniquement les dates
    eventDate.setHours(0, 0, 0, 0);
    today.setHours(0, 0, 0, 0);

    const diffTime = eventDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return '';
    if (diffDays === 1) return '(demain)';
    return `(dans ${diffDays} jours)`;
};

onMounted(() => {
    fetchBirthdays();
});
</script>

<template>
    <BaseWidget :widget="widget" :editable="editable" @remove="$emit('remove', $event)" @update="$emit('update', $event)">
        <div class="h-full flex flex-col">
            <div v-if="isLoading" class="flex-grow flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-pink-500"></div>
            </div>

            <div v-else-if="error" class="flex-grow flex items-center justify-center text-red-500">
                <div class="text-center">
                    <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                    <p>{{ error }}</p>
                </div>
            </div>

            <div v-else-if="sortedBirthdays.length === 0" class="flex-grow flex items-center justify-center text-gray-500 dark:text-gray-400">
                <div class="text-center">
                    <i class="fas fa-birthday-cake text-2xl mb-2"></i>
                    <p>Aucun anniversaire à venir</p>
                    <p class="text-xs mt-2">Vérifiez que les dates de naissance sont renseignées dans les profils utilisateurs</p>
                </div>
            </div>

            <div v-else class="flex-grow overflow-y-auto" style="max-height: 300px;">
                <div class="space-y-3">
                    <div v-for="(birthdaysGroup, date) in sortedBirthdays.reduce((groups, birthday) => {
                        const date = formatDate(birthday.date);
                        if (!groups[date]) groups[date] = [];
                        groups[date].push(birthday);
                        return groups;
                    }, {})" :key="date" class="mb-2">
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1 px-1">
                            {{ date }} <span class="font-normal text-xs text-gray-500">{{ getDaysUntil(birthdaysGroup[0].date) }}</span>
                        </div>
                        <ul class="space-y-2">
                            <li v-for="birthday in birthdaysGroup" :key="birthday.id"
                                :class="[
                                    'border-l-4 pl-3 py-2 rounded-r-md',
                                    (birthday.is_team_member === true) ?
                                        'border-pink-500 bg-pink-50 dark:bg-pink-900/30' :
                                        'border-gray-300 bg-gray-50 dark:bg-gray-800/50'
                                ]">
                                <div class="flex items-start">
                                    <i class="fas fa-birthday-cake mt-0.5 mr-2"
                                       :class="(birthday.is_team_member === true) ? 'text-pink-500' : 'text-gray-400'"></i>
                                    <div>
                                        <div class="flex items-center flex-wrap">
                                            <span class="font-medium text-sm">{{ birthday.name }}</span>
                                            <span v-if="birthday.is_team_member === true"
                                                  class="ml-2 text-xs px-1.5 py-0.5 bg-pink-100 text-pink-700 dark:bg-pink-900 dark:text-pink-300 rounded-full">
                                                Équipe
                                            </span>
                                        </div>
                                        <div class="flex items-center text-xs">
                                            <span v-if="birthday.team_name" class="text-gray-500 dark:text-gray-400">
                                                {{ birthday.team_name }}
                                            </span>
                                            <span v-if="birthday.age" class="text-gray-600 dark:text-gray-400 ml-2">
                                                {{ birthday.age }} ans
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <template #settings>
            <div class="space-y-3">
                <div>
                    <label for="days_ahead" class="block text-sm font-medium mb-1">Jours à l'avance</label>
                    <input type="number" id="days_ahead" v-model="settings.daysAhead" min="30" max="365"
                           class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-xs text-gray-500 mt-1">Nombre de jours à l'avance pour afficher les anniversaires (365 = année entière)</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="team_only" v-model="settings.teamOnly" class="mr-2">
                    <label for="team_only">Afficher uniquement mon équipe</label>
                </div>

                <div>
                    <label for="limit" class="block text-sm font-medium mb-1">Nombre maximum d'anniversaires</label>
                    <input type="number" id="limit" v-model="settings.limit" min="5" max="50"
                           class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <button @click="updateSettings"
                        class="mt-2 px-3 py-1 bg-pink-500 text-white rounded hover:bg-pink-600">
                    Enregistrer
                </button>
            </div>
        </template>
    </BaseWidget>
</template>
