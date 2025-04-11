<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
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
const news = ref([]);
const error = ref(null);
const lastFetchTime = ref(0); // Timestamp de la dernière requête

const settings = ref({
    maxItems: 10,
    refreshInterval: 60, // minutes (minimum 10)
    ...(props.widget.pivot?.settings || {})
});

// S'assurer que l'intervalle de rafraîchissement est d'au moins 10 minutes
if (settings.value.refreshInterval < 10) {
    settings.value.refreshInterval = 10;
}

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
};

const fetchNews = async (forceRefresh = false) => {
    // Vérifier si la dernière requête a été faite il y a moins de 10 minutes
    const now = Date.now();
    const minInterval = 10 * 60 * 1000; // 10 minutes en millisecondes

    if (!forceRefresh && lastFetchTime.value > 0 && now - lastFetchTime.value < minInterval) {
        console.log(`Requête ignorée: dernière requête il y a ${Math.floor((now - lastFetchTime.value) / 1000)} secondes (minimum: ${minInterval / 1000} secondes)`);
        return; // Ne pas exécuter la requête si le délai minimum n'est pas écoulé
    }

    isLoading.value = true;
    error.value = null;

    try {
        // Utiliser un proxy CORS ou une API backend pour récupérer les données
        const response = await axios.post('/api/fetch-news', {
            source: 'universfreebox',
            maxItems: settings.value.maxItems
        });

        news.value = response.data && response.data.items ? response.data.items : [];
        lastFetchTime.value = now; // Mettre à jour le timestamp de la dernière requête
    } catch (err) {
        console.error('Error fetching news:', err);
        error.value = 'Impossible de récupérer les actualités';
        news.value = [];
    } finally {
        isLoading.value = false;
    }
};

const updateSettings = () => {
    if (!props.editable) return;

    // S'assurer que l'intervalle de rafraîchissement est d'au moins 10 minutes
    if (settings.value.refreshInterval < 10) {
        settings.value.refreshInterval = 10;
    }

    axios.patch(route('widgets.update.settings', props.widget.id), {
        settings: settings.value
    })
    .then(() => {
        fetchNews(true); // Force le rafraîchissement des données
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

// Fonction pour ouvrir un lien dans un nouvel onglet
const openLink = (url) => {
    window.open(url, '_blank');
};

let refreshInterval = null;

onMounted(() => {
    fetchNews(true); // Force le premier chargement

    // Rafraîchir les actualités selon l'intervalle défini
    refreshInterval = setInterval(() => {
        fetchNews(); // Les vérifications de délai sont gérées dans la fonction
    }, settings.value.refreshInterval * 60 * 1000);
});

onUnmounted(() => {
    // Nettoyer l'intervalle lorsque le composant est détruit
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
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

            <div v-else-if="news.length === 0" class="flex-grow flex items-center justify-center text-gray-500 dark:text-gray-400">
                <div class="text-center">
                    <i class="fas fa-newspaper text-2xl mb-2"></i>
                    <p>Aucune actualité disponible</p>
                </div>
            </div>

            <div v-else class="flex-grow overflow-y-auto" style="max-height: 300px;">
                <ul class="space-y-3">
                    <li v-for="item in news" :key="item.id"
                        class="border-l-4 border-blue-500 pl-3 py-1 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors cursor-pointer"
                        @click="openLink(item.link)">
                        <div class="flex flex-col">
                            <div class="font-medium text-sm">{{ item.title }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 flex justify-between">
                                <span>{{ formatDate(item.pubDate) }}</span>
                                <span class="italic">Universfreebox.com</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <template #settings>
            <div class="space-y-3">
                <div>
                    <label for="max_items" class="block text-sm font-medium mb-1">Nombre d'actualités</label>
                    <input type="number" id="max_items" v-model="settings.maxItems" min="1" max="10"
                           class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="refresh_interval" class="block text-sm font-medium mb-1">Intervalle de rafraîchissement (minutes)</label>
                    <input type="number" id="refresh_interval" v-model="settings.refreshInterval" min="10" max="120"
                           class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-xs text-gray-500 mt-1">Minimum: 10 minutes, Maximum: 120 minutes</p>
                </div>

                <button @click="updateSettings"
                        class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Enregistrer
                </button>
            </div>
        </template>
    </BaseWidget>
</template>
