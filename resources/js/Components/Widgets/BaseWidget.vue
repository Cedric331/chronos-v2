<script setup>
import { ref, computed } from 'vue';
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

const emit = defineEmits(['remove', 'update']);

const showSettings = ref(false);
const isLoading = ref(false);
const showConfirmModal = ref(false);
const showNotificationModal = ref(false);
const notificationMessage = ref('');
const notificationType = ref('success'); // 'success', 'error', 'info'

const widgetClasses = computed(() => {
    return [
        'widget',
        'rounded-lg',
        'shadow',
        'overflow-hidden',
        'flex',
        'flex-col',
        props.editable ? 'border-2 border-dashed border-gray-300 dark:border-gray-700' : '',
        'bg-white dark:bg-gray-800 text-gray-800 dark:text-white'
    ];
});

const toggleSettings = () => {
    showSettings.value = !showSettings.value;
};

const showNotification = (message, type = 'success') => {
    notificationMessage.value = message;
    notificationType.value = type;
    showNotificationModal.value = true;

    // Auto-hide notification after 3 seconds
    setTimeout(() => {
        showNotificationModal.value = false;
    }, 3000);
};

const confirmRemoveWidget = () => {
    if (!props.editable) return;
    showConfirmModal.value = true;
};

const removeWidget = () => {
    showConfirmModal.value = false;
    isLoading.value = true;

    // Émettre l'événement remove directement sans appel API
    // La suppression sera gérée par le composant parent
    emit('remove', props.widget);
    showNotification("Le widget a été supprimé avec succès.", "success");
    isLoading.value = false;
};
</script>

<template>
    <div :class="widgetClasses" :style="{
        gridColumn: `span ${widget.pivot?.width || 1}`,
        gridRow: `span ${widget.pivot?.height || 1}`,
        opacity: widget.pivot?.is_visible !== false ? 1 : 0.6
    }">
        <!-- Widget Header -->
        <div class="widget-header p-3 flex justify-between items-center border-b dark:border-gray-700">
            <div class="flex items-center">
                <i :class="[widget.icon, 'mr-2 text-blue-500']"></i>
                <h3 class="font-medium">{{ widget.name }}</h3>
            </div>

            <div class="flex space-x-1">

                <button v-if="editable" @click="toggleSettings"
                        class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-cog"></i>
                </button>

                <button v-if="editable" @click="confirmRemoveWidget"
                        class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-red-500">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>

        <!-- Widget Content -->
        <div class="widget-content p-3 flex-grow">
            <slot></slot>
        </div>

        <!-- Widget Settings (if open) -->
        <div v-if="showSettings && editable" class="widget-settings p-3 border-t dark:border-gray-700">
            <slot name="settings"></slot>
        </div>

        <!-- Confirmation Modal -->
        <div v-if="showConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full shadow-xl">
                <h3 class="text-lg font-medium mb-4 dark:text-white">Confirmation</h3>
                <p class="mb-6 dark:text-gray-300">Êtes-vous sûr de vouloir supprimer le widget "{{ widget.name }}" ?</p>
                <div class="flex justify-end space-x-3">
                    <button @click="showConfirmModal = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                        Annuler
                    </button>
                    <button @click="removeWidget"
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>

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
    </div>
</template>

<style scoped>
.widget {
    transition: all 0.3s ease;
    height: 100%;
}

.widget:hover {
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}
</style>
