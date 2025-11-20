<script setup>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Badge from '@/Components/Badge.vue';
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';

const store = useStore();
const isDarkMode = computed(() => store.state.isDarkMode);

const props = defineProps({
    show: Boolean,
    selectedWidgets: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'update']);

const { showSuccess, showError } = useNotification();

const availableWidgets = ref([
    {
        id: 'planning-stats',
        name: 'Statistiques',
        slug: 'planning-stats',
        component_name: 'PlanningStatsWidget',
        icon: 'fas fa-chart-bar',
        description: 'Visualisez vos statistiques de planning avec des graphiques et indicateurs clés',
        color: 'blue',
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
        description: 'Consultez les événements et jours importants à venir',
        color: 'purple',
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
        description: 'Suivez la présence et les absences de votre équipe en temps réel',
        color: 'green',
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
        description: 'Restez informé des dernières actualités de Universfreebox.com',
        color: 'amber',
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
        description: 'Ne manquez jamais un anniversaire des membres de votre équipe',
        color: 'pink',
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
            showError('Vous ne pouvez pas sélectionner plus de 4 widgets.');
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
        showSuccess('Vos préférences de widgets ont été enregistrées.');

        // Émettre l'événement update avec les widgets sélectionnés
        const widgetsCopy = JSON.parse(JSON.stringify(selectedWidgetObjects));
        emit('update', widgetsCopy);

        // Fermer le modal après un court délai
        setTimeout(() => {
            emit('close');
        }, 300);
    })
    .catch(error => {
        console.error(error);
        showError('Une erreur est survenue lors de l\'enregistrement de vos préférences.');
    })
    .finally(() => {
        isLoading.value = false;
    });
};

const selectedCount = computed(() => selectedWidgetIds.value.length);
const maxWidgets = 4;
</script>

<template>
    <Modal :show="show" max-width="3xl" @close="$emit('close')">
        <div class="p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h2 
                        class="text-2xl font-bold mb-1"
                        :style="{ color: isDarkMode ? '#ffffff' : '#111827' }"
                    >
                        Gérer les widgets du planning
                    </h2>
                    <p 
                        class="text-sm"
                        :style="{ color: isDarkMode ? '#9ca3af' : '#4b5563' }"
                    >
                        Personnalisez votre tableau de bord en sélectionnant jusqu'à {{ maxWidgets }} widgets
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

            <!-- Selection Counter -->
            <div class="mb-6 flex items-center justify-between p-4 bg-gradient-to-r from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-xl border border-amber-200 dark:border-amber-800">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-amber-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-amber-900 dark:text-amber-200">
                            Widgets sélectionnés
                        </p>
                        <p class="text-xs text-amber-700 dark:text-amber-300">
                            {{ selectedCount }} / {{ maxWidgets }} widgets
                        </p>
                    </div>
                </div>
                <Badge 
                    :variant="selectedCount >= maxWidgets ? 'warning' : 'primary'"
                    size="lg"
                >
                    {{ selectedCount }} / {{ maxWidgets }}
                </Badge>
            </div>

            <!-- Widgets Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                <div
                    v-for="widget in availableWidgets"
                    :key="widget.id"
                    class="group relative border-2 rounded-xl p-5 cursor-pointer transition-all duration-300 transform"
                    :class="isWidgetSelected(widget.id) 
                        ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-400 shadow-lg scale-105' 
                        : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-md hover:scale-102'"
                    @click="toggleWidget(widget.id)"
                    role="button"
                    tabindex="0"
                    @keyup.enter="toggleWidget(widget.id)"
                    @keyup.space.prevent="toggleWidget(widget.id)"
                >
                    <!-- Selection Indicator -->
                    <div 
                        v-if="isWidgetSelected(widget.id)"
                        class="absolute top-3 right-3"
                    >
                        <div class="flex items-center justify-center w-8 h-8 bg-amber-500 rounded-full shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>

                    <!-- Widget Icon -->
                    <div class="flex items-start mb-4">
                        <div 
                            class="flex items-center justify-center w-12 h-12 rounded-xl shadow-sm transition-transform duration-300 group-hover:scale-110"
                            :class="{
                                'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400': widget.color === 'blue',
                                'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400': widget.color === 'purple',
                                'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400': widget.color === 'green',
                                'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400': widget.color === 'amber',
                                'bg-pink-100 text-pink-600 dark:bg-pink-900/30 dark:text-pink-400': widget.color === 'pink',
                            }"
                        >
                            <i :class="[widget.icon, 'text-xl']"></i>
                        </div>
                    </div>

                    <!-- Widget Content -->
                    <div>
                        <h3 
                            class="text-lg font-semibold mb-2 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors duration-200"
                            :style="{ color: isDarkMode ? '#ffffff' : '#111827' }"
                        >
                            {{ widget.name }}
                        </h3>
                        <p 
                            class="text-sm leading-relaxed"
                            :style="{ color: isDarkMode ? '#9ca3af' : '#4b5563' }"
                        >
                            {{ widget.description }}
                        </p>
                    </div>

                    <!-- Hover Effect -->
                    <div 
                        class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                        :class="{
                            'bg-gradient-to-br from-blue-500/5 to-transparent': widget.color === 'blue',
                            'bg-gradient-to-br from-purple-500/5 to-transparent': widget.color === 'purple',
                            'bg-gradient-to-br from-green-500/5 to-transparent': widget.color === 'green',
                            'bg-gradient-to-br from-amber-500/5 to-transparent': widget.color === 'amber',
                            'bg-gradient-to-br from-pink-500/5 to-transparent': widget.color === 'pink',
                        }"
                    ></div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <p 
                    class="text-xs text-center sm:text-left"
                    :style="{ color: isDarkMode ? '#9ca3af' : '#6b7280' }"
                >
                    <i class="fas fa-info-circle mr-1"></i>
                    Les widgets sélectionnés apparaîtront au-dessus de votre planning
                </p>
                <div class="flex space-x-3 w-full sm:w-auto">
                    <SecondaryButton 
                        @click="$emit('close')"
                        class="w-full sm:w-auto"
                    >
                        Annuler
                    </SecondaryButton>
                    <PrimaryButton 
                        @click="saveWidgetPreferences" 
                        :disabled="isLoading"
                        class="w-full sm:w-auto"
                    >
                        <span v-if="isLoading" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Enregistrement...
                        </span>
                        <span v-else class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Enregistrer
                        </span>
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
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
