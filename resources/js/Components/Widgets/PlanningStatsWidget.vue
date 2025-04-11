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
const stats = ref({
    totalDays: 0,
    workDays: 0,
    restDays: 0,
    teleworkDays: 0
});

const settings = ref({
    period: 'month',
    showTelework: false,
    ...(props.widget.pivot?.settings || {})
});

const workPercentage = computed(() => {
    if (stats.value.totalDays === 0) return 0;
    return Math.round((stats.value.workDays / stats.value.totalDays) * 100);
});

const restPercentage = computed(() => {
    if (stats.value.totalDays === 0) return 0;
    return Math.round((stats.value.restDays / stats.value.totalDays) * 100);
});

const teleworkPercentage = computed(() => {
    if (stats.value.workDays === 0) return 0;
    return Math.round((stats.value.teleworkDays / stats.value.workDays) * 100);
});

// Fonction pour récupérer les statistiques supplémentaires
const getAdditionalStats = () => {

    // Priorité des clés pour chaque type
    const typeConfig = [
        {
            label: 'Jours planifiés',
            color: 'green',
            keys: ['plannedDays', 'type_planifié_count'],
            description: 'Jours de travail planifiés'
        },
        {
          label: 'Jours de repos',
          color: 'blue',
          keys: ['restDays', 'type_repos_count']
        },
        // {
        //     label: 'Télétravail',
        //     color: 'purple',
        //     keys: ['teleworkDays'],
        //     description: 'Jours de télétravail'
        // },
        {
            label: 'Congés payés',
            color: 'blue',
            keys: ['type_congés_payés_count', 'paidLeaveDays']
        },
        {
          label: 'Jour férié',
          color: 'purple',
          keys: ['type_jour_férié_count', 'holidayDays']
        },
        {
            label: 'Arrêt maladie',
            color: 'red',
            keys: ['type_arrêt_maladie_count', 'sickLeaveDays']
        },
        {
            label: 'Formation',
            color: 'yellow',
            keys: ['type_formation_count', 'trainingDays']
        },
    ];

    // Créer le résultat final
    const result = [];

    typeConfig.forEach((config, index) => {
        // Trouver la première clé disponible pour ce type
        let value = 0;
        let keyFound = false;

        for (const key of config.keys) {
            // Recherche exacte d'abord
            if (stats.value[key] !== undefined) {
                value = stats.value[key];
                keyFound = true;
                break;
            }

            // Recherche partielle ensuite
            const partialMatch = Object.keys(stats.value).find(k =>
                k.toLowerCase().includes(key.toLowerCase()) ||
                key.toLowerCase().includes(k.toLowerCase())
            );

            if (partialMatch) {
                value = stats.value[partialMatch];
                keyFound = true;
                break;
            }
        }

        // Si aucune clé n'est trouvée, utiliser 0
        const percentage = stats.value.totalDays > 0 ? Math.round((value / stats.value.totalDays) * 100) : 0;

        result.push({
            label: config.label,
            color: config.color,
            count: value,
            percentage: percentage,
            order: index
        });
    });

    return result;
};

const fetchStats = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post('/planning/stats', {
            period: settings.value.period
        });

        stats.value = response.data;
        console.log('Statistiques reçues:', stats.value);
    } catch (error) {
        console.error('Error fetching planning stats:', error);
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
        fetchStats();
        // Pas de notification, juste rafraîchir les données
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
    fetchStats();
});
</script>

<template>
    <BaseWidget :widget="widget" :editable="editable" @remove="$emit('remove', $event)" @update="$emit('update', $event)">
        <div class="h-full flex flex-col">
            <div v-if="isLoading" class="flex-grow flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
            </div>

            <div v-else class="flex-grow overflow-y-auto" style="max-height: 300px;">
                <div class="space-y-2 w-full pr-1">

                    <div v-for="value in getAdditionalStats()" :key="value.label"
                         :class="`bg-${value.color}-50 dark:bg-${value.color}-900 rounded-lg p-2.5 w-full flex justify-between items-center`">
                        <div :class="`text-${value.color}-700 dark:text-${value.color}-200 text-sm`">{{ value.label }}</div>
                        <div class="flex items-center">
                            <div :class="`text-lg font-bold text-${value.color}-600 dark:text-${value.color}-300 mr-2`">{{ value.count }}</div>
                            <div :class="`text-xs text-${value.color}-500 dark:text-${value.color}-400 bg-${value.color}-100 dark:bg-${value.color}-800 px-2 py-0.5 rounded-full`">{{ value.percentage }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <template #settings>
            <div class="space-y-3">
                <div>
                    <label for="period" class="block text-sm font-medium mb-1">Période</label>
                    <select id="period" v-model="settings.period" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="week">Semaine</option>
                        <option value="month">Mois</option>
                        <option value="year">Année</option>
                    </select>
                </div>

<!--                <div class="flex items-center">-->
<!--                    <input type="checkbox" id="show_telework" v-model="settings.showTelework" class="mr-2">-->
<!--                    <label for="show_telework">Afficher les statistiques de télétravail</label>-->
<!--                </div>-->

                <button @click="updateSettings"
                        class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Enregistrer
                </button>
            </div>
        </template>
    </BaseWidget>
</template>
