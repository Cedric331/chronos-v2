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
const events = ref([]);
const error = ref(null);

const settings = ref({
    showBirthdays: true,
    showTeamEvents: true,
    daysAhead: 30,
    ...(props.widget.pivot?.settings || {})
});

// Filtrer les événements selon les paramètres
const filteredEvents = computed(() => {
    let result = [...events.value];

    if (!settings.value.showBirthdays) {
        result = result.filter(event => event.type !== 'birthday');
    }

    if (!settings.value.showTeamEvents) {
        result = result.filter(event => event.type !== 'team_event');
    }

    return result.sort((a, b) => new Date(a.date) - new Date(b.date));
});

const fetchEvents = async () => {
    isLoading.value = true;
    error.value = null;

    try {
        const response = await axios.post('/api/team-events', {
            daysAhead: settings.value.daysAhead
        });

        if (response.data && response.data.length > 0) {
            events.value = response.data;
        } else {
            // Données de démonstration
            const today = new Date();
            events.value = [
                {
                    id: 'birthday_1',
                    title: 'Anniversaire de Jean Dupont',
                    description: null,
                    date: new Date(today.getFullYear(), today.getMonth(), today.getDate() + 3).toISOString().split('T')[0],
                    type: 'birthday'
                },
                {
                    id: 'birthday_2',
                    title: 'Anniversaire de Marie Martin',
                    description: null,
                    date: new Date(today.getFullYear(), today.getMonth(), today.getDate() + 10).toISOString().split('T')[0],
                    type: 'birthday'
                },
                {
                    id: 'event_1',
                    title: 'Réunion d\'\u00e9quipe',
                    description: 'Réunion mensuelle de l\'\u00e9quipe',
                    date: new Date(today.getFullYear(), today.getMonth(), today.getDate() + 5).toISOString().split('T')[0],
                    type: 'team_event'
                },
                {
                    id: 'event_2',
                    title: 'Afterwork',
                    description: 'Afterwork de fin de mois',
                    date: new Date(today.getFullYear(), today.getMonth(), today.getDate() + 15).toISOString().split('T')[0],
                    type: 'team_event'
                }
            ];
            console.log('Utilisation des données de démonstration pour le widget Anniversaires & Événements');
        }
    } catch (err) {
        console.error('Error fetching events:', err);
        error.value = 'Impossible de récupérer les événements';

        // Données de démonstration en cas d'erreur
        const today = new Date();
        events.value = [
            {
                id: 'birthday_1',
                title: 'Anniversaire de Jean Dupont',
                description: null,
                date: new Date(today.getFullYear(), today.getMonth(), today.getDate() + 3).toISOString().split('T')[0],
                type: 'birthday'
            },
            {
                id: 'event_1',
                title: 'Réunion d\'\u00e9quipe',
                description: 'Réunion mensuelle de l\'\u00e9quipe',
                date: new Date(today.getFullYear(), today.getMonth(), today.getDate() + 5).toISOString().split('T')[0],
                type: 'team_event'
            }
        ];
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
        fetchEvents();
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

const getEventIcon = (type) => {
    switch (type) {
        case 'birthday':
            return 'fas fa-birthday-cake text-pink-500';
        case 'team_event':
            return 'fas fa-glass-cheers text-purple-500';
        default:
            return 'fas fa-calendar-day text-blue-500';
    }
};

const getEventClass = (type) => {
    switch (type) {
        case 'birthday':
            return 'border-pink-500 bg-pink-50 dark:bg-pink-900/30';
        case 'team_event':
            return 'border-purple-500 bg-purple-50 dark:bg-purple-900/30';
        default:
            return 'border-blue-500 bg-blue-50 dark:bg-blue-900/30';
    }
};

onMounted(() => {
    fetchEvents();
});
</script>

<template>
    <BaseWidget :widget="widget" :editable="editable" @remove="$emit('remove', $event)" @update="$emit('update', $event)">
        <div class="h-full flex flex-col">
            <div v-if="isLoading" class="flex-grow flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
            </div>

            <div v-else-if="error" class="flex-grow flex items-center justify-center text-red-500">
                <div class="text-center">
                    <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                    <p>{{ error }}</p>
                </div>
            </div>

            <div v-else-if="filteredEvents.length === 0" class="flex-grow flex items-center justify-center text-gray-500 dark:text-gray-400">
                <div class="text-center">
                    <i class="fas fa-calendar-times text-2xl mb-2"></i>
                    <p>Aucun événement à venir</p>
                </div>
            </div>

            <div v-else class="flex-grow overflow-y-auto" style="max-height: 300px;">
                <div class="space-y-3">
                    <div v-for="(eventsGroup, date) in filteredEvents.reduce((groups, event) => {
                        const date = formatDate(event.date);
                        if (!groups[date]) groups[date] = [];
                        groups[date].push(event);
                        return groups;
                    }, {})" :key="date" class="mb-2">
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1 px-1">
                            {{ date }} <span class="font-normal text-xs text-gray-500">{{ getDaysUntil(eventsGroup[0].date) }}</span>
                        </div>
                        <ul class="space-y-2">
                            <li v-for="event in eventsGroup" :key="event.id"
                                class="border-l-4 pl-3 py-2 rounded-r-md"
                                :class="getEventClass(event.type)">
                                <div class="flex items-start">
                                    <i :class="getEventIcon(event.type)" class="mt-0.5 mr-2"></i>
                                    <div>
                                        <div class="font-medium text-sm">{{ event.title }}</div>
                                        <div v-if="event.description" class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ event.description }}
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
                <div class="flex items-center">
                    <input type="checkbox" id="show_birthdays" v-model="settings.showBirthdays" class="mr-2">
                    <label for="show_birthdays">Afficher les anniversaires</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="show_team_events" v-model="settings.showTeamEvents" class="mr-2">
                    <label for="show_team_events">Afficher les événements d'équipe</label>
                </div>

                <div>
                    <label for="days_ahead" class="block text-sm font-medium mb-1">Jours à l'avance</label>
                    <input type="number" id="days_ahead" v-model="settings.daysAhead" min="7" max="90"
                           class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <button @click="updateSettings"
                        class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Enregistrer
                </button>
            </div>
        </template>
    </BaseWidget>
</template>
