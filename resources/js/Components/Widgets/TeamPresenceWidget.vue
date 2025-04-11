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
const teamMembers = ref([]);
const selectedDate = ref(new Date().toISOString().split('T')[0]);

const settings = ref({
    showAvatars: true,
    showAbsent: true,
    ...(props.widget.pivot?.settings || {})
});

const presentMembers = computed(() => {
    return teamMembers.value.filter(member => member.isPresent);
});

const absentMembers = computed(() => {
    return teamMembers.value.filter(member => !member.isPresent);
});

const fetchTeamPresence = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post('/planning/team-presence', {
            date: selectedDate.value
        });

        teamMembers.value = response.data;
    } catch (error) {
        console.error('Error fetching team presence:', error);
    } finally {
        isLoading.value = false;
    }
};

const updateDate = (event) => {
    selectedDate.value = event.target.value;
    fetchTeamPresence();
};

const updateSettings = () => {
    if (!props.editable) return;

    axios.patch(route('widgets.update.settings', props.widget.id), {
        settings: settings.value
    })
    .then(() => {
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

onMounted(() => {
    fetchTeamPresence();
});
</script>

<template>
    <BaseWidget :widget="widget" :editable="editable" @remove="$emit('remove', $event)" @update="$emit('update', $event)">
        <div class="h-full flex flex-col">
            <div class="mb-3">
                <input
                    type="date"
                    :value="selectedDate"
                    @change="updateDate"
                    class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                >
            </div>

            <div v-if="isLoading" class="flex-grow flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
            </div>

            <div v-else class="flex-grow overflow-auto">
                <div class="mb-2">
                    <h3 class="font-medium text-green-600 dark:text-green-400 mb-1">Présents ({{ presentMembers.length }})</h3>
                    <ul class="space-y-1">
                        <li v-for="member in presentMembers" :key="member.id" class="flex items-center">
                            <img v-if="settings.showAvatars" :src="member.avatar || '/images/avatar_default.png'" @error="e => e.target.src = '/images/avatar_default.png'" class="w-6 h-6 rounded-full mr-2" :alt="member.name">
                            <span>{{ member.name }}</span>
                            <span v-if="member.telework" class="ml-2 text-xs px-1 py-0.5 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 rounded">
                                Télétravail
                            </span>
                        </li>
                    </ul>
                </div>

                <div v-if="settings.showAbsent && absentMembers.length > 0">
                    <h3 class="font-medium text-red-600 dark:text-red-400 mb-1">Absents ({{ absentMembers.length }})</h3>
                    <ul class="space-y-1">
                        <li v-for="member in absentMembers" :key="member.id" class="flex items-center">
                            <img v-if="settings.showAvatars" :src="member.avatar || '/images/avatar_default.png'" @error="e => e.target.src = '/images/avatar_default.png'" class="w-6 h-6 rounded-full mr-2" :alt="member.name">
                            <span>{{ member.name }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <template #settings>
            <div class="space-y-3">
                <div class="flex items-center">
                    <input type="checkbox" id="show_avatars" v-model="settings.showAvatars" class="mr-2">
                    <label for="show_avatars">Afficher les avatars</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="show_absent" v-model="settings.showAbsent" class="mr-2">
                    <label for="show_absent">Afficher les absents</label>
                </div>

                <button @click="updateSettings"
                        class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Enregistrer
                </button>
            </div>
        </template>
    </BaseWidget>
</template>
