<script setup>
import { ref, onMounted, computed } from 'vue';
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

const settings = ref({
    daysAhead: 30,
    ...(props.widget.pivot?.settings || {})
});

// Pas de limite sur le nombre d'événements, on affiche tout avec défilement si nécessaire

const visibleEvents = computed(() => {
    let filteredEvents = [...events.value];

    // Filtrer les événements passés (toujours)
    const now = new Date();
    filteredEvents = filteredEvents.filter(event => {
        const eventDate = new Date(event.date);
        return eventDate >= now;
    });

    // Pas de limite sur le nombre d'événements, on affiche tout
    return filteredEvents;
});

const fetchEvents = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post('/planning/events', {
            daysAhead: settings.value.daysAhead
        });

        events.value = response.data;
        console.log('Événements récupérés:', events.value.length, events.value);
    } catch (error) {
        console.error('Error fetching events:', error);
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
    return date.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });
};

const getEventTypeClass = (type) => {
    switch (type) {
        case 'Télétravail':
            return 'border-purple-500 bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300';
        case 'Repos':
            return 'border-green-500 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300';
        case 'Planifié':
        default:
            return 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300';
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

            <div v-else-if="visibleEvents.length === 0" class="flex-grow flex items-center justify-center text-gray-500 dark:text-gray-400">
                <div class="text-center">
                    <i class="fas fa-calendar-times text-2xl mb-2"></i>
                    <p>Aucun événement à venir</p>
                </div>
            </div>

            <div v-else class="flex-grow overflow-y-auto" style="max-height: 300px; min-height: 200px;">
                <div class="h-full">
                    <ul class="space-y-2 mb-2">
                        <li v-for="event in visibleEvents" :key="event.id" class="border-l-4 pl-2 py-1" :class="getEventTypeClass(event.type)">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium">{{ event.title }}</div>
                                    <div class="text-xs">{{ formatDate(event.date) }}</div>
                                </div>
                                <div class="text-xs px-2 py-1 rounded-full" :class="getEventTypeClass(event.type)">
                                    {{ event.type }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <template #settings>
            <div class="space-y-3">
                <div>
                    <label for="days_ahead" class="block text-sm font-medium mb-1">Jours à l'avance</label>
                    <input type="number" id="days_ahead" v-model="settings.daysAhead" min="1" max="90"
                           class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-xs text-gray-500 mt-1">Nombre de jours à l'avance pour récupérer les événements</p>
                </div>

                <button @click="updateSettings"
                        class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Enregistrer
                </button>
            </div>
        </template>
    </BaseWidget>
</template>
