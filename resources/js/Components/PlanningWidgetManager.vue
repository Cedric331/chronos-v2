<script setup>
import { ref, computed, onMounted } from 'vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    selectedWidgets: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'update']);

const showNotificationModal = ref(false);
const notificationMessage = ref('');
const notificationType = ref('success'); // 'success', 'error', 'info'

const showNotification = (message, type = 'success') => {
    notificationMessage.value = message;
    notificationType.value = type;
    showNotificationModal.value = true;

    // Auto-hide notification after 3 seconds
    setTimeout(() => {
        showNotificationModal.value = false;
    }, 3000);
};

const availableWidgets = ref([
    {
        id: 'planning-stats',
        name: 'Statistiques',
        slug: 'planning-stats',
        component_name: 'PlanningStatsWidget',
        icon: 'fas fa-chart-bar',
        description: 'Statistiques de planning',
        default_settings: {
            period: 'month',
            showTelework: false
        }
    },
    {
        id: 'upcoming-events',
        name: 'Jours à venir',
        slug: 'upcoming-events',
        component_name: 'UpcomingEventsWidget',
        icon: 'fas fa-calendar-alt',
        description: 'Jours à venir',
        default_settings: {
            daysAhead: 7,
            showPastEvents: false,
            maxEvents: 5
        }
    },
    {
        id: 'team-presence',
        name: 'Présence équipe',
        slug: 'team-presence',
        component_name: 'TeamPresenceWidget',
        icon: 'fas fa-users',
        description: "Présence de l'équipe",
        default_settings: {
            showAvatars: true,
            showAbsent: true
        }
    },
    {
        id: 'news-widget',
        name: 'Actualités Freebox',
        slug: 'news-widget',
        component_name: 'NewsWidget',
        icon: 'fas fa-newspaper',
        description: 'Actualités de Universfreebox.com',
        default_settings: {
            maxItems: 5,
            refreshInterval: 60
        }
    },
    {
        id: 'birthdays',
        name: 'Anniversaires',
        slug: 'birthdays',
        component_name: 'BirthdaysWidget',
        icon: 'fas fa-birthday-cake',
        description: 'Anniversaires des membres de l\'\u00e9quipe',
        default_settings: {
            daysAhead: 365,
            teamOnly: true,
            limit: 10
        }
    }
]);

const selectedWidgetIds = ref([]);
const isLoading = ref(false);

// Initialize selected widgets from props
onMounted(() => {
    selectedWidgetIds.value = props.selectedWidgets.map(widget => widget.id);
});

const isWidgetSelected = (widgetId) => {
    return selectedWidgetIds.value.includes(widgetId);
};

const toggleWidget = (widgetId) => {
    if (isWidgetSelected(widgetId)) {
        // Remove widget
        selectedWidgetIds.value = selectedWidgetIds.value.filter(id => id !== widgetId);
    } else {
        // Add widget if less than 4 are selected
        if (selectedWidgetIds.value.length < 4) {
            selectedWidgetIds.value.push(widgetId);
        } else {
            // Show error notification
            showNotification("Vous ne pouvez pas sélectionner plus de 4 widgets.", "error");
        }
    }
};

const saveWidgetPreferences = () => {
    isLoading.value = true;

    // Get the full widget objects for the selected IDs
    const selectedWidgetObjects = availableWidgets.value.filter(widget =>
        selectedWidgetIds.value.includes(widget.id)
    ).map((widget, index) => ({
        ...widget,
        pivot: {
            position_x: index,
            position_y: 0,
            width: 1,
            height: 1,
            settings: widget.default_settings,
            is_visible: true
        }
    }));

    axios.post('/planning/widgets/preferences', {
        widgets: selectedWidgetObjects
    })
    .then(response => {
        showNotification("Vos préférences de widgets ont été enregistrées.", "success");

        // Émettre l'événement update avec les widgets sélectionnés
        // Créer une copie profonde pour éviter les problèmes de référence
        const widgetsCopy = JSON.parse(JSON.stringify(selectedWidgetObjects));
        emit('update', widgetsCopy);

        // Fermer le modal après un court délai pour permettre la mise à jour
        setTimeout(() => {
            emit('close');
        }, 100);
    })
    .catch(error => {
        console.error(error);
        showNotification("Une erreur est survenue lors de l'enregistrement de vos préférences.", "error");
    })
    .finally(() => {
        isLoading.value = false;
    });
};
</script>

<template>
    <Modal :show="show" max-width="md" @close="$emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                Gérer les widgets du planning
            </h2>

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Sélectionnez jusqu'à 4 widgets à afficher au-dessus de votre planning.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div
                    v-for="widget in availableWidgets"
                    :key="widget.id"
                    class="border rounded-lg p-4 cursor-pointer transition-all duration-200"
                    :class="isWidgetSelected(widget.id) ?
                        'border-blue-500 bg-blue-50 dark:bg-blue-900/30 dark:border-blue-400' :
                        'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600'"
                    @click="toggleWidget(widget.id)"
                >
                    <div class="flex items-center mb-2">
                        <i :class="[widget.icon, 'mr-2 text-blue-500']"></i>
                        <h3 class="font-medium dark:text-white">{{ widget.name }}</h3>
                        <div v-if="isWidgetSelected(widget.id)" class="ml-auto">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ widget.description }}</p>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <SecondaryButton @click="$emit('close')">
                    Annuler
                </SecondaryButton>

                <PrimaryButton @click="saveWidgetPreferences" :disabled="isLoading">
                    <i v-if="isLoading" class="fas fa-spinner fa-spin mr-2"></i>
                    Enregistrer
                </PrimaryButton>
            </div>
        </div>
    </Modal>

    <!-- Notification Modal -->
    <div v-if="showNotificationModal"
         class="fixed bottom-4 right-4 bg-white dark:bg-gray-800 rounded-lg p-4 shadow-lg z-50 max-w-sm"
         :class="{
            'border-l-4 border-green-500': notificationType === 'success',
            'border-l-4 border-red-500': notificationType === 'error',
            'border-l-4 border-blue-500': notificationType === 'info'
         }">
        <div class="flex items-center">
            <div v-if="notificationType === 'success'" class="text-green-500 mr-3">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div v-else-if="notificationType === 'error'" class="text-red-500 mr-3">
                <i class="fas fa-exclamation-circle text-xl"></i>
            </div>
            <div v-else class="text-blue-500 mr-3">
                <i class="fas fa-info-circle text-xl"></i>
            </div>
            <div>
                <h4 class="font-medium dark:text-white">{{ notificationType === 'success' ? 'Succès' : notificationType === 'error' ? 'Erreur' : 'Information' }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ notificationMessage }}</p>
            </div>
            <button @click="showNotificationModal = false" class="ml-auto text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</template>
