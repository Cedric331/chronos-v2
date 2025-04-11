<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import PlanningStatsWidget from '@/Components/Widgets/PlanningStatsWidget.vue';
import UpcomingEventsWidget from '@/Components/Widgets/UpcomingEventsWidget.vue';
import TeamPresenceWidget from '@/Components/Widgets/TeamPresenceWidget.vue';
import NewsWidget from '@/Components/Widgets/NewsWidget.vue';
import BirthdaysWidget from '@/Components/Widgets/BirthdaysWidget.vue';

const props = defineProps({
    userWidgets: {
        type: Array,
        default: () => []
    },
    weeklyHours: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['update', 'remove']);

// Widgets actifs (réactifs aux changements)
const activeWidgets = ref([...props.userWidgets]);

// Observer les changements dans userWidgets
watch(() => props.userWidgets, (newWidgets) => {
    activeWidgets.value = [...newWidgets];
}, { deep: true, immediate: true });

// Map of widget component names to actual components
const widgetComponents = {
    'PlanningStatsWidget': PlanningStatsWidget,
    'UpcomingEventsWidget': UpcomingEventsWidget,
    'TeamPresenceWidget': TeamPresenceWidget,
    'NewsWidget': NewsWidget,
    'BirthdaysWidget': BirthdaysWidget
};

// Computed property to determine if we should show widgets
const hasWidgets = computed(() => {
    return activeWidgets.value && activeWidgets.value.length > 0;
});
</script>

<template>
    <!-- Hide on mobile devices -->
    <div v-if="hasWidgets" class="hidden md:block mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <component
                v-for="widget in activeWidgets"
                :key="widget.id"
                :is="widgetComponents[widget.component_name]"
                :widget="widget"
                :editable="true"
                @update="$emit('update', $event)"
                @remove="$emit('remove', $event)"
            />
        </div>
    </div>
</template>
